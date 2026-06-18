@extends('layouts.app')

@section('title', 'Evoluções | PsiGestor')

@section('content')
<style>
    .evo-page {
        width: 100%;
    }

    .evo-content {
        width: 100%;
        max-width: 100%;
    }

    .evo-header {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-bottom: 22px;
    }

    .evo-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .evo-subtitle {
        color: #64748b;
        margin: 4px 0 0;
        font-size: .95rem;
    }

    .evo-btn {
        min-height: 44px;
        border-radius: 14px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        white-space: nowrap;
    }

    .evo-card {
        background: rgba(255,255,255,.94);
        border: 1px solid rgba(226,232,240,.95);
        border-radius: 22px;
        box-shadow: 0 14px 38px rgba(15,23,42,.08);
        padding: 18px;
        margin-bottom: 18px;
    }

    .evo-search-card {
        background: linear-gradient(135deg, rgba(255,255,255,.96), rgba(248,250,252,.96));
    }

    .evo-search-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
        align-items: end;
    }

    .evo-field label {
        font-size: .86rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
        display: block;
    }

    .evo-field .form-control,
    .evo-field .form-select {
        min-height: 44px;
        border-radius: 14px;
        border-color: #dbe3ef;
        font-size: .95rem;
        color: #0f172a;
        background-color: #fff;
    }

    .evo-field .form-control:focus,
    .evo-field .form-select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 .2rem rgba(37,99,235,.12);
    }

    .evo-search-actions {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
        margin-top: 4px;
    }

    .evo-stats {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
        margin-bottom: 18px;
    }

    .evo-stat {
        background: rgba(255,255,255,.94);
        border: 1px solid rgba(226,232,240,.95);
        border-radius: 20px;
        box-shadow: 0 10px 28px rgba(15,23,42,.06);
        padding: 16px;
    }

    .evo-stat-label {
        color: #64748b;
        font-size: .82rem;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .evo-stat-value {
        color: #020617;
        font-size: 1.8rem;
        line-height: 1;
        font-weight: 900;
        margin: 0;
    }

    .evo-stat-help {
        color: #64748b;
        font-size: .82rem;
        margin-top: 6px;
    }

    .evo-list-title-row {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 14px;
    }

    .evo-list-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .evo-list-help {
        color: #64748b;
        font-size: .9rem;
        margin: 0;
    }

    .evo-mobile-list {
        display: grid;
        grid-template-columns: 1fr;
        gap: 14px;
    }

    .evo-item-card {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        background: #ffffff;
        padding: 16px;
    }

    .evo-item-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 14px;
    }

    .evo-item-title {
        font-size: 1rem;
        font-weight: 900;
        color: #0f172a;
        margin: 0;
        line-height: 1.25;
    }

    .evo-item-meta {
        color: #64748b;
        font-size: .84rem;
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .evo-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 6px 10px;
        font-size: .76rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .evo-badge-success { background: #dcfce7; color: #166534; }
    .evo-badge-warning { background: #fef3c7; color: #92400e; }
    .evo-badge-muted { background: #f1f5f9; color: #475569; }

    .evo-info-box {
        background: #f8fafc;
        border-radius: 16px;
        padding: 12px 14px;
        margin-bottom: 14px;
    }

    .evo-info-label {
        display: block;
        color: #64748b;
        font-size: .74rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .03em;
        margin-bottom: 4px;
    }

    .evo-info-value {
        display: block;
        color: #0f172a;
        font-size: .9rem;
        line-height: 1.4;
    }

    .evo-card-actions {
        display: grid;
        grid-template-columns: 1fr;
        gap: 8px;
    }

    .evo-action-btn {
        min-height: 40px;
        border-radius: 12px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        white-space: nowrap;
        font-size: .85rem;
    }

    .evo-desktop-table {
        display: none;
    }

    .evo-table-wrapper {
        width: 100%;
        overflow-x: auto;
        border-radius: 16px;
    }

    .evo-table {
        width: 100%;
        min-width: 1000px;
        margin-bottom: 0;
        table-layout: fixed;
    }

    .evo-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: .76rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        white-space: nowrap;
        border-bottom: 1px solid #e2e8f0;
        padding: 14px 12px;
    }

    .evo-table tbody td {
        vertical-align: middle;
        padding: 14px 12px;
        color: #0f172a;
        border-bottom: 1px solid #edf2f7;
    }

    .evo-table tbody tr:hover {
        background: #f8fafc;
    }

    .evo-col-paciente { width: 20%; }
    .evo-col-data { width: 12%; }
    .evo-col-sessao { width: 18%; }
    .evo-col-anotacao { width: 26%; }
    .evo-col-acoes { width: 24%; }

    .evo-table-name {
        font-weight: 900;
        color: #0f172a;
        line-height: 1.25;
    }

    .evo-nowrap { white-space: nowrap; }

    .evo-ellipsis {
        display: block;
        max-width: 100%;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .evo-table-actions {
        display: flex;
        flex-wrap: nowrap;
        gap: 6px;
        justify-content: flex-end;
        align-items: center;
    }

    .evo-table-actions .btn {
        border-radius: 10px;
        font-weight: 700;
        padding: 6px 9px;
        font-size: .78rem;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .evo-table-actions form { margin: 0; }

    .evo-empty {
        text-align: center;
        padding: 44px 18px;
        color: #64748b;
    }

    .evo-empty-icon {
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

    .evo-pagination { margin-top: 18px; }

    @media (min-width: 576px) {
        .evo-card-actions {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
        .evo-search-actions {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (min-width: 768px) {
        .evo-header {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }
        .evo-card {
            padding: 24px;
        }
        .evo-search-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .evo-search-actions {
            grid-column: 1 / -1;
            grid-template-columns: auto auto;
            justify-content: flex-end;
        }
        .evo-list-title-row {
            flex-direction: row;
            align-items: flex-end;
            justify-content: space-between;
        }
        .evo-mobile-list { display: none; }
        .evo-desktop-table { display: block; }
    }

    @media (min-width: 992px) {
        .evo-search-grid {
            grid-template-columns: 2.5fr 1.5fr 1.5fr 2fr auto;
        }
        .evo-search-actions {
            grid-column: auto;
            margin-top: 0;
        }
        .evo-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 1400px) {
        .evo-table-actions .btn {
            padding: 7px 10px;
            font-size: .8rem;
        }
    }
</style>

@php
    $totalEvolucoes = method_exists($evolucoes, 'total') 
        ? $evolucoes->total() 
        : $evolucoes->count();

    $evolucoesNestaPagina = method_exists($evolucoes, 'count') 
        ? $evolucoes->count() 
        : 0;

    $buscaAtual = request('busca');
    $filtrosAtivos = array_filter([request('busca'), request('periodo'), request('tipo'), request('sessao')]);
@endphp

<div class="container-fluid py-2 evo-page">
    <div class="evo-content">
        
        <div class="evo-header">
            <div>
                <h1 class="evo-title">Evoluções</h1>
                <p class="evo-subtitle">
                    Acompanhe e gerencie os registros clínicos e medicações dos seus pacientes.
                </p>
            </div>

            <a href="{{ route('evolucoes.create') }}" class="btn btn-primary evo-btn">
                <i class="bi bi-plus-circle"></i>
                Nova Evolução
            </a>
        </div>

        <div class="evo-card evo-search-card">
            <form method="GET" class="evo-search-grid">
                
                <div class="evo-field">
                    <label for="busca">Paciente</label>
                    <input type="text" id="busca" name="busca" value="{{ request('busca') }}" 
                           class="form-control" placeholder="Buscar por nome">
                </div>

                <div class="evo-field">
                    <label for="periodo">Período</label>
                    <select name="periodo" id="periodo" class="form-select">
                        <option value="">Todos</option>
                        <option value="hoje" {{ request('periodo') == 'hoje' ? 'selected' : '' }}>Hoje</option>
                        <option value="semana" {{ request('periodo') == 'semana' ? 'selected' : '' }}>Esta semana</option>
                        <option value="mes" {{ request('periodo') == 'mes' ? 'selected' : '' }}>Este mês</option>
                    </select>
                </div>

                <div class="evo-field">
                    <label for="tipo">Tipo</label>
                    <select name="tipo" id="tipo" class="form-select">
                        <option value="">Todos</option>
                        <option value="evolucao" {{ request('tipo') == 'evolucao' ? 'selected' : '' }}>Evolução</option>
                        <option value="medicacao" {{ request('tipo') == 'medicacao' ? 'selected' : '' }}>Medicação</option>
                    </select>
                </div>

                <div class="evo-field">
                    <label for="sessao">Vínculo com Sessão</label>
                    <select name="sessao" id="sessao" class="form-select">
                        <option value="">Todas</option>
                        <option value="com" {{ request('sessao') == 'com' ? 'selected' : '' }}>Com Sessão</option>
                        <option value="sem" {{ request('sessao') == 'sem' ? 'selected' : '' }}>Pendentes (Sem Sessão)</option>
                    </select>
                </div>

                <div class="evo-search-actions">
                    <button type="submit" class="btn btn-outline-primary evo-btn w-100">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('evolucoes.index') }}" class="btn btn-outline-secondary evo-btn w-100">
                        <i class="bi bi-x-circle"></i>
                    </a>
                </div>
            </form>

            @if(!empty($filtrosAtivos))
                <div class="mt-3">
                    <span class="evo-badge evo-badge-muted">
                        <i class="bi bi-filter"></i> Filtros ativos
                    </span>
                </div>
            @endif
        </div>

        <div class="evo-stats">
            <div class="evo-stat">
                <div class="evo-stat-label">Total de evoluções</div>
                <p class="evo-stat-value">{{ $totalEvolucoes }}</p>
                <div class="evo-stat-help">Registros encontrados na busca.</div>
            </div>

            <div class="evo-stat">
                <div class="evo-stat-label">Nesta página</div>
                <p class="evo-stat-value">{{ $evolucoesNestaPagina }}</p>
                <div class="evo-stat-help">Registros exibidos agora.</div>
            </div>
        </div>

        <div class="evo-card">
            <div class="evo-list-title-row">
                <div>
                    <h2 class="evo-list-title">Histórico de Registros</h2>
                    <p class="evo-list-help">Visualize as anotações, edite detalhes ou imprima os registros.</p>
                </div>
            </div>

            {{-- Mobile View --}}
            <div class="evo-mobile-list">
                @forelse($evolucoes as $evolucao)
                    <div class="evo-item-card">
                        <div class="evo-item-head">
                            <div>
                                <p class="evo-item-title">{{ $evolucao->paciente->nome }}</p>
                                <div class="evo-item-meta">
                                    <i class="bi bi-calendar3"></i> 
                                    {{ \Carbon\Carbon::parse($evolucao->data)->format('d/m/Y') }}
                                </div>
                            </div>

                            @if($evolucao->sessao)
                                <span class="evo-badge evo-badge-success">
                                    <i class="bi bi-check-circle"></i> Vinculada
                                </span>
                            @else
                                <span class="evo-badge evo-badge-warning">
                                    <i class="bi bi-exclamation-circle"></i> Pendente
                                </span>
                            @endif
                        </div>

                        <div class="evo-info-box">
                            <span class="evo-info-label">Anotação Clínica</span>
                            <span class="evo-info-value">{{ Str::limit($evolucao->texto, 100) }}</span>
                        </div>

                        <div class="evo-card-actions">
                            <a href="{{ route('evolucoes.edit', $evolucao) }}" class="btn btn-outline-secondary evo-action-btn">
                                <i class="bi bi-pencil-square"></i> Editar
                            </a>

                            <a href="{{ route('evolucoes.print', $evolucao) }}" target="_blank" rel="noopener" class="btn btn-outline-info evo-action-btn">
                                <i class="bi bi-printer"></i> Imprimir
                            </a>

                            <form action="{{ route('evolucoes.destroy', $evolucao) }}" method="POST" class="delete-form no-spinner">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger evo-action-btn w-100">
                                    <i class="bi bi-trash"></i> Excluir
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="evo-empty">
                        <div class="evo-empty-icon"><i class="bi bi-journal-x"></i></div>
                        <h3 class="h5 fw-bold text-dark mb-1">Nenhuma evolução encontrada</h3>
                        <p class="mb-3">Tente ajustar os filtros ou adicione um novo registro.</p>
                        <a href="{{ route('evolucoes.create') }}" class="btn btn-primary evo-btn">
                            <i class="bi bi-plus-circle"></i> Nova Evolução
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- Desktop View --}}
            <div class="evo-desktop-table">
                <div class="evo-table-wrapper">
                    <table class="table evo-table align-middle">
                        <colgroup>
                            <col class="evo-col-paciente">
                            <col class="evo-col-data">
                            <col class="evo-col-sessao">
                            <col class="evo-col-anotacao">
                            <col class="evo-col-acoes">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>Paciente</th>
                                <th>Data</th>
                                <th>Sessão</th>
                                <th>Anotação</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($evolucoes as $evolucao)
                                <tr>
                                    <td>
                                        <div class="evo-table-name">{{ $evolucao->paciente->nome }}</div>
                                    </td>
                                    <td>
                                        <span class="evo-nowrap">{{ \Carbon\Carbon::parse($evolucao->data)->format('d/m/Y') }}</span>
                                    </td>
                                    <td>
                                        @if($evolucao->sessao)
                                            <span class="evo-badge evo-badge-success">
                                                <i class="bi bi-clock"></i> 
                                                {{ $evolucao->sessao->data_hora->format('d/m/Y H:i') }}
                                            </span>
                                        @else
                                            <span class="evo-badge evo-badge-warning">
                                                Sem vínculo
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="evo-ellipsis" title="{{ $evolucao->texto }}">
                                            {{ Str::limit($evolucao->texto, 50) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="evo-table-actions">
                                            <a href="{{ route('evolucoes.edit', $evolucao) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-pencil-square"></i> Editar
                                            </a>
                                            <a href="{{ route('evolucoes.print', $evolucao) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-printer"></i> Imprimir
                                            </a>
                                            <form action="{{ route('evolucoes.destroy', $evolucao) }}" method="POST" class="delete-form no-spinner d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i> Excluir
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="evo-empty">
                                            <div class="evo-empty-icon"><i class="bi bi-journal-x"></i></div>
                                            <h3 class="h5 fw-bold text-dark mb-1">Nenhuma evolução encontrada</h3>
                                            <p class="mb-3">Tente ajustar os filtros ou adicione um novo registro.</p>
                                            <a href="{{ route('evolucoes.create') }}" class="btn btn-primary evo-btn">
                                                <i class="bi bi-plus-circle"></i> Nova Evolução
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="evo-pagination">
                {{ $evolucoes->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: '{{ session('success') }}',
            timer: 3000,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            background: '#fff',
            color: '#0f172a'
        });
    @endif

    document.querySelectorAll('.delete-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Excluir evolução?',
                text: 'Essa ação não poderá ser desfeita.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sim, excluir',
                cancelButtonText: 'Cancelar',
                customClass: {
                    popup: 'evo-swal-popup'
                }
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