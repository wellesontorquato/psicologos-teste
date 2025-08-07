@extends('layouts.app')

@section('title', 'Sessões | PsiGestor')

@section('content')
<div class="container">
    <h2 class="mb-3">Sessões</h2>

    <a href="{{ route('sessoes.create') }}" class="btn btn-primary mb-3 btn-nova-sessao">
        Nova Sessão
    </a>

    {{-- Filtros --}}
    <div class="card p-3 mb-4 shadow-sm">
        <form method="GET" class="row g-3 align-items-end">

            <div class="col-12 col-md-2">
                <label class="form-label small text-muted fw-semibold mb-1">Pago?</label>
                <select name="foi_pago" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="Sim" {{ request('foi_pago') == 'Sim' ? 'selected' : '' }}>Sim</option>
                    <option value="Não" {{ request('foi_pago') == 'Não' ? 'selected' : '' }}>Não</option>
                </select>
            </div>

            <div class="col-12 col-md-2">
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

            <div class="col-12 col-md-2">
                <label class="form-label small text-muted fw-semibold mb-1">Período</label>
                <select name="periodo" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="hoje" {{ request('periodo') == 'hoje' ? 'selected' : '' }}>Hoje</option>
                    <option value="semana" {{ request('periodo') == 'semana' ? 'selected' : '' }}>Esta semana</option>
                    <option value="proxima" {{ request('periodo') == 'proxima' ? 'selected' : '' }}>Próxima semana</option>
                </select>
            </div>

            <div class="col-12 col-md-2">
                <label class="form-label small text-muted fw-semibold mb-1">Ordenar</label>
                <select name="ordenar" class="form-select form-select-sm">
                    <option value="mais_recente" {{ request('ordenar') == 'mais_recente' ? 'selected' : '' }}>Mais recente</option>
                    <option value="mais_antigo" {{ request('ordenar') == 'mais_antigo' ? 'selected' : '' }}>Mais antigo</option>
                </select>
            </div>

            <div class="col-12 col-md-2">
                <label class="form-label small text-muted fw-semibold mb-1">Buscar</label>
                <input type="text" name="busca" class="form-control form-control-sm"
                    placeholder="Nome, CPF ou e-mail" value="{{ request('busca') }}">
            </div>

            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-outline-secondary w-100" title="Aplicar filtros">🔍</button>
                <a href="{{ route('sessoes.index') }}" class="btn btn-sm btn-outline-dark w-100" title="Limpar filtros">❌</a>
            </div>
        </form>
    </div>

    {{-- Exportações + Importação --}}
    <div class="mb-4 d-flex flex-wrap gap-2">
        <a href="{{ route('sessoes.export', array_merge(request()->all(), ['format' => 'pdf'])) }}"
        class="btn btn-danger shadow-sm no-spinner-on-download">
        <i class="bi bi-file-earmark-pdf"></i> PDF
        </a>
        <a href="{{ route('sessoes.export', array_merge(request()->all(), ['format' => 'excel'])) }}"
        class="btn btn-success shadow-sm no-spinner-on-download">
        <i class="bi bi-file-earmark-excel"></i> Excel
        </a>
        <a href="{{ route('sessoes.importar.view') }}" 
        class="btn btn-outline-primary shadow-sm">
        <i class="bi bi-upload"></i> Importar sessões em massa
        </a>

    </div>


    {{-- Badges de Filtros Ativos --}}
    @if(request()->filled('foi_pago') || request()->filled('status') || request()->filled('periodo') || request()->filled('busca'))
        <div class="mb-3">
            <span class="me-2">🔎 <strong>Filtros ativos:</strong></span>
            @if(request()->filled('foi_pago'))
                <span class="badge bg-info text-dark me-1">💰 Pago: {{ request('foi_pago') }}</span>
            @endif
            @if(request()->filled('status') && request('status') !== 'Todos')
                <span class="badge bg-warning text-dark me-1">📋 Status: {{ ucfirst(strtolower(request('status'))) }}</span>
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
                <span class="badge bg-primary me-1">🕐 {{ $dataBadge }}</span>
            @endif
            @if(request()->filled('busca'))
                <span class="badge bg-secondary me-1">🔍 Paciente: {{ request('busca') }}</span>
            @endif
        </div>
    @endif

    {{-- Abas --}}
    <ul class="nav nav-tabs flex-column flex-md-row mb-4">
        <li class="nav-item">
            <a class="nav-link active text-center" data-bs-toggle="tab" href="#futuras">📅 Sessões Marcadas</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-center" data-bs-toggle="tab" href="#realizadas">✅ Sessões Realizadas</a>
        </li>
    </ul>

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
        <div class="modal-dialog">
            <form method="POST" action="{{ route('sessoes.gerarRecorrencias') }}">
                @csrf
                <input type="hidden" name="sessao_id" id="inputSessaoId">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalRecorrenciaLabel">Criar Sessões Recorrentes</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <label for="semanas" class="form-label fw-bold">Quantas semanas deseja repetir?</label>
                        <input type="number" name="semanas" id="semanas" class="form-control mb-3" min="1" required placeholder="Ex: 4">

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="foi_pago" id="foi_pago">
                            <label class="form-check-label fw-bold" for="foi_pago">
                                Marcar sessões como pagas?
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Criar Recorrências</button>
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
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    // Confirmação preservando filtros e aba ativa
    document.querySelectorAll('.form-excluir').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const urlParams = new URLSearchParams(window.location.search);
            const abaAtiva = localStorage.getItem('abaAtivaSessao') || 'futuras';
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
                text: "Essa ação não poderá ser desfeita.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.close();
                    setTimeout(() => {
                        if (typeof showSpinner === 'function') showSpinner();
                        form.submit();
                    }, 300);
                }
            });
        });
    });

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
            if (aba) new bootstrap.Tab(aba).show();
        }

        document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', function (e) {
                localStorage.setItem('abaAtivaSessao', e.target.getAttribute('href'));
            });
        });
    });
</script>
@endsection
