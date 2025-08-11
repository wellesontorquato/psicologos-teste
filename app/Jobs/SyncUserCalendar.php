<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Sessao;
use App\Services\GoogleCalendarService;
use Google\Service\Exception as GoogleServiceException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncUserCalendar implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var int */
    public $tries = 3;

    /** @var array<int,int> Exponential backoff: 1m, 5m, 15m */
    public $backoff = [60, 300, 900];

    /** @var int Tempo máximo por tentativa (s) */
    public $timeout = 120;

    /**
     * @param int  $userId
     * @param bool $onlyUpcoming true = só futuras (recomendado)
     */
    public function __construct(
        public int $userId,
        public bool $onlyUpcoming = true
    ) {
        //
    }

    public function tags(): array
    {
        return ['calendar-sync', 'user:'.$this->userId];
    }

    public function handle(GoogleCalendarService $gcal): void
    {
        $user = User::find($this->userId);
        if (!$user) {
            return;
        }

        // Sem conexão Google válida? Nada a fazer.
        if (
            !$user->google_connected ||
            empty($user->google_access_token) ||
            empty($user->google_refresh_token)
        ) {
            return;
        }

        // Seleciona sessões do usuário (com paciente garantido)
        $query = Sessao::with('paciente')
            ->whereHas('paciente', fn ($q) => $q->where('user_id', $user->id))
            ->whereNotNull('data_hora')
            ->when($this->onlyUpcoming, fn ($q) => $q->where('data_hora', '>=', now()->subDay()))
            ->where(function ($q) {
                $q->whereNull('google_event_id')
                  ->orWhereIn('google_sync_status', ['pending', 'error']);
            });

        // chunkById usa a coluna id; não misture com orderBy custom
        $query->chunkById(50, function ($sessoes) use ($user, $gcal) {
            foreach ($sessoes as $sessao) {
                try {
                    // Monta janelas de tempo
                    $inicio = Carbon::parse($sessao->data_hora);
                    $fim    = $inicio->copy()->addMinutes((int) $sessao->duracao);

                    // ID determinístico (evita duplicatas ao re-sincronizar)
                    $eventKey = 'psg-'.$user->id.'-'.$sessao->id; // caracteres válidos: [a-z0-9-_]

                    $payload = [
                        'summary'     => "Sessão com {$sessao->paciente->nome}",
                        'description' => $sessao->observacoes ?? null,
                        'start'       => $inicio,
                        'end'         => $fim,
                        'attendees'   => $sessao->paciente->email ? [['email' => $sessao->paciente->email]] : [],
                        // 'location' => 'Online', // se quiser padronizar
                        // 'reminders' => [...],   // opcional, usa default do service se omitido
                    ];

                    if ($sessao->google_event_id) {
                        // Já vinculado: atualiza pelo ID salvo
                        $gcal->updateEvent($user, $sessao->google_event_id, $payload);
                    } else {
                        // Tenta atualizar por eventKey (caso já exista no Google)
                        try {
                            $gcal->updateEvent($user, $eventKey, $payload);
                            $sessao->google_event_id = $eventKey;
                        } catch (GoogleServiceException $e) {
                            if ($e->getCode() === 404) {
                                // Não existe no Google: cria usando o mesmo ID determinístico
                                try {
                                    $newId = $gcal->createEvent($user, $payload + ['id' => $eventKey]);
                                    $sessao->google_event_id = $newId; // deve ser == $eventKey
                                } catch (GoogleServiceException $e2) {
                                    // Se der 409 (ID já em uso por outro calendário/conta), tenta sem ID fixo
                                    if ($e2->getCode() === 409) {
                                        $newId = $gcal->createEvent($user, $payload);
                                        $sessao->google_event_id = $newId;
                                    } else {
                                        throw $e2;
                                    }
                                }
                            } else {
                                throw $e;
                            }
                        }
                    }

                    $sessao->google_sync_status = 'ok';
                    $sessao->google_sync_error  = null;
                    $sessao->save();
                } catch (Throwable $e) {
                    // Marca erro na sessão e segue as próximas (evita travar o lote)
                    $sessao->google_sync_status = 'error';
                    $sessao->google_sync_error  = substr($e->getMessage(), 0, 1000);
                    $sessao->save();

                    // Loga só para monitoramento (não rethrow pra não abortar o chunk)
                    Log::warning('Calendar sync failed', [
                        'user_id'   => $user->id,
                        'sessao_id' => $sessao->id,
                        'error'     => $e->getMessage(),
                    ]);
                }
            }
        });
    }
}
