@extends('layouts.app')

@section('title', 'Dashboard | PsiGestor')

@section('content')
<div class="psi-dashboard">

    @php
        $moedaSelecionada = request('moeda', 'BRL');

        $simbolos = [
            'BRL' => 'R$',
            'USD' => 'US$',
            'EUR' => '€',
            'GBP' => '£',
            'ARS' => 'AR$',
            'CLP' => 'CLP$',
            'MXN' => 'MX$',
            'CAD' => 'C$',
            'AUD' => 'A$',
        ];

        $simbolo = $simbolos[$moedaSelecionada] ?? $moedaSelecionada;

        $primeiroNome = explode(' ', Auth::user()->name)[0] ?? 'Profissional';

        $totalRecebidoPeriodo = $totalConvertido ?? $totalMesAtual ?? 0;

        $pendenciasSemConfirmacao = $pacientesSemConfirmacao ?? 0;
        $sessoesSemEvolucao = $sessoesSemEvolucao ?? ($pendenciasEvolucao->count() ?? 0);
        $pacientesInadimplentes = $pacientesInadimplentes ?? ($pendenciasFinanceiras->pluck('paciente_id')->unique()->count() ?? 0);
        $sessoesSemPagamento = $sessoesSemPagamento ?? ($pendenciasFinanceiras->count() ?? 0);

        $receberNoPeriodo = $receberNoPeriodo ?? $pendenciasFinanceiras->sum(function ($item) {
            return $item->valor_convertido ?? $item->valor ?? 0;
        });

        $insightCrescimento = $crescimentoFaturamento ?? null;
    @endphp

    @if (session('status') === 'email-just-verified')
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center psi-alert" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            Seu e-mail foi verificado com sucesso! Bem-vindo(a) ao sistema PsiGestor.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @elseif (session('status') === 'email-already-verified')
        <div class="alert alert-info alert-dismissible fade show d-flex align-items-center psi-alert" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i>
            Seu e-mail já estava verificado anteriormente.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- HERO --}}
    <section class="psi-hero mb-4">
        <div class="psi-hero-left">
            <span class="psi-hero-kicker">Painel principal</span>
            <h1>Olá, {{ $primeiroNome }}</h1>
            <p>Hoje você tem uma visão clara da sua agenda, pendências e financeiro em um só lugar.</p>
        </div>

        <div class="psi-hero-right">
            <a href="{{ route('agenda') }}" class="btn btn-success psi-hero-btn">
                <i class="bi bi-calendar-week me-2"></i> Ver agenda do dia
            </a>
        </div>
    </section>

    {{-- KPIS --}}
    <section class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="psi-stat-card">
                <div class="psi-stat-icon bg-primary-subtle text-primary">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="psi-stat-content">
                    <span class="psi-stat-label">Sessões agendadas</span>
                    <strong>{{ $sessoesHoje }}</strong>
                    <small>para hoje</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="psi-stat-card">
                <div class="psi-stat-icon bg-success-subtle text-success">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="psi-stat-content">
                    <span class="psi-stat-label">{{ $simbolo }} recebido</span>
                    <strong>{{ number_format($totalRecebidoPeriodo, 2, ',', '.') }}</strong>
                    <small>no período selecionado</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="psi-stat-card">
                <div class="psi-stat-icon bg-warning-subtle text-warning">
                    <i class="bi bi-exclamation-circle"></i>
                </div>
                <div class="psi-stat-content">
                    <span class="psi-stat-label">Sem confirmação</span>
                    <strong>{{ $pendenciasSemConfirmacao }}</strong>
                    <small>pacientes pendentes</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="psi-stat-card">
                <div class="psi-stat-icon bg-danger-subtle text-danger">
                    <i class="bi bi-shield-exclamation"></i>
                </div>
                <div class="psi-stat-content">
                    <span class="psi-stat-label">Pendências</span>
                    <strong>{{ $pendenciasTotal }}</strong>
                    <small>itens que exigem atenção</small>
                </div>
            </div>
        </div>
    </section>

    {{-- AÇÕES RÁPIDAS + FILTRO --}}
    <section class="row g-3 mb-4">
        <div class="col-12 col-xl-8">
            <div class="psi-panel">
                <div class="psi-panel-header">
                    <h3>Ações rápidas</h3>
                    <span>atalhos do dia</span>
                </div>

                <div class="psi-quick-actions">
                    <a href="{{ route('sessoes.create') }}" class="psi-action-btn psi-action-primary">
                        <i class="bi bi-plus-lg"></i>
                        <span>Nova Sessão</span>
                    </a>

                    <a href="{{ route('evolucoes.create') }}" class="psi-action-btn psi-action-purple">
                        <i class="bi bi-journal-plus"></i>
                        <span>Nova Evolução</span>
                    </a>

                    <a href="{{ route('pacientes.create') }}" class="psi-action-btn psi-action-teal">
                        <i class="bi bi-person-plus"></i>
                        <span>Novo Paciente</span>
                    </a>

                    <a href="{{ route('agenda') }}" class="psi-action-btn psi-action-light">
                        <i class="bi bi-calendar3"></i>
                        <span>Ver Agenda</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="psi-panel psi-filter-panel">
                <div class="psi-panel-header">
                    <h3>Período e moeda</h3>
                    <span>{{ $dataInicial->format('d/m/Y') }} até {{ $dataFinal->format('d/m/Y') }}</span>
                </div>

                <form method="GET" class="psi-filter-form">
                    <div class="mb-2">
                        <select name="moeda" class="form-select" onchange="this.form.submit()">
                            @php
                                $moedas = ['BRL','USD','EUR','GBP','ARS','CLP','MXN','CAD','AUD'];
                            @endphp

                            @foreach($moedas as $m)
                                <option value="{{ $m }}" {{ $moedaSelecionada === $m ? 'selected' : '' }}>
                                    Moeda: {{ $m }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <select name="periodo" class="form-select" onchange="this.form.submit()">
                            <option value="">Período rápido...</option>
                            <option value="7" {{ request('periodo') == '7' ? 'selected' : '' }}>Últimos 7 dias</option>
                            <option value="15" {{ request('periodo') == '15' ? 'selected' : '' }}>Últimos 15 dias</option>
                            <option value="30" {{ request('periodo') == '30' ? 'selected' : '' }}>Últimos 30 dias</option>
                        </select>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <input type="date" name="de" value="{{ request('de') }}" class="form-control">
                        </div>
                        <div class="col-6">
                            <input type="date" name="ate" value="{{ request('ate') }}" class="form-control">
                        </div>
                    </div>

                    @foreach(request()->except(['moeda', 'de', 'ate', 'periodo']) as $campo => $valor)
                        <input type="hidden" name="{{ $campo }}" value="{{ $valor }}">
                    @endforeach

                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-search me-1"></i> Aplicar filtros
                        </button>
                    </div>
                </form>

                <div class="psi-export-actions">
                    <a href="{{ route('dashboard.pdf', request()->all()) }}" class="btn btn-danger no-spinner-on-download">
                        <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                    </a>
                    <a href="{{ route('dashboard.excel', request()->all()) }}" class="btn btn-success no-spinner-on-download">
                        <i class="bi bi-file-earmark-excel me-1"></i> Excel
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- CONTEÚDO PRINCIPAL --}}
    <section class="row g-3 mb-4 align-items-start">
        <div class="col-12 col-xl-6">
            <div class="psi-panel psi-agenda-panel h-100">
                <div class="psi-panel-header">
                    <div>
                        <h3>Agenda do dia</h3>
                        <span>próximos atendimentos</span>
                    </div>
                </div>

                @if(isset($agendaHoje) && $agendaHoje->count())
                    <div class="psi-agenda-list">
                        @foreach($agendaHoje->take(5) as $sessao)
                            <div class="psi-agenda-item">
                                <div class="psi-agenda-time">
                                    {{ \Carbon\Carbon::parse($sessao->data_hora)->format('H:i') }}
                                </div>

                                <div class="psi-agenda-main">
                                    <strong>{{ $sessao->paciente->nome ?? 'Paciente removido' }}</strong>
                                    <small>{{ \Carbon\Carbon::parse($sessao->data_hora)->format('d/m/Y') }}</small>
                                </div>

                                <div class="psi-agenda-status">
                                    <span class="badge rounded-pill bg-{{ $sessao->foi_pago ? 'success' : 'warning text-dark' }}">
                                        {{ $sessao->foi_pago ? 'Pago' : 'Pendente' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('agenda') }}" class="psi-link-more">
                            Ver agenda completa <i class="bi bi-arrow-right-short"></i>
                        </a>
                    </div>
                @else
                    <div class="psi-empty-state">
                        <i class="bi bi-calendar-x"></i>
                        <p>Nenhuma sessão agendada para hoje.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-12 col-xl-3">
            <div class="d-flex flex-column gap-3">
                <div class="psi-panel psi-finance-panel">
                    <div class="psi-panel-header mb-3">
                        <div>
                            <h3>Financeiro</h3>
                            <span>resumo do período</span>
                        </div>
                    </div>

                    <div class="psi-finance-list">
                        <div class="psi-finance-item">
                            <span>Recebido</span>
                            <strong>{{ $simbolo }} {{ number_format($totalRecebidoPeriodo, 2, ',', '.') }}</strong>
                        </div>

                        <div class="psi-finance-item">
                            <span>A receber</span>
                            <strong>{{ $simbolo }} {{ number_format($receberNoPeriodo, 2, ',', '.') }}</strong>
                        </div>

                        <div class="psi-finance-item">
                            <span>Inadimplente</span>
                            <strong>{{ $simbolo }} {{ number_format($receberNoPeriodo, 2, ',', '.') }}</strong>
                        </div>
                    </div>

                    <div class="psi-chart-mini">
                        <h4>Evolução do faturamento</h4>
                        <div class="psi-finance-chart-wrap">
                            <canvas id="graficoValores"></canvas>
                        </div>
                    </div>
                </div>

                <div class="psi-panel">
                    <div class="psi-panel-header">
                        <div>
                            <h3>Insights</h3>
                            <span>leitura rápida do sistema</span>
                        </div>
                    </div>

                    <div class="psi-insights-list">
                        <div class="psi-insight-item">
                            <i class="bi bi-graph-up-arrow text-success"></i>
                            <span>
                                @if(!is_null($insightCrescimento))
                                    Seu faturamento mudou <strong>{{ $insightCrescimento }}%</strong> no período.
                                @else
                                    Seu painel financeiro foi atualizado com base no período selecionado.
                                @endif
                            </span>
                        </div>

                        <div class="psi-insight-item">
                            <i class="bi bi-clipboard-pulse text-primary"></i>
                            <span>Você possui <strong>{{ $sessoesSemEvolucao }}</strong> sessão(ões) aguardando evolução clínica.</span>
                        </div>

                        <div class="psi-insight-item">
                            <i class="bi bi-person-exclamation text-warning"></i>
                            <span>{{ $pacientesInadimplentes }} paciente(s) exigem acompanhamento financeiro.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-3">
            <div class="psi-panel">
                <div class="psi-panel-header">
                    <div>
                        <h3>Atenção</h3>
                        <span>o que precisa de ação</span>
                    </div>
                </div>

                <div class="psi-alert-list">
                    <div class="psi-alert-item">
                        <i class="bi bi-x-circle-fill text-danger"></i>
                        <span>{{ $sessoesSemEvolucao }} sessões não evoluídas</span>
                    </div>

                    <div class="psi-alert-item">
                        <i class="bi bi-exclamation-diamond-fill text-warning"></i>
                        <span>{{ $pacientesInadimplentes }} pacientes inadimplentes</span>
                    </div>

                    <div class="psi-alert-item">
                        <i class="bi bi-cash-coin text-warning"></i>
                        <span>{{ $sessoesSemPagamento }} sessão(ões) sem pagamento</span>
                    </div>

                    @if($pendenciasTotal > 0)
                        <button class="btn btn-outline-danger btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#modalPendencias">
                            Ver pendências
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- GRÁFICOS --}}
    <section class="row g-3 mb-4">
        <div class="col-12 col-xl-6">
            <div class="psi-panel">
                <div class="psi-panel-header">
                    <div>
                        <h3>Sessões por mês</h3>
                        <span>volume de atendimentos</span>
                    </div>
                </div>
                <div class="psi-chart-box">
                    <canvas id="graficoSessoes" height="120"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="psi-panel">
                <div class="psi-panel-header">
                    <div>
                        <h3>Últimos arquivos enviados</h3>
                        <span>acesso rápido aos arquivos recentes</span>
                    </div>
                </div>

                @if($ultimosArquivos->count())
                    <div class="psi-file-list">
                        @foreach($ultimosArquivos as $arquivo)
                            <div class="psi-file-item">
                                <div class="psi-file-main">
                                    <i class="bi bi-file-earmark-text"></i>
                                    <span>{{ $arquivo->nome }}</span>
                                </div>
                                <a href="{{ $arquivo->url }}" target="_blank" class="btn btn-sm btn-outline-primary">Abrir</a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="psi-empty-state">
                        <i class="bi bi-folder2-open"></i>
                        <p>Nenhum arquivo recente encontrado.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- MODAL PENDÊNCIAS --}}
    <div class="modal fade" id="modalPendencias" tabindex="-1" aria-labelledby="modalPendenciasLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalPendenciasLabel">
                        <i class="bi bi-exclamation-circle"></i> Pendências
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <h6 class="text-danger fw-bold"><i class="bi bi-currency-dollar"></i> Sessões não pagas</h6>
                    @if($pendenciasFinanceiras->count())
                        <ul class="list-group mb-4">
                            @foreach($pendenciasFinanceiras as $pendencia)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $pendencia->paciente->nome ?? 'Paciente removido' }}</strong><br>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($pendencia->data_hora)->format('d/m/Y H:i') }}</small>
                                    </div>
                                    <span class="badge bg-danger">
                                        {{ $simbolo }}
                                        {{ number_format($pendencia->valor_convertido ?? $pendencia->valor, 2, ',', '.') }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Nenhuma pendência financeira encontrada.</p>
                    @endif

                    <h6 class="text-warning fw-bold"><i class="bi bi-journal-text"></i> Sessões sem evolução</h6>
                    @if($pendenciasEvolucao->count())
                        <ul class="list-group">
                            @foreach($pendenciasEvolucao as $pendencia)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $pendencia->paciente->nome ?? 'Paciente removido' }}</strong><br>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($pendencia->data_hora)->format('d/m/Y H:i') }}</small>
                                    </div>
                                    <a href="{{ route('evolucoes.create', ['paciente' => $pendencia->paciente_id, 'sessao_id' => $pendencia->id]) }}" class="btn btn-outline-primary btn-sm">
                                        Criar Evolução
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Nenhuma pendência de evolução encontrada.</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .psi-dashboard {
        padding: 4px 2px 24px;
    }

    .psi-alert {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
    }

    .psi-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        padding: 28px 30px;
        border-radius: 28px;
        background:
            radial-gradient(circle at top left, rgba(59,130,246,0.16), transparent 34%),
            linear-gradient(135deg, #ffffff 0%, #f7faff 100%);
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
        border: 1px solid rgba(255,255,255,0.85);
        margin-top: 8px;
    }

    .psi-hero-kicker {
        display: inline-block;
        font-size: .82rem;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #3b82f6;
        margin-bottom: 10px;
    }

    .psi-hero h1 {
        font-size: 2.3rem;
        line-height: 1.05;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 10px;
    }

    .psi-hero p {
        font-size: 1rem;
        color: #64748b;
        margin: 0;
        max-width: 620px;
    }

    .psi-hero-btn {
        border-radius: 18px;
        padding: 14px 22px;
        font-weight: 700;
        box-shadow: 0 16px 30px rgba(22, 163, 74, 0.18);
    }

    .psi-stat-card,
    .psi-panel {
        background: rgba(255, 255, 255, 0.88);
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 24px;
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.07);
        backdrop-filter: blur(10px);
    }

    .psi-stat-card {
        padding: 22px;
        display: flex;
        align-items: center;
        gap: 16px;
        min-height: 118px;
    }

    .psi-stat-icon {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.45rem;
        flex-shrink: 0;
    }

    .psi-stat-content {
        display: flex;
        flex-direction: column;
        line-height: 1.1;
    }

    .psi-stat-label {
        color: #64748b;
        font-size: .92rem;
        margin-bottom: 6px;
    }

    .psi-stat-content strong {
        font-size: 2rem;
        color: #0f172a;
        font-weight: 800;
    }

    .psi-stat-content small {
        color: #94a3b8;
        font-size: .86rem;
    }

    .psi-panel {
        padding: 22px;
        height: 100%;
    }

    .psi-panel-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 18px;
    }

    .psi-panel-header h3 {
        margin: 0;
        font-size: 1.55rem;
        font-weight: 800;
        color: #0f172a;
    }

    .psi-panel-header span {
        color: #94a3b8;
        font-size: .9rem;
    }

    .psi-quick-actions {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .psi-action-btn {
        min-height: 70px;
        border-radius: 18px;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        text-decoration: none;
        font-weight: 700;
        font-size: 1rem;
        transition: .25s ease;
        border: 1px solid transparent;
    }

    .psi-action-btn:hover {
        transform: translateY(-2px);
    }

    .psi-action-btn i {
        font-size: 1.1rem;
    }

    .psi-action-primary {
        background: linear-gradient(135deg, #2563eb, #3b82f6);
        color: #fff;
        box-shadow: 0 16px 26px rgba(37, 99, 235, 0.22);
    }

    .psi-action-purple {
        background: linear-gradient(135deg, #8b5cf6, #a78bfa);
        color: #fff;
        box-shadow: 0 16px 26px rgba(139, 92, 246, 0.18);
    }

    .psi-action-teal {
        background: linear-gradient(135deg, #14b8a6, #2dd4bf);
        color: #fff;
        box-shadow: 0 16px 26px rgba(20, 184, 166, 0.18);
    }

    .psi-action-light {
        background: #f8fafc;
        color: #475569;
        border-color: #e2e8f0;
    }

    .psi-filter-form .form-control,
    .psi-filter-form .form-select {
        min-height: 46px;
        border-radius: 14px;
        border-color: #dbe3ee;
        box-shadow: none;
    }

    .psi-export-actions {
        display: flex;
        gap: 10px;
        margin-top: 14px;
    }

    .psi-export-actions .btn {
        flex: 1;
        border-radius: 14px;
        min-height: 44px;
        font-weight: 700;
    }

    .psi-agenda-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .psi-agenda-item {
        display: grid;
        grid-template-columns: 78px 1fr auto;
        gap: 12px;
        align-items: center;
        padding: 16px;
        border-radius: 18px;
        background: #f8fbff;
        border: 1px solid #e7eef8;
    }

    .psi-agenda-time {
        font-size: 1.55rem;
        font-weight: 800;
        color: #334155;
    }

    .psi-agenda-main strong {
        display: block;
        color: #0f172a;
        font-size: 1.02rem;
        margin-bottom: 3px;
    }

    .psi-agenda-main small {
        color: #94a3b8;
    }

    .psi-link-more {
        text-decoration: none;
        color: #2563eb;
        font-weight: 700;
    }

    .psi-finance-panel {
        padding-bottom: 18px;
    }

    .psi-finance-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 18px;
    }

    .psi-finance-item {
        display: grid;
        grid-template-columns: 1fr auto;
        align-items: center;
        gap: 14px;
        padding: 16px 18px;
        border-radius: 16px;
        background: #f8fbff;
        border: 1px solid #e8eef8;
    }

    .psi-finance-item span {
        color: #64748b;
        font-weight: 700;
        font-size: 1rem;
    }

    .psi-finance-item strong {
        color: #0f172a;
        font-size: 1.05rem;
        font-weight: 800;
        text-align: right;
        white-space: nowrap;
    }

    .psi-chart-mini {
        margin-top: 6px;
    }

    .psi-chart-mini h4 {
        font-size: 0.98rem;
        font-weight: 800;
        margin-bottom: 12px;
        color: #0f172a;
    }

    .psi-finance-chart-wrap {
        height: 180px;
        min-height: 180px;
        max-height: 180px;
        background: #fff;
        border-radius: 18px;
        padding: 12px;
        border: 1px solid #e8eef8;
        overflow: hidden;
    }

    .psi-finance-chart-wrap canvas {
        width: 100% !important;
        height: 100% !important;
        background: transparent;
        border-radius: 0;
        padding: 0;
    }

    .psi-alert-list,
    .psi-insights-list,
    .psi-file-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .psi-alert-item,
    .psi-insight-item,
    .psi-file-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 16px;
        background: #f8fbff;
        border: 1px solid #e8eef8;
        color: #334155;
    }

    .psi-alert-item span,
    .psi-insight-item span {
        font-weight: 500;
    }

    .psi-file-item {
        justify-content: space-between;
    }

    .psi-file-main {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .psi-file-main span {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .psi-chart-box {
        min-height: 280px;
    }

    .psi-empty-state {
        padding: 26px 20px;
        border-radius: 18px;
        background: #f8fbff;
        border: 1px dashed #d7e3f1;
        text-align: center;
        color: #94a3b8;
    }

    .psi-empty-state i {
        font-size: 2rem;
        display: block;
        margin-bottom: 8px;
    }

    .psi-empty-state p {
        margin: 0;
    }

    .psi-dashboard canvas {
        background: #fff;
        border-radius: 18px;
        padding: 10px;
    }

    @media (max-width: 1199.98px) {
        .psi-quick-actions {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .psi-finance-chart-wrap {
            height: 220px;
            min-height: 220px;
            max-height: 220px;
        }
    }

    @media (max-width: 991.98px) {
        .psi-hero {
            flex-direction: column;
            align-items: flex-start;
        }

        .psi-hero h1 {
            font-size: 2rem;
        }
    }

    @media (max-width: 767.98px) {
        .psi-dashboard {
            padding-top: 0;
        }

        .psi-hero {
            padding: 22px 18px;
            border-radius: 22px;
        }

        .psi-hero h1 {
            font-size: 1.75rem;
        }

        .psi-panel,
        .psi-stat-card {
            border-radius: 20px;
        }

        .psi-quick-actions {
            grid-template-columns: 1fr;
        }

        .psi-agenda-item {
            grid-template-columns: 1fr;
            align-items: flex-start;
        }

        .psi-agenda-time {
            font-size: 1.2rem;
        }

        .psi-export-actions {
            flex-direction: column;
        }

        .psi-finance-item {
            grid-template-columns: 1fr;
            align-items: flex-start;
        }

        .psi-finance-item strong {
            text-align: left;
        }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const meses = {!! json_encode($sessaoPorMes->pluck('mes')) !!};
    const totalSessoes = {!! json_encode($sessaoPorMes->pluck('total')) !!};

    const valoresDiasLabels = {!! json_encode($valoresPorDia->keys()) !!};
    const valoresDiasValores = {!! json_encode($valoresDiasConvertidos ?? $valoresDiasValores ?? []) !!};

    function buildGradient(ctx, area, colorTop, colorBottom) {
        const gradient = ctx.createLinearGradient(0, area.top, 0, area.bottom);
        gradient.addColorStop(0, colorTop);
        gradient.addColorStop(1, colorBottom);
        return gradient;
    }

    const sessoesCtx = document.getElementById('graficoSessoes').getContext('2d');
    const valoresCtx = document.getElementById('graficoValores').getContext('2d');

    new Chart(sessoesCtx, {
        type: 'bar',
        data: {
            labels: meses,
            datasets: [{
                label: 'Sessões',
                data: totalSessoes,
                borderRadius: 12,
                borderSkipped: false,
                backgroundColor: (context) => {
                    const { chart } = context;
                    const { ctx, chartArea } = chart;
                    if (!chartArea) return '#60a5fa';
                    return buildGradient(ctx, chartArea, '#3b82f6', '#bfdbfe');
                }
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        usePointStyle: true,
                        boxWidth: 8,
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#64748b' }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(148, 163, 184, 0.15)' },
                    ticks: { color: '#64748b' }
                }
            }
        }
    });

    new Chart(valoresCtx, {
        type: 'line',
        data: {
            labels: valoresDiasLabels,
            datasets: [{
                label: 'Valor recebido ({{ $moedaSelecionada }})',
                data: valoresDiasValores,
                fill: true,
                tension: 0.35,
                borderColor: '#22c55e',
                backgroundColor: (context) => {
                    const { chart } = context;
                    const { ctx, chartArea } = chart;
                    if (!chartArea) return 'rgba(34, 197, 94, 0.18)';
                    return buildGradient(ctx, chartArea, 'rgba(34,197,94,0.28)', 'rgba(34,197,94,0.03)');
                },
                pointBackgroundColor: '#22c55e',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        usePointStyle: true,
                        boxWidth: 8,
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        color: '#64748b',
                        autoSkip: true,
                        maxRotation: 0
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(148, 163, 184, 0.15)' },
                    ticks: { color: '#64748b' }
                }
            }
        }
    });
</script>
@endsection
