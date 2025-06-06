<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sessao;
use App\Models\Arquivo;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DashboardExport;
use App\Helpers\AuditHelper;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $dados = $this->obterDadosDashboard($request);
        AuditHelper::log('view_dashboard', 'Acessou o painel do dashboard');
        return view('dashboard.index', $dados);
    }

    public function exportarPdf(Request $request)
    {
        $dados = $this->obterDadosDashboard($request);
        AuditHelper::log('export_dashboard_pdf', 'Exportou relatório do dashboard em PDF');
        $pdf = PDF::loadView('dashboard.relatorio_pdf', $dados);
        return $pdf->download('relatorio-dashboard.pdf');
    }

    public function exportarExcel(Request $request)
    {
        $dados = $this->obterDadosDashboard($request);

        AuditHelper::log('export_dashboard_excel', 'Exportou relatório do dashboard em Excel');

        return Excel::download(
            new DashboardExport($dados),
            'relatorio-dashboard.xlsx',
            \Maatwebsite\Excel\Excel::XLSX,
            ['with_chart']
        );
        
    }

    public function obterDadosDashboard(Request $request)
    {
        $userId = auth()->id();
        $hoje = Carbon::today();

        // 🔎 Filtro de período
        $periodo = $request->get('periodo');
        $dataInicial = $request->get('de') ? Carbon::parse($request->get('de')) : null;
        $dataFinal = $request->get('ate') ? Carbon::parse($request->get('ate'))->endOfDay() : null;

        if ($dataInicial && $dataFinal) {
            // datas manuais aplicadas
        } elseif ($periodo) {
            $dataInicial = $hoje->copy()->subDays($periodo);
            $dataFinal = $hoje->copy()->endOfDay();
        } else {
            $dataInicial = $hoje->copy()->subDays(7);
            $dataFinal = $hoje->copy()->endOfDay();
        }

        // 📊 Totais
        $totais = [
            'sessoes' => Sessao::whereHas('paciente', fn($q) => $q->where('user_id', $userId))
                ->whereBetween('data_hora', [$dataInicial, $dataFinal])
                ->count(),
        ];

        $valores = [
            'total' => Sessao::whereHas('paciente', fn($q) => $q->where('user_id', $userId))
                ->whereBetween('data_hora', [$dataInicial, $dataFinal])
                ->where('foi_pago', true)
                ->sum('valor'),
        ];

        // 📅 Sessões por mês
        $sessaoPorMes = Sessao::selectRaw("DATE_FORMAT(data_hora, '%Y-%m') as mes, count(*) as total")
            ->whereHas('paciente', fn($q) => $q->where('user_id', $userId))
            ->whereBetween('data_hora', [$dataInicial, $dataFinal])
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        // 💰 Valor recebido por mês
        $valorPorMes = Sessao::selectRaw("DATE_FORMAT(data_hora, '%Y-%m') as mes, sum(valor) as total")
            ->whereHas('paciente', fn($q) => $q->where('user_id', $userId))
            ->whereBetween('data_hora', [$dataInicial, $dataFinal])
            ->where('foi_pago', true)
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        // 📈 Valor por dia
        $valoresPorDia = Sessao::whereHas('paciente', fn($q) => $q->where('user_id', $userId))
            ->whereBetween('data_hora', [$dataInicial, $dataFinal])
            ->where('foi_pago', true)
            ->get()
            ->groupBy(fn ($s) => Carbon::parse($s->data_hora)->format('Y-m-d'))
            ->map(fn ($group) => $group->sum('valor'));

        // 🗓️ Sessões de hoje
        $sessoesHoje = Sessao::whereHas('paciente', fn($q) => $q->where('user_id', $userId))
            ->whereDate('data_hora', $hoje)
            ->count();

        // ⚠️ Pendências (não pagas)
        $pendencias = Sessao::whereHas('paciente', fn($q) => $q->where('user_id', $userId))
            ->where('foi_pago', false)
            ->count();

        // 💸 Total no mês atual
        $totalMesAtual = Sessao::whereHas('paciente', fn($q) => $q->where('user_id', $userId))
            ->whereBetween('data_hora', [$dataInicial, $dataFinal])
            ->where('foi_pago', true)
            ->sum('valor');


        // 📂 Últimos arquivos enviados
        $ultimosArquivos = Arquivo::whereHas('paciente', fn($q) => $q->where('user_id', $userId))
            ->latest()->take(5)->get();

        // 📅 Próximas sessões (7 dias à frente)
        $proximasSessoes = Sessao::with('paciente')
            ->whereHas('paciente', fn($q) => $q->where('user_id', $userId))
            ->whereBetween('data_hora', [$hoje, $hoje->copy()->addDays(7)->endOfDay()])
            ->orderBy('data_hora')
            ->get();

        return [
            'totais' => $totais,
            'valores' => $valores,
            'sessaoPorMes' => $sessaoPorMes,
            'valorPorMes' => $valorPorMes,
            'valoresPorDia' => $valoresPorDia,
            'dataInicial' => $dataInicial,
            'dataFinal' => $dataFinal,
            'sessoesHoje' => $sessoesHoje,
            'pendencias' => $pendencias,
            'totalMesAtual' => $totalMesAtual,
            'ultimosArquivos' => $ultimosArquivos,
            'proximasSessoes' => $proximasSessoes,
        ];
    }
}
