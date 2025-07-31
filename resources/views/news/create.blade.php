@extends('layouts.app')
@section('title', 'Criar Notícia | PsiGestor')

@section('content')
<style>
    .news-form-container {
        background: #fff;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        margin: auto;
        max-width: 900px;
    }

    .news-form-container h2 {
        font-size: 1.6rem;
        font-weight: 700;
        color: #1f2937;
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.3rem;
    }

    .form-control,
    .form-select {
        font-size: 0.9rem;
        padding: 0.65rem 0.9rem;
        border-radius: 8px;
        border: 1px solid #d1d5db;
    }

    .btn-submit {
        background-color: #00aaff;
        border: none;
        font-weight: 600;
        color: #fff;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        width: 100%;
        transition: background 0.2s;
    }
    .btn-submit:hover {
        background-color: #008ecc;
    }

    /* Responsivo */
    @media (min-width: 768px) {
        .news-form-container h2 {
            font-size: 1.8rem;
        }
        .btn-submit {
            width: auto;
        }
    }
</style>

<div class="container py-5">
    <div class="news-form-container">
        <h2>📝 Criar Nova Notícia</h2>

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
                <select name="category" class="form-select" required>
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

            <div class="text-center">
                <button type="submit" class="btn-submit">
                    💾 Salvar Notícia
                </button>
            </div>
        </form>
    </div>
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
