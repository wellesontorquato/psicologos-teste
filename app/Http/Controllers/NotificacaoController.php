<?php

namespace App\Http\Controllers;

use App\Models\Notificacao;
use App\Models\Sessao;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotificacaoController extends Controller
{
    public function acao($id)
    {
        $notificacao = Notificacao::findOrFail($id);

        // Verifica se pertence ao usuário logado
        if ($notificacao->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        // Marca como lida
        $notificacao->update([
            'lida' => true,
            'visto_em' => now(),
        ]);

        // ✅ MODAIS ESPECIAIS (aniversário, sessão confirmada, sessão cancelada)
        if (in_array($notificacao->tipo, ['aniversario', 'whatsapp_confirmado', 'whatsapp_cancelada'])) {
            $dadosSessao = null;

            if ($notificacao->relacionado instanceof Sessao) {
                $sessao = $notificacao->relacionado;
                $dadosSessao = [
                    'id' => $sessao->id,
                    'paciente' => optional($sessao->paciente)->nome ?? 'Paciente desconhecido',
                    'data' => Carbon::parse($sessao->data_hora)->format('d/m/Y'),
                    'hora' => Carbon::parse($sessao->data_hora)->format('H:i'),
                    'valor' => number_format($sessao->valor, 2, ',', '.'),
                    'foi_pago' => $sessao->foi_pago,
                ];
            }

            return response()->json([
                'abrir_modal' => true,
                'tipo' => $notificacao->tipo,
                'sessao' => $dadosSessao, // 👈 Aqui é o nome correto
            ]);
        }

        // 🔁 Redirecionamento padrão
        if ($notificacao->relacionado) {
            $tipo = class_basename($notificacao->relacionado_type);

            switch ($tipo) {
                case 'Sessao':
                    return redirect()->route('sessoes.edit', $notificacao->relacionado_id);
                case 'Paciente':
                    return redirect()->route('pacientes.edit', $notificacao->relacionado_id);
                default:
                    return redirect()->route('dashboard')->with('status', 'Notificação lida.');
            }
        }

        return redirect()->route('dashboard')->with('status', 'Notificação lida.');
    }

    public function marcarTodasComoLidas()
    {
        Notificacao::where('user_id', auth()->id())
            ->where('lida', false)
            ->update(['lida' => true]);

        return response()->json(['status' => 'ok']);
    }
}
