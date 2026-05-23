@extends('layouts.app')

@section('title', 'Pacientes | PsiGestor')

@section('content')
<style>
    .pac-page {
        width: 100%;
    }

    .pac-content {
        width: 100%;
    }

    .pac-header {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-bottom: 22px;
    }

    .pac-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .pac-subtitle {
        color: #64748b;
        margin: 4px 0 0;
        font-size: .95rem;
    }

    .pac-btn {
        min-height: 44px;
        border-radius: 14px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .pac-card {
        background: rgba(255,255,255,.94);
        border: 1px solid rgba(226,232,240,.95);
        border-radius: 22px;
        box-shadow: 0 14px 38px rgba(15,23,42,.08);
        padding: 18px;
        margin-bottom: 18px;
    }

    .pac-search-card {
        background: linear-gradient(135deg, rgba(255,255,255,.96), rgba(248,250,252,.96));
    }

    .pac-search-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
        align-items: end;
    }

    .pac-field label {
        font-size: .86rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
    }

    .pac-field .form-control {
        min-height: 44px;
        border-radius: 14px;
        border-color: #dbe3ef;
        font-size: .95rem;
        color: #0f172a;
    }

    .pac-field .form-control:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 .2rem rgba(37,99,235,.12);
    }

    .pac-search-actions {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
    }

    .pac-stats {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
        margin-bottom: 18px;
    }

    .pac-stat {
        background: rgba(255,255,255,.94);
        border: 1px solid rgba(226,232,240,.95);
        border-radius: 20px;
        box-shadow: 0 10px 28px rgba(15,23,42,.06);
        padding: 16px;
    }

    .pac-stat-label {
        color: #64748b;
        font-size: .82rem;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .pac-stat-value {
        color: #020617;
        font-size: 1.8rem;
        line-height: 1;
        font-weight: 900;
        margin: 0;
    }

    .pac-stat-help {
        color: #64748b;
        font-size: .82rem;
        margin-top: 6px;
    }

    .pac-list-title-row {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 14px;
    }

    .pac-list-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .pac-list-help {
        color: #64748b;
        font-size: .9rem;
        margin: 0;
    }

    .pac-mobile-list {
        display: grid;
        grid-template-columns: 1fr;
        gap: 14px;
    }

    .pac-patient-card {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        background: #ffffff;
        padding: 16px;
    }

    .pac-patient-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 14px;
    }

    .pac-patient-name {
        font-size: 1rem;
        font-weight: 900;
        color: #0f172a;
        margin: 0;
        line-height: 1.25;
    }

    .pac-patient-meta {
        color: #64748b;
        font-size: .84rem;
        margin-top: 3px;
    }

    .pac-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 6px 10px;
        font-size: .76rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .pac-badge-success {
        background: #dcfce7;
        color: #166534;
    }

    .pac-badge-muted {
        background: #f1f5f9;
        color: #475569;
    }

    .pac-info-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
        margin-bottom: 14px;
    }

    .pac-info-item {
        background: #f8fafc;
        border-radius: 16px;
        padding: 10px 12px;
    }

    .pac-info-label {
        display: block;
        color: #64748b;
        font-size: .74rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .03em;
        margin-bottom: 3px;
    }

    .pac-info-value {
        display: block;
        color: #0f172a;
        font-size: .93rem;
        font-weight: 700;
        word-break: break-word;
    }

    .pac-card-actions {
        display: grid;
        grid-template-columns: 1fr;
        gap: 8px;
    }

    .pac-action-btn {
        min-height: 40px;
        border-radius: 12px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
    }

    .pac-desktop-table {
        display: none;
    }

    .pac-table {
        margin-bottom: 0;
    }

    .pac-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        white-space: nowrap;
        border-bottom: 1px solid #e2e8f0;
        padding: 14px 12px;
    }

    .pac-table tbody td {
        vertical-align: middle;
        padding: 14px 12px;
        color: #0f172a;
        border-bottom: 1px solid #edf2f7;
    }

    .pac-table tbody tr:hover {
        background: #f8fafc;
    }

    .pac-table-name {
        font-weight: 900;
        color: #0f172a;
    }

    .pac-table-sub {
        color: #64748b;
        font-size: .82rem;
    }

    .pac-table-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        justify-content: flex-end;
    }

    .pac-empty {
        text-align: center;
        padding: 44px 18px;
        color: #64748b;
    }

    .pac-empty-icon {
        width: 54px;
        height: 54px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #eff6ff;
        color: #2563eb;
        font-size: 1.5rem;
        margin-bottom: 12px;
    }

    .pac-pagination {
        margin-top: 18px;
    }

    @media (min-width: 576px) {
        .pac-info-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .pac-card-actions {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .pac-search-actions {
            grid-template-columns: auto auto;
            justify-content: flex-start;
        }
    }

    @media (min-width: 768px) {
        .pac-header {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }

        .pac-card {
            padding: 24px;
        }

        .pac-search-grid {
            grid-template-columns: minmax(0, 1fr) auto;
        }

        .pac-stats {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .pac-list-title-row {
            flex-direction: row;
            align-items: flex-end;
            justify-content: space-between;
        }

        .pac-mobile-list {
            display: none;
        }

        .pac-desktop-table {
            display: block;
        }
    }

    @media (min-width: 1200px) {
        .pac-content {
            max-width: 1180px;
        }
    }
</style>

@php
    $formatarTelefone = function ($telefone) {
        $telefone = preg_replace('/\D/', '', (string) $telefone);

        if (!$telefone) {
            return '—';
        }

        if (strlen($telefone) === 11) {
            return '(' . substr($telefone, 0, 2) . ') ' .
                   substr($telefone, 2, 5) . '-' .
                   substr($telefone, 7, 4);
        }

        if (strlen($telefone) === 10) {
            return '(' . substr($telefone, 0, 2) . ') ' .
                   substr($telefone, 2, 4) . '-' .
                   substr($telefone, 6, 4);
        }

        return $telefone;
    };

    $formatarCpf = function ($cpf) {
        $cpf = preg_replace('/\D/', '', (string) $cpf);

        if (!$cpf) {
            return '—';
        }

        if (strlen($cpf) === 11) {
            return substr($cpf, 0, 3) . '.' .
                   substr($cpf, 3, 3) . '.' .
                   substr($cpf, 6, 3) . '-' .
                   substr($cpf, 9, 2);
        }

        return $cpf;
    };

    $totalPacientes = method_exists($pacientes, 'total')
        ? $pacientes->total()
        : $pacientes->count();

    $pacientesNestaPagina = method_exists($pacientes, 'count')
        ? $pacientes->count()
        : 0;

    $buscaAtual = request('busca');
@endphp

<div class="container-fluid py-2 pac-page">
    <div class="pac-content">
        <div class="pac-header">
            <div>
                <h1 class="pac-title">Meus pacientes</h1>
                <p class="pac-subtitle">
                    Gerencie cadastros, dados fiscais, histórico clínico e arquivos dos pacientes.
                </p>
            </div>

            <a href="{{ route('pacientes.create') }}" class="btn btn-primary pac-btn">
                <i class="bi bi-plus-circle"></i>
                Novo paciente
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="pac-card pac-search-card">
            <form method="GET" class="pac-search-grid">
                <div class="pac-field">
                    <label for="busca">Buscar paciente</label>
                    <input type="text"
                           id="busca"
                           name="busca"
                           value="{{ $buscaAtual }}"
                           class="form-control"
                           placeholder="Buscar por nome, CPF, telefone ou email">
                </div>

                <div class="pac-search-actions">
                    <button type="submit" class="btn btn-outline-primary pac-btn">
                        <i class="bi bi-search"></i>
                        Buscar
                    </button>

                    <a href="{{ route('pacientes.index') }}" class="btn btn-outline-secondary pac-btn">
                        <i class="bi bi-x-circle"></i>
                        Limpar
                    </a>
                </div>
            </form>

            @if($buscaAtual)
                <div class="mt-3">
                    <span class="pac-badge pac-badge-muted">
                        <i class="bi bi-filter"></i>
                        Filtro ativo: {{ $buscaAtual }}
                    </span>
                </div>
            @endif
        </div>

        <div class="pac-stats">
            <div class="pac-stat">
                <div class="pac-stat-label">Total encontrado</div>
                <p class="pac-stat-value">{{ $totalPacientes }}</p>
                <div class="pac-stat-help">Considerando o filtro aplicado.</div>
            </div>

            <div class="pac-stat">
                <div class="pac-stat-label">Nesta página</div>
                <p class="pac-stat-value">{{ $pacientesNestaPagina }}</p>
                <div class="pac-stat-help">Pacientes exibidos agora.</div>
            </div>

            <div class="pac-stat">
                <div class="pac-stat-label">Receita Saúde</div>
                <p class="pac-stat-value">
                    {{ $pacientes->where('exige_nota_fiscal', true)->count() }}
                </p>
                <div class="pac-stat-help">Marcados nesta página.</div>
            </div>
        </div>

        <div class="pac-card">
            <div class="pac-list-title-row">
                <div>
                    <h2 class="pac-list-title">Lista de pacientes</h2>
                    <p class="pac-list-help">
                        Acesse edição, histórico clínico, arquivos ou remova um cadastro.
                    </p>
                </div>
            </div>

            <div class="pac-mobile-list">
                @forelse($pacientes as $paciente)
                    <div class="pac-patient-card">
                        <div class="pac-patient-head">
                            <div>
                                <p class="pac-patient-name">{{ $paciente->nome }}</p>
                                <div class="pac-patient-meta">
                                    {{ $paciente->email ?: 'Email não informado' }}
                                </div>
                            </div>

                            @if($paciente->exige_nota_fiscal)
                                <span class="pac-badge pac-badge-success">
                                    <i class="bi bi-receipt"></i>
                                    Receita Saúde
                                </span>
                            @else
                                <span class="pac-badge pac-badge-muted">
                                    Sem recibo
                                </span>
                            @endif
                        </div>

                        <div class="pac-info-grid">
                            <div class="pac-info-item">
                                <span class="pac-info-label">Telefone</span>
                                <span class="pac-info-value">
                                    {{ $formatarTelefone($paciente->telefone) }}
                                </span>
                            </div>

                            <div class="pac-info-item">
                                <span class="pac-info-label">CPF</span>
                                <span class="pac-info-value">
                                    {{ $formatarCpf($paciente->cpf) }}
                                </span>
                            </div>
                        </div>

                        <div class="pac-card-actions">
                            <a href="{{ route('pacientes.edit', $paciente->id) }}" class="btn btn-outline-secondary pac-action-btn">
                                <i class="bi bi-pencil-square"></i>
                                Editar
                            </a>

                            <a href="{{ route('pacientes.historico', $paciente->id) }}" class="btn btn-outline-primary pac-action-btn">
                                <i class="bi bi-clock-history"></i>
                                Histórico
                            </a>

                            <a href="{{ route('arquivos.index', $paciente->id) }}" class="btn btn-outline-info pac-action-btn">
                                <i class="bi bi-folder2-open"></i>
                                Arquivos
                            </a>

                            <form action="{{ route('pacientes.destroy', $paciente->id) }}"
                                  method="POST"
                                  class="delete-form no-spinner">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-outline-danger pac-action-btn w-100">
                                    <i class="bi bi-trash"></i>
                                    Excluir
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="pac-empty">
                        <div class="pac-empty-icon">
                            <i class="bi bi-person-x"></i>
                        </div>

                        <h3 class="h5 fw-bold text-dark mb-1">Nenhum paciente encontrado</h3>

                        <p class="mb-3">
                            Tente ajustar a busca ou cadastre um novo paciente.
                        </p>

                        <a href="{{ route('pacientes.create') }}" class="btn btn-primary pac-btn">
                            <i class="bi bi-plus-circle"></i>
                            Novo paciente
                        </a>
                    </div>
                @endforelse
            </div>

            <div class="pac-desktop-table">
                <div class="table-responsive">
                    <table class="table pac-table align-middle">
                        <thead>
                            <tr>
                                <th>Paciente</th>
                                <th>Telefone</th>
                                <th>Email</th>
                                <th>CPF</th>
                                <th>Receita Saúde</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($pacientes as $paciente)
                                <tr>
                                    <td>
                                        <div class="pac-table-name">{{ $paciente->nome }}</div>
                                        <div class="pac-table-sub">ID #{{ $paciente->id }}</div>
                                    </td>

                                    <td>{{ $formatarTelefone($paciente->telefone) }}</td>

                                    <td>{{ $paciente->email ?: '—' }}</td>

                                    <td>{{ $formatarCpf($paciente->cpf) }}</td>

                                    <td>
                                        @if($paciente->exige_nota_fiscal)
                                            <span class="pac-badge pac-badge-success">
                                                <i class="bi bi-receipt"></i>
                                                Sim
                                            </span>
                                        @else
                                            <span class="pac-badge pac-badge-muted">
                                                Não
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="pac-table-actions">
                                            <a href="{{ route('pacientes.edit', $paciente->id) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-pencil-square"></i>
                                                Editar
                                            </a>

                                            <a href="{{ route('pacientes.historico', $paciente->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-clock-history"></i>
                                                Histórico
                                            </a>

                                            <a href="{{ route('arquivos.index', $paciente->id) }}" class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-folder2-open"></i>
                                                Arquivos
                                            </a>

                                            <form action="{{ route('pacientes.destroy', $paciente->id) }}"
                                                  method="POST"
                                                  class="delete-form no-spinner d-inline">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                    Excluir
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="pac-empty">
                                            <div class="pac-empty-icon">
                                                <i class="bi bi-person-x"></i>
                                            </div>

                                            <h3 class="h5 fw-bold text-dark mb-1">Nenhum paciente encontrado</h3>

                                            <p class="mb-3">
                                                Tente ajustar a busca ou cadastre um novo paciente.
                                            </p>

                                            <a href="{{ route('pacientes.create') }}" class="btn btn-primary pac-btn">
                                                <i class="bi bi-plus-circle"></i>
                                                Novo paciente
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pac-pagination">
                {{ $pacientes->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.delete-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Excluir paciente?',
                text: 'Essa ação não poderá ser desfeita.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sim, excluir',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (typeof showSpinner === 'function') {
                        showSpinner();
                    }

                    form.submit();
                }
            });
        });
    });
});
</script>
@endsection