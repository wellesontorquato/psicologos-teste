@extends('layouts.app')
@section('title', 'Evoluções | PsiGestor')
@section('content')
<div class="container">
    <h2 class="mb-3">Evoluções</h2>

    <a href="{{ route('evolucoes.create') }}" class="btn btn-primary mb-3 btn-nova-evolucao">
        + Nova Evolução
    </a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filtros --}}
    <div class="card p-3 mb-4 shadow-sm">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label fw-bold small">Paciente ou anotação</label>
                <input type="text" name="busca" value="{{ request('busca') }}" 
                       class="form-control shadow-sm" 
                       placeholder="Buscar por nome ou conteúdo">
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label fw-bold small">Período</label>
                <select name="periodo" class="form-select shadow-sm">
                    <option value="">Todos</option>
                    <option value="hoje" {{ request('periodo') == 'hoje' ? 'selected' : '' }}>Hoje</option>
                    <option value="semana" {{ request('periodo') == 'semana' ? 'selected' : '' }}>Esta semana</option>
                    <option value="mes" {{ request('periodo') == 'mes' ? 'selected' : '' }}>Este mês</option>
                </select>
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label fw-bold small">Tipo de evolução</label>
                <select name="tipo" class="form-select shadow-sm">
                    <option value="">Todos</option>
                    <option value="evolucao" {{ request('tipo') == 'evolucao' ? 'selected' : '' }}>Evolução</option>
                    <option value="medicacao" {{ request('tipo') == 'medicacao' ? 'selected' : '' }}>Medicação</option>
                </select>
            </div>

            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary shadow-sm w-100">
                    🔍 Buscar
                </button>
                <a href="{{ route('evolucoes.index') }}" class="btn btn-outline-dark shadow-sm w-100">
                    ❌ Limpar
                </a>
            </div>
        </form>
    </div>

    {{-- Versão Desktop: Tabela --}}
    <div class="d-none d-md-block">
        <table class="table table-bordered shadow-sm bg-white">
            <thead class="table-light">
                <tr>
                    <th>Paciente</th>
                    <th>Data</th>
                    <th>Anotação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($evolucoes as $evolucao)
                    <tr>
                        <td>{{ $evolucao->paciente->nome }}</td>
                        <td>{{ \Carbon\Carbon::parse($evolucao->data)->format('d/m/Y') }}</td>
                        <td>{{ Str::limit($evolucao->texto, 50) }}</td>
                        <td>
                            <a href="{{ route('evolucoes.edit', $evolucao) }}" class="btn btn-warning btn-sm">Editar</a>
                            <form action="{{ route('evolucoes.destroy', $evolucao) }}" method="POST" 
                                  class="form-excluir d-inline no-spinner">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">Nenhuma evolução encontrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Versão Mobile: Cards --}}
    <div class="d-md-none">
        @forelse($evolucoes as $evolucao)
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="card-title mb-2">{{ $evolucao->paciente->nome }}</h5>
                    <p class="mb-1"><i class="bi bi-calendar"></i> {{ \Carbon\Carbon::parse($evolucao->data)->format('d/m/Y') }}</p>
                    <p class="mb-2"><i class="bi bi-journal-text"></i> {{ Str::limit($evolucao->texto, 80) }}</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('evolucoes.edit', $evolucao) }}" class="btn btn-sm btn-warning">Editar</a>
                        <form action="{{ route('evolucoes.destroy', $evolucao) }}" method="POST" 
                              class="form-excluir d-inline no-spinner">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info text-center">Nenhuma evolução encontrada.</div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $evolucoes->withQueryString()->links() }}
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

    document.querySelectorAll('.form-excluir').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
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
                        if (typeof showSpinner === 'function') {
                            showSpinner();
                        }
                        form.submit();
                    }, 300);
                }
            });
        });
    });
</script>
@endsection
