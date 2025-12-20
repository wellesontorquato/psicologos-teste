<?php

namespace App\Services;

use App\Models\Paciente;
use App\Models\Sessao;
use App\Events\SessaoConfirmada;
use App\Events\SessaoCancelada;
use App\Events\SessaoRemarcada;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class WhatsappWebhookService
{
    public function processar(array $dados, ?string $requestId = null): void
    {
        $evento = strtolower(data_get($dados, 'event', 'onmessage'));
        $data   = data_get($dados, 'data', $dados);

        $fromMe = (bool) (data_get($data, 'fromMe') ?? data_get($data, 'isMe') ?? false);
        if ($fromMe) return;

        if (!in_array($evento, ['onmessage', 'message', 'onmessageany', 'onanymessage', 'onmessagecreate'])) return;

        $from = data_get($data, 'from')
            ?? data_get($data, 'sender.id')
            ?? data_get($data, 'chatId')
            ?? data_get($data, 'id.remote');

        $body = data_get($data, 'body')
            ?? data_get($data, 'message')
            ?? data_get($data, 'text')
            ?? data_get($data, 'content')
            ?? data_get($data, 'caption');

        if ($from && !str_contains($from, '@c.us') && preg_match('/^\d{10,13}$/', preg_replace('/\D/', '', $from))) {
            $from = preg_replace('/\D/', '', $from) . '@c.us';
        }

        if (!$from || !$body || !str_contains($from, '@c.us')) return;

        $numeroLimpo   = $this->normalizarNumero($from);
        $mensagemLimpa = strtoupper(Str::ascii(trim((string) $body)));

        $paciente = $this->encontrarPacientePorTelefone($numeroLimpo);
        if (!$paciente) {
            $this->responderNoWhatsapp($numeroLimpo, '❌ Não encontramos seu cadastro. Verifique com o(a) profissional.');
            return;
        }

        $status = $this->mapearRespostaParaStatus($mensagemLimpa);
        if (!$status) {
            $this->responderNoWhatsapp($numeroLimpo, "⚠️ Desculpe, não entendi sua resposta.\n\nResponda com:\n\n*✔️ Confirmar*\n*🔄 Remarcar*\n*❌ Cancelar*");
            return;
        }

        $sessao = $this->encontrarSessaoValidaParaConfirmacao($paciente);
        if (!$sessao) {
            $this->responderNoWhatsapp($numeroLimpo, "Olá {$paciente->nome}, não encontramos uma sessão pendente para você.");
            return;
        }

        // preserva data antiga antes de limpar
        if (in_array($status, ['REMARCAR', 'CANCELADA'])) {
            if ($sessao->data_hora && !$sessao->data_hora_original) {
                $sessao->data_hora_original = $sessao->data_hora;
            }
            $sessao->data_hora = null;
        }

        $sessao->status_confirmacao = $status;

        Sessao::withoutGlobalScopes()
            ->where('id', $sessao->id)
            ->update($sessao->getDirty());

        match ($status) {
            'CONFIRMADA' => event(new SessaoConfirmada($sessao)),
            'CANCELADA'  => event(new SessaoCancelada($sessao)),
            'REMARCAR'   => event(new SessaoRemarcada($sessao)),
        };

        $msg = "✅ Obrigado pela resposta, {$paciente->nome}. Sua sessão foi marcada como: *{$status}*.";
        if ($status === 'REMARCAR') $msg .= "\n\nVamos entrar em contato para reagendar.";

        $this->responderNoWhatsapp($numeroLimpo, $msg);
    }

    private function encontrarSessaoValidaParaConfirmacao(Paciente $paciente): ?Sessao
    {
        $hoje       = Carbon::today(config('app.timezone'));
        $dataLimite = $hoje->copy()->addDays(5);

        $sessoes = Sessao::withoutGlobalScopes()
            ->where('paciente_id', $paciente->id)
            ->where('status_confirmacao', 'PENDENTE')
            ->orderBy('data_hora', 'asc')
            ->get();

        foreach ($sessoes as $sessao) {
            if ((int) $sessao->lembrete_enviado !== 1) continue;
            if (empty($sessao->data_hora)) continue;

            $dataSessao = Carbon::parse($sessao->data_hora)->startOfDay();
            if (!$dataSessao->betweenIncluded($hoje, $dataLimite)) continue;

            return $sessao;
        }

        return null;
    }

    private function encontrarPacientePorTelefone(string $numeroLimpo): ?Paciente
    {
        // MySQL OK
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
            'NAO VOU' => 'CANCELADA','NÃO VOU' => 'CANCELADA','CANCELAR' => 'CANCELADA','CANCELADO' => 'CANCELADA',
            'CANCELADA' => 'CANCELADA','DESMARCAR' => 'CANCELADA','DESMARQUE' => 'CANCELADA','CANCELE' => 'CANCELADA','3' => 'CANCELADA',

            'REMARCAR' => 'REMARCAR','REMARCACAO' => 'REMARCAR','REMARCAÇÃO' => 'REMARCAR','REAGENDAR' => 'REMARCAR',
            'REAGENDAMENTO' => 'REMARCAR','REMARQUE' => 'REMARCAR','MUDAR' => 'REMARCAR','TROCAR' => 'REMARCAR','ADIAR' => 'REMARCAR','2' => 'REMARCAR',

            'CONFIRMADO' => 'CONFIRMADA','CONFIRMAR' => 'CONFIRMADA','CONFIRMADA' => 'CONFIRMADA','CONFIRMEI' => 'CONFIRMADA',
            'OK' => 'CONFIRMADA','CERTO' => 'CONFIRMADA','SIM' => 'CONFIRMADA','VOU' => 'CONFIRMADA','ESTAREI' => 'CONFIRMADA','CONFIRMA' => 'CONFIRMADA','1' => 'CONFIRMADA',
        ];

        foreach ($mapa as $chave => $valor) {
            if (Str::contains($bodyLimpo, $chave)) return $valor;
        }
        return null;
    }

    private function normalizarNumero(string $numero): string
    {
        $num = preg_replace('/\D/', '', $numero);
        if (str_starts_with($num, '55')) $num = substr($num, 2);
        return $num;
    }

    private function responderNoWhatsapp(string $numero, string $mensagem): void
    {
        $numeroComPrefixo = '55' . preg_replace('/[^0-9]/', '', $numero);

        $baseUrl = rtrim(config('services.wppconnect.base_url'), '/');
        $session = config('services.wppconnect.session', 'psigestor');
        $token   = config('services.wppconnect.token');

        if (!$baseUrl || !$token) {
            Log::channel('whatsapp')->error('[WPP] base_url ou token não configurados');
            return;
        }

        $endpoint = "{$baseUrl}/api/{$session}/send-message";

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->asJson()
                ->post($endpoint, [
                    'phone' => $numeroComPrefixo,
                    'isGroup' => false,
                    'isNewsletter' => false,
                    'isLid' => false,
                    'message' => $mensagem,
                ]);

            if ($response->failed()) {
                Log::channel('whatsapp')->error('[WPP] Falha ao enviar mensagem', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (Throwable $e) {
            Log::channel('whatsapp')->error('[WPP] Erro ao enviar mensagem', ['erro' => $e->getMessage()]);
        }
    }
}
