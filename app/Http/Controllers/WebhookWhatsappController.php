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
        $rawContent = $request->getContent();
        Log::channel('whatsapp')->info('[Webhook] 📩 Conteúdo cru recebido:', ['raw' => $rawContent]);

        $dados = json_decode($rawContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::channel('whatsapp')->error('[Webhook] ❌ JSON inválido.');
            return response()->json(['message' => 'JSON inválido.'], 400);
        }

        $evento = strtolower($dados['event'] ?? '');
        $data = $dados['data'] ?? $dados;

        if (!in_array($evento, ['message', 'onmessage']) || empty($data['from']) || empty($data['body'])) {
            Log::channel('whatsapp')->info('[Webhook] Ignorado: evento não relevante ou faltando dados.', ['event' => $evento]);
            return response()->json(['message' => 'Evento ignorado.'], 200);
        }

        $from = $data['from'];
        if (!str_contains($from, '@c.us')) {
            Log::channel('whatsapp')->warning('[Webhook] 🚫 Número inválido', ['from' => $from]);
            return response()->json(['message' => 'Número inválido.'], 200);
        }

        $numeroLimpo = $this->normalizarNumero($from);
        $bodyLimpo = strtoupper(Str::ascii(trim($data['body'])));
        Log::channel('whatsapp')->info('[Webhook] 🧪 Dados recebidos limpos', ['numero' => $numeroLimpo, 'mensagem' => $bodyLimpo]);

        $paciente = $this->encontrarPacientePorTelefone($numeroLimpo);
        if (!$paciente) {
            Log::channel('whatsapp')->warning('[Webhook] ❌ Paciente não encontrado.', ['numero' => $numeroLimpo]);
            $this->responderNoWhatsapp($numeroLimpo, '❌ Não encontramos seu cadastro. Verifique com o(a) profissional.');
            return response()->json(['message' => 'Paciente não encontrado.'], 200);
        }

        Log::channel('whatsapp')->info('[Webhook] ✅ Paciente identificado', ['paciente_id' => $paciente->id]);

        $status = $this->mapearRespostaParaStatus($bodyLimpo);
        if (!$status) {
            $mensagemErro = "⚠️ Desculpe, não entendi sua resposta.\n\nResponda com:\n\n*✔️ Confirmar*\n*🔄 Remarcar*\n*❌ Cancelar*";
            $this->responderNoWhatsapp($numeroLimpo, $mensagemErro);
            return response()->json(['message' => 'Mensagem inválida.'], 200);
        }

        $sessao = $this->encontrarSessaoValidaParaConfirmacao($paciente);
        if (!$sessao) {
            $this->responderNoWhatsapp($numeroLimpo, "Olá {$paciente->nome}, não encontramos uma sessão pendente para você.");
            return response()->json(['message' => 'Sessão não encontrada.'], 200);
        }

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

        match ($status) {
            'CONFIRMADA' => event(new SessaoConfirmada($sessao)),
            'CANCELADA'  => event(new SessaoCancelada($sessao)),
            'REMARCAR'   => event(new SessaoRemarcada($sessao)),
        };

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

    public function diagnosticarWebhook(Request $request)
    {
        $diagnostico = [];
        $diagnostico['ambiente_laravel'] = ['APP_ENV' => config('app.env'), 'APP_DEBUG' => config('app.debug'), 'APP_URL' => config('app.url'), 'LOG_CHANNEL' => config('logging.default')];
        try { $db_time = DB::select('select now() as db_time')[0]->db_time; } catch (\Exception $e) { $db_time = 'ERRO: ' . $e->getMessage(); }
        $diagnostico['fuso_horario'] = ['app_timezone_config' => config('app.timezone'), 'php_default_timezone' => date_default_timezone_get(), 'carbon_now (app_timezone)' => Carbon::now()->toDateTimeString(), 'hora_banco_dados (db_timezone)' => $db_time];
        $diagnostico['wppconnect'] = ['url' => config('services.wppconnect.url'), 'session' => config('services.wppconnect.session'), 'token_presente' => !empty(config('services.wppconnect.token'))];

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
                
                // [CORREÇÃO 500] Verifica se o modelo usa SoftDeletes antes de tentar usar withTrashed()
                $query = Sessao::withoutGlobalScopes()->where('paciente_id', $paciente->id);
                $modelUsaSoftDeletes = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(Sessao::class));

                if ($modelUsaSoftDeletes) {
                    $query->withTrashed();
                }
                
                $sessoes = $query->orderBy('data_hora', 'desc')->take(20)->get();
                
                if($sessoes->isEmpty()){
                     $diagnostico['diagnostico_paciente']['sessoes'] = 'NENHUMA SESSÃO ENCONTRADA PARA ESTE PACIENTE (mesmo incluindo deletadas)';
                } else {
                    $diagnostico['diagnostico_paciente']['sessoes_analisadas'] = [];
                    foreach($sessoes as $sessao) {
                        $dataSessao = Carbon::parse($sessao->data_hora)->startOfDay();
                        $elegivel = ($sessao->status_confirmacao === 'PENDENTE' && $sessao->lembrete_enviado == 1 && $dataSessao->betweenIncluded($hoje, $dataLimite));
                        
                        $diagnostico['diagnostico_paciente']['sessoes_analisadas'][] = [
                            'sessao_id' => $sessao->id,
                            'FOI_DELETADA_VIA_SOFTDELETE' => $modelUsaSoftDeletes ? $sessao->trashed() : false, // Verifica antes de chamar
                            'data_hora_db' => $sessao->data_hora,
                            'status_confirmacao_db' => $sessao->status_confirmacao,
                            'lembrete_enviado_db' => $sessao->lembrete_enviado,
                            'ANALISE' => ['1_status_eh_pendente' => ($sessao->status_confirmacao === 'PENDENTE'), '2_lembrete_foi_enviado' => ($sessao->lembrete_enviado == 1), '3_esta_no_intervalo_de_data' => $dataSessao->betweenIncluded($hoje, $dataLimite), 'intervalo_usado' => "De {$hoje->toDateString()} até {$dataLimite->toDateString()}"],
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