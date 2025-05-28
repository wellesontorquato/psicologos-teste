@extends('layouts.app')
@section('title', 'Evoluções | PsiGestor')
@section('content')
<div class="container">
    <h2>Evoluções</h2>

    <a href="{{ route('evolucoes.create') }}" class="btn btn-primary mb-3">Nova Evolução</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card p-3 mb-4 shadow-sm">
        <form method="GET" class="row g-3 mb-4 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold">Paciente ou anotação</label>
                <input type="text" name="busca" value="{{ request('busca') }}" class="form-control shadow-sm" placeholder="Buscar por nome ou conteúdo">
            </div>

            <div class="col-md-2">
                <label class="form-label fw-bold">Período</label>
                <select name="periodo" class="form-select shadow-sm">
                    <option value="">Todos</option>
                    <option value="hoje" {{ request('periodo') == 'hoje' ? 'selected' : '' }}>Hoje</option>
                    <option value="semana" {{ request('periodo') == 'semana' ? 'selected' : '' }}>Esta semana</option>
                    <option value="mes" {{ request('periodo') == 'mes' ? 'selected' : '' }}>Este mês</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold">Tipo de evolução</label>
                <select name="tipo" class="form-select shadow-sm">
                    <option value="">Todos</option>
                    <option value="evolucao" {{ request('tipo') == 'evolucao' ? 'selected' : '' }}>Evolução</option>
                    <option value="medicacao" {{ request('tipo') == 'medicacao' ? 'selected' : '' }}>Medicação</option>            </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary w-100" title="Aplicar filtros">🔍 Buscar</button>
                <a href="{{ route('evolucoes.index') }}" class="btn btn-outline-dark w-100" title="Limpar filtros">❌ Limpar</a>
            </div>
        </form>
    </div>

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
            @foreach($evolucoes as $evolucao)
                <tr>
                    <td>{{ $evolucao->paciente->nome }}</td>
                    <td>{{ \Carbon\Carbon::parse($evolucao->data)->format('d/m/Y') }}</td>
                    <td>{{ Str::limit($evolucao->texto, 50) }}</td>
                    <td>
                        <a href="{{ route('evolucoes.edit', $evolucao) }}" class="btn btn-warning btn-sm">Editar</a>
                        <form action="{{ route('evolucoes.destroy', $evolucao) }}" method="POST" class="form-excluir d-inline no-spinner">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
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
                    // ✅ Fecha o modal SweetAlert2 suavemente
                    Swal.close();

                    // ✅ Dá um leve delay para garantir que o SweetAlert fechou antes de mostrar o spinner
                    setTimeout(() => {
                        if (typeof showSpinner === 'function') {
                            showSpinner();
                        }
                        form.submit();
                    }, 300); // 300ms deixa a animação do SweetAlert fechar bonito
                }
            });
        });
    });
</script>
@endsection
