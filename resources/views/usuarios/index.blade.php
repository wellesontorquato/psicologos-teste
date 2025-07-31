@extends('layouts.app')

@section('title', 'Usuários | PsiGestor')

@section('content')
<style>
    .usuarios-container {
        max-width: 1100px;
        margin: auto;
        background: white;
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    }

    .usuarios-container h2 {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 1.5rem;
        color: #1f2937;
        text-align: center;
    }

    .badge-admin {
        background-color: #16a34a;
        color: white;
        padding: 4px 10px;
        font-size: 0.75rem;
        border-radius: 9999px;
    }

    .badge-user {
        background-color: #6b7280;
        color: white;
        padding: 4px 10px;
        font-size: 0.75rem;
        border-radius: 9999px;
    }

    .btn-toggle {
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .btn-admin {
        background-color: #dc2626;
        color: white;
    }

    .btn-user {
        background-color: #00aaff;
        color: white;
    }

    .pagination {
        margin-top: 1.5rem;
        text-align: center;
    }
</style>

<div class="usuarios-container">
    <h2>Gerenciar Usuários</h2>

    {{-- Alertas --}}
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#00aaff'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#e3342f'
            });
        </script>
    @endif

    {{-- Tabela em telas médias e grandes --}}
    <div class="d-none d-md-block">
        <table class="table table-bordered table-hover shadow-sm bg-white">
            <thead class="table-light">
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Admin</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($usuarios as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if ($user->is_admin)
                                <span class="badge-admin">Sim</span>
                            @else
                                <span class="badge-user">Não</span>
                            @endif
                        </td>
                        <td>
                            @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.usuarios.toggleAdmin', $user) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="btn-toggle {{ $user->is_admin ? 'btn-admin' : 'btn-user' }}">
                                        {{ $user->is_admin ? 'Revogar Admin' : 'Tornar Admin' }}
                                    </button>
                                </form>
                            @else
                                <em>(você)</em>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">Nenhum usuário encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Cards em telas pequenas --}}
    <div class="d-md-none">
        @forelse ($usuarios as $user)
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="card-title mb-2">{{ $user->name }}</h5>
                    <p class="mb-1"><i class="bi bi-envelope"></i> {{ $user->email }}</p>
                    <p class="mb-1">
                        @if ($user->is_admin)
                            👑 <span class="badge-admin">Administrador</span>
                        @else
                            👤 <span class="badge-user">Usuário</span>
                        @endif
                    </p>
                    
                    <div class="mt-2">
                        @if ($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.usuarios.toggleAdmin', $user) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="btn-toggle {{ $user->is_admin ? 'btn-admin' : 'btn-user' }}">
                                    {{ $user->is_admin ? 'Revogar Admin' : 'Tornar Admin' }}
                                </button>
                            </form>
                        @else
                            <em class="text-muted">(você)</em>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info text-center">Nenhum usuário encontrado.</div>
        @endforelse
    </div>

    <div class="pagination">
        {{ $usuarios->links() }}
    </div>
</div>
@endsection
