@extends('layouts.app')

@section('title', 'Auditoria | PsiGestor')

@section('content')
<style>
    .audit-container {
        background-color: #ffffff;
        padding: 1.5rem;
        border-radius: 1rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        max-width: 1100px;
        margin: auto;
    }

    .audit-container h1 {
        font-size: 1.6rem;
        font-weight: bold;
        margin-bottom: 1.5rem;
        color: #1f2937;
        text-align: center;
    }

    .audit-filters {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .audit-filters select,
    .audit-filters input[type="date"],
    .audit-filters input[type="text"] {
        padding: 0.6rem 0.8rem;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 0.9rem;
        width: 100%;
    }

    .badge {
        background-color: #e0f2fe;
        color: #0369a1;
        padding: 2px 8px;
        font-size: 0.75rem;
        border-radius: 6px;
        display: inline-block;
    }
</style>

<div class="audit-container">
    <h1>Logs de Auditoria</h1>

    {{-- Filtros --}}
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
        <a href="{{ route('admin.auditoria.index') }}" class="btn btn-secondary">Limpar</a>
    </form>

    {{-- Exportações --}}
    <div class="d-flex flex-wrap justify-content-center gap-2 mb-3">
        <a href="{{ route('admin.auditoria.exportar.pdf', request()->query()) }}" class="btn btn-sm btn-danger no-spinner">
            📄 Exportar PDF
        </a>
        <a href="{{ route('admin.auditoria.exportar.excel', request()->query()) }}" class="btn btn-sm btn-success no-spinner">
            📊 Exportar Excel
        </a>
    </div>

    {{-- Tabela em telas médias para cima --}}
    <div class="d-none d-md-block">
        <table class="table table-bordered table-hover shadow-sm bg-white">
            <thead class="table-light">
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
                        <td><span class="badge">{{ $log->user->name ?? 'Desconhecido' }}</span></td>
                        <td>{{ $log->action }}</td>
                        <td>{{ $log->description ?? '-' }}</td>
                        <td>{{ $log->ip_address }}</td>
                        <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Nenhum registro encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Cards em telas pequenas --}}
    <div class="d-md-none">
        @forelse ($audits as $log)
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="card-title mb-2">
                        <span class="badge">{{ $log->user->name ?? 'Desconhecido' }}</span>
                    </h5>
                    <p class="mb-1"><i class="bi bi-check-circle"></i> <strong>Ação:</strong> {{ $log->action }}</p>
                    <p class="mb-1"><i class="bi bi-text-paragraph"></i> <strong>Descrição:</strong> {{ $log->description ?? '-' }}</p>
                    <p class="mb-1"><i class="bi bi-wifi"></i> <strong>IP:</strong> {{ $log->ip_address }}</p>
                    <p class="mb-1"><i class="bi bi-calendar-event"></i> <strong>Data:</strong> {{ $log->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        @empty
            <div class="alert alert-info text-center">Nenhum registro encontrado.</div>
        @endforelse
    </div>

    <div class="audit-pagination">
        {{ $audits->appends(request()->query())->links() }}
    </div>
</div>
@endsection
