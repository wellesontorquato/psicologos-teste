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
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 12px;
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
    .btn-submit:hover { background-color: #008ecc; }

    /* Contador estilo Twitter */
    .char-counter {
        font-size: 0.8rem;
        font-weight: 600;
        color: #6b7280; /* cinza */
        white-space: nowrap;
        padding: 2px 8px;
        border-radius: 999px;
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
    }
    .char-counter.near {
        color: #92400e;
        background: #fffbeb;
        border-color: #fcd34d;
    }
    .char-counter.over {
        color: #991b1b;
        background: #fef2f2;
        border-color: #fca5a5;
    }

    .help-text {
        font-size: 0.8rem;
        color: #6b7280;
        margin-top: 0.35rem;
    }

    /* Responsivo */
    @media (min-width: 768px) {
        .news-form-container h2 { font-size: 1.8rem; }
        .btn-submit { width: auto; }
    }
</style>

@php
    // ✅ Defina seus limites aqui
    $TITLE_MAX = 120;
    $SUBTITLE_MAX = 200;
@endphp

<div class="container py-5">
    <div class="news-form-container">
        <h2>📝 Criar Nova Notícia</h2>

        <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- TÍTULO --}}
            <div class="mb-3">
                <label class="form-label" for="title">
                    <span>Título</span>
                    <span class="char-counter" data-for="title" data-max="{{ $TITLE_MAX }}">
                        0/{{ $TITLE_MAX }}
                    </span>
                </label>
                <input
                    id="title"
                    type="text"
                    name="title"
                    value="{{ old('title') }}"
                    class="form-control"
                    required
                    maxlength="{{ $TITLE_MAX }}"
                    autocomplete="off"
                >
                <div class="help-text">Máximo de {{ $TITLE_MAX }} caracteres.</div>
            </div>

            {{-- SUBTÍTULO --}}
            <div class="mb-3">
                <label class="form-label" for="subtitle">
                    <span>Subtítulo (opcional)</span>
                    <span class="char-counter" data-for="subtitle" data-max="{{ $SUBTITLE_MAX }}">
                        0/{{ $SUBTITLE_MAX }}
                    </span>
                </label>
                <input
                    id="subtitle"
                    type="text"
                    name="subtitle"
                    value="{{ old('subtitle') }}"
                    class="form-control"
                    maxlength="{{ $SUBTITLE_MAX }}"
                    autocomplete="off"
                >
                <div class="help-text">Máximo de {{ $SUBTITLE_MAX }} caracteres.</div>
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
    // ✅ Contador estilo Twitter (digitado / máximo)
    function setupCounter(inputId) {
        const input = document.getElementById(inputId);
        if (!input) return;

        const counter = document.querySelector(`.char-counter[data-for="${inputId}"]`);
        if (!counter) return;

        const max = parseInt(counter.dataset.max || '0', 10);
        const warnAt = Math.max(0, Math.floor(max * 0.85)); // 85%

        function render() {
            const len = (input.value || '').length;
            counter.textContent = `${len}/${max}`;

            counter.classList.remove('near', 'over');

            if (len > max) {
                counter.classList.add('over');
            } else if (len >= warnAt) {
                counter.classList.add('near');
            }
        }

        // Atualiza em tudo: digitar, colar, desfazer, etc.
        ['input', 'change', 'keyup', 'paste'].forEach(evt => {
            input.addEventListener(evt, render);
        });

        // Inicial (para old())
        render();
    }

    setupCounter('title');
    setupCounter('subtitle');

    tinymce.init({
        selector: '#editor',
        height: 400,
        plugins: 'link image code lists',
        toolbar: 'undo redo | formatselect | bold italic underline | bullist numlist | link image | code',
        language: 'pt-PT'
    });
</script>
@endpush
