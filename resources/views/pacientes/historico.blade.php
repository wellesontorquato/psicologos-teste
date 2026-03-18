@extends('layouts.app')

@section('title', 'Histórico do Paciente | PsiGestor')

@section('content')
<div class="container">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 py-3 mb-4 border-bottom">
        <h2 class="mb-0">
            <i class="bi bi-journal-text me-2 text-primary"></i>
            Histórico de {{ $paciente->nome }}
        </h2>

        <div class="d-flex flex-column flex-sm-row gap-2">
            <button type="button"
                    class="btn btn-primary shadow-sm"
                    id="abrirIndicadoresBtn"
                    data-url="{{ route('pacientes.indicadores', $paciente->id) }}">
                <i class="bi bi-graph-up me-1"></i> Indicadores Clínicos
            </button>

            <a href="{{ route('pacientes.historico.pdf', $paciente->id) }}" target="_blank" class="btn btn-danger shadow-sm">
                <i class="bi bi-file-earmark-pdf me-1"></i> Exportar PDF
            </a>
        </div>
    </div>

    @if ($eventosAgrupados->isEmpty())
        <div class="alert alert-warning">
            Nenhuma sessão, medicação ou evolução registrada para este paciente.
        </div>
    @else
        @foreach ($eventosAgrupados as $index => $eventosPorDia)
            @php
                $data = $index;
                $corFundo = $loop->even ? 'bg-light' : 'bg-white';
                $borda = 'border border-primary-subtle';
            @endphp

            <div class="{{ $corFundo }} p-3 rounded shadow-sm mb-4 {{ $borda }}">
                <h5 class="fw-bold text-primary mb-3">
                    📅
                    @if (\Carbon\Carbon::hasFormat($data, 'Y-m-d'))
                        {{ \Carbon\Carbon::parse($data)->translatedFormat('l, d \d\e F \d\e Y') }}
                    @else
                        Data inválida
                    @endif
                </h5>

                <ul class="timeline">
                    @foreach ($eventosPorDia as $evento)
                        @php
                            $isMedicacao = str_starts_with($evento['descricao'], 'Medicação registrada:') || str_starts_with($evento['descricao'], 'Medicação Inicial:');
                            $isInicial = str_starts_with($evento['descricao'], 'Medicação Inicial:');
                            $status = \Illuminate\Support\Str::upper(trim($evento['status_confirmacao'] ?? ''));
                            $isSessaoConfirmada = $evento['tipo'] === 'Sessão' && $status === 'CONFIRMADA';
                        @endphp

                        @if ($evento['tipo'] === 'Sessão' && !$isSessaoConfirmada)
                            @continue
                        @endif

                        <li class="timeline-item position-relative mb-4 ps-4 border-start
                            {{ $evento['tipo'] === 'Sessão' ? 'border-success' : ($isMedicacao ? 'border-danger' : 'border-primary') }}">
                            <span class="position-absolute top-0 start-0 translate-middle p-2 rounded-circle border border-light
                                {{ $evento['tipo'] === 'Sessão' ? 'bg-success' : ($isMedicacao ? 'bg-danger' : 'bg-primary') }}">
                            </span>

                            <h6 class="fw-bold mb-1">
                                @if ($evento['tipo'] === 'Sessão')
                                    🧘 Sessão
                                @elseif ($isMedicacao)
                                    💊 {{ $isInicial ? 'Medicação Inicial' : 'Nova Medicação' }}
                                @else
                                    📄 Evolução (Lembretes para próxima sessão)
                                @endif
                            </h6>

                            @if($evento['hora'])
                                <p class="text-muted mb-1">{{ $evento['hora'] }}</p>
                            @endif

                            <div>
                                {!! $evento['descricao'] !!}
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach

        <div class="mt-4 d-flex justify-content-center">
            {{ $eventos->links() }}
        </div>
    @endif

    <a href="{{ route('pacientes.index') }}" class="btn btn-secondary mt-4">← Voltar</a>
</div>

{{-- Modal de Indicadores Clínicos --}}
<div class="modal fade" id="indicadoresModal" tabindex="-1" aria-labelledby="indicadoresModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down modal-xl modal-dialog-centered modal-dialog-scrollable indicadores-modal-dialog">
        <div class="modal-content border-0 shadow-lg indicadores-modal-content">
            <div class="modal-header indicadores-modal-header">
                <div class="me-3">
                    <h5 class="modal-title fw-bold mb-1" id="indicadoresModalLabel">Indicadores Clínicos</h5>
                    <small class="indicadores-subtitle">Análise visual e evolução emocional de {{ $paciente->nome }}</small>
                </div>
                <button type="button" class="btn-close btn-close-white btn-close-premium" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <div class="modal-body indicadores-modal-body">
                <div id="indicadoresLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-3 mb-0 text-muted">Carregando indicadores...</p>
                </div>

                <div id="indicadoresConteudo" style="display: none;">
                    {{-- Resumo automático --}}
                    <div class="premium-summary-box mb-4">
                        <div class="d-flex align-items-start gap-3">
                            <div class="premium-summary-icon">
                                <i class="bi bi-clipboard2-pulse"></i>
                            </div>
                            <div>
                                <div class="premium-summary-title">Resumo clínico visual</div>
                                <div class="premium-summary-text" id="resumoAutomatico">—</div>
                            </div>
                        </div>
                    </div>

                    {{-- Cards principais --}}
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="card border-0 shadow-sm h-100 indicador-card premium-card">
                                <div class="card-body">
                                    <div class="indicador-label">Estado atual</div>
                                    <div class="indicador-valor" id="cardUltimoEstado">—</div>
                                    <div class="indicador-meta" id="cardEstadoPredominante">Predomínio: —</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="card border-0 shadow-sm h-100 indicador-card premium-card">
                                <div class="card-body">
                                    <div class="indicador-label">Tendência recente</div>
                                    <div class="indicador-valor" id="cardTendencia">—</div>
                                    <div class="indicador-meta" id="cardOscilacao">Oscilação: —</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="card border-0 shadow-sm h-100 indicador-card premium-card">
                                <div class="card-body">
                                    <div class="indicador-label">Média de intensidade</div>
                                    <div class="indicador-valor" id="cardMediaIntensidade">—</div>
                                    <div class="indicador-meta" id="cardDistribuicaoAlerta">Alertas: —</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="card border-0 shadow-sm h-100 indicador-card premium-card">
                                <div class="card-body">
                                    <div class="indicador-label">Pico registrado</div>
                                    <div class="indicador-valor" id="cardPico">—</div>
                                    <div class="indicador-meta" id="cardPicoData">Data: —</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Gráfico principal --}}
                    <div class="card border-0 shadow-sm mb-4 premium-panel">
                        <div class="card-body">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                                <div>
                                    <h6 class="fw-bold mb-1">Intensidade emocional ao longo do tempo</h6>
                                    <small class="text-muted">Passe o mouse sobre os pontos para ver intensidade e estado emocional registrado.</small>
                                </div>
                                <div class="premium-badge" id="badgeTotalRegistros">
                                    0 registros
                                </div>
                            </div>
                            <div class="chart-wrap chart-wrap-lg">
                                <canvas id="graficoIntensidade"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- Gráficos secundários --}}
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-lg-6">
                            <div class="card border-0 shadow-sm h-100 premium-panel">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-1">Frequência dos estados emocionais</h6>
                                    <small class="text-muted d-block mb-3">Distribuição dos estados mais recorrentes no período.</small>
                                    <div class="chart-wrap">
                                        <canvas id="graficoEstados"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="card border-0 shadow-sm h-100 premium-panel">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-1">Alertas por evolução</h6>
                                    <small class="text-muted d-block mb-3">Visualize a incidência de atenção clínica ao longo do histórico.</small>
                                    <div class="chart-wrap">
                                        <canvas id="graficoAlertas"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Distribuição + tabela --}}
                    <div class="row g-3">
                        <div class="col-12 col-xl-5">
                            <div class="card border-0 shadow-sm h-100 premium-panel">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-1">Distribuição da intensidade</h6>
                                    <small class="text-muted d-block mb-3">Faixas agrupadas para leitura rápida do nível emocional predominante.</small>
                                    <div class="chart-wrap chart-wrap-doughnut">
                                        <canvas id="graficoDistribuicao"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-xl-7">
                            <div class="card border-0 shadow-sm h-100 premium-panel">
                                <div class="card-body">
                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                                        <div>
                                            <h6 class="fw-bold mb-1">Últimas evoluções com indicador</h6>
                                            <small class="text-muted">Visão resumida das últimas sessões registradas com marcadores clínicos.</small>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle mb-0 indicadores-table">
                                            <thead>
                                                <tr>
                                                    <th>Data</th>
                                                    <th>Estado</th>
                                                    <th>Intensidade</th>
                                                    <th>Alerta</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tabelaHistoricoRecente">
                                                <tr>
                                                    <td colspan="4" class="text-muted text-center py-3">Sem dados</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="indicadoresVazio" class="alert alert-warning mb-0" style="display: none;">
                    Nenhum indicador clínico foi registrado para este paciente até o momento.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.timeline {
    list-style: none;
    padding-left: 0;
    position: relative;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #0d6efd;
}

.timeline-item {
    position: relative;
    margin-left: 20px;
}

.indicadores-modal-dialog {
    max-width: 1380px;
}

.indicadores-modal-content {
    border-radius: 1.5rem;
    overflow: hidden;
    backdrop-filter: blur(12px);
    background: #fff;
}

.indicadores-modal-header {
    padding: 1.15rem 1.15rem;
    border-bottom: 1px solid rgba(255,255,255,.08);
    background:
        radial-gradient(circle at top left, rgba(255,255,255,.12), transparent 28%),
        linear-gradient(135deg, #0d6efd 0%, #2563eb 45%, #4f46e5 100%);
    color: #fff;
}

.indicadores-subtitle {
    color: rgba(255,255,255,.82);
}

.btn-close-premium {
    opacity: .9;
}

.indicadores-modal-body {
    padding: 1rem;
    background:
        linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
}

.premium-summary-box {
    border-radius: 1.25rem;
    padding: 1rem 1rem;
    background: linear-gradient(180deg, rgba(255,255,255,1) 0%, rgba(248,250,252,1) 100%);
    border: 1px solid rgba(15,23,42,.06);
    box-shadow: 0 8px 24px rgba(15,23,42,.04);
}

.premium-summary-icon {
    width: 42px;
    height: 42px;
    min-width: 42px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, rgba(37,99,235,.12), rgba(79,70,229,.16));
    color: #2563eb;
    font-size: 1.1rem;
}

.premium-summary-title {
    font-weight: 700;
    color: #0f172a;
    margin-bottom: .2rem;
}

.premium-summary-text {
    font-size: .92rem;
    color: #64748b;
    line-height: 1.55;
}

.premium-card,
.premium-panel {
    border-radius: 1.15rem;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    box-shadow: 0 10px 28px rgba(15,23,42,.05);
    border: 1px solid rgba(15,23,42,.04);
}

.indicador-card {
    transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
}

.indicador-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 36px rgba(15,23,42,.08);
    border-color: rgba(37,99,235,.12);
}

.indicador-label {
    font-size: .82rem;
    color: #64748b;
    margin-bottom: .42rem;
}

.indicador-valor {
    font-size: 1.4rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
    margin-bottom: .4rem;
    word-break: break-word;
}

.indicador-meta {
    font-size: .82rem;
    color: #64748b;
}

.premium-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: .5rem .85rem;
    border-radius: 999px;
    font-size: .8rem;
    font-weight: 700;
    color: #334155;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid rgba(15,23,42,.08);
    box-shadow: 0 4px 14px rgba(15,23,42,.04);
}

.chart-wrap {
    position: relative;
    width: 100%;
    min-height: 270px;
}

.chart-wrap-lg {
    min-height: 370px;
}

.chart-wrap-doughnut {
    min-height: 320px;
}

.indicadores-table thead th {
    font-size: .82rem;
    color: #64748b;
    font-weight: 700;
    border-bottom-width: 1px;
    white-space: nowrap;
}

.indicadores-table tbody td {
    font-size: .93rem;
    vertical-align: middle;
}

.badge-soft {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .45rem .72rem;
    border-radius: 999px;
    font-size: .78rem;
    font-weight: 700;
    border: 1px solid transparent;
    white-space: nowrap;
}

.badge-soft.estado {
    background: rgba(37,99,235,.08);
    color: #2563eb;
    border-color: rgba(37,99,235,.14);
}

.badge-soft.intensidade {
    background: rgba(16,185,129,.08);
    color: #059669;
    border-color: rgba(16,185,129,.14);
}

.badge-soft.alerta-neutro {
    background: rgba(100,116,139,.1);
    color: #64748b;
    border-color: rgba(100,116,139,.14);
}

.badge-soft.alerta-atencao {
    background: rgba(245,158,11,.16);
    color: #b45309;
    border-color: rgba(245,158,11,.22);
}

.badge-soft.alerta-critico {
    background: rgba(239,68,68,.1);
    color: #dc2626;
    border-color: rgba(239,68,68,.16);
}

@media (max-width: 991.98px) {
    .indicadores-modal-dialog {
        max-width: 97vw;
    }

    .chart-wrap-lg {
        min-height: 320px;
    }
}

@media (max-width: 767.98px) {
    .indicadores-modal-header {
        padding: 1rem;
    }

    .indicadores-modal-body {
        padding: .9rem;
    }

    .indicador-valor {
        font-size: 1.18rem;
    }

    .chart-wrap,
    .chart-wrap-lg,
    .chart-wrap-doughnut {
        min-height: 250px;
    }

    .table-responsive {
        font-size: .88rem;
    }
}

@media (max-width: 575.98px) {
    .container {
        padding-left: .85rem;
        padding-right: .85rem;
    }

    .chart-wrap,
    .chart-wrap-lg,
    .chart-wrap-doughnut {
        min-height: 230px;
    }

    .indicadores-modal-content {
        border-radius: 0;
    }

    .premium-summary-box {
        padding: .9rem;
    }
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const abrirBtn = document.getElementById('abrirIndicadoresBtn');
    const modalEl = document.getElementById('indicadoresModal');
    const loadingEl = document.getElementById('indicadoresLoading');
    const conteudoEl = document.getElementById('indicadoresConteudo');
    const vazioEl = document.getElementById('indicadoresVazio');

    const resumoAutomatico = document.getElementById('resumoAutomatico');
    const cardUltimoEstado = document.getElementById('cardUltimoEstado');
    const cardEstadoPredominante = document.getElementById('cardEstadoPredominante');
    const cardTendencia = document.getElementById('cardTendencia');
    const cardOscilacao = document.getElementById('cardOscilacao');
    const cardMediaIntensidade = document.getElementById('cardMediaIntensidade');
    const cardDistribuicaoAlerta = document.getElementById('cardDistribuicaoAlerta');
    const cardPico = document.getElementById('cardPico');
    const cardPicoData = document.getElementById('cardPicoData');
    const badgeTotalRegistros = document.getElementById('badgeTotalRegistros');
    const tabelaHistoricoRecente = document.getElementById('tabelaHistoricoRecente');

    let graficoIntensidade = null;
    let graficoEstados = null;
    let graficoAlertas = null;
    let graficoDistribuicao = null;
    let modalInstance = null;

    function destruirGraficos() {
        if (graficoIntensidade) {
            graficoIntensidade.destroy();
            graficoIntensidade = null;
        }
        if (graficoEstados) {
            graficoEstados.destroy();
            graficoEstados = null;
        }
        if (graficoAlertas) {
            graficoAlertas.destroy();
            graficoAlertas = null;
        }
        if (graficoDistribuicao) {
            graficoDistribuicao.destroy();
            graficoDistribuicao = null;
        }
    }

    function resetarModal() {
        destruirGraficos();

        loadingEl.style.display = 'block';
        conteudoEl.style.display = 'none';
        vazioEl.style.display = 'none';
        vazioEl.className = 'alert alert-warning mb-0';
        vazioEl.textContent = 'Nenhum indicador clínico foi registrado para este paciente até o momento.';

        resumoAutomatico.textContent = '—';
        cardUltimoEstado.textContent = '—';
        cardEstadoPredominante.textContent = 'Predomínio: —';
        cardTendencia.textContent = '—';
        cardOscilacao.textContent = 'Oscilação: —';
        cardMediaIntensidade.textContent = '—';
        cardDistribuicaoAlerta.textContent = 'Alertas: —';
        cardPico.textContent = '—';
        cardPicoData.textContent = 'Data: —';
        badgeTotalRegistros.textContent = '0 registros';

        tabelaHistoricoRecente.innerHTML = `
            <tr>
                <td colspan="4" class="text-muted text-center py-3">Sem dados</td>
            </tr>
        `;
    }

    function formatarEstado(valor) {
        if (!valor) return '—';
        return valor.replaceAll('_', ' ').replace(/\b\w/g, function (l) { return l.toUpperCase(); });
    }

    function formatarTendencia(valor) {
        if (valor === 'melhora') return 'Melhora';
        if (valor === 'piora') return 'Piora';
        return 'Estável';
    }

    function formatarOscilacao(valor) {
        if (valor === null || valor === undefined) return 'Oscilação: —';
        return `Oscilação: ${valor}%`;
    }

    function formatarAlertaBadge(alerta, label) {
        let classe = 'alerta-neutro';

        if (Number(alerta) === 1) classe = 'alerta-atencao';
        if (Number(alerta) === 2) classe = 'alerta-critico';

        return `<span class="badge-soft ${classe}">${label || 'Não informado'}</span>`;
    }

    function formatarEstadoBadge(label) {
        return `<span class="badge-soft estado">${label || 'Não informado'}</span>`;
    }

    function formatarIntensidadeBadge(valor, label) {
        const texto = valor ? `${valor}/5 • ${label}` : (label || 'Não informado');
        return `<span class="badge-soft intensidade">${texto}</span>`;
    }

    function preencherTabelaHistorico(itens) {
        if (!Array.isArray(itens) || !itens.length) {
            tabelaHistoricoRecente.innerHTML = `
                <tr>
                    <td colspan="4" class="text-muted text-center py-3">Sem dados</td>
                </tr>
            `;
            return;
        }

        tabelaHistoricoRecente.innerHTML = itens.map(function (item) {
            return `
                <tr>
                    <td class="fw-medium">${item.data || '—'}</td>
                    <td>${formatarEstadoBadge(item.estado_emocional_label)}</td>
                    <td>${formatarIntensidadeBadge(item.intensidade, item.intensidade_label)}</td>
                    <td>${formatarAlertaBadge(item.alerta, item.alerta_label)}</td>
                </tr>
            `;
        }).join('');
    }

    function criarGraficoIntensidade(labels, dados, estados) {
        const ctx = document.getElementById('graficoIntensidade');

        graficoIntensidade = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Intensidade emocional',
                    data: dados,
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#ffffff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: false,
                        callbacks: {
                            title: function(context) {
                                return `📅 ${context[0].label}`;
                            },
                            label: function(context) {
                                const index = context.dataIndex;
                                const intensidade = context.parsed.y;
                                const estado = Array.isArray(estados) ? estados[index] : null;
                                const estadoFormatado = estado
                                    ? estado.replaceAll('_', ' ').replace(/\b\w/g, function (l) { return l.toUpperCase(); })
                                    : 'Não informado';

                                return [
                                    `💡 Intensidade: ${intensidade}/5`,
                                    `🧠 Estado emocional: ${estadoFormatado}`
                                ];
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        min: 0,
                        max: 5,
                        ticks: {
                            stepSize: 1
                        },
                        grid: {
                            color: 'rgba(15,23,42,0.06)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    function criarGraficoEstados(labels, dados) {
        const ctx = document.getElementById('graficoEstados');

        graficoEstados = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Quantidade',
                    data: dados,
                    borderWidth: 1,
                    borderRadius: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        },
                        grid: {
                            color: 'rgba(15,23,42,0.06)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    function criarGraficoAlertas(labels, dados) {
        const ctx = document.getElementById('graficoAlertas');

        graficoAlertas = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nível de alerta',
                    data: dados,
                    borderWidth: 1,
                    borderRadius: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const valor = context.parsed.y;
                                const label = valor === 2 ? 'Ponto crítico' : (valor === 1 ? 'Atenção' : 'Sem alerta');
                                return ` ${label}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        min: 0,
                        max: 2,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                if (value === 0) return 'Sem alerta';
                                if (value === 1) return 'Atenção';
                                if (value === 2) return 'Crítico';
                                return value;
                            }
                        },
                        grid: {
                            color: 'rgba(15,23,42,0.06)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    function criarGraficoDistribuicao(labels, dados) {
        const ctx = document.getElementById('graficoDistribuicao');

        graficoDistribuicao = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dados,
                    borderWidth: 1,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    async function carregarIndicadores() {
        const url = abrirBtn?.dataset?.url;
        if (!url) return;

        resetarModal();

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data?.message || 'Erro ao carregar indicadores.');
            }

            const labelsIntensidade = data?.graficos?.linha_intensidade?.labels || [];
            const dadosIntensidade = data?.graficos?.linha_intensidade?.data || [];
            const estadosIntensidade = data?.graficos?.linha_intensidade?.estados || [];
            const labelsEstados = data?.graficos?.frequencia_estados?.labels || [];
            const dadosEstados = data?.graficos?.frequencia_estados?.data || [];
            const labelsAlertas = data?.graficos?.barras_alerta?.labels || [];
            const dadosAlertas = data?.graficos?.barras_alerta?.data || [];
            const labelsDistribuicao = data?.graficos?.distribuicao_intensidade?.labels || [];
            const dadosDistribuicao = data?.graficos?.distribuicao_intensidade?.data || [];

            if (!labelsIntensidade.length) {
                loadingEl.style.display = 'none';
                vazioEl.style.display = 'block';
                return;
            }

            const resumo = data?.resumo || {};

            resumoAutomatico.textContent = resumo?.resumo_automatico || 'Sem leitura automática disponível.';
            cardUltimoEstado.textContent = resumo?.ultimo_estado_emocional_label || '—';
            cardEstadoPredominante.textContent = `Predomínio: ${resumo?.estado_predominante_label || '—'}`;
            cardTendencia.textContent = formatarTendencia(resumo?.tendencia_recente);
            cardOscilacao.textContent = formatarOscilacao(resumo?.indice_oscilacao);
            cardMediaIntensidade.textContent = resumo?.media_intensidade !== null && resumo?.media_intensidade !== undefined
                ? `${resumo.media_intensidade}/5`
                : '—';
            cardDistribuicaoAlerta.textContent = `Alertas: ${resumo?.total_alertas ?? 0} (${resumo?.percentual_alertas ?? 0}%)`;
            cardPico.textContent = resumo?.pico_intensidade ? `${resumo.pico_intensidade}/5` : '—';
            cardPicoData.textContent = `Data: ${resumo?.pico_intensidade_data || '—'}`;
            badgeTotalRegistros.textContent = `${resumo?.total_evolucoes_com_indicador ?? 0} registros`;

            preencherTabelaHistorico(data?.historico_recente || []);
            criarGraficoIntensidade(labelsIntensidade, dadosIntensidade, estadosIntensidade);
            criarGraficoEstados(labelsEstados, dadosEstados);
            criarGraficoAlertas(labelsAlertas, dadosAlertas);
            criarGraficoDistribuicao(labelsDistribuicao, dadosDistribuicao);

            loadingEl.style.display = 'none';
            conteudoEl.style.display = 'block';
        } catch (error) {
            loadingEl.style.display = 'none';
            vazioEl.style.display = 'block';
            vazioEl.className = 'alert alert-danger mb-0';
            vazioEl.textContent = error.message || 'Não foi possível carregar os indicadores.';
        }
    }

    if (abrirBtn && modalEl) {
        modalInstance = new bootstrap.Modal(modalEl);

        abrirBtn.addEventListener('click', function () {
            modalInstance.show();
        });

        modalEl.addEventListener('show.bs.modal', function () {
            carregarIndicadores();
        });

        modalEl.addEventListener('hidden.bs.modal', function () {
            destruirGraficos();
        });
    }
});
</script>
@endsection