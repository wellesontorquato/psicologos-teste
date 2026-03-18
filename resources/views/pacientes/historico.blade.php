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
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="indicadoresModalLabel">Indicadores Clínicos</h5>
                    <small class="text-muted">Visualização gráfica do histórico emocional de {{ $paciente->nome }}</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <div class="modal-body">
                <div id="indicadoresLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-3 mb-0 text-muted">Carregando indicadores...</p>
                </div>

                <div id="indicadoresConteudo" style="display: none;">
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card shadow-sm border-0 h-100 indicador-card">
                                <div class="card-body">
                                    <div class="text-muted small mb-1">Último estado emocional</div>
                                    <div class="fs-5 fw-semibold" id="cardUltimoEstado">—</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card shadow-sm border-0 h-100 indicador-card">
                                <div class="card-body">
                                    <div class="text-muted small mb-1">Média de intensidade</div>
                                    <div class="fs-5 fw-semibold" id="cardMediaIntensidade">—</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card shadow-sm border-0 h-100 indicador-card">
                                <div class="card-body">
                                    <div class="text-muted small mb-1">Sessões com alerta</div>
                                    <div class="fs-5 fw-semibold" id="cardTotalAlertas">—</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Intensidade emocional ao longo do tempo</h6>
                            <div class="chart-wrap">
                                <canvas id="graficoIntensidade"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Frequência dos estados emocionais</h6>
                            <div class="chart-wrap">
                                <canvas id="graficoEstados"></canvas>
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

.indicador-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.indicador-card:hover {
    transform: translateY(-2px);
}

.chart-wrap {
    position: relative;
    width: 100%;
    min-height: 320px;
}

@media (max-width: 768px) {
    .chart-wrap {
        min-height: 260px;
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

    const cardUltimoEstado = document.getElementById('cardUltimoEstado');
    const cardMediaIntensidade = document.getElementById('cardMediaIntensidade');
    const cardTotalAlertas = document.getElementById('cardTotalAlertas');

    let graficoIntensidade = null;
    let graficoEstados = null;
    let modalInstance = null;
    let estadoErro = false;

    function destruirGraficos() {
        if (graficoIntensidade) {
            graficoIntensidade.destroy();
            graficoIntensidade = null;
        }

        if (graficoEstados) {
            graficoEstados.destroy();
            graficoEstados = null;
        }
    }

    function resetarModal() {
        estadoErro = false;
        destruirGraficos();

        loadingEl.style.display = 'block';
        conteudoEl.style.display = 'none';
        vazioEl.style.display = 'none';
        vazioEl.className = 'alert alert-warning mb-0';
        vazioEl.textContent = 'Nenhum indicador clínico foi registrado para este paciente até o momento.';

        cardUltimoEstado.textContent = '—';
        cardMediaIntensidade.textContent = '—';
        cardTotalAlertas.textContent = '—';
    }

    function formatarEstado(valor) {
        if (!valor) return '—';

        return valor
            .replaceAll('_', ' ')
            .replace(/\b\w/g, function (l) { return l.toUpperCase(); });
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
            const labelsEstados = data?.graficos?.frequencia_estados?.labels || [];
            const dadosEstados = data?.graficos?.frequencia_estados?.data || [];

            if (!labelsIntensidade.length) {
                loadingEl.style.display = 'none';
                vazioEl.style.display = 'block';
                return;
            }

            cardUltimoEstado.textContent = formatarEstado(data?.resumo?.ultimo_estado_emocional);
            cardMediaIntensidade.textContent = data?.resumo?.media_intensidade !== null && data?.resumo?.media_intensidade !== undefined
                ? `${data.resumo.media_intensidade}/5`
                : '—';
            cardTotalAlertas.textContent = data?.resumo?.total_alertas ?? 0;

            const ctxIntensidade = document.getElementById('graficoIntensidade');
            const ctxEstados = document.getElementById('graficoEstados');

            graficoIntensidade = new Chart(ctxIntensidade, {
                type: 'line',
                data: {
                    labels: labelsIntensidade,
                    datasets: [{
                        label: 'Intensidade',
                        data: dadosIntensidade,
                        tension: 0.3,
                        fill: false
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
                            display: true
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            min: 0,
                            max: 5,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });

            graficoEstados = new Chart(ctxEstados, {
                type: 'bar',
                data: {
                    labels: labelsEstados,
                    datasets: [{
                        label: 'Quantidade',
                        data: dadosEstados
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
                            }
                        }
                    }
                }
            });

            loadingEl.style.display = 'none';
            conteudoEl.style.display = 'block';
        } catch (error) {
            estadoErro = true;
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