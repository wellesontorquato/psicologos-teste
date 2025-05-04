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
        $bodyLimpo = explode(' ', $bodyLimpo)[0];
        Log::info('[Webhook] 🧪 Texto limpo gerado:', ['original' => $bodyOriginal, 'limpo' => $bodyLimpo]);

        if (!str_contains($from, '@c.us')) {
            Log::warning('[Webhook] Número malformado:', ['from' => $from]);
            return response()->json(['message' => 'Número inválido.'], 200);
        }

        $numero = str_replace(['@c.us', '+'], '', $from);
        Log::info('[Webhook] 📞 Número extraído:', ['numero' => $numero]);

        $paciente = Paciente::get()->first(function ($p) use ($numero) {
            $numeroWhats = preg_replace('/^55/', '', preg_replace('/\D/', '', $numero));
            $numeroBanco = preg_replace('/\D/', '', $p->telefone);
            $numeroBanco = preg_replace('/^55/', '', $numeroBanco);
            return levenshtein($numeroWhats, $numeroBanco) <= 1;
        });

        if (!$paciente) {
            Log::warning('[Webhook] Paciente não encontrado:', ['numero' => $numero]);
            $this->responderNoWhatsapp($numero, '❌ Não encontramos seu número no sistema. Verifique com sua psicóloga.');
            return response()->json(['message' => 'Paciente não encontrado.'], 200);
        }

        $mapa = [
            'CONFIRMADO' => 'CONFIRMADA',
            'CONFIRMAR'  => 'CONFIRMADA',
            'REMARCAR'   => 'REMARCAR',
            'CANCELAR'   => 'CANCELADA',
        ];

        if (!isset($mapa[$bodyLimpo])) {
            Log::info('[Webhook] ❌ Resposta inválida do paciente', ['recebido' => $bodyLimpo]);
            $this->responderNoWhatsapp($numero, '⚠️ Desculpe, não entendi sua resposta. Envie: CONFIRMADO, REMARCAR ou CANCELAR.');
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
            Log::warning('⚠️ Nenhuma sessão encontrada para atualizar.', ['paciente' => $paciente->nome, 'numero' => $numero]);
            $this->responderNoWhatsapp($numero, "⚠️ Nenhuma sessão pendente encontrada para atualizar.");
            return response()->json(['message' => 'Nenhuma sessão encontrada.'], 200);
        }

        $sessao->status_confirmacao = $mapa[$bodyLimpo];

        if (in_array($mapa[$bodyLimpo], ['REMARCAR', 'CANCELADA'])) {
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
        $this->responderNoWhatsapp($numero, $mensagem);

        return response()->json(['message' => $mensagem], 200);
    }

    private function responderNoWhatsapp($numero, $mensagem)
    {
        $numeroLimpo = preg_replace('/\D/', '', $numero);
        if (!str_starts_with($numeroLimpo, '55')) {
            $numeroLimpo = '55' . $numeroLimpo;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.wppconnect.token'),
        ])->post(app()->isLocal()
            ? 'http://localhost:21465/api/psigestor/send-message'
            : config('services.wppconnect.url') . '/api/psigestor/send-message', [
            'phone' => $numeroLimpo,
            'message' => $mensagem,
        ]);        

        if ($response->successful()) {
            Log::info('[Webhook] 📤 Mensagem enviada com sucesso para o WhatsApp', [
                'numero' => $numeroLimpo,
                'mensagem' => $mensagem,
            ]);
        } else {
            Log::error('[Webhook] ❌ Falha ao enviar mensagem ao WhatsApp', [
                'numero' => $numeroLimpo,
                'mensagem' => $mensagem,
                'status' => $response->status(),
                'body' => $response->body(),
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
