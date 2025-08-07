@php
    $title = 'Blog | PsiGestor';
@endphp

@extends('layouts.landing')

@section('content')
<section style="padding: 60px 20px; max-width: 1200px; margin: auto;">
    <h1 style="
        font-size: clamp(1.8rem, 4vw, 2.5rem);
        font-weight: 700;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #111;
        border-left: 5px solid #00aaff;
        padding-left: 15px;
    ">
        <i class="bi bi-newspaper" style="font-size: 1.6rem; color: #00aaff;"></i>
        <span>Blog do <span style="color: #00aaff;">PsiGestor</span></span>
    </h1>

    <form method="GET" action="{{ route('blog.index') }}" style="margin-bottom: 30px; display: flex; gap: 10px; flex-wrap: wrap;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar notícia..." style="
            width: 100%;
            max-width: 400px;
            padding: 10px 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            flex: 1;
        ">
        <button type="submit" style="
            padding: 10px 20px;
            background-color: #00aaff;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease;
        " onmouseover="this.style.background='#008ecc'" onmouseout="this.style.background='#00aaff'">
            Buscar
        </button>
    </form>

    @if($news->count())
        <div style="display: flex; flex-direction: column; gap: 30px;">
            @foreach($news as $article)
                <a href="{{ route('blog.show', $article->slug) }}" class="blog-card">
                    @if ($article->image)
                        <picture>
                            {{-- WebP otimizado (se existir e não for igual ao fallback) --}}
                            @if($article->image_webp_url && $article->image_webp_url !== $article->image_url)
                                <source srcset="{{ $article->image_webp_url }}" type="image/webp">
                            @endif

                            {{-- Fallback JPG/PNG --}}
                            <img src="{{ $article->image_url }}"
                                 alt="{{ $article->title }}"
                                 loading="lazy"
                                 width="160" height="100"
                                 class="blog-thumbnail">
                        </picture>
                    @endif
                    <div class="blog-content">
                        {{-- Categoria --}}
                        @if ($article->category)
                            <p class="blog-category">{{ $article->category }}</p>
                        @endif

                        <h2 class="blog-title">{{ $article->title }}</h2>
                        <p class="blog-excerpt">
                            {!! $article->excerpt !!}
                        </p>
                        <span class="blog-read-more">Ler mais →</span>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Paginação --}}
        <div style="margin-top: 40px;">
            {{ $news->links() }}
        </div>
    @else
        <p style="text-align: center; color: #888;">Nenhuma notícia encontrada.</p>
    @endif
</section>
@endsection

@push('styles')
<style>
    .blog-card {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        align-items: flex-start;
        gap: 20px;
        background: #fff;
        padding: 16px;
        border: 1px solid #eee;
        border-radius: 10px;
        text-decoration: none;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        transition: box-shadow 0.3s ease;
    }

    .blog-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .blog-thumbnail {
        width: 160px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
        flex-shrink: 0;
    }

    .blog-content {
        flex: 1;
        min-width: 0;
    }

    .blog-category {
        font-size: 0.75rem;
        color: #00aaff;
        text-transform: uppercase;
        font-weight: 500;
        margin-bottom: 5px;
        letter-spacing: 0.5px;
    }

    .blog-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #222;
        margin-bottom: 6px;
    }

    .blog-excerpt {
        font-size: 0.95rem;
        color: #555;
        line-height: 1.4;
        margin-bottom: 5px;
        max-height: 3.6em;
        overflow: hidden;
    }

    .blog-read-more {
        font-size: 0.9rem;
        color: #00aaff;
        display: inline-block;
        margin-top: 4px;
    }

    /* Responsividade */
    @media (max-width: 640px) {
        .blog-card {
            flex-direction: column;
        }

        .blog-thumbnail {
            width: 100% !important;
            height: auto !important;
        }

        .blog-content {
            margin-top: 10px;
        }
    }
</style>
@endpush
