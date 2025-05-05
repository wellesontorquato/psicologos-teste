<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sessao;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Helpers\AuditHelper;

class AgendaController extends Controller
{
    public function index()
    {
        AuditHelper::log('view_agenda', 'Acessou a agenda com o calendário');
        return view('agenda.index');
    }

    public function eventos(Request $request)
    {
        $userId = Auth::id();

        $sessoes = Sessao::with('paciente')
            ->whereHas('paciente', fn ($q) => $q->where('user_id', $userId))
            ->whereNotIn('status_confirmacao', ['CANCELADA', 'REMARCAR'])
            ->get();

        $eventos = $sessoes->map(function ($sessao) {
            $start = Carbon::parse($sessao->data_hora)->setTimezone('America/Sao_Paulo');
            $endReal = (clone $start)->addMinutes($sessao->duracao);            

            // Se ultrapassar a meia-noite, forçar visualmente para 23:59:59
            $endVisual = $endReal->isSameDay($start)
                ? $endReal
                : $start->copy()->setTime(23, 59, 59);

            $classes = [$sessao->foi_pago ? 'evento-pago' : 'evento-pendente'];

            $tooltip = 'Início: ' . $start->format('d/m/Y H:i') .
                     ' | Fim: ' . $endReal->format('d/m/Y H:i') .
                     ' | ' . ($sessao->foi_pago ? 'Pago' : 'Não pago');

            $prefixo = '';

            if (!$endReal->isSameDay($start)) {
                $tooltip .= ' | Ultrapassa a madrugada';
                $prefixo = '🌙 ';
            }

            return [
                'id' => $sessao->id,
                'title' => $prefixo . $sessao->paciente->nome . ' (R$ ' . number_format($sessao->valor, 2, ',', '.') . ')',
                'start' => $start->format('Y-m-d\TH:i:s'),
                'end' => $endVisual->format('Y-m-d\TH:i:s'),
                'classNames' => $classes,
                'tooltip' => $tooltip,
            ];
        });

        AuditHelper::log('load_agenda_events', 'Consultou os eventos da agenda');
        return response()->json($eventos);
    }
}
