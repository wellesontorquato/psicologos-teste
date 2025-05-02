@extends('layouts.app')
@section('title', 'Auditoria | PsiGestor')
@section('content')
<style>
    .audit-pagination {
    margin-top: 2rem;
    text-align: center;
    }

    .audit-container {
        background-color: #ffffff;
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        max-width: 1000px;
        margin: auto;
    }

    .audit-container h1 {
        font-size: 1.75rem;
        font-weight: bold;
        margin-bottom: 1.5rem;
        color: #1f2937;
        text-align: center;
    }

    .audit-filters {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .audit-filters select,
    .audit-filters input[type="date"],
    .audit-filters input[type="text"] {
        padding: 0.5rem 0.75rem;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 0.875rem;
        width: 100%;
    }

    .audit-table {
        width: 100%;
        border-collapse: collapse;
    }

    .audit-table th,
    .audit-table td {
        padding: 0.75rem;
        text-align: left;
        border-bottom: 1px solid #e5e7eb;
        font-size: 0.875rem;
    }

    .audit-table th {
        background-color: #f1f5f9;
        font-weight: 600;
        color: #374151;
    }

    .audit-pagination {
        margin-top: 1.5rem;
        text-align: center;
    }

    .badge {
        background-color: #e0f2fe;
        color: #0369a1;
        padding: 2px 8px;
        font-size: 0.75rem;
        border-radius: 6px;
    }

    .btn {
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.875rem;
        text-decoration: none;
        display: inline-block;
    }

    .btn-primary {
        background-color: #fff;
        color: #00aaff;
        border: 1px solid #00aaff;
        font-weight: bold;
    }

    .btn-primary:hover {
        background-color: #00aaff;
    }

    .btn-secondary {
        background-color: #e5e7eb;
        color: #374151;
        border: none;
        font-weight: bold;
    }

    .btn-secondary:hover {
        background-color:rgb(129, 129, 130);
    }
</style>

<div class="audit-container">
    <h1>Logs de Auditoria</h1>

    <form method="GET" class="audit-filters">
        <select name="user_id">
            <option value="">-- Usuário --</option>
            @foreach ($usuarios as $usuario)
                <option value="{{ $usuario->id }}" {{ request('user_id') == $usuario->id ? 'selected' : '' }}>
                    {{ $usuario->name }}
                </option>
            @endforeach
        </select>

        <input type="text" name="action" placeholder="Ação" value="{{ request('action') }}" />

        <input type="date" name="de" value="{{ request('de') }}" />
        <input type="date" name="ate" value="{{ request('ate') }}" />
        
        <button type="submit" class="btn btn-primary">Filtrar</button>
        <a href="{{ route('auditoria.index') }}" class="btn btn-secondary">Limpar</a>
    </form>
        <div class="flex flex-wrap gap-2 mb-4">
        <a href="{{ route('auditoria.exportar.pdf', request()->query()) }}" class="btn btn-sm btn-danger">
            📄 Exportar PDF
        </a>
        <a href="{{ route('auditoria.exportar.excel', request()->query()) }}" class="btn btn-sm btn-success">
            📊 Exportar Excel
        </a>
    </div>

    <table class="audit-table">
        <thead>
            <tr>
                <th>Usuário</th>
                <th>Ação</th>
                <th>Descrição</th>
                <th>IP</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($audits as $log)
                <tr>
                    <td>
                        <span class="badge">
                            {{ $log->user->name ?? 'Desconhecido' }}
                        </span>
                    </td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->description ?? '-' }}</td>
                    <td>{{ $log->ip_address }}</td>
                    <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Nenhum registro encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="audit-pagination">
        {{ $audits->appends(request()->query())->links() }}
    </div>
</div>
@endsection
