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
    public function receberMensagem(Request $request)
    {
        $rawContent = $request->getContent();
        Log::info('[Webhook] 📩 Corpo bruto recebido:', ['raw' => $rawContent]);

        $dados = json_decode($rawContent, true);
        Log::info('[Webhook] 🧾 Dados decodificados:', is_array($dados) ? $dados : []);

        $evento = strtolower($dados['event'] ?? '');
        $data = $dados['data'] ?? $dados;

        if (!in_array($evento, ['message', 'onmessage']) || empty($data['from']) || empty($data['body'])) {
            return response()->json(['message' => 'Evento ignorado.'], 200);
        }

        $from = $data['from'];
        $bodyOriginal = $data['body'];
        $bodyLimpo = strtoupper(Str::ascii(trim($bodyOriginal)));
        Log::info('[Webhook] 🧪 Texto limpo gerado:', ['original' => $bodyOriginal, 'limpo' => $bodyLimpo]);

        if (!str_contains($from, '@c.us')) {
            Log::warning('[Webhook] 🚫 Número malformado:', ['from' => $from]);
            return response()->json(['message' => 'Número inválido.'], 200);
        }

        $numero = str_replace(['@c.us', '+'], '', $from);
        $numeroLimpo = $this->normalizarNumero($numero);
        Log::info('[Webhook] 📞 Número extraído e normalizado:', ['original' => $numero, 'normalizado' => $numeroLimpo]);

        // Busca direta pelo número já normalizado
        $paciente = Paciente::get()->first(function ($p) use ($numeroLimpo) {
            $telefoneBanco = preg_replace('/\D/', '', $p->telefone);

            // Tenta casar diretamente
            if ($numeroLimpo === $telefoneBanco) {
                return true;
            }

            // Se o número tem 8 dígitos após o DDD (faltando o 9), tenta adicionar o 9 para comparar
            if (preg_match('/^(\d{2})(\d{8})$/', $numeroLimpo, $matches)) {
                $numeroComNove = $matches[1] . '9' . $matches[2];
                if ($numeroComNove === $telefoneBanco) {
                    Log::info('[Webhook] 🔄 Casou número adicionando 9:', ['original' => $numeroLimpo, 'ajustado' => $numeroComNove]);
                    return true;
                }
            }

            return false;
        });

        if (!$paciente) {
            Log::warning('[Webhook] ❌ Paciente não encontrado:', ['numero' => $numeroLimpo]);
            $this->responderNoWhatsapp($numeroLimpo, '❌ Não encontramos seu número no sistema. Verifique com o(a) profissional que te acompanha.');
            return response()->json(['message' => 'Paciente não encontrado.'], 200);
        }

        $mapa = [
            // CONFIRMAR
            'CONFIRMADO'    => 'CONFIRMADA',
            'CONFIRMAR'     => 'CONFIRMADA',
            'CONFIRMADA'    => 'CONFIRMADA',
            'OK'            => 'CONFIRMADA',
            'CERTO'         => 'CONFIRMADA',
            'SIM'           => 'CONFIRMADA',
            'VOU'           => 'CONFIRMADA',
            'ESTAREI'       => 'CONFIRMADA',
            'CONFIRMEI'     => 'CONFIRMADA',

            // REMARCAR
            'REMARCAR'      => 'REMARCAR',
            'REMARCAÇÃO'    => 'REMARCAR',
            'REAGENDAR'     => 'REMARCAR',
            'REAGENDAMENTO' => 'REMARCAR',
            'REMARQUE'      => 'REMARCAR',
            'REMARCARÁ'     => 'REMARCAR',
            'MUDAR'         => 'REMARCAR',
            'TROCAR'        => 'REMARCAR',
            'ADIAR'         => 'REMARCAR',

            // CANCELAR
            'CANCELAR'      => 'CANCELADA',
            'CANCELADO'     => 'CANCELADA',
            'CANCELADA'     => 'CANCELADA',
            'DESMARCAR'     => 'CANCELADA',
            'DESMARQUE'     => 'CANCELADA',
            'NAO VOU'       => 'CANCELADA',
            'NÃO VOU'       => 'CANCELADA',
            'CANCELE'       => 'CANCELADA',
        ];

        // 🔍 Busca inteligente pela primeira palavra-chave que bate
        $status = null;
        foreach ($mapa as $chave => $valor) {
            if (Str::contains($bodyLimpo, $chave)) {
                $status = $valor;
                Log::info('[Webhook] ✅ Palavra-chave detectada:', ['chave' => $chave, 'status' => $valor]);
                break;
            }
        }

        if (!$status) {
            Log::info('[Webhook] ⚠️ Resposta inválida do paciente', ['recebido' => $bodyLimpo]);
            $mensagemErro = "⚠️ Desculpe, não entendi sua resposta.\n\nPara confirmar sua sessão, responda com:\n\n*✔️ Confirmar*\n*🔄 Remarcar*\n*❌ Cancelar*";
            $this->responderNoWhatsapp($numeroLimpo, $mensagemErro);
            return response()->json(['message' => 'Mensagem inválida.'], 200);
        }

        $sessao = Sessao::where('paciente_id', $paciente->id)
            ->where('status_confirmacao', 'PENDENTE')
            ->where('lembrete_enviado', 1)
            ->whereBetween('data_hora', [
                Carbon::today(config('app.timezone')),
                Carbon::today(config('app.timezone'))->addDays(5),
            ])
            ->orderBy('data_hora')
            ->first();

        if (!$sessao) {
            Log::warning('[Webhook] ⚠️ Nenhuma sessão encontrada para atualizar.', [
                'paciente' => $paciente->nome,
                'numero' => $numeroLimpo
            ]);
            $this->responderNoWhatsapp($numeroLimpo, "⚠️ Nenhuma sessão pendente encontrada para atualizar.");
            return response()->json(['message' => 'Nenhuma sessão encontrada.'], 200);
        }

        $sessao->status_confirmacao = $status;

        if (in_array($sessao->status_confirmacao, ['REMARCAR', 'CANCELADA'])) {
            $sessao->data_hora = null;
        }

        $sessao->save();
        $sessao->loadMissing('paciente');

        match ($sessao->status_confirmacao) {
            'CONFIRMADA' => event(new SessaoConfirmada($sessao)),
            'CANCELADA'  => event(new SessaoCancelada($sessao)),
            'REMARCAR'   => event(new SessaoRemarcada($sessao)),
            default      => null,
        };

        Log::info('[Webhook] ✅ Sessão atualizada com sucesso', [
            'sessao_id' => $sessao->id,
            'novo_status' => $sessao->status_confirmacao,
        ]);

        $mensagem = "✅ Sessão marcada como *{$sessao->status_confirmacao}*.\nObrigado pela resposta, {$paciente->nome}!";
        $this->responderNoWhatsapp($numeroLimpo, $mensagem);

        return response()->json(['message' => $mensagem], 200);
    }

    private function responderNoWhatsapp($numero, $mensagem)
    {
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
            'url' => $url,
            'session' => $session,
            'token' => $token,
            'env' => app()->environment(),
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

        if ($response->successful()) {
            Log::info('[Webhook] ✅ Mensagem enviada com sucesso para o WhatsApp', [
                'numero' => $numeroCompleto,
                'mensagem' => $mensagem,
            ]);
        } else {
            Log::error('[Webhook] ❌ Falha ao enviar mensagem ao WhatsApp', [
                'numero' => $numeroCompleto,
                'mensagem' => $mensagem,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }
    }

    private function normalizarNumero($numero)
    {
        // Remove caracteres não numéricos e o prefixo 55 se houver
        $num = preg_replace('/\D/', '', $numero);
        if (str_starts_with($num, '55')) {
            $num = substr($num, 2);
        }
        return $num;
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
}
