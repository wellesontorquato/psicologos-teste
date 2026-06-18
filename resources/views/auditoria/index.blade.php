@extends('layouts.app')

@section('title', 'Auditoria | PsiGestor')

@section('content')
<style>
    .aud-page {
        width: 100%;
    }

    .aud-content {
        width: 100%;
        max-width: 100%;
    }

    .aud-header {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-bottom: 22px;
    }

    .aud-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .aud-subtitle {
        color: #64748b;
        margin: 4px 0 0;
        font-size: .95rem;
    }

    .aud-header-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .aud-btn {
        min-height: 44px;
        border-radius: 14px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        white-space: nowrap;
        padding: 0 16px;
    }

    .aud-card {
        background: rgba(255,255,255,.94);
        border: 1px solid rgba(226,232,240,.95);
        border-radius: 22px;
        box-shadow: 0 14px 38px rgba(15,23,42,.08);
        padding: 18px;
        margin-bottom: 18px;
    }

    .aud-search-card {
        background: linear-gradient(135deg, rgba(255,255,255,.96), rgba(248,250,252,.96));
    }

    .aud-search-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
        align-items: end;
    }

    .aud-field label {
        font-size: .86rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
        display: block;
    }

    .aud-field .form-control,
    .aud-field .form-select {
        min-height: 44px;
        border-radius: 14px;
        border-color: #dbe3ef;
        font-size: .95rem;
        color: #0f172a;
        background-color: #fff;
    }

    .aud-field .form-control:focus,
    .aud-field .form-select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 .2rem rgba(37,99,235,.12);
    }

    .aud-search-actions {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
        margin-top: 4px;
    }

    .aud-list-title-row {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 14px;
    }

    .aud-list-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .aud-list-help {
        color: #64748b;
        font-size: .9rem;
        margin: 0;
    }

    .aud-mobile-list {
        display: grid;
        grid-template-columns: 1fr;
        gap: 14px;
    }

    .aud-item-card {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        background: #ffffff;
        padding: 16px;
    }

    .aud-item-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 14px;
    }

    .aud-item-title {
        font-size: 1rem;
        font-weight: 900;
        color: #0f172a;
        margin: 0;
        line-height: 1.25;
    }

    .aud-item-meta {
        color: #64748b;
        font-size: .84rem;
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .aud-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 6px 10px;
        font-size: .76rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .aud-badge-primary { background: #e0f2fe; color: #0369a1; }
    .aud-badge-muted { background: #f1f5f9; color: #475569; }

    .aud-info-box {
        background: #f8fafc;
        border-radius: 16px;
        padding: 12px 14px;
        margin-bottom: 10px;
    }

    .aud-info-label {
        display: block;
        color: #64748b;
        font-size: .74rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .03em;
        margin-bottom: 4px;
    }

    .aud-info-value {
        display: block;
        color: #0f172a;
        font-size: .9rem;
        line-height: 1.4;
    }

    .aud-desktop-table {
        display: none;
    }

    .aud-table-wrapper {
        width: 100%;
        overflow-x: auto;
        border-radius: 16px;
    }

    .aud-table {
        width: 100%;
        min-width: 1000px;
        margin-bottom: 0;
        table-layout: fixed;
    }

    .aud-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: .76rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        white-space: nowrap;
        border-bottom: 1px solid #e2e8f0;
        padding: 14px 12px;
    }

    .aud-table tbody td {
        vertical-align: middle;
        padding: 14px 12px;
        color: #0f172a;
        border-bottom: 1px solid #edf2f7;
    }

    .aud-table tbody tr:hover {
        background: #f8fafc;
    }

    .aud-col-user { width: 20%; }
    .aud-col-action { width: 15%; }
    .aud-col-desc { width: 35%; }
    .aud-col-ip { width: 15%; }
    .aud-col-date { width: 15%; }

    .aud-table-name {
        font-weight: 900;
        color: #0f172a;
        line-height: 1.25;
    }

    .aud-nowrap { white-space: nowrap; }

    .aud-empty {
        text-align: center;
        padding: 44px 18px;
        color: #64748b;
    }

    .aud-empty-icon {
        width: 54px;
        height: 54px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        color: #475569;
        font-size: 1.5rem;
        margin-bottom: 12px;
    }

    .aud-pagination { margin-top: 18px; }

    @media (min-width: 576px) {
        .aud-search-actions {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (min-width: 768px) {
        .aud-header {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }
        .aud-card {
            padding: 24px;
        }
        .aud-search-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .aud-search-actions {
            grid-column: 1 / -1;
            grid-template-columns: auto auto;
            justify-content: flex-end;
        }
        .aud-list-title-row {
            flex-direction: row;
            align-items: flex-end;
            justify-content: space-between;
        }
        .aud-mobile-list { display: none; }
        .aud-desktop-table { display: block; }
    }

    @media (min-width: 1200px) {
        .aud-search-grid {
            grid-template-columns: 2fr 1.5fr 1fr 1fr auto;
        }
        .aud-search-actions {
            grid-column: auto;
            margin-top: 0;
        }
    }
</style>

@php
    $filtrosAtivos = array_filter([request('user_id'), request('action'), request('de'), request('ate')]);
@endphp

<div class="container-fluid py-2 aud-page">
    <div class="aud-content">
        
        <div class="aud-header">
            <div>
                <h1 class="aud-title">Logs de Auditoria</h1>
                <p class="aud-subtitle">Acompanhe as ações, registros de sistema e acessos na plataforma.</p>
            </div>

            <div class="aud-header-actions">
                <a href="{{ route('admin.auditoria.exportar.pdf', request()->query()) }}" class="btn btn-outline-danger aud-btn no-spinner">
                    <i class="bi bi-file-earmark-pdf"></i> PDF
                </a>
                <a href="{{ route('admin.auditoria.exportar.excel', request()->query()) }}" class="btn btn-outline-success aud-btn no-spinner">
                    <i class="bi bi-file-earmark-excel"></i> Excel
                </a>
            </div>
        </div>

        <div class="aud-card aud-search-card">
            <form method="GET" class="aud-search-grid">
                
                <div class="aud-field">
                    <label for="user_id">Usuário</label>
                    <select name="user_id" id="user_id" class="form-select">
                        <option value="">-- Todos os usuários --</option>
                        @foreach ($usuarios as $usuario)
                            <option value="{{ $usuario->id }}" {{ request('user_id') == $usuario->id ? 'selected' : '' }}>
                                {{ $usuario->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="aud-field">
                    <label for="action">Ação / Módulo</label>
                    <input type="text" id="action" name="action" value="{{ request('action') }}" 
                           class="form-control" placeholder="Ex: login, update...">
                </div>

                <div class="aud-field">
                    <label for="de">Data Inicial</label>
                    <input type="date" id="de" name="de" value="{{ request('de') }}" class="form-control">
                </div>

                <div class="aud-field">
                    <label for="ate">Data Final</label>
                    <input type="date" id="ate" name="ate" value="{{ request('ate') }}" class="form-control">
                </div>

                <div class="aud-search-actions">
                    <button type="submit" class="btn btn-outline-primary aud-btn w-100">
                        <i class="bi bi-funnel"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.auditoria.index') }}" class="btn btn-outline-secondary aud-btn w-100">
                        <i class="bi bi-x-circle"></i> Limpar
                    </a>
                </div>
            </form>

            @if(!empty($filtrosAtivos))
                <div class="mt-3">
                    <span class="aud-badge aud-badge-muted">
                        <i class="bi bi-filter"></i> Filtros aplicados na busca
                    </span>
                </div>
            @endif
        </div>

        <div class="aud-card">
            <div class="aud-list-title-row">
                <div>
                    <h2 class="aud-list-title">Lista de Registros</h2>
                    <p class="aud-list-help">Histórico completo baseado no período e filtros selecionados.</p>
                </div>
            </div>

            {{-- Mobile View --}}
            <div class="aud-mobile-list">
                @forelse ($audits as $log)
                    <div class="aud-item-card">
                        <div class="aud-item-head">
                            <div>
                                <p class="aud-item-title">
                                    <span class="aud-badge aud-badge-primary">
                                        <i class="bi bi-person"></i> {{ $log->user->name ?? 'Desconhecido' }}
                                    </span>
                                </p>
                                <div class="aud-item-meta">
                                    <i class="bi bi-calendar3"></i> 
                                    {{ $log->created_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>

                        <div class="aud-info-box">
                            <span class="aud-info-label">Ação Realizada</span>
                            <span class="aud-info-value fw-bold text-dark">{{ $log->action }}</span>
                        </div>

                        @if($log->description)
                            <div class="aud-info-box">
                                <span class="aud-info-label">Descrição</span>
                                <span class="aud-info-value">{{ $log->description }}</span>
                            </div>
                        @endif

                        <div class="aud-info-box mb-0">
                            <span class="aud-info-label">Endereço IP</span>
                            <span class="aud-info-value"><i class="bi bi-router text-muted"></i> {{ $log->ip_address }}</span>
                        </div>
                    </div>
                @empty
                    <div class="aud-empty">
                        <div class="aud-empty-icon"><i class="bi bi-shield-x"></i></div>
                        <h3 class="h5 fw-bold text-dark mb-1">Nenhum log encontrado</h3>
                        <p class="mb-0">Não há registros para os filtros selecionados.</p>
                    </div>
                @endforelse
            </div>

            {{-- Desktop View --}}
            <div class="aud-desktop-table">
                <div class="aud-table-wrapper">
                    <table class="table aud-table align-middle">
                        <colgroup>
                            <col class="aud-col-user">
                            <col class="aud-col-action">
                            <col class="aud-col-desc">
                            <col class="aud-col-ip">
                            <col class="aud-col-date">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>Usuário</th>
                                <th>Ação</th>
                                <th>Descrição</th>
                                <th>IP</th>
                                <th>Data / Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($audits as $log)
                                <tr>
                                    <td>
                                        <span class="aud-badge aud-badge-primary">
                                            {{ $log->user->name ?? 'Desconhecido' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark aud-nowrap">{{ $log->action }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $log->description ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="aud-nowrap text-secondary">
                                            <i class="bi bi-router small"></i> {{ $log->ip_address }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="aud-nowrap">
                                            {{ $log->created_at->format('d/m/Y H:i') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="aud-empty">
                                            <div class="aud-empty-icon"><i class="bi bi-shield-x"></i></div>
                                            <h3 class="h5 fw-bold text-dark mb-1">Nenhum log encontrado</h3>
                                            <p class="mb-0">Não há registros para os filtros selecionados.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="aud-pagination">
                {{ $audits->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection