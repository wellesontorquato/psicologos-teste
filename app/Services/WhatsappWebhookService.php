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

        $numeroResolvido = $this->extrairTelefoneDoPayload($data, $from);
        $destinoResposta = $this->extrairDestinoValidoParaResposta($data);

        Log::channel('whatsapp')->info('[WPP SERVICE] Diagnóstico inicial', [
            'request_id' => $requestId,
            'event' => $evento,
            'from' => $from,
            'sender_id' => data_get($data, 'sender.id'),
            'chatId' => data_get($data, 'chatId'),
            'participant' => data_get($data, 'participant'),
            'author' => data_get($data, 'author'),
            'shortName' => data_get($data, 'shortName'),
            'numero_resolvido_extraido' => $numeroResolvido,
            'destino_resposta_extraido' => $destinoResposta,
            'body' => $body,
        ]);

        if ((!$numeroResolvido || !$destinoResposta) && $from && str_ends_with(strtolower($from), '@lid')) {
            $resolucao = $this->resolverContatoPorLid($from, $requestId);

            if (!$numeroResolvido) {
                $numeroResolvido = $resolucao['numero_resolvido'] ?? null;
            }

            if (!$destinoResposta) {
                $destinoResposta = $resolucao['destino_resposta'] ?? null;
            }

            Log::channel('whatsapp')->info('[WPP SERVICE] Dados após resolver LID', [
                'request_id' => $requestId,
                'from' => $from,
                'numero_resolvido_final' => $numeroResolvido,
                'destino_resposta_final' => $destinoResposta,
            ]);
        }

        if (!$numeroResolvido) {
            Log::channel('whatsapp')->warning('[WPP SERVICE] Não foi possível extrair/resolver telefone do payload', [
                'request_id' => $requestId,
                'event' => $evento,
                'from' => $from,
                'sender_id' => data_get($data, 'sender.id'),
                'chatId' => data_get($data, 'chatId'),
                'participant' => data_get($data, 'participant'),
            ]);
            return;
        }

        $mensagemLimpa = strtoupper(Str::ascii(trim((string) $body)));

        Log::channel('whatsapp')->info('[WPP SERVICE] Payload normalizado', [
            'request_id' => $requestId,
            'event' => $evento,
            'from' => $from,
            'numero_resolvido' => $numeroResolvido,
            'destino_resposta' => $destinoResposta,
            'body' => $body,
            'mensagem_limpa' => $mensagemLimpa,
        ]);

        $paciente = $this->encontrarPacientePorTelefone($numeroResolvido);

        Log::channel('whatsapp')->info('[WPP SERVICE] Diagnóstico de paciente', [
            'request_id' => $requestId,
            'numero_resolvido' => $numeroResolvido,
            'paciente_encontrado' => (bool) $paciente,
            'paciente_id' => $paciente?->id,
            'paciente_user_id' => $paciente?->user_id,
            'paciente_nome' => $paciente?->nome,
            'telefone_banco' => $paciente?->telefone,
        ]);

        if (!$paciente) {
            Log::channel('whatsapp')->warning('[WPP SERVICE] Paciente não encontrado', [
                'request_id' => $requestId,
                'numero_resolvido' => $numeroResolvido,
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
                'mensagem_limpa' => $mensagemLimpa,
            ]);

            $this->responderNoWhatsappSePossivel(
                $destinoResposta,
                "⚠️ Desculpe, não entendi sua resposta.\n\nResponda com:\n\n*1 - Confirmar*\n*2 - Remarcar*\n*3 - Cancelar*",
                $requestId
            );
            return;
        }

        $sessao = $this->encontrarSessaoValidaParaConfirmacao($paciente);

        Log::channel('whatsapp')->info('[WPP SERVICE] Diagnóstico de sessão', [
            'request_id' => $requestId,
            'paciente_id' => $paciente->id,
            'sessao_encontrada' => (bool) $sessao,
            'sessao_id' => $sessao?->id,
            'status_confirmacao' => $sessao?->status_confirmacao,
            'lembrete_enviado' => $sessao?->lembrete_enviado,
            'data_hora' => $sessao?->data_hora,
        ]);

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

        return in_array($type, ['chat', 'text'], true) || $type === '';
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
        $tz = config('app.timezone');

        $inicioHoje = Carbon::today($tz)->startOfDay();
        $fimLimite  = Carbon::today($tz)->addDays(5)->endOfDay();

        $sessao = Sessao::withoutGlobalScopes()
            ->where('paciente_id', $paciente->id)
            ->whereRaw('UPPER(status_confirmacao) = ?', ['PENDENTE'])
            ->where('lembrete_enviado', 1)
            ->whereNotNull('data_hora')
            ->whereBetween('data_hora', [$inicioHoje, $fimLimite])
            ->orderBy('data_hora', 'desc')
            ->first();

        if ($sessao) {
            return $sessao;
        }

        Log::channel('whatsapp')->warning('[WPP SERVICE] Nenhuma sessão elegível dentro da janela encontrada', [
            'paciente_id' => $paciente->id,
            'janela_inicio' => $inicioHoje->toDateTimeString(),
            'janela_fim' => $fimLimite->toDateTimeString(),
        ]);

        return null;
    }

    private function encontrarPacientePorTelefone(string $numeroResolvido): ?Paciente
    {
        $candidatos = $this->gerarCandidatosTelefone($numeroResolvido);

        return Paciente::withoutGlobalScopes()
            ->where(function ($query) use ($candidatos) {
                foreach ($candidatos as $i => $telefone) {
                    if ($i === 0) {
                        $query->whereRaw('REGEXP_REPLACE(telefone, "[^0-9]", "") = ?', [$telefone]);
                    } else {
                        $query->orWhereRaw('REGEXP_REPLACE(telefone, "[^0-9]", "") = ?', [$telefone]);
                    }
                }
            })
            ->first();
    }

    private function gerarCandidatosTelefone(string $numero): array
    {
        $numero = preg_replace('/\D/', '', $numero);
        $candidatos = [];

        if ($numero === '') {
            return [];
        }

        $add = function (string $valor) use (&$candidatos) {
            $valor = preg_replace('/\D/', '', $valor);
            if ($valor !== '' && !in_array($valor, $candidatos, true)) {
                $candidatos[] = $valor;
            }
        };

        $add($numero);

        // Versões sem/ com 55
        if (str_starts_with($numero, '55') && strlen($numero) > 2) {
            $sem55 = substr($numero, 2);
            $add($sem55);

            // Brasil: com e sem o 9 após DDD
            if (strlen($sem55) === 10) {
                $ddd = substr($sem55, 0, 2);
                $resto = substr($sem55, 2);
                $add($ddd . '9' . $resto);
                $add('55' . $ddd . '9' . $resto);
            }

            if (strlen($sem55) === 11 && substr($sem55, 2, 1) === '9') {
                $ddd = substr($sem55, 0, 2);
                $resto = substr($sem55, 3);
                $add($ddd . $resto);
                $add('55' . $ddd . $resto);
            }
        } else {
            // Sem país explícito, tenta BR e formato original
            $add('55' . $numero);

            if (strlen($numero) === 10) {
                $ddd = substr($numero, 0, 2);
                $resto = substr($numero, 2);
                $add($ddd . '9' . $resto);
                $add('55' . $ddd . '9' . $resto);
            }

            if (strlen($numero) === 11 && substr($numero, 2, 1) === '9') {
                $ddd = substr($numero, 0, 2);
                $resto = substr($numero, 3);
                $add($ddd . $resto);
                $add('55' . $ddd . $resto);
            }
        }

        return $candidatos;
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
        $candidatos = [
            $from,
            data_get($data, 'sender.id'),
            data_get($data, 'chatId'),
            data_get($data, 'id.remote'),
            data_get($data, 'key.remoteJid'),
            data_get($data, 'message.from'),
        ];

        foreach ($candidatos as $cand) {
            if (!is_string($cand) || trim($cand) === '') {
                continue;
            }

            $cand = trim($cand);

            if (str_ends_with(strtolower($cand), '@c.us')) {
                return $this->normalizarNumero($cand);
            }
        }

        return null;
    }

    private function resolverContatoPorLid(string $lid, ?string $requestId = null): array
    {
        $baseUrl = rtrim((string) config('services.wppconnect.base_url'), '/');
        $session = (string) config('services.wppconnect.session', 'psigestor');
        $token   = (string) config('services.wppconnect.token');

        if (!$baseUrl || !$token) {
            Log::channel('whatsapp')->warning('[WPP SERVICE] Não foi possível resolver LID: base_url/token ausentes', [
                'request_id' => $requestId,
                'lid' => $lid,
            ]);

            return [
                'numero_resolvido' => null,
                'destino_resposta' => null,
            ];
        }

        try {
            $url = "{$baseUrl}/api/{$session}/contact/pn-lid/" . urlencode($lid);

            $response = Http::withToken($token)
                ->acceptJson()
                ->timeout(30)
                ->get($url);

            Log::channel('whatsapp')->info('[WPP SERVICE] Resposta da resolução PN-LID', [
                'request_id' => $requestId,
                'lid' => $lid,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            if ($response->failed()) {
                return [
                    'numero_resolvido' => null,
                    'destino_resposta' => null,
                ];
            }

            $data = $response->json();

            $destinoResposta = data_get($data, 'phoneNumber._serialized');

            if (!is_string($destinoResposta) || trim($destinoResposta) === '') {
                $phoneId = data_get($data, 'phoneNumber.id');

                if (is_string($phoneId) && trim($phoneId) !== '') {
                    $digits = preg_replace('/\D/', '', $phoneId);
                    if ($digits !== '') {
                        $destinoResposta = $digits . '@c.us';
                    }
                }
            }

            $numeroResolvido = null;

            $possiveis = [
                data_get($data, 'phoneNumber._serialized'),
                data_get($data, 'phoneNumber.id'),
                data_get($data, 'phoneNumber'),
                data_get($data, 'pn'),
                data_get($data, 'phone'),
                data_get($data, 'wid'),
                data_get($data, 'user'),
            ];

            foreach ($possiveis as $valor) {
                if (!is_string($valor) || trim($valor) === '') {
                    continue;
                }

                $digits = preg_replace('/\D/', '', $valor);

                if ($digits === '') {
                    continue;
                }

                $numeroResolvido = $digits;
                break;
            }

            return [
                'numero_resolvido' => $numeroResolvido,
                'destino_resposta' => $destinoResposta,
            ];
        } catch (Throwable $e) {
            Log::channel('whatsapp')->error('[WPP SERVICE] Erro ao resolver LID', [
                'request_id' => $requestId,
                'lid' => $lid,
                'erro' => $e->getMessage(),
            ]);

            return [
                'numero_resolvido' => null,
                'destino_resposta' => null,
            ];
        }
    }

    private function extrairDestinoValidoParaResposta(array $data): ?string
    {
        $candidatos = [
            data_get($data, 'from'),
            data_get($data, 'sender.id'),
            data_get($data, 'chatId'),
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
        return preg_replace('/\D/', '', $numero);
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
            return $numero !== '' ? $numero : null;
        }

        $numero = preg_replace('/\D/', '', $destino);
        return $numero !== '' ? $numero : null;
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
        $numeroDestino = $this->normalizarDestinoParaEnvio($numero);

        if (!$numeroDestino) {
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
                'numero_envio' => $numeroDestino,
                'endpoint' => $endpoint,
            ]);

            $response = Http::withToken($token)
                ->timeout(30)
                ->acceptJson()
                ->asJson()
                ->post($endpoint, [
                    'phone' => $numeroDestino,
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
                    'numero_envio' => $numeroDestino,
                ]);
                return;
            }

            Log::channel('whatsapp')->info('[WPP SERVICE] Mensagem enviada com sucesso', [
                'request_id' => $requestId,
                'numero_envio' => $numeroDestino,
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
