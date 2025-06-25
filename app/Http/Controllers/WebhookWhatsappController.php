<?php

namespace App\Http\Controllers;

use App\Models\Sessao;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Events\SessaoConfirmada;
use App\Events\SessaoCancelada;
use App\Events\SessaoRemarcada;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Illuminate\Support\Str;

class WebhookWhatsappController extends Controller
{
    /**
     * Ponto de entrada principal para o webhook do WhatsApp.
     */
    public function receberMensagem(Request $request)
    {
        $rawContent = $request->getContent();
        Log::info('[Webhook] 📩 Corpo bruto recebido:', ['raw' => $rawContent]);

        $dados = json_decode($rawContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('[Webhook] ❌ Erro ao decodificar JSON.', ['raw' => $rawContent]);
            return response()->json(['message' => 'JSON inválido.'], 400);
        }

        Log::info('[Webhook] 🧾 Dados decodificados:', is_array($dados) ? $dados : []);

        // Estrutura unificada para diferentes payloads de webhook
        $evento = strtolower($dados['event'] ?? '');
        $data = $dados['data'] ?? $dados;

        if (!in_array($evento, ['message', 'onmessage']) || empty($data['from']) || empty($data['body'])) {
            Log::info('[Webhook] ➡️ Evento ignorado (não é uma mensagem de usuário).', ['event' => $evento]);
            return response()->json(['message' => 'Evento ignorado.'], 200);
        }

        $from = $data['from'];
        $bodyOriginal = $data['body'];

        if (!str_contains($from, '@c.us')) {
            Log::warning('[Webhook] 🚫 Número malformado, ignorando.', ['from' => $from]);
            return response()->json(['message' => 'Número inválido.'], 200);
        }

        // --- Processamento e Busca ---

        $numeroLimpo = $this->normalizarNumero($from);
        $bodyLimpo = strtoupper(Str::ascii(trim($bodyOriginal)));
        
        Log::info('[Webhook] 🧪 Dados processados:', [
            'numero_normalizado' => $numeroLimpo,
            'texto_limpo' => $bodyLimpo
        ]);

        $paciente = $this->encontrarPacientePorTelefone($numeroLimpo);

        if (!$paciente) {
            Log::warning('[Webhook] ❌ Paciente não encontrado com o número.', ['numero' => $numeroLimpo]);
            $this->responderNoWhatsapp($numeroLimpo, '❌ Não encontramos seu cadastro em nosso sistema. Por favor, verifique o número com o(a) profissional que te acompanha.');
            return response()->json(['message' => 'Paciente não encontrado.'], 200);
        }
        
        Log::info('[Webhook] ✅ Paciente encontrado:', ['id' => $paciente->id, 'nome' => $paciente->nome]);

        $status = $this->mapearRespostaParaStatus($bodyLimpo);

        if (!$status) {
            Log::info('[Webhook] ⚠️ Resposta inválida do paciente.', ['recebido' => $bodyLimpo]);
            $mensagemErro = "⚠️ Desculpe, não entendi sua resposta.\n\nPara confirmar sua sessão, responda com uma das palavras:\n\n*✔️ Confirmar*\n*🔄 Remarcar*\n*❌ Cancelar*";
            $this->responderNoWhatsapp($numeroLimpo, $mensagemErro);
            return response()->json(['message' => 'Mensagem inválida.'], 200);
        }
        
        Log::info('[Webhook] ✅ Intenção detectada:', ['palavra_chave' => $bodyLimpo, 'status_mapeado' => $status]);

        // --- Busca da Sessão (Ponto Crítico com a Correção de Timezone e Logs) ---
        
        $hoje = Carbon::today(config('app.timezone'));
        $dataLimite = $hoje->copy()->addDays(5);
        
        Log::info('[Webhook] 🔍 Buscando sessão para o paciente...', [
            'paciente_id' => $paciente->id,
            'data_inicio_busca' => $hoje->toDateString(),
            'data_fim_busca' => $dataLimite->toDateString(),
            'condicoes' => [
                'status_confirmacao' => 'PENDENTE',
                'lembrete_enviado' => 1
            ]
        ]);

        // [MUDANÇA AQUI] Usando whereDate para uma comparação mais segura contra problemas de fuso horário.
        $sessao = Sessao::where('paciente_id', $paciente->id)
            ->where('status_confirmacao', 'PENDENTE')
            ->where('lembrete_enviado', 1)
            ->whereDate('data_hora', '>=', $hoje->toDateString())
            ->whereDate('data_hora', '<=', $dataLimite->toDateString())
            ->orderBy('data_hora', 'asc')
            ->first();

        if (!$sessao) {
            // [LOG MELHORADO AQUI] Verifica se existem sessões para este paciente que falharam em alguma condição.
            $sessoesCandidatas = Sessao::where('paciente_id', $paciente->id)->where('status_confirmacao', 'PENDENTE')->get();

            Log::warning('[Webhook] ⚠️ Nenhuma sessão PENDENTE e com LEMBRETE ENVIADO foi encontrada no período correto.', [
                'paciente' => $paciente->nome,
                'numero' => $numeroLimpo,
                'sessoes_pendentes_encontradas_fora_criterio' => $sessoesCandidatas->toArray() // Isso vai nos mostrar o que existe no banco
            ]);
            
            $this->responderNoWhatsapp($numeroLimpo, "Olá, {$paciente->nome}! Recebemos sua mensagem, mas não encontramos uma sessão pendente de confirmação para você nos próximos dias.");
            return response()->json(['message' => 'Nenhuma sessão encontrada.'], 200);
        }

        Log::info('[Webhook] ✅ Sessão encontrada para atualização:', ['sessao_id' => $sessao->id, 'data_hora' => $sessao->data_hora->toDateTimeString()]);
        
        // --- Atualização e Resposta ---

        $sessao->status_confirmacao = $status;
        if (in_array($status, ['REMARCAR', 'CANCELADA'])) {
            $sessao->data_hora = null;
        }

        try {
            $sessao->save();
            Log::info('[Webhook] 💾 SESSÃO SALVA COM SUCESSO NO BANCO DE DADOS!', ['sessao_id' => $sessao->id, 'novo_status' => $status]);
        } catch (\Exception $e) {
            Log::error('[Webhook] 💥 CRÍTICO: Erro ao salvar a sessão no banco de dados!', [
                'sessao_id' => $sessao->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString() // Para debug aprofundado
            ]);
            $this->responderNoWhatsapp($numeroLimpo, "🚨 Ocorreu um erro interno ao processar sua resposta. Já fomos notificados. Por favor, entre em contato com a clínica diretamente.");
            return response()->json(['message' => 'Erro ao salvar sessão.'], 500);
        }

        $sessao->loadMissing('paciente');

        match ($status) {
            'CONFIRMADA' => event(new SessaoConfirmada($sessao)),
            'CANCELADA'  => event(new SessaoCancelada($sessao)),
            'REMARCAR'   => event(new SessaoRemarcada($sessao)),
            default      => null,
        };

        $mensagem = "✅ Obrigado pela resposta, {$paciente->nome}! Sua sessão foi atualizada para: *{$status}*.";
        if ($status === 'REMARCAR') {
            $mensagem .= "\n\nEntraremos em contato para encontrar um novo horário.";
        }
        
        $this->responderNoWhatsapp($numeroLimpo, $mensagem);

        return response()->json(['message' => 'Sessão atualizada com sucesso.'], 200);
    }

    /**
     * Encontra um paciente pelo número de telefone de forma eficiente.
     */
    private function encontrarPacientePorTelefone(string $numeroLimpo)
    {
        // Esta query é muito mais eficiente que Paciente::get()
        return Paciente::where(function ($query) use ($numeroLimpo) {
                // Remove todos os caracteres não numéricos do campo do banco na própria query
                $query->whereRaw('REGEXP_REPLACE(telefone, "[^0-9]", "") = ?', [$numeroLimpo]);

                // Lógica para o "9" ausente: se o número recebido tem 10 dígitos (DDD + 8),
                // busca no banco por números com 11 dígitos que correspondam.
                if (strlen($numeroLimpo) === 10) {
                    $ddd = substr($numeroLimpo, 0, 2);
                    $resto = substr($numeroLimpo, 2);
                    $numeroComNove = $ddd . '9' . $resto;
                    $query->orWhereRaw('REGEXP_REPLACE(telefone, "[^0-9]", "") = ?', [$numeroComNove]);
                }
            })
            ->first();
    }
    
    /**
     * Mapeia a resposta do usuário para um status interno.
     */
    private function mapearRespostaParaStatus(string $bodyLimpo): ?string
    {
        // NOTA: A ordem é importante! Chaves mais específicas (com negação) vêm primeiro.
        $mapa = [
            // CANCELAR (mais específico primeiro)
            'NAO VOU'       => 'CANCELADA',
            'NÃO VOU'       => 'CANCELADA',
            'CANCELAR'      => 'CANCELADA',
            'CANCELADO'     => 'CANCELADA',
            'CANCELADA'     => 'CANCELADA',
            'DESMARCAR'     => 'CANCELADA',
            'DESMARQUE'     => 'CANCELADA',
            'CANCELE'       => 'CANCELADA',

            // REMARCAR
            'REMARCAR'      => 'REMARCAR',
            'REMARCAÇÃO'    => 'REMARCAR',
            'REAGENDAR'     => 'REMARCAR',
            'REAGENDAMENTO' => 'REMARCAR',
            'REMARQUE'      => 'REMARCAR',
            'MUDAR'         => 'REMARCAR',
            'TROCAR'        => 'REMARCAR',
            'ADIAR'         => 'REMARCAR',
            
            // CONFIRMAR (mais genérico por último)
            'CONFIRMADO'    => 'CONFIRMADA',
            'CONFIRMAR'     => 'CONFIRMADA',
            'CONFIRMADA'    => 'CONFIRMADA',
            'CONFIRMEI'     => 'CONFIRMADA',
            'OK'            => 'CONFIRMADA',
            'CERTO'         => 'CONFIRMADA',
            'SIM'           => 'CONFIRMADA',
            'VOU'           => 'CONFIRMADA',
            'ESTAREI'       => 'CONFIRMADA',
        ];

        foreach ($mapa as $chave => $valor) {
            if (Str::contains($bodyLimpo, $chave)) {
                return $valor;
            }
        }
        return null;
    }

    /**
     * Normaliza um número de telefone vindo do WhatsApp.
     */
    private function normalizarNumero(string $numero): string
    {
        $num = preg_replace('/\D/', '', $numero);
        if (str_starts_with($num, '55')) {
            return substr($num, 2); // Retorna DDD + Número
        }
        return $num;
    }

    /**
     * Envia uma resposta via API WPPConnect.
     */
    private function responderNoWhatsapp($numero, $mensagem)
    {
        // Implementação original mantida...
        $numeroCompleto = preg_replace('/\D/', '', $numero);
        if (!str_starts_with($numeroCompleto, '55')) {
            $numeroCompleto = '55' . $numeroCompleto;
        }

        $token = config('services.wppconnect.token');
        $url = config('services.wppconnect.url');
        $session = config('services.wppconnect.session');

        Log::info('[Webhook] 🚀 Preparando envio WhatsApp:', [
            'numero' => $numeroCompleto,
            'mensagem' => $mensagem,
        ]);

        $endpoint = app()->isLocal()
            ? "http://localhost:21465/api/{$session}/send-message"
            : "{$url}/api/{$session}/send-message";

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
        ])->post($endpoint, [
            'phone' => $numeroCompleto,
            'message' => $mensagem,
        ]);
        
        if (!$response->successful()) {
            Log::error('[Webhook] ❌ Falha ao enviar mensagem de resposta ao WhatsApp', [
                'numero' => $numeroCompleto,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } else {
            Log::info('[Webhook] ✅ Mensagem de resposta enviada com sucesso.', ['numero' => $numeroCompleto]);
        }
    }

    public function testeManual(Request $request)
    {
        $dados = [
            'event' => 'message',
            'data' => [
                'from' => '5582999405099@c.us', // Use um número de paciente de teste válido
                'body' => 'Confirmado', // Teste com 'Cancelar', 'Remarcar', etc.
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

    public function diagnosticarWebhook()
    {
        try {
            $logs = [];

            $logs[] = '🔍 Iniciando diagnóstico...';

            $token = config('services.wppconnect.token');
            $url = config('services.wppconnect.url');
            $session = config('services.wppconnect.session');
            $env = app()->environment();

            // ✅ Token
            if (!$token || strlen($token) < 10) {
                $logs[] = '❌ Token do WPPConnect inválido ou ausente.';
            } else {
                $logs[] = '✅ Token carregado: ' . substr($token, 0, 10) . '...';
            }

            // ✅ URL
            if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
                $logs[] = '❌ URL da API WPPConnect inválida.';
            } else {
                $logs[] = '✅ URL da API carregada: ' . $url;
            }

            // ✅ Session
            if (!$session) {
                $logs[] = '❌ Nome da sessão ausente.';
            } else {
                $logs[] = '✅ Sessão: ' . $session;
            }

            // ✅ Ambiente
            $logs[] = '✅ Ambiente atual: ' . $env;
            if ($env !== 'production') {
                $logs[] = '⚠️  Ambiente não está em produção. Verifique APP_ENV no .env';
            }

            // ✅ Endpoint
            $endpoint = app()->isLocal()
                ? "http://localhost:21465/api/{$session}/send-message"
                : "{$url}/api/{$session}/send-message";
            $logs[] = '✅ Endpoint detectado: ' . $endpoint;

            // ✅ Teste de acesso à URL
            try {
                $test = Http::timeout(5)->get($url);
                $logs[] = $test->ok()
                    ? '✅ URL da API WPPConnect respondeu com sucesso.'
                    : '❌ A URL da API WPPConnect não respondeu corretamente. Status: ' . $test->status();
            } catch (\Exception $e) {
                $logs[] = '❌ Falha ao tentar acessar a URL da API: ' . $e->getMessage();
            }

            return response()->json([
                'status' => 'ok',
                'logs' => $logs,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Erro durante o diagnóstico',
                'erro' => $e->getMessage(),
            ], 500);
        }
    }
}
