@extends('layouts.landing')

@section('content')
<section style="padding: 40px 20px; background: #fff;">
    <div style="max-width: 800px; margin: auto;">
        {{-- Título --}}
        <h1 style="font-size: 2rem; font-weight: bold; margin-bottom: 10px; color: #111;">
            {{ $news->title }}
        </h1>

        {{-- Metadados --}}
        <p style="color: #666; font-size: 0.9rem; margin-bottom: 20px;">
            <i class="bi bi-person"></i> Por <strong>{{ $news->author_name ?? 'Equipe PsiGestor' }}</strong><br>
            <i class="bi bi-calendar"></i> Publicado em {{ $news->created_at->format('d/m/Y \à\s H:i') }}
            @if ($news->updated_at && $news->updated_at->gt($news->created_at))
                <br><i class="bi bi-clock-history"></i> Atualizado {{ $news->updated_at->diffForHumans() }}
            @endif
        </p>

        {{-- Imagem de capa (opcional) --}}
        @if ($news->image)
                <img src="{{ $news->image_url }}"
                    alt="{{ $news->title }}"
                    style="width: 100%; border-radius: 10px; margin-bottom: 30px;">
        @endif

        {{-- Conteúdo --}}
        <div class="noticia-conteudo">
            {!! $news->content !!}
        </div>

        {{-- Voltar --}}
        <div style="margin-top: 40px;">
            <a href="{{ route('blog.index') }}" style="
                display: inline-block;
                background: #00aaff;
                color: #fff;
                padding: 10px 20px;
                border-radius: 30px;
                font-weight: 500;
                text-decoration: none;
                transition: background 0.3s ease;
            " onmouseover="this.style.background='#008ecc'" onmouseout="this.style.background='#00aaff'">
                ← Voltar ao blog
            </a>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .noticia-conteudo {
        line-height: 1.7;
        font-size: 1.05rem;
        color: #333;
        overflow: hidden;
    }

    /* Aplica estilo flutuante às duas primeiras imagens do conteúdo */
    .noticia-conteudo img:nth-of-type(1),
    .noticia-conteudo img:nth-of-type(2) {
        float: left;
        max-width: 280px;
        margin: 0 20px 20px 0;
        border-radius: 10px;
        object-fit: cover;
    }

    /* Responsividade: imagens 100% em telas menores */
    @media (max-width: 768px) {
        .noticia-conteudo img:nth-of-type(1),
        .noticia-conteudo img:nth-of-type(2) {
            float: none;
            display: block;
            margin: 0 auto 20px auto;
            width: 100%;
            max-width: 100%;
        }
    }
</style>
@endpush
