@extends('layouts.app')

@section('content')
<div class="container py-6 max-w-3xl">
    <h2 class="text-2xl font-bold mb-4">📝 Criar Nova Notícia</h2>

    <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- TÍTULO --}}
        <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" name="title" value="{{ old('title') }}" class="form-control" required>
        </div>

        {{-- SUBTÍTULO --}}
        <div class="mb-3">
            <label class="form-label">Subtítulo (opcional)</label>
            <input type="text" name="subtitle" value="{{ old('subtitle') }}" class="form-control">
        </div>

        {{-- CATEGORIA --}}
        <div class="mb-3">
            <label class="form-label">Categoria</label>
            <select name="category" class="form-control" required>
                <option disabled {{ old('category') ? '' : 'selected' }}>Selecione uma categoria</option>
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
                    <option value="{{ $categoria }}" {{ old('category') === $categoria ? 'selected' : '' }}>
                        {{ $categoria }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- IMAGEM --}}
        <div class="mb-3">
            <label class="form-label">Imagem de Capa</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        {{-- CONTEÚDO --}}
        <div class="mb-3">
            <label class="form-label">Conteúdo</label>
            <textarea name="content" id="editor" class="form-control" rows="10">{{ old('content') }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Salvar</button>
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
@endpush
