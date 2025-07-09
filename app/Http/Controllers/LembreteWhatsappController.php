<?php

namespace App\Http\Controllers;

use App\Models\Sessao;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Renomeado para manter a consistência com o que já fizemos
class LembreteController extends Controller 
{
    public function enviarLembretesManualmente()
    {
        $agora = Carbon::now(config('app.timezone'));
        
        // Lógica de busca de datas correta (lembretes para o dia seguinte)
        $amanha = $agora->copy()->addDay()->toDateString();
        $segundaFeira = $agora->isFriday() ? $agora->copy()->addDays(3)->toDateString() : null;

        $sessoes = Sessao::with(['paciente', 'usuario'])
            ->where('lembrete_enviado', 0) // Essencial para não reenviar
            ->where(function ($query) use ($amanha, $segundaFeira) {
                $query->whereDate('data_hora', $amanha);
                if ($segundaFeira) {
                    $query->orWhereDate('data_hora', $segundaFeira);
                }
            })
            ->get();

        if ($sessoes->isEmpty()) {
            Log::info('Nenhuma sessão encontrada para envio de lembretes.');
            return response()->json(['status' => 'Nenhuma sessão para enviar lembretes.']);
        }

        $erros = [];
        foreach ($sessoes as $sessao) {
            if (!$sessao->paciente || !$sessao->paciente->telefone || !$sessao->usuario) {
                Log::warning("Dados incompletos para Sessão ID {$sessao->id}. Pulando envio.");
                continue;
            }

            // Formatação de telefone segura
            $numeroLimpo = preg_replace('/\D/', '', $sessao->paciente->telefone);
            $numero = str_starts_with($numeroLimpo, '55') ? $numeroLimpo : '55' . $numeroLimpo;

            // Mensagem robusta com fallback e formato completo de data/hora
            $nomeProfissional = $sessao->usuario->name;
            $profissao = $sessao->usuario->tipo_profissional ?? 'Profissional';
            $dataHoraFormatada = Carbon::parse($sessao->data_hora)->format('d/m/Y \à\s H:i');

            $mensagem = "👋 Olá {$sessao->paciente->nome}, tudo bem? 😊\n\n" .
                        "Lembrando da sua sessão agendada para 📅 {$dataHoraFormatada} com o(a) 🧑‍⚕️ {$nomeProfissional} ({$profissao}).\n\n" .
                        "Por favor, responda com *CONFIRMAR*, *REMARCAR* ou *CANCELAR*.";

            $url = config('services.venom.url');

            $resposta = Http::post($url, [
                'to' => $numero,
                'body' => $mensagem,
            ]);

            // Tratamento de sucesso e erro
            if ($resposta->successful() && ($resposta->json('status') ?? '') === 'success') {
                $sessao->lembrete_enviado = 1;
                $sessao->save();
                Log::info("✅ Lembrete enviado para {$sessao->paciente->nome} (Sessão ID {$sessao->id}).");
            } else {
                $erroMsg = "❌ Falha ao enviar para {$sessao->paciente->nome} (Sessão ID {$sessao->id}): " . $resposta->body();
                $erros[] = $erroMsg;
                Log::error($erroMsg);
            }
        }

        return response()->json($erros ?: ['status' => 'Todos os lembretes foram processados com sucesso.']);
    }
}