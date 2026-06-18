@extends('layouts.app')

@section('title', 'Lista de Notícias | PsiGestor')

@section('content')
<style>
    .news-page {
        width: 100%;
    }

    .news-content {
        width: 100%;
        max-width: 100%;
    }

    .news-header {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-bottom: 22px;
    }

    .news-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .news-subtitle {
        color: #64748b;
        margin: 4px 0 0;
        font-size: .95rem;
    }

    .news-btn {
        min-height: 44px;
        border-radius: 14px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        white-space: nowrap;
        padding: 0 20px;
    }

    .news-card {
        background: rgba(255,255,255,.94);
        border: 1px solid rgba(226,232,240,.95);
        border-radius: 22px;
        box-shadow: 0 14px 38px rgba(15,23,42,.08);
        padding: 18px;
        margin-bottom: 18px;
    }

    .news-search-card {
        background: linear-gradient(135deg, rgba(255,255,255,.96), rgba(248,250,252,.96));
    }

    .news-search-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
        align-items: end;
    }

    .news-field label {
        font-size: .86rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
        display: block;
    }

    .news-field .form-control,
    .news-field .form-select {
        min-height: 44px;
        border-radius: 14px;
        border-color: #dbe3ef;
        font-size: .95rem;
        color: #0f172a;
        background-color: #fff;
    }

    .news-field .form-control:focus,
    .news-field .form-select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 .2rem rgba(37,99,235,.12);
    }

    .news-search-actions {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
        margin-top: 4px;
    }

    .news-list-title-row {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 14px;
    }

    .news-list-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .news-list-help {
        color: #64748b;
        font-size: .9rem;
        margin: 0;
    }

    .news-mobile-list {
        display: grid;
        grid-template-columns: 1fr;
        gap: 14px;
    }

    .news-item-card {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        background: #ffffff;
        padding: 16px;
    }

    .news-item-head {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 14px;
    }

    .news-item-img {
        width: 64px;
        height: 64px;
        border-radius: 14px;
        object-fit: cover;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        flex-shrink: 0;
    }

    .news-item-img-placeholder {
        width: 64px;
        height: 64px;
        border-radius: 14px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .news-item-title {
        font-size: 1rem;
        font-weight: 900;
        color: #0f172a;
        margin: 0;
        line-height: 1.25;
    }

    .news-item-meta {
        color: #64748b;
        font-size: .84rem;
        margin-top: 4px;
    }

    .news-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 6px 10px;
        font-size: .76rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .news-badge-primary { background: #eff6ff; color: #1d4ed8; }
    .news-badge-muted { background: #f1f5f9; color: #475569; }

    .news-info-box {
        background: #f8fafc;
        border-radius: 16px;
        padding: 12px 14px;
        margin-bottom: 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .news-info-col {
        display: flex;
        flex-direction: column;
    }

    .news-info-label {
        color: #64748b;
        font-size: .74rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .03em;
        margin-bottom: 2px;
    }

    .news-info-value {
        color: #0f172a;
        font-size: .85rem;
        font-weight: 600;
    }

    .news-card-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }

    .news-action-btn {
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

    .news-desktop-table {
        display: none;
    }

    .news-table-wrapper {
        width: 100%;
        overflow-x: auto;
        border-radius: 16px;
    }

    .news-table {
        width: 100%;
        min-width: 1000px;
        margin-bottom: 0;
        table-layout: fixed;
    }

    .news-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: .76rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        white-space: nowrap;
        border-bottom: 1px solid #e2e8f0;
        padding: 14px 12px;
    }

    .news-table tbody td {
        vertical-align: middle;
        padding: 14px 12px;
        color: #0f172a;
        border-bottom: 1px solid #edf2f7;
    }

    .news-table tbody tr:hover {
        background: #f8fafc;
    }

    .news-col-img { width: 80px; }
    .news-col-title { width: 25%; }
    .news-col-sub { width: 20%; }
    .news-col-cat { width: 15%; }
    .news-col-date { width: 12%; }
    .news-col-edit { width: 12%; }
    .news-col-actions { width: 120px; }

    .news-table-name {
        font-weight: 900;
        color: #0f172a;
        line-height: 1.25;
        margin-bottom: 2px;
    }

    .news-ellipsis {
        display: block;
        max-width: 100%;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .news-table-actions {
        display: flex;
        flex-wrap: nowrap;
        gap: 6px;
        justify-content: flex-end;
        align-items: center;
    }

    .news-table-actions .btn {
        border-radius: 10px;
        font-weight: 700;
        padding: 6px 9px;
        font-size: .78rem;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .news-table-actions form { margin: 0; }

    .news-empty {
        text-align: center;
        padding: 44px 18px;
        color: #64748b;
    }

    .news-empty-icon {
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

    .news-pagination { margin-top: 18px; }

    @media (min-width: 576px) {
        .news-search-actions {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (min-width: 768px) {
        .news-header {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }
        .news-card {
            padding: 24px;
        }
        .news-search-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .news-search-actions {
            grid-column: 1 / -1;
            grid-template-columns: auto auto;
            justify-content: flex-end;
        }
        .news-list-title-row {
            flex-direction: row;
            align-items: flex-end;
            justify-content: space-between;
        }
        .news-mobile-list { display: none; }
        .news-desktop-table { display: block; }
    }

    @media (min-width: 992px) {
        .news-search-grid {
            grid-template-columns: 2fr 1.5fr 1fr 1fr auto;
        }
        .news-search-actions {
            grid-column: auto;
            margin-top: 0;
        }
    }
</style>

@php
    $categorias = ['Saúde Mental', 'Bem-estar', 'Psicologia Clínica', 'Tecnologia', 'Atualidades', 'Sociedade', 'Carreira & Trabalho', 'PsiGestor'];
    $filtrosAtivos = array_filter([request('search'), request('category'), request('start_date'), request('end_date')]);
@endphp

<div class="container-fluid py-2 news-page">
    <div class="news-content">
        
        <div class="news-header">
            <div>
                <h1 class="news-title">Notícias do Blog</h1>
                <p class="news-subtitle">Gerencie os artigos, publicações e atualizações de conteúdo da plataforma.</p>
            </div>

            <a href="{{ route('admin.news.create') }}" class="btn btn-primary news-btn">
                <i class="bi bi-plus-circle"></i>
                Nova Notícia
            </a>
        </div>

        <div class="news-card news-search-card">
            <form method="GET" action="{{ route('admin.news.index') }}" class="news-search-grid">
                
                <div class="news-field">
                    <label for="search">Buscar Notícia</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" 
                           class="form-control" placeholder="Buscar por título...">
                </div>

                <div class="news-field">
                    <label for="category">Categoria</label>
                    <select name="category" id="category" class="form-select">
                        <option value="">Todas as categorias</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="news-field">
                    <label for="start_date">Data Inicial</label>
                    <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                </div>

                <div class="news-field">
                    <label for="end_date">Data Final</label>
                    <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                </div>

                <div class="news-search-actions">
                    <button type="submit" class="btn btn-outline-primary news-btn w-100">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary news-btn w-100">
                        <i class="bi bi-x-circle"></i>
                    </a>
                </div>
            </form>

            @if(!empty($filtrosAtivos))
                <div class="mt-3">
                    <span class="news-badge news-badge-muted">
                        <i class="bi bi-filter"></i> Filtros aplicados na busca
                    </span>
                </div>
            @endif
        </div>

        <div class="news-card">
            <div class="news-list-title-row">
                <div>
                    <h2 class="news-list-title">Lista de Publicações</h2>
                    <p class="news-list-help">Edite ou remova as notícias publicadas no sistema.</p>
                </div>
            </div>

            {{-- Mobile View --}}
            <div class="news-mobile-list">
                @forelse ($news as $article)
                    <div class="news-item-card">
                        <div class="news-item-head">
                            @if ($article->image)
                                @php
                                    $webp = $article->image_webp_url;
                                    $orig = $article->image_url;
                                @endphp
                                <picture>
                                    @if($webp)
                                        <source srcset="{{ $webp }}" type="image/webp">
                                    @endif
                                    <img src="{{ $orig }}" alt="{{ $article->title }}" loading="lazy" class="news-item-img">
                                </picture>
                            @else
                                <div class="news-item-img-placeholder">
                                    <i class="bi bi-image"></i>
                                </div>
                            @endif

                            <div>
                                <p class="news-item-title">{{ Str::limit($article->title, 40) }}</p>
                                <div class="news-item-meta">
                                    <span class="news-badge news-badge-primary">
                                        {{ $article->category ?? 'Sem categoria' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="news-info-box">
                            <div class="news-info-col">
                                <span class="news-info-label">Criado em</span>
                                <span class="news-info-value"><i class="bi bi-calendar3"></i> {{ $article->created_at->format('d/m/Y') }}</span>
                            </div>
                            <div class="news-info-col text-end">
                                <span class="news-info-label">Última Edição</span>
                                <span class="news-info-value text-muted">
                                    @if($article->updated_at->ne($article->created_at))
                                        {{ $article->updated_at->diffForHumans() }}
                                    @else
                                        —
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="news-card-actions">
                            <a href="{{ route('admin.news.edit', $article) }}" class="btn btn-outline-secondary news-action-btn">
                                <i class="bi bi-pencil-square"></i> Editar
                            </a>

                            <form action="{{ route('admin.news.destroy', $article) }}" method="POST" class="delete-form no-spinner">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger news-action-btn w-100">
                                    <i class="bi bi-trash"></i> Excluir
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="news-empty">
                        <div class="news-empty-icon"><i class="bi bi-newspaper"></i></div>
                        <h3 class="h5 fw-bold text-dark mb-1">Nenhuma notícia encontrada</h3>
                        <p class="mb-3">Tente ajustar a busca ou publique um novo artigo.</p>
                        <a href="{{ route('admin.news.create') }}" class="btn btn-primary news-btn">
                            <i class="bi bi-plus-circle"></i> Nova Notícia
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- Desktop View --}}
            <div class="news-desktop-table">
                <div class="news-table-wrapper">
                    <table class="table news-table align-middle">
                        <colgroup>
                            <col class="news-col-img">
                            <col class="news-col-title">
                            <col class="news-col-sub">
                            <col class="news-col-cat">
                            <col class="news-col-date">
                            <col class="news-col-edit">
                            <col class="news-col-actions">
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="text-center">Imagem</th>
                                <th>Título</th>
                                <th>Subtítulo</th>
                                <th class="text-center">Categoria</th>
                                <th class="text-center">Criado em</th>
                                <th class="text-center">Última edição</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($news as $article)
                                <tr>
                                    <td class="text-center">
                                        @if ($article->image)
                                            @php
                                                $webp = $article->image_webp_url;
                                                $orig = $article->image_url;
                                            @endphp
                                            <picture>
                                                @if($webp)
                                                    <source srcset="{{ $webp }}" type="image/webp">
                                                @endif
                                                <img src="{{ $orig }}" alt="{{ $article->title }}" loading="lazy" class="news-item-img" style="width: 48px; height: 48px; border-radius: 10px;">
                                            </picture>
                                        @else
                                            <div class="news-item-img-placeholder" style="width: 48px; height: 48px; border-radius: 10px; font-size: 1.2rem; margin: 0 auto;">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="news-table-name news-ellipsis" title="{{ $article->title }}">
                                            {{ $article->title }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="news-ellipsis text-muted" title="{{ $article->subtitle }}">
                                            {{ $article->subtitle ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="news-badge news-badge-primary">
                                            {{ $article->category ?? 'Sem categoria' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="news-nowrap">
                                            {{ $article->created_at->format('d/m/Y') }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="news-nowrap text-muted">
                                            @if($article->updated_at->ne($article->created_at))
                                                {{ $article->updated_at->diffForHumans() }}
                                            @else
                                                —
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <div class="news-table-actions">
                                            <a href="{{ route('admin.news.edit', $article) }}" class="btn btn-sm btn-outline-secondary" title="Editar">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('admin.news.destroy', $article) }}" method="POST" class="delete-form no-spinner d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="news-empty">
                                            <div class="news-empty-icon"><i class="bi bi-newspaper"></i></div>
                                            <h3 class="h5 fw-bold text-dark mb-1">Nenhuma notícia encontrada</h3>
                                            <p class="mb-3">Tente ajustar a busca ou publique um novo artigo.</p>
                                            <a href="{{ route('admin.news.create') }}" class="btn btn-primary news-btn">
                                                <i class="bi bi-plus-circle"></i> Nova Notícia
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="news-pagination">
                {{ $news->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: '{{ session('error') }}',
            timer: 4000,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            background: '#fff',
            color: '#0f172a'
        });
    @endif

    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Excluir notícia?',
                text: 'Essa ação não poderá ser desfeita e o artigo sairá do ar.',
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
@endpush