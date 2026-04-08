<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
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
            $dados  = $this->extrairPayload($request);
            $evento = strtolower((string) data_get($dados, 'event', 'onmessage'));
            $data   = data_get($dados, 'data', $dados);

            if (!is_array($data)) {
                $data = [];
            }

            $from   = $this->extrairFrom($data);
            $body   = $this->extrairBody($data);
            $fromMe = (bool) (data_get($data, 'fromMe') ?? data_get($data, 'isMe') ?? false);

            Log::channel('whatsapp')->info('[Webhook] Payload recebido', [
                'request_id' => $rid,
                'event'      => $evento,
                'from'       => $from,
                'fromMe'     => $fromMe,
                'has_body'   => !empty($body),
                'type'       => data_get($data, 'type'),
                'sender_id'  => data_get($data, 'sender.id'),
                'chatId'     => data_get($data, 'chatId'),
                'message_id' => $this->extrairMessageId($data),
            ]);

            if (!$this->deveProcessarEvento($evento, $data)) {
                Log::channel('whatsapp')->info('[Webhook] Evento ignorado', [
                    'request_id' => $rid,
                    'event'      => $evento,
                ]);

                return response()->json([
                    'ok' => true,
                    'message' => 'Evento ignorado.',
                    'request_id' => $rid,
                ], 200);
            }

            if ($fromMe) {
                Log::channel('whatsapp')->info('[Webhook] Mensagem do próprio bot ignorada', [
                    'request_id' => $rid,
                    'event'      => $evento,
                    'from'       => $from,
                ]);

                return response()->json([
                    'ok' => true,
                    'message' => 'Mensagem fromMe ignorada.',
                    'request_id' => $rid,
                ], 200);
            }

            if (empty($from) || $this->isGrupoOuBroadcast($from)) {
                Log::channel('whatsapp')->info('[Webhook] Payload sem remetente válido ou grupo/broadcast', [
                    'request_id' => $rid,
                    'event'      => $evento,
                    'from'       => $from,
                ]);

                return response()->json([
                    'ok' => true,
                    'message' => 'Payload ignorado por remetente inválido.',
                    'request_id' => $rid,
                ], 200);
            }

            $remoteId = $this->extrairMessageId($data);
            $stableKey = $remoteId ?: hash(
                'sha256',
                $evento . '|' .
                (string) $from . '|' .
                (string) $body . '|' .
                (string) data_get($data, 't', data_get($data, 'timestamp', ''))
            );

            $payloadJson = $this->safeJsonEncode($dados);

            $inbox = WebhookInbox::firstOrCreate(
                ['message_key' => $stableKey],
                [
                    'source'       => 'wppconnect',
                    'request_id'   => $rid,
                    'event'        => $evento,
                    'from'         => (string) $from,
                    'body'         => (string) ($body ?? ''),
                    'status'       => 'RECEIVED',
                    'payload_json' => $payloadJson,
                ]
            );

            if (!$inbox->wasRecentlyCreated && $inbox->status === 'PROCESSED') {
                Log::channel('whatsapp')->info('[Webhook] Duplicado já processado', [
                    'request_id'  => $rid,
                    'inbox_id'    => $inbox->id,
                    'message_key' => $stableKey,
                ]);

                return response()->json([
                    'ok' => true,
                    'message' => 'Duplicado ignorado.',
                    'request_id' => $rid,
                ], 200);
            }

            ProcessWhatsappWebhookJob::dispatch($inbox->id)->onQueue('webhooks');

            Log::channel('whatsapp')->info('[Webhook] Inbox enfileirado', [
                'request_id'  => $rid,
                'inbox_id'    => $inbox->id,
                'event'       => $evento,
                'from'        => $from,
                'message_key' => $stableKey,
            ]);

            return response()->json([
                'ok'         => true,
                'message'    => 'Recebido e enfileirado.',
                'request_id' => $rid,
                'inbox_id'   => $inbox->id,
            ], 200);

        } catch (Throwable $e) {
            Log::channel('whatsapp')->error('[Webhook] Erro no recebimento', [
                'request_id'  => $rid,
                'error'       => $e->getMessage(),
                'trace'       => $e->getTraceAsString(),
                'payload_raw' => $request->getContent(),
            ]);

            return response()->json([
                'ok' => true,
                'message' => 'Webhook recebido com falha interna tratada.',
                'request_id' => $rid,
            ], 200);
        }
    }

    public function testeManual(Request $request)
    {
        $dados = [
            'event' => 'onmessage',
            'data'  => [
                'from'   => '5538999814308@c.us',
                'body'   => '1',
                'fromMe' => false,
                'type'   => 'chat',
                'id'     => [
                    '_serialized' => 'CATCHAU12345678901234',
                ],
            ],
        ];

        return $this->processarPayloadInterno($dados);
    }

    public function formSimulacaoAdmin()
    {
        return view('whatsapp.simular');
    }

    public function simularMensagemAdmin(Request $request)
    {
        $validated = $request->validate([
            'telefone'   => ['required', 'string', 'max:30'],
            'resposta'   => ['required', 'string', 'max:5000'],
            'event'      => ['nullable', 'string', 'max:50'],
            'type'       => ['nullable', 'string', 'max:50'],
            'message_id' => ['nullable', 'string', 'max:255'],
        ]);

        $telefoneWhatsapp = $this->normalizarTelefoneParaWhatsappId($validated['telefone']);
        $messageId = !empty($validated['message_id'])
            ? $validated['message_id']
            : 'ADMIN-SIMULACAO-' . Str::upper(Str::random(24));

        $dados = [
            'event' => strtolower(trim($validated['event'] ?? 'onmessage')),
            'data'  => [
                'from'   => $telefoneWhatsapp,
                'body'   => trim($validated['resposta']),
                'fromMe' => false,
                'type'   => strtolower(trim($validated['type'] ?? 'chat')),
                'id'     => [
                    '_serialized' => $messageId,
                ],
                'sender' => [
                    'id' => $telefoneWhatsapp,
                ],
                'chatId' => $telefoneWhatsapp,
                'simulated_by_admin' => true,
                'simulated_at'       => now()->toDateTimeString(),
                'simulated_user_id'  => optional(auth()->user())->id,
            ],
        ];

        Log::channel('whatsapp')->info('[Webhook] Simulação manual via painel admin', [
            'admin_user_id' => optional(auth()->user())->id,
            'telefone'      => $validated['telefone'],
            'telefone_wa'   => $telefoneWhatsapp,
            'resposta'      => $validated['resposta'],
            'event'         => $dados['event'],
            'message_id'    => $messageId,
        ]);

        $response = $this->processarPayloadInterno($dados);

        if ($request->expectsJson()) {
            return $response;
        }

        $content = $response->getData(true);

        return back()->with('success', 'Simulação enviada com sucesso.')->with('resultado_webhook', $content);
    }

    private function processarPayloadInterno(array $dados)
    {
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

        if (auth()->check()) {
            $laravelRequest->setUserResolver(function () {
                return auth()->user();
            });
        }

        return $this->receberMensagem($laravelRequest);
    }

    private function normalizarTelefoneParaWhatsappId(string $telefone): string
    {
        $numero = preg_replace('/\D+/', '', $telefone ?? '');

        if (!$numero) {
            return '';
        }

        if (!str_starts_with($numero, '55') && strlen($numero) <= 11) {
            $numero = '55' . $numero;
        }

        return $numero . '@c.us';
    }

    private function extrairPayload(Request $request): array
    {
        $dados = $request->json()->all();

        if (!is_array($dados) || empty($dados)) {
            $decoded = json_decode($request->getContent(), true);
            if (is_array($decoded)) {
                $dados = $decoded;
            }
        }

        if (!is_array($dados) || empty($dados)) {
            $dados = $request->all();
        }

        return is_array($dados) ? $dados : [];
    }

    private function deveProcessarEvento(string $evento, array $data): bool
    {
        $eventosAceitos = [
            'onmessage',
            'onanymessage',
        ];

        if (!in_array($evento, $eventosAceitos, true)) {
            return false;
        }

        $type = strtolower((string) data_get($data, 'type', 'chat'));
        $tiposAceitos = ['chat', 'text'];

        return in_array($type, $tiposAceitos, true) || empty($type);
    }

    private function extrairFrom(array $data): ?string
    {
        $candidatos = [
            data_get($data, 'from'),
            data_get($data, 'sender.id'),
            data_get($data, 'chatId'),
            data_get($data, 'id.remote'),
            data_get($data, 'key.remoteJid'),
            data_get($data, 'message.from'),
        ];

        foreach ($candidatos as $valor) {
            if (is_string($valor) && trim($valor) !== '') {
                return trim($valor);
            }
        }

        return null;
    }

    private function extrairBody(array $data): ?string
    {
        $candidatos = [
            data_get($data, 'body'),
            data_get($data, 'message'),
            data_get($data, 'text'),
            data_get($data, 'content'),
            data_get($data, 'caption'),
            data_get($data, 'message.body'),
            data_get($data, 'message.text'),
        ];

        foreach ($candidatos as $valor) {
            if (is_string($valor)) {
                $valor = trim($valor);
                if ($valor !== '') {
                    return $valor;
                }
            }
        }

        return null;
    }

    private function extrairMessageId(array $data): ?string
    {
        $idSerialized = data_get($data, 'id._serialized');
        if (is_string($idSerialized) && trim($idSerialized) !== '') {
            return trim($idSerialized);
        }

        $messageId = data_get($data, 'messageId');
        if (is_string($messageId) && trim($messageId) !== '') {
            return trim($messageId);
        }

        $id = data_get($data, 'id');
        if (is_string($id) && trim($id) !== '') {
            return trim($id);
        }

        return null;
    }

    private function safeJsonEncode(array $dados): string
    {
        try {
            return json_encode(
                $dados,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
                | JSON_PARTIAL_OUTPUT_ON_ERROR
            ) ?: '{}';
        } catch (Throwable $e) {
            Log::channel('whatsapp')->warning('[Webhook] Falha ao serializar payload_json', [
                'error' => $e->getMessage(),
            ]);

            return '{}';
        }
    }

    private function isGrupoOuBroadcast(string $from): bool
    {
        $from = strtolower($from);

        return str_ends_with($from, '@g.us')
            || str_contains($from, 'status@broadcast')
            || str_contains($from, 'broadcast');
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
            return '<pre style="color: red;">Arquivo de log não encontrado: ' . $logPath . '</pre>';
        }

        $conteudo = File::get($logPath);

        if (empty($conteudo)) {
            return '<pre style="color: orange;">Arquivo existe, mas está vazio.</pre>';
        }

        return '<pre style="color: green;">' . htmlentities($conteudo) . '</pre>';
    }
}
