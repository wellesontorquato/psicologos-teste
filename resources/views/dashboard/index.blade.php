@extends('layouts.app')

@section('title', 'Dashboard | PsiGestor')

@section('content')
<div class="container">

    {{-- Mensagens de status --}}
    @if (session('status') === 'email-just-verified')
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
             Seu e-mail foi verificado com sucesso! Bem-vindo(a) ao sistema PsiGestor.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @elseif (session('status') === 'email-already-verified')
        <div class="alert alert-info alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i>
             Seu e-mail já estava verificado anteriormente.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- MENSAGEM DE BOAS-VINDAS --}}
    <div class="welcome-header mt-4 mb-5">
    <h1 class="display-6 mb-1">
        Bem-vindo(a) de volta,
        <span class="fw-bold">{{ explode(' ', Auth::user()->name)[0] }}</span>!
    </h1>
    <p class="text-muted mb-0">
        Aqui está um resumo da sua atividade recente. Tenha um ótimo dia de trabalho!
    </p>
    </div>

    <h2 class="mb-4">
    <i class="bi bi-speedometer2"></i> Dashboard
    <small class="text-muted fs-6">Visão geral do sistema</small>
    </h2>

    <div class="text-end mb-4">
        <a href="{{ route('dashboard.pdf', request()->all()) }}" class="btn btn-danger me-2 no-spinner-on-download">
            <i class="bi bi-file-earmark-pdf"></i> Exportar PDF
        </a>
        <a href="{{ route('dashboard.excel', request()->all()) }}" class="btn btn-success no-spinner-on-download">
            <i class="bi bi-file-earmark-excel"></i> Exportar Excel
        </a>
    </div>

    {{-- Cards principais --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-wrapper bg-primary text-white me-3">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Sessões de Hoje</p>
                        <h4 class="fw-bold mb-0">{{ $sessoesHoje }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-wrapper bg-success text-white me-3">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Recebido no Período</p>
                        <h4 class="fw-bold mb-0">R$ {{ number_format($totalMesAtual, 2, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="icon-wrapper bg-danger text-white me-3">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-1">Pendências</p>
                                <h4 class="fw-bold mb-0">{{ $pendenciasTotal }}</h4>
                            </div>
                        </div>
                        @if($pendenciasTotal > 0)
                        <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalPendencias">
                            Ver pendências
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 d-flex">
            <div class="card shadow-sm border-0 h-100 w-100">
                <div class="card-body">
                    <div class="icon-wrapper bg-dark text-white me-3 float-start">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Período Selecionado</p>
                        <h6 class="fw-semibold mb-3">{{ $dataInicial->format('d/m/Y') }} a {{ $dataFinal->format('d/m/Y') }}</h6>
                        
                        {{-- Formulário de filtros --}}
                        <form method="GET" class="d-flex flex-column gap-2">
                            <div class="d-flex gap-2">
                                <select name="periodo" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">Rápido...</option>
                                    <option value="7" {{ request('periodo') == '7' ? 'selected' : '' }}>Últimos 7 dias</option>
                                    <option value="15" {{ request('periodo') == '15' ? 'selected' : '' }}>Últimos 15 dias</option>
                                    <option value="30" {{ request('periodo') == '30' ? 'selected' : '' }}>Últimos 30 dias</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-secondary">🔍</button>
                            </div>
                            <div class="d-flex gap-2">
                                <input type="date" name="de" value="{{ request('de') }}" class="form-control form-control-sm">
                                <input type="date" name="ate" value="{{ request('ate') }}" class="form-control form-control-sm">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Pendências --}}
    <div class="modal fade" id="modalPendencias" tabindex="-1" aria-labelledby="modalPendenciasLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalPendenciasLabel"><i class="bi bi-exclamation-circle"></i> Pendências</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    {{-- Pendências Financeiras --}}
                    <h6 class="text-danger fw-bold"><i class="bi bi-currency-dollar"></i> Sessões não pagas</h6>
                    @if($pendenciasFinanceiras->count())
                        <ul class="list-group mb-4">
                            @foreach($pendenciasFinanceiras as $pendencia)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $pendencia->paciente->nome ?? 'Paciente removido' }}</strong><br>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($pendencia->data_hora)->format('d/m/Y H:i') }}</small>
                                    </div>
                                    <span class="badge bg-danger">R$ {{ number_format($pendencia->valor, 2, ',', '.') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Nenhuma pendência financeira encontrada.</p>
                    @endif

                    {{-- Pendências de Evolução --}}
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

    {{-- Gráficos --}}
    <div class="row mb-5 mt-4">
        <div class="col-md-6">
            <div class="card bg-light card-hover">
                <div class="card-body">
                    <h5 class="card-title">Sessões por Mês</h5>
                    <div class="canvas-wrapper">
                        <canvas id="graficoSessoes" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-light card-hover">
                <div class="card-body">
                    <h5 class="card-title">Valor Recebido por Dia</h5>
                    <div class="canvas-wrapper">
                        <canvas id="graficoValores" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Últimos Arquivos --}}
    @if($ultimosArquivos->count())
        <div class="card mb-5">
            <div class="card-header">📁 Últimos Arquivos Enviados</div>
            <ul class="list-group list-group-flush">
                @foreach($ultimosArquivos as $arquivo)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>{{ $arquivo->nome }}</span>
                        <a href="{{ $arquivo->url }}" target="_blank" class="btn btn-sm btn-outline-primary">Abrir</a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Próximas Sessões --}}
    @if(isset($proximasSessoes) && $proximasSessoes->count())
        <div class="card mb-5">
            <div class="card-header bg-warning text-dark d-flex align-items-center gap-2">
                <i class="bi bi-calendar-event-fill me-2"></i> <strong>Próximas Sessões Agendadas</strong>
            </div>
            <ul class="list-group list-group-flush">
                @foreach($proximasSessoes as $sessao)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $sessao->paciente->nome ?? 'Paciente removido' }}</strong><br>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($sessao->data_hora)->format('d/m/Y H:i') }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $sessao->foi_pago ? 'success' : 'danger' }}">
                                {{ $sessao->foi_pago ? 'Pago' : 'Pendente' }}
                            </span><br>
                            <small class="text-muted">R$ {{ number_format($sessao->valor, 2, ',', '.') }}</small>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection

@section('styles')
<style>
    .card-hover:hover {
        transform: scale(1.02);
        transition: 0.3s ease;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }
    .canvas-wrapper {
        overflow-x: auto;
        padding: 8px;
    }
    canvas {
        background-color: white;
        border-radius: 10px;
        padding: 10px;
    }
    .welcome-header h1 { font-size: 1.8rem; }
    .welcome-header p { font-size: 1rem; margin-top: 0.5rem; }
    .form-control-sm, .form-select-sm { font-size: 0.85rem; }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const meses = {!! json_encode($sessaoPorMes->pluck('mes')) !!};
    const totalSessoes = {!! json_encode($sessaoPorMes->pluck('total')) !!};

    const valoresDiasLabels = {!! json_encode($valoresPorDia->keys()) !!};
    const valoresDiasValores = {!! json_encode($valoresPorDia->values()) !!};

    const gradient = (ctx, color) => {
        const gradient = ctx.createLinearGradient(0, 0, 0, 200);
        gradient.addColorStop(0, color);
        gradient.addColorStop(1, '#ffffff');
        return gradient;
    };

    const sessoesCanvas = document.getElementById('graficoSessoes').getContext('2d');
    const valoresCanvas = document.getElementById('graficoValores').getContext('2d');

    new Chart(sessoesCanvas, {
        type: 'bar',
        data: {
            labels: meses,
            datasets: [{
                label: 'Sessões',
                data: totalSessoes,
                backgroundColor: gradient(sessoesCanvas, '#0d6efd'),
                borderRadius: 8,
                borderWidth: 1,
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'top' } }, scales: { y: { beginAtZero: true } } }
    });

    new Chart(valoresCanvas, {
        type: 'line',
        data: {
            labels: valoresDiasLabels,
            datasets: [{
                label: 'Valor Recebido (R$) por Dia',
                data: valoresDiasValores,
                fill: true,
                tension: 0.4,
                borderColor: '#198754',
                backgroundColor: gradient(valoresCanvas, 'rgba(25, 135, 84, 0.3)'),
                borderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true }, x: { ticks: { autoSkip: true, maxRotation: 90, minRotation: 30 } } }
        }
    });
</script>
@endsection
