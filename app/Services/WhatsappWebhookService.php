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
        $evento = strtolower((string) data_get($dados, 'event', 'onmessage'));
        $data   = (array) data_get($dados, 'data', $dados);

        // ignora mensagens enviadas por você/bot
        $fromMe = (bool) (data_get($data, 'fromMe') ?? data_get($data, 'isMe') ?? false);
        if ($fromMe) return;

        // aceita eventos comuns do WPPConnect
        if (!in_array($evento, ['onmessage', 'message', 'onmessageany', 'onanymessage', 'onmessagecreate'], true)) {
            return;
        }

        // tenta achar o "from" em vários formatos
        $from = data_get($data, 'from')
            ?? data_get($data, 'sender.id')
            ?? data_get($data, 'chatId')
            ?? data_get($data, 'id.remote')
            ?? data_get($data, 'key.remoteJid')
            ?? data_get($data, 'message.from');

        // tenta achar o "body" em vários formatos
        $body = data_get($data, 'body')
            ?? data_get($data, 'message')
            ?? data_get($data, 'text')
            ?? data_get($data, 'content')
            ?? data_get($data, 'caption')
            ?? data_get($data, 'message.body')
            ?? data_get($data, 'message.text');

        // precisa ter mensagem
        if (!$body) return;

        // extrai/normaliza telefone (c.us / lid / shortName / fallback)
        $numeroLimpo = $this->extrairTelefoneDoPayload($data, is_string($from) ? $from : null);
        if (!$numeroLimpo) return;

        $mensagemLimpa = strtoupper(Str::ascii(trim((string) $body)));

        $paciente = $this->encontrarPacientePorTelefone($numeroLimpo);
        if (!$paciente) {
            $this->responderNoWhatsapp($numeroLimpo, '❌ Não encontramos seu cadastro. Verifique com o(a) profissional.');
            return;
        }

        $status = $this->mapearRespostaParaStatus($mensagemLimpa);
        if (!$status) {
            $this->responderNoWhatsapp(
                $numeroLimpo,
                "⚠️ Desculpe, não entendi sua resposta.\n\nResponda com:\n\n*✔️ Confirmar*\n*🔄 Remarcar*\n*❌ Cancelar*"
            );
            return;
        }

        $sessao = $this->encontrarSessaoValidaParaConfirmacao($paciente);
        if (!$sessao) {
            $this->responderNoWhatsapp($numeroLimpo, "Olá {$paciente->nome}, não encontramos uma sessão pendente para você.");
            return;
        }

        // preserva data antiga antes de limpar
        if (in_array($status, ['REMARCAR', 'CANCELADA'], true)) {
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
            ->whereRaw('UPPER(status_confirmacao) = ?', ['PENDENTE'])
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

            // se vier com 10 dígitos (sem 9), tenta também com 11 dígitos (com 9)
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

    private function extrairTelefoneDoPayload(array $data, ?string $from): ?string
    {
        // Caso padrão (@c.us)
        if ($from && str_contains($from, '@c.us')) {
            return $this->normalizarNumero($from);
        }

        // Caso LID (ex.: "...@lid"): tenta pegar do shortName
        $short  = (string) (data_get($data, 'shortName') ?? '');
        $digits = preg_replace('/\D/', '', $short);

        if ($digits) {
            // shortName geralmente vem com 55
            if (str_starts_with($digits, '55')) $digits = substr($digits, 2);
            return $digits;
        }

        // Outros possíveis campos onde pode aparecer o número (variações comuns)
        $candidatos = [
            data_get($data, 'sender.id'),
            data_get($data, 'sender.pushname'),
            data_get($data, 'sender.shortName'),
            data_get($data, 'id.participant'),
            data_get($data, 'participant'),
        ];

        foreach ($candidatos as $cand) {
            if (!is_string($cand) || $cand === '') continue;
            $d = preg_replace('/\D/', '', $cand);
            if (strlen($d) >= 10) {
                if (str_starts_with($d, '55')) $d = substr($d, 2);
                return $d;
            }
        }

        // fallback: tenta extrair dígitos do próprio "from"
        if ($from) {
            $digits = preg_replace('/\D/', '', $from);
            if (strlen($digits) >= 10) {
                if (str_starts_with($digits, '55')) $digits = substr($digits, 2);
                return $digits;
            }
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

        $baseUrl = rtrim((string) config('services.wppconnect.base_url'), '/');
        $session = (string) config('services.wppconnect.session', 'psigestor');
        $token   = (string) config('services.wppconnect.token');

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
            Log::channel('whatsapp')->error('[WPP] Erro ao enviar mensagem', [
                'erro' => $e->getMessage(),
            ]);
        }
    }
}
