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
        $data   = data_get($dados, 'data', $dados);

        if (!is_array($data)) {
            $data = [];
        }

        $fromMe = (bool) (data_get($data, 'fromMe') ?? data_get($data, 'isMe') ?? false);
        if ($fromMe) {
            Log::channel('whatsapp')->info('[WPP SERVICE] Ignorado: mensagem enviada pelo próprio bot', [
                'request_id' => $requestId,
                'event' => $evento,
            ]);
            return;
        }

        if (!$this->eventoAceito($evento)) {
            Log::channel('whatsapp')->info('[WPP SERVICE] Ignorado: evento não aceito', [
                'request_id' => $requestId,
                'event' => $evento,
            ]);
            return;
        }

        if (!$this->tipoMensagemAceito($data)) {
            Log::channel('whatsapp')->info('[WPP SERVICE] Ignorado: tipo de mensagem não aceito', [
                'request_id' => $requestId,
                'event' => $evento,
                'type' => data_get($data, 'type'),
            ]);
            return;
        }

        $from = $this->extrairFrom($data);
        $body = $this->extrairBody($data);

        if (!$body) {
            Log::channel('whatsapp')->info('[WPP SERVICE] Ignorado: mensagem sem body útil', [
                'request_id' => $requestId,
                'event' => $evento,
                'from' => $from,
            ]);
            return;
        }

        $numeroLimpo = $this->extrairTelefoneDoPayload($data, $from);
        if (!$numeroLimpo) {
            Log::channel('whatsapp')->warning('[WPP SERVICE] Não foi possível extrair telefone do payload', [
                'request_id' => $requestId,
                'event' => $evento,
                'from' => $from,
                'sender_id' => data_get($data, 'sender.id'),
                'chatId' => data_get($data, 'chatId'),
                'participant' => data_get($data, 'participant'),
            ]);
            return;
        }

        $destinoResposta = $this->extrairDestinoValidoParaResposta($data);
        $mensagemLimpa = strtoupper(Str::ascii(trim((string) $body)));

        Log::channel('whatsapp')->info('[WPP SERVICE] Payload normalizado', [
            'request_id' => $requestId,
            'event' => $evento,
            'from' => $from,
            'numero_limpo' => $numeroLimpo,
            'destino_resposta' => $destinoResposta,
            'body' => $body,
        ]);

        $paciente = $this->encontrarPacientePorTelefone($numeroLimpo);
        if (!$paciente) {
            Log::channel('whatsapp')->warning('[WPP SERVICE] Paciente não encontrado', [
                'request_id' => $requestId,
                'numero_limpo' => $numeroLimpo,
                'from' => $from,
            ]);

            $this->responderNoWhatsappSePossivel(
                $destinoResposta,
                '❌ Não encontramos seu cadastro. Verifique com o(a) profissional.',
                $requestId
            );
            return;
        }

        $status = $this->mapearRespostaParaStatus($mensagemLimpa);
        if (!$status) {
            Log::channel('whatsapp')->info('[WPP SERVICE] Resposta não reconhecida', [
                'request_id' => $requestId,
                'paciente_id' => $paciente->id,
                'mensagem' => $body,
            ]);

            $this->responderNoWhatsappSePossivel(
                $destinoResposta,
                "⚠️ Desculpe, não entendi sua resposta.\n\nResponda com:\n\n*1 - Confirmar*\n*2 - Remarcar*\n*3 - Cancelar*",
                $requestId
            );
            return;
        }

        $sessao = $this->encontrarSessaoValidaParaConfirmacao($paciente);
        if (!$sessao) {
            Log::channel('whatsapp')->warning('[WPP SERVICE] Nenhuma sessão pendente elegível encontrada', [
                'request_id' => $requestId,
                'paciente_id' => $paciente->id,
                'paciente_nome' => $paciente->nome,
            ]);

            $this->responderNoWhatsappSePossivel(
                $destinoResposta,
                "Olá {$paciente->nome}, não encontramos uma sessão pendente para você.",
                $requestId
            );
            return;
        }

        if (in_array($status, ['REMARCAR', 'CANCELADA'], true)) {
            if ($sessao->data_hora && !$sessao->data_hora_original) {
                $sessao->data_hora_original = $sessao->data_hora;
            }

            $sessao->data_hora = null;
        }

        $sessao->status_confirmacao = $status;

        $dirty = $sessao->getDirty();

        if (!empty($dirty)) {
            Sessao::withoutGlobalScopes()
                ->where('id', $sessao->id)
                ->update($dirty);
        }

        match ($status) {
            'CONFIRMADA' => event(new SessaoConfirmada($sessao)),
            'CANCELADA'  => event(new SessaoCancelada($sessao)),
            'REMARCAR'   => event(new SessaoRemarcada($sessao)),
            default      => null,
        };

        $msg = "✅ Obrigado pela resposta, {$paciente->nome}. Sua sessão foi marcada como: *{$status}*.";
        if ($status === 'REMARCAR') {
            $msg .= "\n\nVamos entrar em contato para reagendar.";
        }

        Log::channel('whatsapp')->info('[WPP SERVICE] Sessão atualizada com sucesso', [
            'request_id' => $requestId,
            'paciente_id' => $paciente->id,
            'sessao_id' => $sessao->id,
            'status' => $status,
            'destino_resposta' => $destinoResposta,
        ]);

        $this->responderNoWhatsappSePossivel($destinoResposta, $msg, $requestId);
    }

    private function eventoAceito(string $evento): bool
    {
        return in_array($evento, [
            'onmessage',
            'message',
            'onmessageany',
            'onanymessage',
            'onmessagecreate',
        ], true);
    }

    private function tipoMensagemAceito(array $data): bool
    {
        $type = strtolower((string) data_get($data, 'type', 'chat'));

        return in_array($type, [
            'chat',
            'text',
        ], true) || $type === '';
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
            data_get($data, 'participant'),
            data_get($data, 'author'),
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
            if ((int) $sessao->lembrete_enviado !== 1) {
                continue;
            }

            if (empty($sessao->data_hora)) {
                continue;
            }

            $dataSessao = Carbon::parse($sessao->data_hora)->startOfDay();

            if (!$dataSessao->betweenIncluded($hoje, $dataLimite)) {
                continue;
            }

            return $sessao;
        }

        return null;
    }

    private function encontrarPacientePorTelefone(string $numeroLimpo): ?Paciente
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
            'NAO VOU'       => 'CANCELADA',
            'NÃO VOU'       => 'CANCELADA',
            'CANCELAR'      => 'CANCELADA',
            'CANCELADO'     => 'CANCELADA',
            'CANCELADA'     => 'CANCELADA',
            'DESMARCAR'     => 'CANCELADA',
            'DESMARQUE'     => 'CANCELADA',
            'CANCELE'       => 'CANCELADA',
            '3'             => 'CANCELADA',

            'REMARCAR'      => 'REMARCAR',
            'REMARCACAO'    => 'REMARCAR',
            'REMARCAÇÃO'    => 'REMARCAR',
            'REAGENDAR'     => 'REMARCAR',
            'REAGENDAMENTO' => 'REMARCAR',
            'REMARQUE'      => 'REMARCAR',
            'MUDAR'         => 'REMARCAR',
            'TROCAR'        => 'REMARCAR',
            'ADIAR'         => 'REMARCAR',
            '2'             => 'REMARCAR',

            'CONFIRMADO'    => 'CONFIRMADA',
            'CONFIRMAR'     => 'CONFIRMADA',
            'CONFIRMADA'    => 'CONFIRMADA',
            'CONFIRMEI'     => 'CONFIRMADA',
            'OK'            => 'CONFIRMADA',
            'CERTO'         => 'CONFIRMADA',
            'SIM'           => 'CONFIRMADA',
            'VOU'           => 'CONFIRMADA',
            'ESTAREI'       => 'CONFIRMADA',
            'CONFIRMA'      => 'CONFIRMADA',
            '1'             => 'CONFIRMADA',
        ];

        foreach ($mapa as $chave => $valor) {
            if (Str::contains($bodyLimpo, $chave)) {
                return $valor;
            }
        }

        return null;
    }

    private function extrairTelefoneDoPayload(array $data, ?string $from): ?string
    {
        if ($from && str_contains($from, '@c.us')) {
            return $this->normalizarNumero($from);
        }

        $short = (string) (data_get($data, 'shortName') ?? '');
        $digits = preg_replace('/\D/', '', $short);

        if ($digits && strlen($digits) >= 10) {
            if (str_starts_with($digits, '55')) {
                $digits = substr($digits, 2);
            }
            return $digits;
        }

        $candidatos = [
            data_get($data, 'sender.id'),
            data_get($data, 'sender.pushname'),
            data_get($data, 'sender.shortName'),
            data_get($data, 'id.participant'),
            data_get($data, 'participant'),
            data_get($data, 'author'),
            data_get($data, 'chatId'),
        ];

        foreach ($candidatos as $cand) {
            if (!is_string($cand) || trim($cand) === '') {
                continue;
            }

            if (str_contains($cand, '@c.us')) {
                return $this->normalizarNumero($cand);
            }

            $d = preg_replace('/\D/', '', $cand);
            if (strlen($d) >= 10) {
                if (str_starts_with($d, '55')) {
                    $d = substr($d, 2);
                }
                return $d;
            }
        }

        if ($from) {
            $digits = preg_replace('/\D/', '', $from);
            if (strlen($digits) >= 10) {
                if (str_starts_with($digits, '55')) {
                    $digits = substr($digits, 2);
                }
                return $digits;
            }
        }

        return null;
    }

    private function extrairDestinoValidoParaResposta(array $data): ?string
    {
        $candidatos = [
            data_get($data, 'from'),
            data_get($data, 'sender.id'),
            data_get($data, 'author'),
            data_get($data, 'chatId'),
            data_get($data, 'participant'),
            data_get($data, 'id.remote'),
            data_get($data, 'key.remoteJid'),
            data_get($data, 'message.from'),
        ];

        foreach ($candidatos as $valor) {
            if (!is_string($valor) || trim($valor) === '') {
                continue;
            }

            $valor = trim($valor);

            if (str_ends_with(strtolower($valor), '@c.us')) {
                return $valor;
            }
        }

        return null;
    }

    private function normalizarNumero(string $numero): string
    {
        $num = preg_replace('/\D/', '', $numero);

        if (str_starts_with($num, '55')) {
            $num = substr($num, 2);
        }

        return $num;
    }

    private function normalizarDestinoParaEnvio(string $destino): ?string
    {
        $destino = trim($destino);

        if ($destino === '') {
            return null;
        }

        if (str_ends_with(strtolower($destino), '@lid')) {
            return null;
        }

        if (str_ends_with(strtolower($destino), '@c.us')) {
            $numero = preg_replace('/\D/', '', str_ireplace('@c.us', '', $destino));

            if ($numero === '') {
                return null;
            }

            if (!str_starts_with($numero, '55')) {
                $numero = '55' . $numero;
            }

            return $numero;
        }

        $numero = preg_replace('/\D/', '', $destino);

        if ($numero === '') {
            return null;
        }

        if (!str_starts_with($numero, '55')) {
            $numero = '55' . $numero;
        }

        return $numero;
    }

    private function responderNoWhatsappSePossivel(?string $destinoResposta, string $mensagem, ?string $requestId = null): void
    {
        if (!$destinoResposta) {
            Log::channel('whatsapp')->warning('[WPP SERVICE] Resposta automática não enviada: destino seguro não encontrado', [
                'request_id' => $requestId,
                'mensagem' => $mensagem,
            ]);
            return;
        }

        $this->responderNoWhatsapp($destinoResposta, $mensagem, $requestId);
    }

    private function responderNoWhatsapp(string $numero, string $mensagem, ?string $requestId = null): void
    {
        $numeroComPrefixo = $this->normalizarDestinoParaEnvio($numero);

        if (!$numeroComPrefixo) {
            Log::channel('whatsapp')->warning('[WPP SERVICE] Número inválido para envio', [
                'request_id' => $requestId,
                'numero_original' => $numero,
            ]);
            return;
        }

        $baseUrl = rtrim((string) config('services.wppconnect.base_url'), '/');
        $session = (string) config('services.wppconnect.session', 'psigestor');
        $token   = (string) config('services.wppconnect.token');

        if (!$baseUrl || !$token) {
            Log::channel('whatsapp')->error('[WPP SERVICE] base_url ou token não configurados', [
                'request_id' => $requestId,
            ]);
            return;
        }

        $endpoint = "{$baseUrl}/api/{$session}/send-message";

        try {
            Log::channel('whatsapp')->info('[WPP SERVICE] Enviando resposta via WPPConnect', [
                'request_id' => $requestId,
                'numero_original' => $numero,
                'numero_envio' => $numeroComPrefixo,
                'endpoint' => $endpoint,
            ]);

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
                Log::channel('whatsapp')->error('[WPP SERVICE] Falha ao enviar mensagem', [
                    'request_id' => $requestId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'numero_original' => $numero,
                    'numero_envio' => $numeroComPrefixo,
                ]);
                return;
            }

            Log::channel('whatsapp')->info('[WPP SERVICE] Mensagem enviada com sucesso', [
                'request_id' => $requestId,
                'numero_envio' => $numeroComPrefixo,
                'response' => $response->json(),
            ]);
        } catch (Throwable $e) {
            Log::channel('whatsapp')->error('[WPP SERVICE] Erro ao enviar mensagem', [
                'request_id' => $requestId,
                'numero_original' => $numero,
                'erro' => $e->getMessage(),
            ]);
        }
    }
}
