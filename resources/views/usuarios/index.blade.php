@extends('layouts.app')

@section('content')
<style>
    .usuarios-container {
        max-width: 1000px;
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
    }

    .table-usuarios {
        width: 100%;
        border-collapse: collapse;
    }

    .table-usuarios th,
    .table-usuarios td {
        padding: 0.75rem;
        text-align: left;
        border-bottom: 1px solid #e5e7eb;
        font-size: 0.9rem;
    }

    .table-usuarios th {
        background-color: #f8fafc;
        font-weight: 600;
        color: #374151;
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

    <table class="table-usuarios">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Admin</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($usuarios as $user)
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
                            <form method="POST" action="{{ route('usuarios.toggleAdmin', $user) }}">
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
            @endforeach
        </tbody>
    </table>

    <div class="pagination">
        {{ $usuarios->links() }}
    </div>
</div>
@endsection
