@extends('layouts.app')
@section('title', 'Editar Notícia | PsiGestor')
@section('content')
<div class="container py-6 max-w-3xl">
    <h2 class="text-2xl font-bold mb-4">✏️ Editar Notícia</h2>

    <form action="{{ route('admin.news.update', $news) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" name="title" value="{{ old('title', $news->title) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Subtítulo</label>
            <input type="text" name="subtitle" value="{{ old('subtitle', $news->subtitle) }}" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Categoria</label>
            <select name="category" class="form-control" required>
                <option disabled {{ !$news->category ? 'selected' : '' }}>Selecione uma categoria</option>
                @php
                    $categorias = [
                        'Saúde Mental',
                        'Bem-estar',
                        'Psicologia Clínica',
                        'Tecnologia',
                        'Atualidades',
                        'Sociedade',
                        'Carreira & Trabalho',
                        'PsiGestor'
                    ];
                @endphp
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria }}" {{ old('category', $news->category) === $categoria ? 'selected' : '' }}>
                        {{ $categoria }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Imagem de Capa</label>
            <input type="file" name="image" class="form-control" accept="image/*">
            @if ($news->image)
                <div class="mt-2">
                    <img src="{{ $news->image_url }}" alt="Capa atual" class="w-32 rounded shadow">
                </div>
            @endif
        </div>

        <div class="mb-3">
            <label class="form-label">Conteúdo</label>
            <textarea name="content" id="editor" class="form-control" rows="10">{{ old('content', $news->content) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Atualizar</button>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('libs/tinymce/tinymce.min.js') }}"></script>
<script>
    tinymce.init({
        selector: '#editor',
        height: 400,
        plugins: 'link image code lists',
        toolbar: 'undo redo | formatselect | bold italic underline | bullist numlist | link image | code',
        language: 'pt-PT'
    });
</script>

@if ($errors->any())
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Erro ao atualizar informações',
            text: '{{ $errors->first() }}'
        });
    </script>
@endif
@endpush
