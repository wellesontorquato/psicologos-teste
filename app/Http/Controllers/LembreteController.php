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
        $agora = Carbon::now(config('app.timezone'));
        $amanha = $agora->copy()->addDay()->toDateString();
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
            ->with('paciente')
            ->get();

        $detalhes = $sessoes->map(function ($sessao) {
            return [
                'sessao_id' => $sessao->id,
                'data_hora' => $sessao->data_hora,
                'lembrete_enviado' => $sessao->lembrete_enviado,
                'paciente' => $sessao->paciente->nome ?? 'SEM PACIENTE',
                'telefone' => $sessao->paciente->telefone ?? 'NÃO INFORMADO',
                'enviar' => $sessao->lembrete_enviado == 0 ? 'SIM' : 'NÃO',
            ];
        });

        Log::info('📬 Scanner de lembretes gerado:', [
            'verificando_para' => [
                'amanha' => $amanha,
                'segunda' => $segunda,
            ],
            'hoje' => $agora->toDateTimeString(),
            'total' => $sessoes->count(),
            'detalhes' => $detalhes,
        ]);

        if ($sessoes->isEmpty()) {
            return response()->json(['info' => 'Nenhuma sessão encontrada para lembrete.']);
        }

        $erros = [];

        foreach ($sessoes as $sessao) {
            $paciente = $sessao->paciente;

            if (!$paciente || !$paciente->telefone) {
                $erros[] = "Paciente sem telefone válido: Sessão ID {$sessao->id}";
                continue;
            }

            $numero = preg_replace('/\D/', '', $paciente->telefone);
            if (!str_starts_with($numero, '55')) {
                $numero = '55' . $numero;
            }

            $dataHoraFormatada = Carbon::parse($sessao->data_hora)->format('d/m/Y \à\s H:i');
            $mensagem = "Olá {$paciente->nome}, tudo bem? Sua sessão está marcada para {$dataHoraFormatada}. Por favor, responda com CONFIRMADO, REMARCAR ou CANCELAR.";

            $resposta = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.wppconnect.token'),
            ])->post('http://localhost:21465/api/psicologo/send-message', [
                'phone' => $numero,
                'message' => $mensagem,
            ]);

            $json = $resposta->json();

            if ($resposta->successful() && ($json['status'] ?? '') === 'success') {
                $sessao->lembrete_enviado = 1;
                $sessao->save();

                Log::info('✅ Lembrete enviado com sucesso', [
                    'sessao_id' => $sessao->id,
                    'paciente' => $paciente->nome,
                    'numero' => $numero,
                ]);
            } else {
                $erroMsg = "❌ Erro ao enviar para {$paciente->nome} (Sessão ID {$sessao->id}). Código: {$resposta->status()} - " . $resposta->body();
                Log::error($erroMsg);
                $erros[] = $erroMsg;
            }
        }

        return response()->json($erros ?: ['status' => 'Todos os lembretes foram enviados com sucesso!']);
    }
}
