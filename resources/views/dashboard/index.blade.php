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

    <div class="psi-bento-grid">
        
        <div class="bento-item bento-hero">
            <div class="psi-hero-left">
                <span class="psi-hero-kicker">Painel principal</span>
                <h1>Olá, {{ $primeiroNome }}</h1>
                <p>Visão clara da sua agenda, pendências e financeiro em um só lugar.</p>
            </div>
            <div class="psi-hero-right">
                <a href="{{ route('agenda') }}" class="btn btn-success psi-hero-btn">
                    <i class="bi bi-calendar-week me-2"></i> Ver agenda do dia
                </a>
            </div>
        </div>

        <div class="bento-item bento-stat">
            <div class="psi-stat-icon bg-primary-subtle text-primary">
                <i class="bi bi-calendar-check"></i>
            </div>
            <div class="psi-stat-content">
                <span class="psi-stat-label">Sessões agendadas</span>
                <strong>{{ $sessoesHoje }}</strong>
                <small>para hoje</small>
            </div>
        </div>

        <div class="bento-item bento-stat">
            <div class="psi-stat-icon bg-success-subtle text-success">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="psi-stat-content">
                <span class="psi-stat-label">{{ $simbolo }} recebido</span>
                <strong>{{ number_format($totalRecebidoPeriodo, 2, ',', '.') }}</strong>
                <small>no período</small>
            </div>
        </div>

        <div class="bento-item bento-stat">
            <div class="psi-stat-icon bg-warning-subtle text-warning">
                <i class="bi bi-exclamation-circle"></i>
            </div>
            <div class="psi-stat-content">
                <span class="psi-stat-label">Sem confirmação</span>
                <strong>{{ $pendenciasSemConfirmacao }}</strong>
                <small>pacientes</small>
            </div>
        </div>

        <div class="bento-item bento-stat">
            <div class="psi-stat-icon bg-danger-subtle text-danger">
                <i class="bi bi-shield-exclamation"></i>
            </div>
            <div class="psi-stat-content">
                <span class="psi-stat-label">Pendências</span>
                <strong>{{ $pendenciasTotal }}</strong>
                <small>exigem atenção</small>
            </div>
        </div>

        <div class="bento-item bento-actions">
            <div class="bento-header">
                <h3>Ações rápidas</h3>
                <span>atalhos do dia</span>
            </div>
            <div class="bento-quick-actions">
                <a href="{{ route('sessoes.create') }}" class="psi-action-btn psi-action-primary">
                    <i class="bi bi-plus-lg"></i>
                    <span>Sessão</span>
                </a>
                <a href="{{ route('evolucoes.create') }}" class="psi-action-btn psi-action-purple">
                    <i class="bi bi-journal-plus"></i>
                    <span>Evolução</span>
                </a>
                <a href="{{ route('pacientes.create') }}" class="psi-action-btn psi-action-teal">
                    <i class="bi bi-person-plus"></i>
                    <span>Paciente</span>
                </a>
                <a href="{{ route('agenda') }}" class="psi-action-btn psi-action-light">
                    <i class="bi bi-calendar3"></i>
                    <span>Agenda</span>
                </a>
            </div>
        </div>

        <div class="bento-item bento-insights">
            <div class="bento-header">
                <h3>Insights</h3>
                <span>resumo analítico</span>
            </div>
            <div class="psi-inline-insights">
                <div class="psi-inline-insight-card">
                    <div class="psi-inline-insight-icon text-success">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div class="psi-inline-insight-text">
                        <span class="psi-inline-insight-label">Faturamento</span>
                        <strong>
                            @if(!is_null($insightCrescimento))
                                Mudou {{ $insightCrescimento }}% no período
                            @else
                                Atualizado pelo período
                            @endif
                        </strong>
                    </div>
                </div>
                <div class="psi-inline-insight-card">
                    <div class="psi-inline-insight-icon text-primary">
                        <i class="bi bi-clipboard-pulse"></i>
                    </div>
                    <div class="psi-inline-insight-text">
                        <span class="psi-inline-insight-label">Evoluções</span>
                        <strong>{{ $sessoesSemEvolucao }} pendentes</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="bento-item bento-filters">
            <div class="bento-header">
                <h3>Período</h3>
                <span>{{ $dataInicial->format('d/m') }} a {{ $dataFinal->format('d/m/Y') }}</span>
            </div>
            <form method="GET" class="psi-filter-form">
                <div class="mb-2">
                    <select name="moeda" class="form-select form-select-sm" onchange="this.form.submit()">
                        @php $moedas = ['BRL','USD','EUR','GBP','ARS','CLP','MXN','CAD','AUD']; @endphp
                        @foreach($moedas as $m)
                            <option value="{{ $m }}" {{ $moedaSelecionada === $m ? 'selected' : '' }}>Moeda: {{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2">
                    <select name="periodo" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Período rápido...</option>
                        <option value="7" {{ request('periodo') == '7' ? 'selected' : '' }}>Últimos 7 dias</option>
                        <option value="15" {{ request('periodo') == '15' ? 'selected' : '' }}>Últimos 15 dias</option>
                        <option value="30" {{ request('periodo') == '30' ? 'selected' : '' }}>Últimos 30 dias</option>
                    </select>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6"><input type="date" name="de" value="{{ request('de') }}" class="form-control form-control-sm"></div>
                    <div class="col-6"><input type="date" name="ate" value="{{ request('ate') }}" class="form-control form-control-sm"></div>
                </div>
                @foreach(request()->except(['moeda', 'de', 'ate', 'periodo']) as $campo => $valor)
                    <input type="hidden" name="{{ $campo }}" value="{{ $valor }}">
                @endforeach
                <div class="psi-export-actions">
                    <button type="submit" class="btn btn-sm btn-outline-primary flex-grow-1"><i class="bi bi-search"></i> Aplicar</button>
                    <a href="{{ route('dashboard.pdf', request()->all()) }}" class="btn btn-sm btn-danger no-spinner-on-download"><i class="bi bi-file-earmark-pdf"></i></a>
                    <a href="{{ route('dashboard.excel', request()->all()) }}" class="btn btn-sm btn-success no-spinner-on-download"><i class="bi bi-file-earmark-excel"></i></a>
                </div>
            </form>
        </div>

        <div class="bento-item bento-agenda">
            <div class="bento-header">
                <h3>Agenda do dia</h3>
                <span>próximos atendimentos</span>
            </div>
            @if(isset($agendaHoje) && $agendaHoje->count())
                <div class="psi-agenda-list">
                    @foreach($agendaHoje->take(4) as $sessao)
                        <div class="psi-agenda-item">
                            <div class="psi-agenda-time">{{ \Carbon\Carbon::parse($sessao->data_hora)->format('H:i') }}</div>
                            <div class="psi-agenda-main">
                                <strong>{{ $sessao->paciente->nome ?? 'Removido' }}</strong>
                            </div>
                            <div class="psi-agenda-status">
                                <span class="badge bg-{{ $sessao->foi_pago ? 'success' : 'warning text-dark' }}">
                                    {{ $sessao->foi_pago ? 'Pago' : 'Pendente' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('agenda') }}" class="psi-link-more mt-auto pt-3">Ver agenda completa <i class="bi bi-arrow-right-short"></i></a>
            @else
                <div class="psi-empty-state psi-empty-state-compact h-100">
                    <i class="bi bi-calendar-x"></i>
                    <p>Nenhuma sessão para hoje.</p>
                </div>
            @endif
        </div>

        <div class="bento-item bento-finance">
            <div class="bento-header">
                <h3>Financeiro</h3>
                <span>resumo do período</span>
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
            <div class="psi-chart-mini mt-auto">
                <div class="psi-finance-chart-wrap">
                    <canvas id="graficoValores"></canvas>
                </div>
            </div>
        </div>

        <div class="bento-item bento-alerts">
            <div class="bento-header">
                <h3>Atenção</h3>
                <span>exigem ação</span>
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
                    <span>{{ $sessoesSemPagamento }} sem pagamento</span>
                </div>
            </div>
            @if($pendenciasTotal > 0)
                <button class="btn btn-outline-danger btn-sm mt-auto w-100" data-bs-toggle="modal" data-bs-target="#modalPendencias">
                    Resolver pendências
                </button>
            @endif
        </div>

        <div class="bento-item bento-files">
            <div class="bento-header">
                <h3>Arquivos</h3>
                <span>recentes</span>
            </div>
            @if($ultimosArquivos->count())
                <div class="psi-file-list">
                    @foreach($ultimosArquivos->take(3) as $arquivo)
                        <div class="psi-file-item">
                            <div class="psi-file-main">
                                <i class="bi bi-file-earmark-text text-primary"></i>
                                <span>{{ $arquivo->nome }}</span>
                            </div>
                            <a href="{{ $arquivo->url }}" target="_blank" class="btn btn-sm btn-light"><i class="bi bi-box-arrow-up-right"></i></a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="psi-empty-state psi-empty-state-compact h-100">
                    <i class="bi bi-folder2-open"></i>
                    <p>Nenhum arquivo recente.</p>
                </div>
            @endif
        </div>

        <div class="bento-item bento-chart">
            <div class="bento-header">
                <h3>Sessões por mês</h3>
                <span>volume de atendimentos anuais</span>
            </div>
            <div class="psi-chart-box">
                <canvas id="graficoSessoes"></canvas>
            </div>
        </div>

    </div>
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
    /* =========================================
       CONFIGURAÇÕES GERAIS (MOBILE POR PADRÃO)
       ========================================= */
    .psi-dashboard {
        padding: 4px 2px 24px;
    }

    .psi-alert {
        border: 0;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        margin-bottom: 24px;
    }

    /* Grid principal em 1 coluna para celular */
    .psi-bento-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .bento-item {
        background: rgba(255, 255, 255, 0.88);
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
        backdrop-filter: blur(10px);
        padding: 20px;
        display: flex;
        flex-direction: column;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        overflow: hidden;
    }

    .bento-item:hover {
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
        transform: translateY(-2px);
    }

    .bento-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 16px;
        gap: 10px;
    }

    .bento-header h3 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }

    .bento-header span {
        color: #94a3b8;
        font-size: .85rem;
        white-space: nowrap;
    }

    /* =========================================
       COMPONENTES INTERNOS (MOBILE)
       ========================================= */
    
    /* Hero */
    .bento-hero {
        background: radial-gradient(circle at top left, rgba(59,130,246,0.16), transparent 34%), linear-gradient(135deg, #ffffff 0%, #f7faff 100%);
        align-items: flex-start;
        gap: 16px;
    }
    .psi-hero-kicker { display: inline-block; font-size: .82rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #3b82f6; margin-bottom: 8px; }
    .psi-hero-left h1 { font-size: 1.8rem; font-weight: 800; color: #0f172a; margin: 0 0 8px; }
    .psi-hero-left p { margin: 0; color: #64748b; font-size: 0.95rem; }
    .psi-hero-btn { border-radius: 16px; padding: 12px 20px; font-weight: 700; box-shadow: 0 12px 24px rgba(22, 163, 74, 0.18); width: 100%; text-align: center; }

    /* Stats */
    .bento-stat { flex-direction: row; align-items: center; gap: 16px; }
    .psi-stat-icon { width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0; }
    .psi-stat-content { display: flex; flex-direction: column; line-height: 1.1; }
    .psi-stat-label { color: #64748b; font-size: .85rem; margin-bottom: 4px; }
    .psi-stat-content strong { font-size: 1.5rem; color: #0f172a; font-weight: 800; }
    .psi-stat-content small { color: #94a3b8; font-size: .8rem; }

    /* Ações Rápidas */
    .bento-quick-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; height: 100%; }
    .psi-action-btn { border-radius: 16px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; text-decoration: none; font-weight: 700; font-size: .95rem; transition: .2s ease; padding: 16px 10px; }
    .psi-action-btn:hover { transform: scale(1.03); }
    .psi-action-btn i { font-size: 1.4rem; }
    .psi-action-primary { background: linear-gradient(135deg, #2563eb, #3b82f6); color: #fff; }
    .psi-action-purple { background: linear-gradient(135deg, #8b5cf6, #a78bfa); color: #fff; }
    .psi-action-teal { background: linear-gradient(135deg, #14b8a6, #2dd4bf); color: #fff; }
    .psi-action-light { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }

    /* Insights */
    .psi-inline-insights { display: flex; flex-direction: column; gap: 12px; height: 100%; justify-content: center; }
    .psi-inline-insight-card { display: flex; align-items: center; gap: 14px; padding: 16px; border-radius: 16px; background: #f8fbff; border: 1px solid #e8eef8; }
    .psi-inline-insight-icon { font-size: 1.4rem; }
    .psi-inline-insight-text { display: flex; flex-direction: column; gap: 2px; }
    .psi-inline-insight-label { font-size: .75rem; font-weight: 700; text-transform: uppercase; color: #94a3b8; }
    .psi-inline-insight-text strong { font-size: .95rem; color: #334155; font-weight: 700; }

    /* Filtros */
    .psi-filter-form { display: flex; flex-direction: column; height: 100%; }
    .psi-filter-form .form-control, .psi-filter-form .form-select { border-radius: 12px; box-shadow: none; border-color: #dbe3ee;}
    .psi-export-actions { display: flex; gap: 8px; margin-top: auto; flex-direction: column; }
    .psi-export-actions .btn { border-radius: 12px; font-weight: 700; width: 100%; }

    /* Agenda */
    .psi-agenda-list { display: flex; flex-direction: column; gap: 10px; }
    .psi-agenda-item { display: grid; grid-template-columns: 1fr; align-items: flex-start; gap: 8px; padding: 14px; border-radius: 16px; background: #f8fbff; border: 1px solid #e7eef8; }
    .psi-agenda-time { font-size: 1.2rem; font-weight: 800; color: #334155; }
    .psi-agenda-main strong { color: #0f172a; font-size: .95rem; }
    .psi-link-more { text-decoration: none; color: #2563eb; font-weight: 700; font-size: .95rem; text-align: center; display: block; margin-top: 16px;}

    /* Financeiro */
    .psi-finance-list { display: flex; flex-direction: column; gap: 10px; }
    .psi-finance-item { display: flex; flex-direction: column; align-items: flex-start; padding: 14px; border-radius: 14px; background: #f8fbff; border: 1px solid #e8eef8; gap: 4px;}
    .psi-finance-item span { color: #64748b; font-weight: 600; font-size: .9rem; }
    .psi-finance-item strong { color: #0f172a; font-size: 1.05rem; font-weight: 800; text-align: left;}
    .psi-finance-chart-wrap { height: 160px; background: #fff; border-radius: 16px; padding: 10px; border: 1px solid #e8eef8; margin-top: 16px;}

    /* Alertas e Arquivos */
    .psi-alert-list, .psi-file-list { display: flex; flex-direction: column; gap: 10px; }
    .psi-alert-item { display: flex; align-items: center; gap: 12px; padding: 14px; border-radius: 14px; background: #fff5f5; border: 1px solid #ffe3e3; font-weight: 600; font-size: .95rem; }
    .psi-file-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; border-radius: 14px; background: #f8fbff; border: 1px solid #e8eef8; font-weight: 600; }
    .psi-file-main { display: flex; align-items: center; gap: 10px; overflow: hidden; }
    .psi-file-main span { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: .95rem; }

    /* Empty States & Gráficos */
    .psi-chart-box { min-height: 220px; height: 100%; }
    .psi-empty-state { border-radius: 16px; background: #f8fbff; border: 1px dashed #d7e3f1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8; text-align: center; min-height: 120px; }
    .psi-empty-state i { font-size: 2rem; margin-bottom: 8px; }
    .psi-empty-state p { margin: 0; font-weight: 500;}


    /* =========================================
       TABLETS E TELAS MÉDIAS (>= 768px)
       ========================================= */
    @media (min-width: 768px) {
        .psi-bento-grid {
            grid-template-columns: repeat(12, 1fr);
            gap: 20px;
        }

        .bento-item {
            padding: 24px;
            border-radius: 24px;
        }

        /* Hero passa a ocupar a linha toda e flex row */
        .bento-hero {
            grid-column: span 12;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }
        .psi-hero-left h1 { font-size: 2.3rem; }
        .psi-hero-btn { width: auto; }

        /* Stats dividem o espaço em 2 por linha */
        .bento-stat { grid-column: span 6; }
        
        /* Outros painéis pegam meia tela ou tela inteira dependendo da necessidade */
        .bento-actions, .bento-insights { grid-column: span 6; }
        .bento-filters, .bento-agenda, .bento-finance, .bento-alerts, .bento-files { grid-column: span 12; }
        .bento-chart { grid-column: span 12; }
        
        .psi-export-actions { flex-direction: row; }
        .psi-finance-item { flex-direction: row; justify-content: space-between; align-items: center; }
        .psi-finance-item strong { text-align: right; }
        .psi-agenda-item { grid-template-columns: 60px 1fr auto; align-items: center; }
    }


    /* =========================================
       DESKTOP E TELAS GRANDES (>= 1200px)
       ========================================= */
    @media (min-width: 1200px) {
        
        /* Montagem do Bento Box avançado */
        .bento-stat { grid-column: span 3; }
        
        .bento-actions { grid-column: span 4; }
        .bento-insights { grid-column: span 4; }
        .bento-filters { grid-column: span 4; }
        
        .bento-agenda { grid-column: span 4; grid-row: span 2; }
        .bento-finance { grid-column: span 4; grid-row: span 2; }
        
        .bento-alerts { grid-column: span 4; }
        .bento-files { grid-column: span 4; }
        
        .bento-chart { grid-column: span 12; }

        /* Ajustes finos para desktop */
        .psi-stat-icon { width: 54px; height: 54px; font-size: 1.4rem; }
        .psi-stat-content strong { font-size: 1.7rem; }
        .psi-chart-box { min-height: 250px; }
        .psi-finance-chart-wrap { height: 140px; margin-top: auto; }
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
                legend: { display: false }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { display: false }
                },
                y: {
                    display: false,
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection