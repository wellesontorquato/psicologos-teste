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
        Log::channel('whatsapp')->info('[Webhook] 🔔 VENOM-BOT INICIADO');

        // Diagnóstico completo da requisição
        Log::channel('whatsapp')->info('[Webhook] 🩺 Diagnóstico da requisição recebida', [
            'method'        => $request->method(),
            'content_type'  => $request->header('Content-Type'),
            'headers'       => $request->headers->all(),
            'body_raw'      => $request->getContent(),
            'all_inputs'    => $request->all(),
            'ip'            => $request->ip(),
        ]);

        // Captura conteúdo cru
        $rawContent = $request->getContent();
        Log::channel('whatsapp')->info('[Webhook] 📩 Conteúdo cru recebido', ['raw' => $rawContent]);

        // Decodifica JSON ou usa fallback
        $dados = json_decode($rawContent, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($dados)) {
            Log::channel('whatsapp')->warning('[Webhook] ⚠️ JSON inválido. Usando request->all() como fallback.');
            $dados = $request->all();
        }

        // Flexibilidade: se houver 'event', espera 'data'. Se não houver, assume que tudo já está em $dados.
        $evento = strtolower($dados['event'] ?? 'onmessage'); // Assume padrão 'onmessage'
        $data = $dados['data'] ?? $dados;

        // Validação mínima
        $from = $data['from'] ?? null;
        $body = $data['body'] ?? null;

        if (!$from || !$body || !str_contains($from, '@c.us')) {
            Log::channel('whatsapp')->info('[Webhook] Ignorado: dados incompletos ou número inválido.', compact('evento', 'from', 'body'));
            return response()->json(['message' => 'Evento ignorado ou inválido.'], 200);
        }

        // Normaliza dados
        $numeroLimpo = $this->normalizarNumero($from);
        $mensagemLimpa = strtoupper(Str::ascii(trim($body)));

        Log::channel('whatsapp')->info('[Webhook] 🧪 Dados normalizados', [
            'numero' => $numeroLimpo,
            'mensagem' => $mensagemLimpa,
        ]);

        // Busca paciente
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
            Sessao::withoutGlobalScopes()->where('id', $sessao->id)->update($sessao->getDirty());
            Log::channel('whatsapp')->info('[Webhook] 💾 Sessão atualizada com sucesso.', ['sessao_id' => $sessao->id, 'status' => $status]);
        } catch (\Exception $e) {
            Log::channel('whatsapp')->error('[Webhook] 💥 Erro ao salvar sessão', ['erro' => $e->getMessage()]);
            $this->responderNoWhatsapp($numeroLimpo, "🚨 Erro ao processar sua resposta. Já fomos notificados.");
            return response()->json(['message' => 'Erro ao salvar sessão.'], 500);
        }

        // Dispara evento
        match ($status) {
            'CONFIRMADA' => event(new SessaoConfirmada($sessao)),
            'CANCELADA'  => event(new SessaoCancelada($sessao)),
            'REMARCAR'   => event(new SessaoRemarcada($sessao)),
        };

        // Mensagem final
        $mensagem = "✅ Obrigado pela resposta, {$paciente->nome}. Sua sessão foi marcada como: *{$status}*.";
        if ($status === 'REMARCAR') {
            $mensagem .= "\n\nVamos entrar em contato para reagendar.";
        }

        $this->responderNoWhatsapp($numeroLimpo, $mensagem);
        return response()->json(['message' => 'Sessão atualizada com sucesso.'], 200);
    }


    private function encontrarSessaoValidaParaConfirmacao(Paciente $paciente): ?Sessao
    {
        $hoje = Carbon::today(config('app.timezone'));
        $dataLimite = $hoje->copy()->addDays(5);

        // Usamos withoutGlobalScopes() para garantir que vemos TODAS as sessões.
        $sessoesCandidatas = Sessao::withoutGlobalScopes()
                                ->where('paciente_id', $paciente->id)
                                ->where('status_confirmacao', 'PENDENTE')
                                ->orderBy('data_hora', 'asc')
                                ->get();
        
        if ($sessoesCandidatas->isEmpty()) {
            Log::channel('whatsapp')->warning('[DIAGNÓSTICO] Nenhuma sessão com status PENDENTE encontrada para o paciente.', ['paciente_id' => $paciente->id]);
            return null;
        }
        
        foreach ($sessoesCandidatas as $sessao) {
            $lembreteOk = $sessao->lembrete_enviado == 1;
            if (!$lembreteOk) continue;
            
            $dataSessao = Carbon::parse($sessao->data_hora)->startOfDay();
            $dataOk = $dataSessao->betweenIncluded($hoje, $dataLimite);
            if (!$dataOk) continue;

            return $sessao;
        }
        
        Log::channel('whatsapp')->warning('[DIAGNÓSTICO] Nenhuma das sessões candidatas passou em todos os critérios de validação (lembrete ou data).');
        return null;
    }
    
    private function encontrarPacientePorTelefone(string $numeroLimpo)
    {
        return Paciente::where(function ($query) use ($numeroLimpo) {
                $query->whereRaw('REGEXP_REPLACE(telefone, "[^0-9]", "") = ?', [$numeroLimpo]);
                if (strlen($numeroLimpo) === 10) {
                    $ddd = substr($numeroLimpo, 0, 2);
                    $resto = substr($numeroLimpo, 2);
                    $numeroComNove = $ddd . '9' . $resto;
                    $query->orWhereRaw('REGEXP_REPLACE(telefone, "[^0-9]", "") = ?', [$numeroComNove]);
                }
            })->first();
    }
    
    private function mapearRespostaParaStatus(string $bodyLimpo): ?string
    {
        $mapa = [
            'NAO VOU' => 'CANCELADA', 'NÃO VOU' => 'CANCELADA', 'CANCELAR' => 'CANCELADA', 'CANCELADO' => 'CANCELADA', 'CANCELADA' => 'CANCELADA', 'DESMARCAR' => 'CANCELADA', 'DESMARQUE' => 'CANCELADA', 'CANCELE' => 'CANCELADA',
            'REMARCAR' => 'REMARCAR', 'REMARCAÇÃO' => 'REMARCAR', 'REAGENDAR' => 'REMARCAR', 'REAGENDAMENTO' => 'REMARCAR', 'REMARQUE' => 'REMARCAR', 'MUDAR' => 'REMARCAR', 'TROCAR' => 'REMARCAR', 'ADIAR' => 'REMARCAR',
            'CONFIRMADO' => 'CONFIRMADA', 'CONFIRMAR' => 'CONFIRMADA', 'CONFIRMADA' => 'CONFIRMADA', 'CONFIRMEI' => 'CONFIRMADA', 'OK' => 'CONFIRMADA', 'CERTO' => 'CONFIRMADA', 'SIM' => 'CONFIRMADA', 'VOU' => 'CONFIRMADA', 'ESTAREI' => 'CONFIRMADA',
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
            return substr($num, 2);
        }
        return $num;
    }
    
    private function responderNoWhatsapp($numero, $mensagem)
    {
        $numeroComPrefixo = '55' . preg_replace('/[^0-9]/', '', $numero);
        $urlBase = config('services.venom.url');

        Log::channel('whatsapp')->info('[Webhook] 🚀 Preparando envio WhatsApp', [
            'numero' => $numeroComPrefixo,
            'mensagem' => $mensagem,
            'endpoint' => $urlBase . '/sendText',
        ]);

        try {
            $response = Http::post(rtrim($urlBase, '/') . '/sendText', [
                'to' => $numeroComPrefixo . '@c.us',
                'text' => $mensagem,
            ]);

            if (!$response->successful()) {
                Log::channel('whatsapp')->error('[Webhook] ❌ Falha ao enviar mensagem de resposta ao WhatsApp', [
                    'numero' => $numeroComPrefixo,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::channel('whatsapp')->error('[Webhook] 💥 Erro ao enviar mensagem', [
                'erro' => $e->getMessage(),
            ]);
        }
    }

    public function testeManual(Request $request)
    {
        $dados = [
            'event' => 'message',
            'data' => [
                'from' => '5582999405099@c.us',
                'body' => 'Confirmado',
            ]
        ];

        $symfonyRequest = SymfonyRequest::create(
            '/api/webhook/whatsapp', 'POST', [], [], [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($dados)
        );

        $laravelRequest = Request::createFromBase($symfonyRequest);
        return $this->receberMensagem($laravelRequest);
    }

    public function handle(Request $request)
    {
        Log::channel('whatsapp')->info('✅ Webhook recebido!', [
            'data' => $request->all(),
            'ip' => $request->ip(),
            'headers' => $request->headers->all(),
        ]);
        return response()->json(['status' => 'ok']);
    }

    public function verLogWhatsapp()
    {
        $hoje = now()->format('Y-m-d');
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