@extends('layouts.app')

@section('title', 'Sessões | PsiGestor')

@section('content')
<div class="container py-3 sessoes-page">

    {{-- Topo --}}
    <div class="card border-0 shadow-sm mb-4 sessoes-topbar">
        <div class="card-body p-3 p-lg-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <div class="sessoes-kicker">Agenda clínica</div>
                    <h1 class="sessoes-title mb-1">Sessões</h1>
                    <p class="sessoes-subtitle mb-0">
                        Visualize atendimentos, acompanhe confirmações e envie lembretes com mais facilidade.
                    </p>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('sessoes.export', array_merge(request()->all(), ['format' => 'pdf'])) }}"
                       class="btn btn-outline-danger shadow-sm no-spinner-on-download">
                        <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                    </a>

                    <a href="{{ route('sessoes.export', array_merge(request()->all(), ['format' => 'excel'])) }}"
                       class="btn btn-outline-success shadow-sm no-spinner-on-download">
                        <i class="bi bi-file-earmark-excel me-1"></i> Excel
                    </a>

                    <a href="{{ route('sessoes.create') }}" class="btn btn-primary shadow-sm">
                        <i class="bi bi-plus-lg me-1"></i> Nova Sessão
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Resumo --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm resumo-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="resumo-icon bg-primary-subtle text-primary">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <div>
                            <div class="resumo-label">Sessões marcadas</div>
                            <div class="resumo-value">{{ $sessoesMarcadas->total() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm resumo-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="resumo-icon bg-success-subtle text-success">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                        <div>
                            <div class="resumo-label">Sessões realizadas</div>
                            <div class="resumo-value">{{ $sessoesRealizadas->total() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3 p-lg-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <h2 class="section-title mb-1">Filtros</h2>
                    <p class="section-subtitle mb-0">Encontre sessões com mais rapidez.</p>
                </div>

                <a href="{{ route('sessoes.importar.view') }}" class="btn btn-outline-primary btn-sm shadow-sm">
                    <i class="bi bi-upload me-1"></i> Importar em massa
                </a>
            </div>

            <form method="GET" class="row g-3 align-items-end">
                <div class="col-12 col-sm-6 col-lg-2">
                    <label class="form-label small text-muted fw-semibold mb-1">Pago?</label>
                    <select name="foi_pago" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="Sim" {{ request('foi_pago') == 'Sim' ? 'selected' : '' }}>Sim</option>
                        <option value="Não" {{ request('foi_pago') == 'Não' ? 'selected' : '' }}>Não</option>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-lg-2">
                    <label class="form-label small text-muted fw-semibold mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="Todos">Todos</option>
                        <option value="CONFIRMADA" {{ request('status') == 'CONFIRMADA' ? 'selected' : '' }}>Confirmada</option>
                        <option value="REMARCAR" {{ request('status') == 'REMARCAR' ? 'selected' : '' }}>Remarcar</option>
                        <option value="REMARCADO" {{ request('status') == 'REMARCADO' ? 'selected' : '' }}>Remarcado</option>
                        <option value="CANCELADA" {{ request('status') == 'CANCELADA' ? 'selected' : '' }}>Cancelada</option>
                        <option value="PENDENTE" {{ request('status') == 'PENDENTE' ? 'selected' : '' }}>Pendente</option>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-lg-2">
                    <label class="form-label small text-muted fw-semibold mb-1">Período</label>
                    <select name="periodo" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="hoje" {{ request('periodo') == 'hoje' ? 'selected' : '' }}>Hoje</option>
                        <option value="semana" {{ request('periodo') == 'semana' ? 'selected' : '' }}>Esta semana</option>
                        <option value="proxima" {{ request('periodo') == 'proxima' ? 'selected' : '' }}>Próxima semana</option>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-lg-2">
                    <label class="form-label small text-muted fw-semibold mb-1">Ordenar</label>
                    <select name="ordenar" class="form-select form-select-sm">
                        <option value="mais_recente" {{ request('ordenar') == 'mais_recente' ? 'selected' : '' }}>Mais recente</option>
                        <option value="mais_antigo" {{ request('ordenar') == 'mais_antigo' ? 'selected' : '' }}>Mais antigo</option>
                    </select>
                </div>

                <div class="col-12 col-lg-3">
                    <label class="form-label small text-muted fw-semibold mb-1">Buscar</label>
                    <input
                        type="text"
                        name="busca"
                        class="form-control form-control-sm"
                        placeholder="Nome, CPF, telefone ou e-mail"
                        value="{{ request('busca') }}"
                    >
                </div>

                <div class="col-12 col-lg-1 d-grid d-lg-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100" title="Aplicar filtros">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('sessoes.index') }}" class="btn btn-sm btn-outline-secondary w-100" title="Limpar filtros">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>

            @if(request()->filled('foi_pago') || request()->filled('status') || request()->filled('periodo') || request()->filled('busca'))
                <div class="mt-3 d-flex flex-wrap gap-2">
                    @if(request()->filled('foi_pago'))
                        <span class="badge rounded-pill text-bg-info">Pago: {{ request('foi_pago') }}</span>
                    @endif

                    @if(request()->filled('status') && request('status') !== 'Todos')
                        <span class="badge rounded-pill text-bg-warning">Status: {{ ucfirst(strtolower(request('status'))) }}</span>
                    @endif

                    @if(request()->filled('periodo'))
                        @php
                            $hoje = \Carbon\Carbon::now('America/Sao_Paulo')->startOfDay();
                            $dataBadge = match(request('periodo')) {
                                'hoje' => 'Hoje: ' . $hoje->format('d/m'),
                                'semana' => 'Semana: ' . $hoje->copy()->startOfWeek()->format('d/m') . ' – ' . $hoje->copy()->endOfWeek()->format('d/m'),
                                'proxima' => 'Próxima: ' . $hoje->copy()->addWeek()->startOfWeek()->format('d/m') . ' – ' . $hoje->copy()->addWeek()->endOfWeek()->format('d/m'),
                                default => ucfirst(request('periodo')),
                            };
                        @endphp
                        <span class="badge rounded-pill text-bg-primary">{{ $dataBadge }}</span>
                    @endif

                    @if(request()->filled('busca'))
                        <span class="badge rounded-pill text-bg-secondary">Busca: {{ request('busca') }}</span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Abas --}}
    <div class="tabs-wrap mb-4">
        <ul class="nav nav-pills sessoes-tabs" id="abasSessoes">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#futuras">
                    <i class="bi bi-calendar-event me-1"></i> Marcadas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#realizadas">
                    <i class="bi bi-check2-square me-1"></i> Realizadas
                </a>
            </li>
        </ul>
    </div>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="futuras">
            @include('sessoes.partials.tabela', ['sessoes' => $sessoesMarcadas])

            <div class="mt-3">
                {{ $sessoesMarcadas->appends(request()->except('page'))->fragment('futuras')->links() }}
            </div>
        </div>

        <div class="tab-pane fade" id="realizadas">
            @include('sessoes.partials.tabela', ['sessoes' => $sessoesRealizadas])

            <div class="mt-3">
                {{ $sessoesRealizadas->appends(request()->except('page'))->fragment('realizadas')->links() }}
            </div>
        </div>
    </div>

    {{-- Modal Recorrência --}}
    <div class="modal fade" id="modalRecorrencia" tabindex="-1" aria-labelledby="modalRecorrenciaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('sessoes.gerarRecorrencias') }}">
                @csrf
                <input type="hidden" name="sessao_id" id="inputSessaoId">

                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-0 pb-0">
                        <div>
                            <h5 class="modal-title" id="modalRecorrenciaLabel">Criar sessões recorrentes</h5>
                            <p class="text-muted small mb-0">Repita a sessão pelas próximas semanas.</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>

                    <div class="modal-body pt-3">
                        <label for="semanas" class="form-label fw-semibold">Quantas semanas deseja repetir?</label>
                        <input
                            type="number"
                            name="semanas"
                            id="semanas"
                            class="form-control mb-3"
                            min="1"
                            required
                            placeholder="Ex: 4"
                        >

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="foi_pago" id="foi_pago">
                            <label class="form-check-label fw-semibold" for="foi_pago">
                                Marcar sessões como pagas
                            </label>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Criar recorrências</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: @json(session('success')),
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Atenção',
            text: @json(session('error')),
            confirmButtonText: 'Ok'
        });
    @endif

    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('modalRecorrencia');

        if (modal) {
            modal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const sessaoId = button.getAttribute('data-sessao-id');
                document.getElementById('inputSessaoId').value = sessaoId;
            });
        }

        const abaAtiva = localStorage.getItem('abaAtivaSessao');
        if (abaAtiva) {
            const aba = document.querySelector(`a[data-bs-toggle="tab"][href="${abaAtiva}"]`);
            if (aba) {
                new bootstrap.Tab(aba).show();
            }
        }

        document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', function (e) {
                localStorage.setItem('abaAtivaSessao', e.target.getAttribute('href'));
            });
        });

        document.querySelectorAll('.form-excluir').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const urlParams = new URLSearchParams(window.location.search);
                const abaAtiva = localStorage.getItem('abaAtivaSessao') || '#futuras';
                const queryString = urlParams.toString();

                let inputQuery = form.querySelector('input[name="query_string"]');
                if (!inputQuery) {
                    inputQuery = document.createElement('input');
                    inputQuery.type = 'hidden';
                    inputQuery.name = 'query_string';
                    form.appendChild(inputQuery);
                }
                inputQuery.value = queryString;

                let inputAba = form.querySelector('input[name="aba"]');
                if (!inputAba) {
                    inputAba = document.createElement('input');
                    inputAba.type = 'hidden';
                    inputAba.name = 'aba';
                    form.appendChild(inputAba);
                }
                inputAba.value = abaAtiva.replace('#', '');

                Swal.fire({
                    title: 'Tem certeza?',
                    text: 'Essa ação não poderá ser desfeita.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sim, excluir!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.close();

                        setTimeout(() => {
                            if (typeof showSpinner === 'function') {
                                showSpinner();
                            }
                            form.submit();
                        }, 300);
                    }
                });
            });
        });

        document.querySelectorAll('.form-status-confirmacao').forEach(form => {
            const select = form.querySelector('select[name="status_confirmacao"]');

            if (!select) return;

            select.addEventListener('change', function () {
                const textoSelecionado = this.options[this.selectedIndex]?.text || 'novo status';

                Swal.fire({
                    title: 'Atualizar confirmação?',
                    text: `Deseja alterar o status para "${textoSelecionado}"?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, atualizar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    } else {
                        this.value = this.dataset.current || 'PENDENTE';
                    }
                });
            });
        });

        document.querySelectorAll('.btn-whatsapp-lembrete').forEach(btn => {
            btn.addEventListener('click', function () {
                if (typeof showSpinner === 'function') {
                    showSpinner();
                }
            });
        });
    });
</script>

<style>
    .sessoes-page {
        --soft-border: #e9ecef;
        --soft-text: #6c757d;
    }

    .sessoes-topbar,
    .resumo-card {
        border-radius: 1rem;
    }

    .sessoes-topbar {
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
    }

    .sessoes-kicker {
        display: inline-block;
        font-size: .78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #0d6efd;
        margin-bottom: .35rem;
    }

    .sessoes-title {
        font-size: clamp(1.55rem, 2vw, 2rem);
        font-weight: 800;
        color: #212529;
    }

    .sessoes-subtitle,
    .section-subtitle {
        color: var(--soft-text);
        font-size: .94rem;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #212529;
    }

    .resumo-icon {
        width: 2.65rem;
        height: 2.65rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: .85rem;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .resumo-label {
        font-size: .9rem;
        color: #495057;
        font-weight: 700;
        margin-bottom: .2rem;
    }

    .resumo-value {
        font-size: 1.85rem;
        line-height: 1;
        font-weight: 800;
        color: #212529;
    }

    .tabs-wrap {
        overflow-x: auto;
        padding-bottom: .2rem;
    }

    .sessoes-tabs {
        gap: .65rem;
        flex-wrap: nowrap;
    }

    .sessoes-tabs .nav-link {
        border: 0;
        border-radius: 999px;
        background: #fff;
        color: #495057;
        font-weight: 600;
        padding: .75rem 1rem;
        white-space: nowrap;
        box-shadow: 0 .125rem .5rem rgba(0,0,0,.04);
    }

    .sessoes-tabs .nav-link.active {
        background: #0d6efd;
        color: #fff;
        box-shadow: 0 .35rem .85rem rgba(13,110,253,.22);
    }

    .card,
    .modal-content {
        border-radius: 1rem;
    }

    .form-control,
    .form-select,
    .btn {
        border-radius: .75rem;
    }

    @media (max-width: 991.98px) {
        .sessoes-topbar .card-body {
            padding: 1rem !important;
        }
    }
</style>
@endsection
