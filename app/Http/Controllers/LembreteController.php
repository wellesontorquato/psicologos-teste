<?php

namespace App\Http\Controllers;

use App\Models\Sessao;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LembreteController extends Controller
{
    public function enviarLembretesManualmente()
    {
        $agora   = Carbon::now(config('app.timezone'));
        $amanha  = $agora->copy()->addDay()->toDateString();
        $segunda = $agora->isFriday()
            ? Carbon::parse($agora)->addDays(3)->toDateString()
            : null;

        $sessoes = Sessao::where('lembrete_enviado', 0)
            ->where(function ($query) use ($amanha, $segunda) {
                $query->whereDate('data_hora', $amanha);
                if ($segunda) {
                    $query->orWhereDate('data_hora', $segunda);
                }
            })
            ->with('paciente.user')
            ->get();

        $detalhes = $sessoes->map(function ($sessao) {
            return [
                'sessao_id'         => $sessao->id,
                'data_hora'         => $sessao->data_hora,
                'lembrete_enviado'  => $sessao->lembrete_enviado,
                'paciente'          => $sessao->paciente->nome ?? 'SEM PACIENTE',
                'telefone'          => $sessao->paciente->telefone ?? 'NÃO INFORMADO',
                'profissional'      => $sessao->paciente?->user?->name ?? 'SEM PROFISSIONAL',
                'enviar'            => $sessao->lembrete_enviado == 0 ? 'SIM' : 'NÃO',
            ];
        });

        Log::channel('whatsapp')->info('📬 Scanner de lembretes gerado:', [
            'verificando_para' => [
                'amanha'  => $amanha,
                'segunda' => $segunda,
            ],
            'hoje'     => $agora->toDateTimeString(),
            'total'    => $sessoes->count(),
            'detalhes' => $detalhes,
        ]);

        if ($sessoes->isEmpty()) {
            return response()->json(['info' => 'Nenhuma sessão encontrada para lembrete.']);
        }

        $erros = [];

        // Config WPPConnect
        $baseUrl = rtrim(config('services.wppconnect.base_url'), '/');
        $token   = config('services.wppconnect.token');

        foreach ($sessoes as $sessao) {
            $paciente = $sessao->paciente;
            $usuario  = $sessao->paciente?->user;

            if (!$paciente || !$paciente->telefone || !$usuario) {
                $msg = "Dados incompletos (paciente, telefone ou profissional não encontrado): Sessão ID {$sessao->id}";
                Log::channel('whatsapp')->warning('[Lembretes] ⚠️ ' . $msg);
                $erros[] = $msg;
                continue;
            }

            // Normaliza número: só dígitos + prefixo 55
            $numero = preg_replace('/\D/', '', $paciente->telefone);
            if (!str_starts_with($numero, '55')) {
                $numero = '55' . $numero;
            }

            $dataHoraFormatada = Carbon::parse($sessao->data_hora)->format('d/m/Y \à\s H:i');
            $nomeProfissional  = $usuario->name;
            $profissao         = $usuario->tipo_profissional ?? 'Profissional';

            $mensagem = "👋 Olá {$paciente->nome}, tudo bem? 😊\n\n" .
                        "Lembrando da sua sessão agendada para 📅 {$dataHoraFormatada} com o(a) 🧑‍⚕️ {$nomeProfissional} ({$profissao}).\n\n" .
                        "Por favor, responda com *CONFIRMAR*, *REMARCAR* ou *CANCELAR*.";

            Log::channel('whatsapp')->info('[Lembretes] 🚀 Enviando lembrete via WPPConnect', [
                'sessao_id' => $sessao->id,
                'paciente'  => $paciente->nome,
                'numero'    => $numero,
                'mensagem'  => $mensagem,
            ]);

            try {
                $resposta = Http::withHeaders([
                        'Authorization' => "Bearer {$token}",
                        'Accept'        => 'application/json',
                    ])
                    ->post("{$baseUrl}/api/psigestor/send-message", [
                        'phone'   => $numero,
                        'message' => $mensagem,
                    ]);

                $json = $resposta->json();

                if ($resposta->successful() && ($json['status'] ?? null) === 'success') {
                    $sessao->lembrete_enviado = 1;
                    $sessao->save();

                    Log::channel('whatsapp')->info('✅ Lembrete enviado com sucesso via WPPConnect', [
                        'sessao_id' => $sessao->id,
                        'paciente'  => $paciente->nome,
                        'numero'    => $numero,
                        'response'  => $json,
                    ]);
                } else {
                    $erroMsg = "❌ Erro ao enviar para {$paciente->nome} (Sessão ID {$sessao->id}). " .
                               "Status HTTP: {$resposta->status()} - Resposta: " . $resposta->body();

                    Log::channel('whatsapp')->error('[Lembretes] ' . $erroMsg);
                    $erros[] = $erroMsg;
                }
            } catch (\Exception $e) {
                $erroMsg = "💥 Exceção ao enviar para {$paciente->nome} (Sessão ID {$sessao->id}): {$e->getMessage()}";
                Log::channel('whatsapp')->error('[Lembretes] ' . $erroMsg);
                $erros[] = $erroMsg;
            }
        }

        return response()->json($erros ?: ['status' => 'Todos os lembretes foram enviados com sucesso!']);
    }
}
