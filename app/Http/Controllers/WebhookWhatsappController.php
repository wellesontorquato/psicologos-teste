<?php

namespace App\Http\Controllers;

use App\Models\Sessao;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use App\Events\SessaoConfirmada;
use App\Events\SessaoCancelada;
use App\Events\SessaoRemarcada;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Illuminate\Support\Str;

class WebhookWhatsappController extends Controller
{
    public function receberMensagem(Request $request)
    {
        Log::channel('whatsapp')->info('[Webhook] 🔔 WPPConnect Webhook recebido');

        // Diagnóstico completo da requisição
        Log::channel('whatsapp')->info('[Webhook] 🩺 Diagnóstico da requisição recebida', [
            'method'       => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'headers'      => $request->headers->all(),
            'body_raw'     => $request->getContent(),
            'all_inputs'   => $request->all(),
            'ip'           => $request->ip(),
        ]);

        // Conteúdo cru
        $rawContent = $request->getContent();
        Log::channel('whatsapp')->info('[Webhook] 📩 Conteúdo cru recebido', ['raw' => $rawContent]);

        // Decodifica JSON ou usa fallback
        $dados = json_decode($rawContent, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($dados)) {
            Log::channel('whatsapp')->warning('[Webhook] ⚠️ JSON inválido. Usando request->all() como fallback.');
            $dados = $request->all();
        }

        // Estrutura típica do WPPConnect:
        // {
        //   "event": "onmessage",
        //   "session": "psigestor",
        //   "data": { ... mensagem ... }
        // }
        $evento = strtolower($dados['event'] ?? 'onmessage');
        $data   = $dados['data'] ?? $dados;

        // Evita loop: se vier mensagem enviada POR VOCÊ, ignora
        $fromMe = $data['fromMe'] ?? $data['isMe'] ?? false;
        if ($fromMe) {
            Log::channel('whatsapp')->info('[Webhook] 🔁 Ignorado (mensagem enviada pelo próprio bot).', [
                'evento' => $evento,
            ]);
            return response()->json(['message' => 'Mensagem do próprio bot ignorada.'], 200);
        }

        // Só processa eventos de mensagem
        if (!in_array($evento, ['onmessage', 'message', 'onmessageany'])) {
            Log::channel('whatsapp')->info('[Webhook] ℹ️ Evento ignorado (não é mensagem de chat).', ['event' => $evento]);
            return response()->json(['message' => 'Evento ignorado.'], 200);
        }

        // WPPConnect geralmente envia:
        // data.from  => "55XXXXXXXXXXX@c.us"
        // data.body  => texto da mensagem
        $from = $data['from'] ?? null;
        $body = $data['body'] ?? ($data['message'] ?? null);

        if (!$from || !$body || !str_contains($from, '@c.us')) {
            Log::channel('whatsapp')->info('[Webhook] Ignorado: dados incompletos ou número inválido.', compact('evento', 'from', 'body'));
            return response()->json(['message' => 'Evento ignorado ou inválido.'], 200);
        }

        // Normaliza
        $numeroLimpo   = $this->normalizarNumero($from);
        $mensagemLimpa = strtoupper(Str::ascii(trim($body)));

        Log::channel('whatsapp')->info('[Webhook] 🧪 Dados normalizados', [
            'numero'   => $numeroLimpo,
            'mensagem' => $mensagemLimpa,
        ]);

        // Busca paciente pelo telefone
        $paciente = $this->encontrarPacientePorTelefone($numeroLimpo);
        if (!$paciente) {
            Log::channel('whatsapp')->warning('[Webhook] ❌ Paciente não encontrado.', ['numero' => $numeroLimpo]);
            $this->responderNoWhatsapp($numeroLimpo, '❌ Não encontramos seu cadastro. Verifique com o(a) profissional.');
            return response()->json(['message' => 'Paciente não encontrado.'], 200);
        }

        Log::channel('whatsapp')->info('[Webhook] ✅ Paciente identificado', ['paciente_id' => $paciente->id]);

        // Interpreta a resposta
        $status = $this->mapearRespostaParaStatus($mensagemLimpa);
        if (!$status) {
            $mensagemErro = "⚠️ Desculpe, não entendi sua resposta.\n\nResponda com:\n\n*✔️ Confirmar*\n*🔄 Remarcar*\n*❌ Cancelar*";
            $this->responderNoWhatsapp($numeroLimpo, $mensagemErro);
            return response()->json(['message' => 'Mensagem inválida.'], 200);
        }

        // Procura sessão válida
        $sessao = $this->encontrarSessaoValidaParaConfirmacao($paciente);
        if (!$sessao) {
            $this->responderNoWhatsapp($numeroLimpo, "Olá {$paciente->nome}, não encontramos uma sessão pendente para você.");
            return response()->json(['message' => 'Sessão não encontrada.'], 200);
        }

        // Atualiza status e data se necessário
        $sessao->status_confirmacao = $status;
        if (in_array($status, ['REMARCAR', 'CANCELADA'])) {
            $sessao->data_hora = null;
        }

        try {
            Sessao::withoutGlobalScopes()
                ->where('id', $sessao->id)
                ->update($sessao->getDirty());

            Log::channel('whatsapp')->info('[Webhook] 💾 Sessão atualizada com sucesso.', [
                'sessao_id' => $sessao->id,
                'status'    => $status,
            ]);
        } catch (\Exception $e) {
            Log::channel('whatsapp')->error('[Webhook] 💥 Erro ao salvar sessão', [
                'erro' => $e->getMessage(),
            ]);
            $this->responderNoWhatsapp($numeroLimpo, "🚨 Erro ao processar sua resposta. Já fomos notificados.");
            return response()->json(['message' => 'Erro ao salvar sessão.'], 500);
        }

        // Dispara evento de domínio
        match ($status) {
            'CONFIRMADA' => event(new SessaoConfirmada($sessao)),
            'CANCELADA'  => event(new SessaoCancelada($sessao)),
            'REMARCAR'   => event(new SessaoRemarcada($sessao)),
        };

        // Mensagem de retorno
        $mensagem = "✅ Obrigado pela resposta, {$paciente->nome}. Sua sessão foi marcada como: *{$status}*.";
        if ($status === 'REMARCAR') {
            $mensagem .= "\n\nVamos entrar em contato para reagendar.";
        }

        $this->responderNoWhatsapp($numeroLimpo, $mensagem);
        return response()->json(['message' => 'Sessão atualizada com sucesso.'], 200);
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
            Log::channel('whatsapp')->warning('[DIAGNÓSTICO] Nenhuma sessão com status PENDENTE encontrada para o paciente.', [
                'paciente_id' => $paciente->id,
            ]);
            return null;
        }

        foreach ($sessoesCandidatas as $sessao) {
            $lembreteOk = $sessao->lembrete_enviado == 1;
            if (!$lembreteOk) {
                continue;
            }

            $dataSessao = Carbon::parse($sessao->data_hora)->startOfDay();
            $dataOk     = $dataSessao->betweenIncluded($hoje, $dataLimite);
            if (!$dataOk) {
                continue;
            }

            return $sessao;
        }

        Log::channel('whatsapp')->warning('[DIAGNÓSTICO] Nenhuma das sessões candidatas passou nos critérios (lembrete/data).');
        return null;
    }

    private function encontrarPacientePorTelefone(string $numeroLimpo)
    {
        return Paciente::where(function ($query) use ($numeroLimpo) {
            $query->whereRaw('REGEXP_REPLACE(telefone, "[^0-9]", "") = ?', [$numeroLimpo]);

            // Tenta também com "9" inserido (caso cadastro esteja diferente)
            if (strlen($numeroLimpo) === 10) {
                $ddd          = substr($numeroLimpo, 0, 2);
                $resto        = substr($numeroLimpo, 2);
                $numeroComNove = $ddd . '9' . $resto;
                $query->orWhereRaw('REGEXP_REPLACE(telefone, "[^0-9]", "") = ?', [$numeroComNove]);
            }
        })->first();
    }

    private function mapearRespostaParaStatus(string $bodyLimpo): ?string
    {
        $mapa = [
            // CANCELAR
            'NAO VOU'     => 'CANCELADA',
            'NÃO VOU'     => 'CANCELADA',
            'CANCELAR'    => 'CANCELADA',
            'CANCELADO'   => 'CANCELADA',
            'CANCELADA'   => 'CANCELADA',
            'DESMARCAR'   => 'CANCELADA',
            'DESMARQUE'   => 'CANCELADA',
            'CANCELE'     => 'CANCELADA',
            '3'           => 'CANCELADA',

            // REMARCAR
            'REMARCAR'    => 'REMARCAR',
            'REMARCACAO'  => 'REMARCAR',
            'REMARCAÇÃO'  => 'REMARCAR',
            'REAGENDAR'   => 'REMARCAR',
            'REAGENDAMENTO' => 'REMARCAR',
            'REMARQUE'    => 'REMARCAR',
            'MUDAR'       => 'REMARCAR',
            'TROCAR'      => 'REMARCAR',
            'ADIAR'       => 'REMARCAR',
            '2'           => 'REMARCAR',

            // CONFIRMAR
            'CONFIRMADO'  => 'CONFIRMADA',
            'CONFIRMAR'   => 'CONFIRMADA',
            'CONFIRMADA'  => 'CONFIRMADA',
            'CONFIRMEI'   => 'CONFIRMADA',
            'OK'          => 'CONFIRMADA',
            'CERTO'       => 'CONFIRMADA',
            'SIM'         => 'CONFIRMADA',
            'VOU'         => 'CONFIRMADA',
            'ESTAREI'     => 'CONFIRMADA',
            'CONFIRMA'    => 'CONFIRMADA',
            '1'           => 'CONFIRMADA',
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
        // Remove tudo que não for número
        $num = preg_replace('/\D/', '', $numero);

        // Remove o 55 se vier com DDI
        if (str_starts_with($num, '55')) {
            $num = substr($num, 2);
        }

        return $num;
    }

    /**
     * Envia mensagem usando WPPConnect-Server:
     * POST {base_url}/api/{session}/send-message
     */
    private function responderNoWhatsapp(string $numero, string $mensagem): void
    {
        // normaliza número e garante DDI 55
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
        } catch (\Throwable $e) {
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
                'from' => '5582999405099@c.us',
                'body' => 'Confirmado',
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
