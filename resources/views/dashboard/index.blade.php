@extends('layouts.app')

@section('title', 'Dashboard | PsiGestor')

@section('content')
<div class="container">

    {{-- Mensagem de sucesso/aviso se o e-mail foi verificado --}}
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
                <div class="card-body d-flex align-items-center">
                    <div class="icon-wrapper bg-danger text-white me-3">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Pendências</p>
                        <h4 class="fw-bold mb-0">{{ $pendencias }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 d-flex">
            <div class="card shadow-sm border-0 h-100 w-100">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-wrapper bg-dark text-white me-3">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Período Selecionado</p>
                        <h6 class="fw-semibold mb-0">{{ $dataInicial->format('d/m/Y') }} a {{ $dataFinal->format('d/m/Y') }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form method="GET" class="row g-2 mb-4 align-items-end">
        <div class="col-md-3">
            <label>Período rápido</label>
            <select name="periodo" class="form-control" onchange="this.form.submit()">
                <option value="">Escolha...</option>
                <option value="7" {{ request('periodo') == '7' ? 'selected' : '' }}>Últimos 7 dias</option>
                <option value="15" {{ request('periodo') == '15' ? 'selected' : '' }}>Últimos 15 dias</option>
                <option value="30" {{ request('periodo') == '30' ? 'selected' : '' }}>Últimos 30 dias</option>
            </select>
        </div>
        <div class="col-md-3">
            <label>De</label>
            <input type="date" name="de" value="{{ request('de') }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label>Até</label>
            <input type="date" name="ate" value="{{ request('ate') }}" class="form-control">
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-outline-secondary shadow-sm w-100">
                🔍 Filtrar
            </button>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-dark shadow-sm w-100">
                ❌ Limpar
            </a>
        </div>
    </form>

    <div class="row mb-5">
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
    .icon-circle {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        border-radius: 50%;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    .card-hover:hover {
        transform: scale(1.02);
        transition: 0.3s ease;
        box-shadow: 0 0 25px rgba(0, 0, 0, 0.12);
    }
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
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
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
            plugins: {
                legend: {
                    position: 'top',
                },
            },
            scales: {
                y: {
                    beginAtZero: true
                },
                x: {
                    ticks: {
                        autoSkip: true,
                        maxRotation: 90,
                        minRotation: 30
                    }
                }
            }
        }
    });
</script>
@endsection
