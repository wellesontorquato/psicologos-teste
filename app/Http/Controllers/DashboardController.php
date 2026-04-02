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
use Illuminate\Support\Facades\Schema;

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

        $pdf = Pdf::loadView('dashboard.relatorio_pdf', $dados);

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

        $moedaSelecionada = $request->get('moeda', 'BRL');

        $temColunaMoeda = Schema::hasColumn('sessoes', 'moeda');
        $temColunaConfirmado = Schema::hasColumn('sessoes', 'confirmado');
        $temColunaStatusConfirmacao = Schema::hasColumn('sessoes', 'status_confirmacao');

        $periodo = $request->get('periodo');
        $dataInicial = $request->get('de') ? Carbon::parse($request->get('de'))->startOfDay() : null;
        $dataFinal = $request->get('ate') ? Carbon::parse($request->get('ate'))->endOfDay() : null;

        if ($dataInicial && $dataFinal) {
            // período manual
        } elseif ($periodo) {
            $dataInicial = $hoje->copy()->subDays((int) $periodo)->startOfDay();
            $dataFinal = $hoje->copy()->endOfDay();
        } else {
            $dataInicial = $hoje->copy()->subDays(7)->startOfDay();
            $dataFinal = $hoje->copy()->endOfDay();
        }

        if ($dataInicial->gt($dataFinal)) {
            [$dataInicial, $dataFinal] = [$dataFinal->copy()->startOfDay(), $dataInicial->copy()->endOfDay()];
        }

        $diasNoPeriodo = max(1, $dataInicial->diffInDays($dataFinal) + 1);
        $inicioPeriodoAnterior = $dataInicial->copy()->subDays($diasNoPeriodo);
        $fimPeriodoAnterior = $dataInicial->copy()->subSecond();

        $filtroMoeda = function ($query) use ($moedaSelecionada, $temColunaMoeda) {
            if (!$temColunaMoeda) {
                return;
            }

            if ($moedaSelecionada === 'BRL') {
                $query->where(function ($q) {
                    $q->whereNull('moeda')
                      ->orWhere('moeda', 'BRL');
                });
            } else {
                $query->where('moeda', $moedaSelecionada);
            }
        };

        $baseQuery = Sessao::whereHas('paciente', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });

        $totais = [
            'sessoes' => (clone $baseQuery)
                ->whereBetween('data_hora', [$dataInicial, $dataFinal])
                ->count(),
        ];

        $totalMesAtual = (clone $baseQuery)
            ->whereBetween('data_hora', [$dataInicial, $dataFinal])
            ->where('foi_pago', true)
            ->where(function ($q) use ($filtroMoeda) {
                $filtroMoeda($q);
            })
            ->sum('valor');

        $totalPeriodoAnterior = (clone $baseQuery)
            ->whereBetween('data_hora', [$inicioPeriodoAnterior, $fimPeriodoAnterior])
            ->where('foi_pago', true)
            ->where(function ($q) use ($filtroMoeda) {
                $filtroMoeda($q);
            })
            ->sum('valor');

        $crescimentoFaturamento = null;
        if ((float) $totalPeriodoAnterior > 0) {
            $crescimentoFaturamento = round(
                ((($totalMesAtual - $totalPeriodoAnterior) / $totalPeriodoAnterior) * 100),
                1
            );
        }

        $valores = [
            'total' => $totalMesAtual,
            'total_periodo_anterior' => $totalPeriodoAnterior,
        ];

        $sessaoPorMes = Sessao::selectRaw("DATE_FORMAT(data_hora, '%Y-%m') as mes, COUNT(*) as total")
            ->whereHas('paciente', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereBetween('data_hora', [$dataInicial, $dataFinal])
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $valorPorMes = Sessao::selectRaw("DATE_FORMAT(data_hora, '%Y-%m') as mes, SUM(valor) as total")
            ->whereHas('paciente', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereBetween('data_hora', [$dataInicial, $dataFinal])
            ->where('foi_pago', true)
            ->where(function ($q) use ($filtroMoeda) {
                $filtroMoeda($q);
            })
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $valoresPorDia = (clone $baseQuery)
            ->whereBetween('data_hora', [$dataInicial, $dataFinal])
            ->where('foi_pago', true)
            ->where(function ($q) use ($filtroMoeda) {
                $filtroMoeda($q);
            })
            ->get()
            ->groupBy(function ($s) {
                return Carbon::parse($s->data_hora)->format('Y-m-d');
            })
            ->map(function ($group) {
                return $group->sum('valor');
            });

        $valoresDiasConvertidos = $valoresPorDia->values();

        $sessoesHoje = (clone $baseQuery)
            ->whereDate('data_hora', $hoje)
            ->count();

        $agendaHoje = Sessao::with('paciente')
            ->whereHas('paciente', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereDate('data_hora', $hoje)
            ->orderBy('data_hora')
            ->get();

        $proximasSessoes = Sessao::with('paciente')
            ->whereHas('paciente', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereBetween('data_hora', [$hoje->copy()->startOfDay(), $hoje->copy()->addDays(7)->endOfDay()])
            ->orderBy('data_hora')
            ->get();

        $pendenciasFinanceiras = Sessao::with('paciente')
            ->whereHas('paciente', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('foi_pago', false)
            ->whereNotNull('data_hora')
            ->orderBy('data_hora', 'asc')
            ->get();

        $pendenciasEvolucao = Sessao::with(['paciente', 'evolucoes'])
            ->whereHas('paciente', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('data_hora', '<', Carbon::now())
            ->whereDoesntHave('evolucoes')
            ->orderBy('data_hora', 'asc')
            ->get();

        $pendenciasTotal = $pendenciasFinanceiras->count() + $pendenciasEvolucao->count();

        $pacientesAtivos = (clone $baseQuery)
            ->whereBetween('data_hora', [$dataInicial, $dataFinal])
            ->distinct('paciente_id')
            ->count('paciente_id');

        $ultimosArquivos = Arquivo::whereHas('paciente', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->latest()
            ->take(5)
            ->get();

        $pacientesInadimplentes = $pendenciasFinanceiras
            ->pluck('paciente_id')
            ->filter()
            ->unique()
            ->count();

        $receberNoPeriodo = $pendenciasFinanceiras->sum(function ($sessao) {
            return $sessao->valor_convertido ?? $sessao->valor ?? 0;
        });

        $sessoesSemPagamento = $pendenciasFinanceiras->count();
        $sessoesSemEvolucao = $pendenciasEvolucao->count();

        $pacientesSemConfirmacao = 0;
        $sessoesSemConfirmacao = 0;

        if ($temColunaConfirmado) {
            $sessoesSemConfirmacaoQuery = Sessao::whereHas('paciente', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
                ->whereBetween('data_hora', [$hoje->copy()->startOfDay(), $hoje->copy()->addDays(7)->endOfDay()])
                ->where(function ($q) {
                    $q->whereNull('confirmado')
                      ->orWhere('confirmado', false)
                      ->orWhere('confirmado', 0);
                });

            $sessoesSemConfirmacao = (clone $sessoesSemConfirmacaoQuery)->count();
            $pacientesSemConfirmacao = (clone $sessoesSemConfirmacaoQuery)
                ->distinct('paciente_id')
                ->count('paciente_id');
        } elseif ($temColunaStatusConfirmacao) {
            $sessoesSemConfirmacaoQuery = Sessao::whereHas('paciente', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
                ->whereBetween('data_hora', [$hoje->copy()->startOfDay(), $hoje->copy()->addDays(7)->endOfDay()])
                ->where(function ($q) {
                    $q->whereNull('status_confirmacao')
                      ->orWhereIn('status_confirmacao', ['pendente', 'aguardando', 'nao_confirmado', 'não_confirmado']);
                });

            $sessoesSemConfirmacao = (clone $sessoesSemConfirmacaoQuery)->count();
            $pacientesSemConfirmacao = (clone $sessoesSemConfirmacaoQuery)
                ->distinct('paciente_id')
                ->count('paciente_id');
        }

        return [
            'totais'                    => $totais,
            'valores'                   => $valores,
            'sessaoPorMes'              => $sessaoPorMes,
            'valorPorMes'               => $valorPorMes,
            'valoresPorDia'             => $valoresPorDia,
            'valoresDiasConvertidos'    => $valoresDiasConvertidos,
            'dataInicial'               => $dataInicial,
            'dataFinal'                 => $dataFinal,
            'sessoesHoje'               => $sessoesHoje,
            'agendaHoje'                => $agendaHoje,
            'pendenciasTotal'           => $pendenciasTotal,
            'pendenciasFinanceiras'     => $pendenciasFinanceiras,
            'pendenciasEvolucao'        => $pendenciasEvolucao,
            'totalMesAtual'             => $totalMesAtual,
            'totalConvertido'           => $totalMesAtual,
            'totalPeriodoAnterior'      => $totalPeriodoAnterior,
            'crescimentoFaturamento'    => $crescimentoFaturamento,
            'ultimosArquivos'           => $ultimosArquivos,
            'proximasSessoes'           => $proximasSessoes,
            'pacientesAtivos'           => $pacientesAtivos,
            'pacientesInadimplentes'    => $pacientesInadimplentes,
            'pacientesSemConfirmacao'   => $pacientesSemConfirmacao,
            'sessoesSemConfirmacao'     => $sessoesSemConfirmacao,
            'sessoesSemPagamento'       => $sessoesSemPagamento,
            'sessoesSemEvolucao'        => $sessoesSemEvolucao,
            'receberNoPeriodo'          => $receberNoPeriodo,
            'moedaSelecionada'          => $moedaSelecionada,
            'periodoDias'               => $diasNoPeriodo,
            'temColunaMoeda'            => $temColunaMoeda,
            'temColunaConfirmado'       => $temColunaConfirmado,
            'temColunaStatusConfirmacao'=> $temColunaStatusConfirmacao,
        ];
    }
}
