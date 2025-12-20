<?php

namespace App\Http\Controllers;

use App\Models\Sessao;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use App\Events\SessaoConfirmada;
use App\Events\SessaoCancelada;
use App\Events\SessaoRemarcada;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Illuminate\Support\Str;
use App\Models\WebhookInbox;
use App\Jobs\ProcessWhatsappWebhookJob;
use Throwable;

class WebhookWhatsappController extends Controller
{
    public function receberMensagem(Request $request)
    {
        $rid = $request->attributes->get('request_id')
            ?? $request->header('X-Request-Id')
            ?? (string) Str::uuid();
        
        try {
            $dados = $request->json()->all();
            if (!is_array($dados) || empty($dados)) $dados = json_decode($request->getContent(), true);
            if (!is_array($dados) || empty($dados)) $dados = $request->all();

            $evento = strtolower(data_get($dados, 'event', 'onmessage'));
            $data   = data_get($dados, 'data', $dados);

            $from = data_get($data, 'from') ?? data_get($data, 'sender.id') ?? data_get($data, 'chatId') ?? data_get($data, 'id.remote');
            $body = data_get($data, 'body') ?? data_get($data, 'message') ?? data_get($data, 'text') ?? data_get($data, 'content') ?? data_get($data, 'caption');

            // message_id se existir; se não, hash estável
            $remoteId = data_get($data, 'id') ?? data_get($data, 'messageId') ?? data_get($data, 'id._serialized');
            $stableKey = $remoteId ?: hash('sha256', $evento.'|'.(string)$from.'|'.(string)$body.'|'.(string)data_get($data,'t',''));

            $inbox = WebhookInbox::firstOrCreate(
                ['message_key' => $stableKey],
                [
                    'source' => 'wppconnect',
                    'request_id' => $rid,
                    'event' => $evento,
                    'from' => (string) $from,
                    'body' => (string) $body,
                    'status' => 'RECEIVED',
                    'payload_json' => json_encode($dados, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
                ]
            );

            if (!$inbox->wasRecentlyCreated && $inbox->status === 'PROCESSED') {
                return response()->json(['ok' => true, 'message' => 'Duplicado ignorado.', 'request_id' => $rid], 200);
            }

            ProcessWhatsappWebhookJob::dispatch($inbox->id)->onQueue('webhooks');

            return response()->json([
                'ok' => true,
                'message' => 'Recebido e enfileirado.',
                'request_id' => $rid,
                'inbox_id' => $inbox->id,
            ], 200);

        } catch (Throwable $e) {
            Log::channel('whatsapp')->error('[Webhook] Erro no recebimento', [
                'request_id' => $rid,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['ok' => false, 'message' => 'Falha ao receber webhook.', 'request_id' => $rid], 500);
        }
    }

    private function encontrarSessaoValidaParaConfirmacao(Paciente $paciente): ?Sessao
    {
        $hoje       = Carbon::today(config('app.timezone'));
        $dataLimite = $hoje->copy()->addDays(5);

        $sessoesCandidatas = Sessao::withoutGlobalScopes()
            ->where('paciente_id', $paciente->id)
            ->where('status_confirmacao', 'PENDENTE')
            ->orderBy('data_hora', 'asc')
            ->get();

        if ($sessoesCandidatas->isEmpty()) {
            Log::channel('whatsapp')->warning('[DIAGNÓSTICO] Nenhuma sessão PENDENTE encontrada.', [
                'paciente_id' => $paciente->id,
            ]);
            return null;
        }

        foreach ($sessoesCandidatas as $sessao) {
            $lembreteOk = (int) $sessao->lembrete_enviado === 1;
            if (!$lembreteOk) continue;

            // Se data_hora estiver nula por algum motivo, pula
            if (empty($sessao->data_hora)) continue;

            $dataSessao = Carbon::parse($sessao->data_hora)->startOfDay();
            $dataOk     = $dataSessao->betweenIncluded($hoje, $dataLimite);
            if (!$dataOk) continue;

            return $sessao;
        }

        Log::channel('whatsapp')->warning('[DIAGNÓSTICO] Nenhuma sessão passou nos critérios (lembrete/data).');
        return null;
    }

    private function encontrarPacientePorTelefone(string $numeroLimpo)
    {
        return Paciente::where(function ($query) use ($numeroLimpo) {
            $query->whereRaw('REGEXP_REPLACE(telefone, "[^0-9]", "") = ?', [$numeroLimpo]);

            // Tenta também com "9" inserido (caso cadastro esteja diferente)
            if (strlen($numeroLimpo) === 10) {
                $ddd           = substr($numeroLimpo, 0, 2);
                $resto         = substr($numeroLimpo, 2);
                $numeroComNove  = $ddd . '9' . $resto;
                $query->orWhereRaw('REGEXP_REPLACE(telefone, "[^0-9]", "") = ?', [$numeroComNove]);
            }
        })->first();
    }

    private function mapearRespostaParaStatus(string $bodyLimpo): ?string
    {
        $mapa = [
            // CANCELAR
            'NAO VOU'        => 'CANCELADA',
            'NÃO VOU'        => 'CANCELADA',
            'CANCELAR'       => 'CANCELADA',
            'CANCELADO'      => 'CANCELADA',
            'CANCELADA'      => 'CANCELADA',
            'DESMARCAR'      => 'CANCELADA',
            'DESMARQUE'      => 'CANCELADA',
            'CANCELE'        => 'CANCELADA',
            '3'              => 'CANCELADA',

            // REMARCAR
            'REMARCAR'       => 'REMARCAR',
            'REMARCACAO'     => 'REMARCAR',
            'REMARCAÇÃO'     => 'REMARCAR',
            'REAGENDAR'      => 'REMARCAR',
            'REAGENDAMENTO'  => 'REMARCAR',
            'REMARQUE'       => 'REMARCAR',
            'MUDAR'          => 'REMARCAR',
            'TROCAR'         => 'REMARCAR',
            'ADIAR'          => 'REMARCAR',
            '2'              => 'REMARCAR',

            // CONFIRMAR
            'CONFIRMADO'     => 'CONFIRMADA',
            'CONFIRMAR'      => 'CONFIRMADA',
            'CONFIRMADA'     => 'CONFIRMADA',
            'CONFIRMEI'      => 'CONFIRMADA',
            'OK'             => 'CONFIRMADA',
            'CERTO'          => 'CONFIRMADA',
            'SIM'            => 'CONFIRMADA',
            'VOU'            => 'CONFIRMADA',
            'ESTAREI'        => 'CONFIRMADA',
            'CONFIRMA'       => 'CONFIRMADA',
            '1'              => 'CONFIRMADA',
        ];

        foreach ($mapa as $chave => $valor) {
            if (Str::contains($bodyLimpo, $chave)) {
                return $valor;
            }
        }

        return null;
    }

    private function normalizarNumero(string $numero): string
    {
        $num = preg_replace('/\D/', '', $numero);

        if (str_starts_with($num, '55')) {
            $num = substr($num, 2);
        }

        return $num;
    }

    private function responderNoWhatsapp(string $numero, string $mensagem): void
    {
        $numeroComPrefixo = '55' . preg_replace('/[^0-9]/', '', $numero);

        $baseUrl = rtrim(config('services.wppconnect.base_url'), '/');
        $session = config('services.wppconnect.session', 'psigestor');
        $token   = config('services.wppconnect.token');

        if (!$baseUrl || !$token) {
            Log::channel('whatsapp')->error('[Webhook] ❌ base_url ou token do WPPConnect não configurados');
            return;
        }

        $endpoint = "{$baseUrl}/api/{$session}/send-message";

        Log::channel('whatsapp')->info('[Webhook] 🚀 Enviando resposta via WPPConnect', [
            'numero'   => $numeroComPrefixo,
            'mensagem' => $mensagem,
            'endpoint' => $endpoint,
        ]);

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->asJson()
                ->post($endpoint, [
                    'phone'        => $numeroComPrefixo,
                    'isGroup'      => false,
                    'isNewsletter' => false,
                    'isLid'        => false,
                    'message'      => $mensagem,
                ]);

            if ($response->failed()) {
                Log::channel('whatsapp')->error('[Webhook] ❌ Falha ao enviar mensagem via WPPConnect', [
                    'numero' => $numeroComPrefixo,
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            } else {
                Log::channel('whatsapp')->info('[Webhook] ✅ Mensagem enviada com sucesso via WPPConnect', [
                    'numero' => $numeroComPrefixo,
                    'res'    => $response->json(),
                ]);
            }
        } catch (Throwable $e) {
            Log::channel('whatsapp')->error('[Webhook] 💥 Erro ao enviar mensagem via WPPConnect', [
                'erro' => $e->getMessage(),
            ]);
        }
    }

    public function testeManual(Request $request)
    {
        $dados = [
            'event' => 'onmessage',
            'data'  => [
                'from'   => '5582999405099@c.us',
                'body'   => 'Confirmado',
                'fromMe' => false,
            ],
        ];

        $symfonyRequest = SymfonyRequest::create(
            '/api/webhook/whatsapp',
            'POST',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($dados)
        );

        $laravelRequest = Request::createFromBase($symfonyRequest);
        return $this->receberMensagem($laravelRequest);
    }

    public function handle(Request $request)
    {
        Log::channel('whatsapp')->info('✅ Webhook recebido (handle simples)', [
            'data'    => $request->all(),
            'ip'      => $request->ip(),
            'headers' => $request->headers->all(),
        ]);

        return response()->json(['status' => 'ok']);
    }

    public function verLogWhatsapp()
    {
        $hoje    = now()->format('Y-m-d');
        $logPath = storage_path("logs/whatsapp-{$hoje}.log");

        if (!File::exists($logPath)) {
            return '<pre style="color: red;">Arquivo de log não encontrado: '.$logPath.'</pre>';
        }

        $conteudo = File::get($logPath);

        if (empty($conteudo)) {
            return '<pre style="color: orange;">Arquivo existe, mas está vazio.</pre>';
        }

        return '<pre style="color: green;">' . htmlentities($conteudo) . '</pre>';
    }
}
