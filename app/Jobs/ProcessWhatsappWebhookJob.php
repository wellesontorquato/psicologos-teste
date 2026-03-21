<?php

namespace App\Jobs;

use App\Models\WebhookInbox;
use App\Services\WhatsappWebhookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessWhatsappWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 6;
    public int $timeout = 25;
    public array $backoff = [5, 15, 45, 120, 300, 600];

    public function __construct(public int $inboxId) {}

    public function handle(WhatsappWebhookService $service): void
    {
        $inbox = null;

        DB::transaction(function () use (&$inbox) {
            $inbox = WebhookInbox::lockForUpdate()->find($this->inboxId);

            if (!$inbox) {
                throw new ModelNotFoundException("Inbox {$this->inboxId} não encontrado.");
            }

            if ($inbox->status === 'PROCESSED') {
                return;
            }

            $inbox->status = 'PROCESSING';
            $inbox->attempts = (int) $inbox->attempts + 1;
            $inbox->save();
        });

        if (!$inbox) {
            Log::warning('[WPP JOB] Inbox não encontrado', ['inbox_id' => $this->inboxId]);
            return;
        }

        if ($inbox->status === 'PROCESSED') {
            Log::info('[WPP JOB] Inbox já processado, ignorando', [
                'inbox_id' => $this->inboxId,
            ]);
            return;
        }

        try {
            $payload = json_decode($inbox->payload_json, true);

            if (!is_array($payload)) {
                throw new \RuntimeException('Payload JSON inválido no WebhookInbox.');
            }

            $resultado = $service->processar($payload, $inbox->request_id);

            $inbox->update([
                'status' => 'PROCESSED',
                'last_error' => null,
            ]);

            Log::info('[WPP JOB] Processado com sucesso', [
                'inbox_id' => $inbox->id,
                'request_id' => $inbox->request_id,
                'resultado' => $resultado,
            ]);
        } catch (Throwable $e) {
            $tentativaAtual = method_exists($this, 'attempts') ? $this->attempts() : null;
            $ultimaTentativa = $tentativaAtual !== null && $tentativaAtual >= $this->tries;

            $inbox->update([
                'status' => $ultimaTentativa ? 'FAILED' : 'RETRY',
                'last_error' => substr($e->getMessage(), 0, 1000),
            ]);

            Log::error('[WPP JOB] Falha no processamento', [
                'inbox_id' => $inbox->id,
                'request_id' => $inbox->request_id,
                'attempt' => $tentativaAtual,
                'max_tries' => $this->tries,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(Throwable $e): void
    {
        $inbox = WebhookInbox::find($this->inboxId);

        if ($inbox) {
            $inbox->update([
                'status' => 'FAILED',
                'last_error' => substr($e->getMessage(), 0, 1000),
            ]);
        }

        Log::critical('[WPP JOB] Job esgotou todas as tentativas', [
            'inbox_id' => $this->inboxId,
            'error' => $e->getMessage(),
        ]);
    }
}
