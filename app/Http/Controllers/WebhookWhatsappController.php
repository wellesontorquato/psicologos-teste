<?php

namespace App\Http\Controllers;

use App\Models\Sessao;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
        Log::info('[Webhook] DIAGNÓSTICO DE TIMEZONE:', ['hora_do_servidor' => now()->toDateTimeString(), 'timezone_app_config' => config('app.timezone')]);
        $rawContent = $request->getContent();
        Log::info('[Webhook] 📩 Corpo bruto recebido:', ['raw' => $rawContent]);
        $dados = json_decode($rawContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('[Webhook] ❌ Erro ao decodificar JSON.', ['raw' => $rawContent]);
            return response()->json(['message' => 'JSON inválido.'], 400);
        }
        Log::info('[Webhook] 🧾 Dados decodificados:', is_array($dados) ? $dados : []);
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
        $numeroLimpo = $this->normalizarNumero($from);
        $bodyLimpo = strtoupper(Str::ascii(trim($bodyOriginal)));
        Log::info('[Webhook] 🧪 Dados processados:', ['numero_normalizado' => $numeroLimpo, 'texto_limpo' => $bodyLimpo]);
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
        $sessao = $this->encontrarSessaoValidaParaConfirmacao($paciente);
        if (!$sessao) {
            $this->responderNoWhatsapp($numeroLimpo, "Olá, {$paciente->nome}! Recebemos sua mensagem, mas não encontramos uma sessão pendente de confirmação para você nos próximos dias.");
            return response()->json(['message' => 'Nenhuma sessão encontrada após verificação detalhada.'], 200);
        }
        Log::info('[Webhook] ✅ SESSÃO VALIDADA COM SUCESSO!', ['sessao_id' => $sessao->id, 'data_hora' => $sessao->data_hora->toDateTimeString()]);
        $sessao->status_confirmacao = $status;
        if (in_array($status, ['REMARCAR', 'CANCELADA'])) {
            $sessao->data_hora = null;
        }
        try {
            $sessao->save();
            Log::info('[Webhook] 💾 SESSÃO SALVA COM SUCESSO NO BANCO DE DADOS!', ['sessao_id' => $sessao->id, 'novo_status' => $status]);
        } catch (\Exception $e) {
            Log::error('[Webhook] 💥 CRÍTICO: Erro ao salvar a sessão no banco de dados!', ['sessao_id' => $sessao->id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
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

    private function encontrarSessaoValidaParaConfirmacao(Paciente $paciente): ?Sessao
    {
        $hoje = Carbon::today(config('app.timezone'));
        $dataLimite = $hoje->copy()->addDays(5);
        $sessoesCandidatas = Sessao::where('paciente_id', $paciente->id)->where('status_confirmacao', 'PENDENTE')->orderBy('data_hora', 'asc')->get();
        if ($sessoesCandidatas->isEmpty()) {
            Log::warning('[DIAGNÓSTICO] Nenhuma sessão com status PENDENTE encontrada para o paciente.', ['paciente_id' => $paciente->id]);
            return null;
        }
        Log::info('[DIAGNÓSTICO] Sessões candidatas encontradas:', $sessoesCandidatas->pluck('id')->toArray());
        foreach ($sessoesCandidatas as $sessao) {
            Log::info('----------------------------------------------------');
            Log::info('[DIAGNÓSTICO] Verificando sessão ID: ' . $sessao->id, ['dados_completos' => $sessao->toArray()]);
            $lembreteOk = $sessao->lembrete_enviado == 1;
            Log::info('[DIAGNÓSTICO] Checando `lembrete_enviado == 1`... Resultado: ' . ($lembreteOk ? '✅ SUCESSO' : '❌ FALHA'));
            if (!$lembreteOk) continue;
            $dataSessao = Carbon::parse($sessao->data_hora)->startOfDay();
            $dataOk = $dataSessao->betweenIncluded($hoje, $dataLimite);
            Log::info('[DIAGNÓSTICO] Checando data... ', ['data_sessao' => $dataSessao->toDateString(), 'data_inicio_intervalo' => $hoje->toDateString(), 'data_fim_intervalo' => $dataLimite->toDateString(), 'resultado' => ($dataOk ? '✅ SUCESSO' : '❌ FALHA')]);
            if (!$dataOk) continue;
            Log::info('[DIAGNÓSTICO] ✅ SESSÃO ID ' . $sessao->id . ' passou em todas as validações!');
            Log::info('----------------------------------------------------');
            return $sessao;
        }
        Log::warning('[DIAGNÓSTICO] Nenhuma das sessões candidatas passou em todos os critérios de validação.');
        Log::info('----------------------------------------------------');
        return null;
    }

    /**
     * [NOVA FERRAMENTA DE DIAGNÓSTICO]
     * Acessível via /api/webhook/diagnostico
     */
    public function diagnosticarWebhook(Request $request)
    {
        $diagnostico = [];

        // 1. Configurações do Ambiente Laravel
        $diagnostico['ambiente_laravel'] = [
            'APP_ENV' => config('app.env'),
            'APP_DEBUG' => config('app.debug'),
            'APP_URL' => config('app.url'),
            'LOG_CHANNEL' => config('logging.default'),
        ];

        // 2. Fuso Horário (Timezone)
        try {
            $db_time = DB::select('select now() as db_time')[0]->db_time;
        } catch (\Exception $e) {
            $db_time = 'ERRO AO ACESSAR BANCO: ' . $e->getMessage();
        }
        $diagnostico['fuso_horario'] = [
            'app_timezone_config' => config('app.timezone'),
            'php_default_timezone' => date_default_timezone_get(),
            'carbon_now (app_timezone)' => Carbon::now()->toDateTimeString(),
            'hora_banco_dados (db_timezone)' => $db_time,
        ];

        // 3. Configurações WPPConnect
        $diagnostico['wppconnect'] = [
            'url' => config('services.wppconnect.url'),
            'session' => config('services.wppconnect.session'),
            'token_presente' => !empty(config('services.wppconnect.token')),
        ];

        // 4. Teste de Conectividade WPPConnect
        try {
            $response = Http::timeout(5)->get(config('services.wppconnect.url'));
            $diagnostico['wppconnect']['conectividade'] = [
                'status' => 'SUCESSO',
                'http_status' => $response->status(),
            ];
        } catch (\Exception $e) {
            $diagnostico['wppconnect']['conectividade'] = [
                'status' => 'FALHA',
                'erro' => $e->getMessage(),
            ];
        }
        
        // 5. Diagnóstico Específico por Telefone (se fornecido)
        if ($request->has('telefone')) {
            $telefoneRaw = $request->input('telefone');
            $numeroLimpo = $this->normalizarNumero($telefoneRaw);
            $diagnostico['diagnostico_paciente'] = ['telefone_fornecido' => $telefoneRaw, 'numero_normalizado' => $numeroLimpo];
            
            $paciente = $this->encontrarPacientePorTelefone($numeroLimpo);
            
            if (!$paciente) {
                $diagnostico['diagnostico_paciente']['status'] = 'PACIENTE NÃO ENCONTRADO';
            } else {
                $diagnostico['diagnostico_paciente']['status'] = 'PACIENTE ENCONTRADO';
                $diagnostico['diagnostico_paciente']['paciente_id'] = $paciente->id;
                $diagnostico['diagnostico_paciente']['paciente_nome'] = $paciente->nome;

                $hoje = Carbon::today(config('app.timezone'));
                $dataLimite = $hoje->copy()->addDays(5);
                
                $sessoes = Sessao::where('paciente_id', $paciente->id)->orderBy('data_hora', 'desc')->take(10)->get();

                if($sessoes->isEmpty()){
                     $diagnostico['diagnostico_paciente']['sessoes'] = 'NENHUMA SESSÃO ENCONTRADA PARA ESTE PACIENTE';
                } else {
                    $diagnostico['diagnostico_paciente']['sessoes_analisadas'] = [];
                    foreach($sessoes as $sessao) {
                        $dataSessao = Carbon::parse($sessao->data_hora)->startOfDay();
                        $elegivel = ($sessao->status_confirmacao === 'PENDENTE' && $sessao->lembrete_enviado == 1 && $dataSessao->betweenIncluded($hoje, $dataLimite));
                        
                        $diagnostico['diagnostico_paciente']['sessoes_analisadas'][] = [
                            'sessao_id' => $sessao->id,
                            'data_hora_db' => $sessao->data_hora,
                            'status_confirmacao_db' => $sessao->status_confirmacao,
                            'lembrete_enviado_db' => $sessao->lembrete_enviado,
                            'ANALISE' => [
                                '1_status_eh_pendente' => ($sessao->status_confirmacao === 'PENDENTE'),
                                '2_lembrete_foi_enviado' => ($sessao->lembrete_enviado == 1),
                                '3_esta_no_intervalo_de_data' => $dataSessao->betweenIncluded($hoje, $dataLimite),
                                'intervalo_usado' => "De {$hoje->toDateString()} até {$dataLimite->toDateString()}"
                            ],
                            'RESULTADO_FINAL' => $elegivel ? '✅ ELEGÍVEL PARA CONFIRMAÇÃO' : '❌ NÃO ELEGÍVEL'
                        ];
                    }
                }
            }
        } else {
             $diagnostico['diagnostico_paciente'] = 'Nenhum telefone fornecido. Adicione ?telefone=NUMERO na URL para um diagnóstico específico.';
        }
        return response()->json($diagnostico, 200, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
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
}
