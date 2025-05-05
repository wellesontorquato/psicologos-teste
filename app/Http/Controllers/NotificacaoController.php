<?php

namespace App\Http\Controllers;

use App\Models\Notificacao;
use App\Models\Sessao;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotificacaoController extends Controller
{
    public function acao($id, Request $request)
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

        // ✅ MODAIS ESPECIAIS (aniversário, sessão confirmada, sessão cancelada, sessão remarcar)
        if (in_array($notificacao->tipo, ['aniversario', 'whatsapp_confirmado', 'whatsapp_cancelada', 'whatsapp_remarcar'])) {
            $dadosSessao = null;

            if ($notificacao->relacionado instanceof Sessao) {
                $sessao = $notificacao->relacionado;

                $data = $sessao->data_hora ? Carbon::parse($sessao->data_hora)->format('d/m/Y') : null;
                $hora = $sessao->data_hora ? Carbon::parse($sessao->data_hora)->format('H:i') : null;

                $dadosSessao = [
                    'id' => $sessao->id,
                    'paciente' => optional($sessao->paciente)->nome ?? 'Paciente desconhecido',
                    'data' => $data,
                    'hora' => $hora,
                    'valor' => number_format($sessao->valor, 2, ',', '.'),
                    'foi_pago' => $sessao->foi_pago,
                    'mensagem' => $notificacao->mensagem,
                ];
            }

            // Se for AJAX, retorna JSON para abrir modal
            if ($request->expectsJson()) {
                return response()->json([
                    'abrir_modal' => true,
                    'tipo' => $notificacao->tipo,
                    'sessao' => $dadosSessao,
                ]);
            }

            // 👇 **Apenas para remarcar** faz fallback para redirecionamento (se não for AJAX)
            if ($notificacao->tipo === 'whatsapp_remarcar' && $notificacao->relacionado instanceof Sessao) {
                return redirect()->route('sessoes.edit', $notificacao->relacionado_id);
            }

            // ✅ Para confirmada e cancelada (e aniversário), não faz nada além de voltar à dashboard
            return redirect()->route('dashboard')->with('status', 'Notificação lida.');
        }

        // 🔁 Redirecionamento padrão para outros tipos
        if ($notificacao->relacionado) {
            $tipo = class_basename($notificacao->relacionado_type);
        
            $route = match ($tipo) {
                'Sessao' => route('sessoes.edit', $notificacao->relacionado_id),
                'Paciente' => route('pacientes.edit', $notificacao->relacionado_id),
                default => route('dashboard'),
            };
        
            // ✅ Se for AJAX (JSON), devolve a URL para o front redirecionar
            if (request()->expectsJson()) {
                return response()->json([
                    'abrir_modal' => false,
                    'redirect_to' => $route,
                ]);
            }
        
            // Caso não seja AJAX (fallback)
            return redirect($route);
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
