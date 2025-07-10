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
            // <-- AJUSTE 1: Adicionado 'usuario' para carregar os dados do profissional.
            ->with('paciente.user') 
            ->get();

        $detalhes = $sessoes->map(function ($sessao) {
            // <-- AJUSTE 2: Adicionado o nome do profissional aos logs para facilitar a depuração.
            return [
                'sessao_id' => $sessao->id,
                'data_hora' => $sessao->data_hora,
                'lembrete_enviado' => $sessao->lembrete_enviado,
                'paciente' => $sessao->paciente->nome ?? 'SEM PACIENTE',
                'telefone' => $sessao->paciente->telefone ?? 'NÃO INFORMADO',
                'profissional' => $sessao->paciente?->user?->name ?? 'SEM PROFISSIONAL',
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
            // <-- AJUSTE 3: Definida a variável $usuario para ser usada logo abaixo.
            $usuario = $sessao->paciente?->user;

            // <-- AJUSTE 4: Adicionada verificação para garantir que o profissional existe.
            if (!$paciente || !$paciente->telefone || !$usuario) {
                $erros[] = "Dados incompletos (paciente, telefone ou profissional não encontrado): Sessão ID {$sessao->id}";
                continue;
            }

            $numero = preg_replace('/\D/', '', $paciente->telefone);
            if (!str_starts_with($numero, '55')) {
                $numero = '55' . $numero;
            }

            // Este bloco agora funcionará sem erros.
            $dataHoraFormatada = Carbon::parse($sessao->data_hora)->format('d/m/Y \à\s H:i');
            $nomeProfissional = $usuario->name;
            $profissao = $usuario->tipo_profissional ?? 'Profissional';

            $mensagem = "👋 Olá {$paciente->nome}, tudo bem? 😊\n\n" .
                        "Lembrando da sua sessão agendada para 📅 {$dataHoraFormatada} com o(a) 🧑‍⚕️ {$nomeProfissional} ({$profissao}).\n\n" .
                        "Por favor, responda com *CONFIRMAR*, *REMARCAR* ou *CANCELAR*.";
                        
            $url = rtrim(config('services.venom.url'), '/') . '/sendText';

            $resposta = Http::post($url, [
                'to' => $numero . '@c.us',
                'text' => $mensagem,
            ]);

            $json = $resposta->json();

            if (
                $resposta->successful() &&
                isset($json['status']) &&
                $json['status'] === 'success' &&
                isset($json['result']['to']['_serialized'])
            ) {
                $sessao->lembrete_enviado = 1;
                $sessao->save();

                Log::info('✅ Lembrete enviado com sucesso', [
                    'sessao_id' => $sessao->id,
                    'paciente' => $paciente->nome,
                    'numero' => $numero,
                ]);
            }
            else {
                $erroMsg = "❌ Erro ao enviar para {$paciente->nome} (Sessão ID {$sessao->id}). Código: {$resposta->status()} - " . $resposta->body();
                Log::error($erroMsg);
                $erros[] = $erroMsg;
            }
        }

        return response()->json($erros ?: ['status' => 'Todos os lembretes foram enviados com sucesso!']);
    }
}