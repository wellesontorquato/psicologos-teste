@extends('layouts.app')

@section('title', 'Pacientes | PsiGestor')

@section('content')
<div class="container">
    <h2 class="mb-3">Meus Pacientes</h2>

    <a href="{{ route('pacientes.create') }}" class="btn btn-primary mb-4">Novo Paciente</a>

    {{-- Filtro de busca --}}
    <form method="GET" class="row g-3 mb-4 align-items-end">
        <div class="col-md-6">
            <input type="text" name="busca" value="{{ request('busca') }}" class="form-control shadow-sm" placeholder="Buscar por nome, CPF, telefone ou email">
        </div>

        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-outline-secondary shadow-sm w-100">🔍 Buscar</button>
            <a href="{{ route('pacientes.index') }}" class="btn btn-outline-dark shadow-sm w-100">❌ Limpar</a>
        </div>
    </form>

    {{-- Mensagem de sucesso --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Tabela --}}
    <table class="table table-bordered table-striped shadow-sm bg-white">
        <thead class="table-light">
            <tr>
                <th>Nome</th>
                <th>Telefone</th>
                <th>Email</th>
                <th>Receita Saúde</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        @forelse($pacientes as $paciente)
            <tr>
                <td>{{ $paciente->nome }}</td>
                <td>{{ $paciente->telefone }}</td>
                <td>{{ $paciente->email }}</td>
                <td>{{ $paciente->exige_nota_fiscal ? 'Sim' : 'Não' }}</td>
                <td>
                    <a href="{{ route('pacientes.edit', $paciente->id) }}" class="btn btn-sm btn-secondary">Editar</a>

                    <a href="{{ route('pacientes.historico', $paciente->id) }}" class="btn btn-outline-primary btn-sm">
                        Ver Histórico
                    </a>

                    <a href="{{ route('arquivos.index', $paciente->id) }}" class="btn btn-sm btn-info">
                        Arquivos
                    </a>

                    <form action="{{ route('pacientes.destroy', $paciente->id) }}" method="POST"
                          class="d-inline delete-form no-spinner">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">Nenhum paciente encontrado.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Tem certeza?',
                text: "Você não poderá reverter isso!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, excluir',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    showSpinner();
                    this.submit();
                }
            });
        });
    });
</script>
@endsection
