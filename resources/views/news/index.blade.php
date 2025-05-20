@extends('layouts.app')
@section('title', 'Lista de Notícias | PsiGestor')

@section('content')
<div class="container py-6">
    <h2 class="text-2xl font-bold mb-4">🗂️ Todas as Notícias</h2>

    {{-- Botão Nova --}}
    <a href="{{ route('admin.news.create') }}" class="btn btn-success mb-4">
        <i class="bi bi-plus-lg"></i> Nova Notícia
    </a>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('admin.news.index') }}" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Buscar por título...">
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">Todas as categorias</option>
                @php
                    $categorias = ['Saúde Mental', 'Bem-estar', 'Psicologia Clínica', 'Tecnologia', 'Atualidades', 'Sociedade', 'Carreira & Trabalho', 'PsiGestor'];
                @endphp
                @foreach($categorias as $cat)
                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                        {{ $cat }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
        </div>
        <div class="col-md-2">
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
        </div>
        <div class="col-md-1 d-grid">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </form>

    {{-- Tabela --}}
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 80px;">Imagem</th>
                    <th>Título</th>
                    <th>Subtítulo</th>
                    <th>Categoria</th>
                    <th>Criado em</th>
                    <th>Última edição</th>
                    <th style="width: 150px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($news as $article)
                    <tr>
                        <td>
                            @if ($article->image)
                                <img src="{{ $article->image_url }}" class="rounded shadow-sm" style="width: 60px; height: 60px; object-fit: cover;">
                            @endif
                        </td>
                        <td><strong>{{ $article->title }}</strong></td>
                        <td>{{ $article->subtitle ?? '—' }}</td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1">
                                {{ $article->category ?? 'Sem categoria' }}
                            </span>
                        </td>
                        <td>{{ $article->created_at->format('d/m/Y') }}</td>
                        <td>
                            @if($article->updated_at->ne($article->created_at))
                                {{ $article->updated_at->diffForHumans() }}
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.news.edit', $article) }}" class="btn btn-sm btn-warning me-1">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <form action="{{ route('admin.news.destroy', $article) }}" method="POST" class="d-inline delete-form no-spinner">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">Nenhuma notícia encontrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $news->appends(request()->query())->links() }}
    </div>
</div>
@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Confirmação de exclusão
    document.querySelectorAll('.delete-form').forEach(form => {
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
                    Swal.close();
                    setTimeout(() => {
                        if (typeof showSpinner === 'function') showSpinner();
                        form.submit();
                    }, 300);
                }
            });
        });
    });

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: '{{ session('success') }}',
            confirmButtonText: 'OK'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: '{{ session('error') }}',
            confirmButtonText: 'OK'
        });
    @endif
</script>
@endpush
