@extends('layouts.landing')

@section('title', $news->title . ' | PsiGestor')

@section('content')
<section class="article-page">
    {{-- HERO (quando existe imagem) --}}
    @if ($news->image)
        <header class="hero">
            <div class="hero-media hero-skeleton">
                <picture>
                    {{-- WebP otimizado (se existir e não for igual ao fallback) --}}
                    @if(!empty($news->image_webp_url) && $news->image_webp_url !== $news->image_url)
                        <source srcset="{{ $news->image_webp_url }}" type="image/webp">
                    @endif

                    {{-- Fallback JPG/PNG/legado --}}
                    <img
                        src="{{ $news->image_url }}"
                        alt="{{ $news->title }}"
                        class="hero-img"
                        loading="lazy"
                        decoding="async"
                        fetchpriority="high"
                    >
                </picture>

                {{-- Overlay editorial --}}
                <div class="hero-overlay">
                    <div class="hero-inner">
                        @if ($news->category)
                            <p class="hero-category">
                                <i class="bi bi-tag-fill"></i> {{ $news->category }}
                            </p>
                        @endif

                        <h1 class="hero-title">{{ $news->title }}</h1>

                        @if ($news->subtitle)
                            <p class="hero-subtitle">{{ $news->subtitle }}</p>
                        @endif

                        <p class="hero-meta">
                            <span><i class="bi bi-person"></i> Por <strong>{{ $news->author_name ?? 'Equipe PsiGestor' }}</strong></span>
                            <span class="hero-dot">•</span>
                            <span><i class="bi bi-calendar"></i> {{ $news->created_at->format('d/m/Y \à\s H:i') }}</span>

                            @if ($news->updated_at && $news->updated_at->gt($news->created_at))
                                <span class="hero-dot">•</span>
                                <span><i class="bi bi-clock-history"></i> Atualizado {{ $news->updated_at->diffForHumans() }}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </header>
    @endif

    {{-- CONTEÚDO (container alinhado) --}}
    <div class="article-container">
        {{-- Fallback do cabeçalho caso NÃO tenha imagem --}}
        @if (!$news->image)
            @if ($news->category)
                <p class="plain-category">
                    <i class="bi bi-tag-fill"></i> {{ $news->category }}
                </p>
            @endif

            <h1 class="plain-title">{{ $news->title }}</h1>

            @if ($news->subtitle)
                <p class="plain-subtitle">{{ $news->subtitle }}</p>
            @endif

            <p class="plain-meta">
                <i class="bi bi-person"></i> Por <strong>{{ $news->author_name ?? 'Equipe PsiGestor' }}</strong><br>
                <i class="bi bi-calendar"></i> Publicado em {{ $news->created_at->format('d/m/Y \à\s H:i') }}
                @if ($news->updated_at && $news->updated_at->gt($news->created_at))
                    <br><i class="bi bi-clock-history"></i> Atualizado {{ $news->updated_at->diffForHumans() }}
                @endif
            </p>
        @endif

        {{-- Conteúdo --}}
        <div class="noticia-conteudo">
            {!! $news->content !!}
        </div>

        @php
            $shareUrl = urlencode(route('blog.show', $news->slug));
            $shareTitle = urlencode($news->title);
        @endphp

        {{-- Compartilhamento --}}
        <div class="mt-5 mb-4">
            <p class="fw-bold mb-2 text-dark">Compartilhe:</p>

            <div class="d-flex align-items-center gap-3 flex-wrap">
                <a href="https://wa.me/?text={{ $shareTitle }}%0A{{ $shareUrl }}" target="_blank" class="share-btn whatsapp" title="Compartilhar no WhatsApp">
                    <i class="bi bi-whatsapp"></i>
                </a>

                <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" class="share-btn facebook" title="Compartilhar no Facebook">
                    <i class="bi bi-facebook"></i>
                </a>

                <a href="https://twitter.com/intent/tweet?text={{ $shareTitle }}&url={{ $shareUrl }}" target="_blank" class="share-btn twitter" title="Compartilhar no Twitter">
                    <i class="bi bi-twitter-x"></i>
                </a>

                <button onclick="copiarLinkInstagram('{{ route('blog.show', $news->slug) }}')" class="share-btn instagram" title="Copiar link">
                    <i class="bi bi-instagram"></i>
                </button>
            </div>
        </div>

        {{-- Notícias Relacionadas --}}
        @if ($related->count())
            <div class="related-wrap">
                <h3 class="related-title">Leia também</h3>

                <div class="related-list">
                    @foreach ($related as $item)
                        <a href="{{ route('blog.show', $item->slug) }}" class="related-card">
                            @if ($item->image)
                                <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="related-thumb" loading="lazy" decoding="async">
                            @endif

                            <div class="related-content">
                                <h4 class="related-h">{{ $item->title }}</h4>
                                <p class="related-p">{!! $item->excerpt !!}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Voltar --}}
        <div class="back-wrap">
            <a href="{{ route('blog.index') }}" class="back-btn"
               onmouseover="this.style.background='#008ecc'"
               onmouseout="this.style.background='#00aaff'">
                ← Voltar ao blog
            </a>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    /* ===== Base ===== */
    .article-page {
        padding: 24px 0 40px;
        background: #fff;
    }

    .article-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* ===== HERO (mobile-first) ===== */
    .hero { margin-bottom: 18px; }

    .hero-media {
        position: relative;
        width: 100%;
        overflow: hidden;
        background: #f3f3f3;
        aspect-ratio: 16 / 9; /* evita capa “alta demais” no mobile */
        border-radius: 14px;
        margin: 0 20px;       /* alinha com o container no mobile */
    }

    /* imagem inicia com blur + invisível */
    .hero-img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;

        opacity: 0;
        filter: blur(18px);
        transform: scale(1.02);
        transition: opacity .45s ease, filter .45s ease, transform .45s ease;
    }

    /* quando carregar: remove blur e mostra */
    .hero-media.is-loaded .hero-img {
        opacity: 1;
        filter: blur(0);
        transform: scale(1);
    }

    /* Skeleton shimmer enquanto não carrega */
    .hero-skeleton::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, rgba(0,0,0,0.04), rgba(0,0,0,0.10), rgba(0,0,0,0.04));
        background-size: 240% 100%;
        animation: shimmer 1.25s infinite linear;
        z-index: 1;
    }

    .hero-media.is-loaded.hero-skeleton::before {
        display: none;
    }

    @keyframes shimmer {
        0% { background-position: 0% 0; }
        100% { background-position: 200% 0; }
    }

    /* Overlay editorial */
    .hero-overlay {
        position: absolute;
        inset: 0;
        z-index: 2;
        display: flex;
        align-items: flex-end;
        padding: 18px;
        background: linear-gradient(to top, rgba(0,0,0,.64), rgba(0,0,0,.18), rgba(0,0,0,0));
    }

    .hero-inner {
        width: 100%;
        max-width: 800px;
        margin: 0 auto;
    }

    .hero-category {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.78rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #bfeaff;
        margin: 0 0 8px;
    }

    .hero-title {
        font-size: clamp(1.5rem, 4.2vw, 2.4rem);
        font-weight: 900;
        color: #fff;
        margin: 0 0 8px;
        line-height: 1.12;
        text-shadow: 0 10px 28px rgba(0,0,0,.35);
    }

    .hero-subtitle {
        font-size: clamp(1rem, 2.3vw, 1.2rem);
        color: rgba(255,255,255,.92);
        margin: 0 0 10px;
        line-height: 1.35;
        max-width: 62ch;
    }

    .hero-meta {
        color: rgba(255,255,255,.9);
        font-size: 0.92rem;
        margin: 0;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }

    .hero-dot { opacity: .85; }

    /* ===== Desktop: Full-bleed NYTimes ===== */
    @media (min-width: 992px) {
        .hero-media {
            width: 100vw;
            margin-left: calc(50% - 50vw);
            margin-right: calc(50% - 50vw);
            border-radius: 0;
            aspect-ratio: auto;
            height: min(62vh, 520px);
        }

        .hero-overlay {
            padding: 34px 20px;
        }

        .hero-inner {
            max-width: 900px;
        }

        .article-container {
            padding-top: 6px;
        }
    }

    /* ===== Fallback header sem imagem ===== */
    .plain-category {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #00aaff;
        letter-spacing: 1px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-left: 4px solid #00aaff;
        padding-left: 10px;
        margin-bottom: 12px;
    }

    .plain-title {
        font-size: 2rem;
        font-weight: 900;
        margin: 0 0 10px;
        color: #111;
        line-height: 1.15;
    }

    .plain-subtitle {
        font-size: 1.15rem;
        color: #444;
        margin: -2px 0 16px;
        line-height: 1.35;
    }

    .plain-meta {
        color: #666;
        font-size: 0.92rem;
        margin-bottom: 18px;
    }

    /* ===== Conteúdo ===== */
    .noticia-conteudo {
        line-height: 1.7;
        font-size: 1.05rem;
        color: #333;
        overflow: hidden;
    }

    .noticia-conteudo a {
        color: #00aaff;
        font-weight: 500;
        text-decoration: underline;
        transition: color 0.3s ease;
    }

    .noticia-conteudo a:hover {
        color: #008ecc;
    }

    .noticia-conteudo img {
        max-width: 280px;
        border-radius: 10px;
        object-fit: cover;
        height: auto;
    }

    @media (max-width: 768px) {
        .noticia-conteudo img {
            float: none !important;
            display: block;
            margin: 0 auto 20px auto !important;
            width: 100%;
            max-width: 100%;
        }
    }

    /* ===== Share ===== */
    .share-btn {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        color: #fff !important;
        font-size: 1.4rem;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        border: none;
        cursor: pointer;
    }

    .share-btn:hover {
        transform: scale(1.12);
        box-shadow: 0 4px 15px rgba(0,0,0,0.22);
    }

    .share-btn.whatsapp { background-color: #25D366; }
    .share-btn.facebook { background-color: #1877F2; }
    .share-btn.twitter  { background-color: #000; }
    .share-btn.instagram {
        background: linear-gradient(45deg, #f58529, #dd2a7b, #8134af);
    }

    /* ===== Relacionadas ===== */
    .related-wrap { margin-top: 56px; }

    .related-title {
        font-size: 1.35rem;
        font-weight: 900;
        margin: 0 0 18px;
        color: #111;
    }

    .related-list {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .related-card {
        display: flex;
        gap: 16px;
        align-items: flex-start;
        background: #f9f9f9;
        padding: 12px;
        border-radius: 10px;
        text-decoration: none;
        color: inherit;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        transition: box-shadow 0.25s ease, transform 0.25s ease;
    }

    .related-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transform: translateY(-1px);
    }

    .related-thumb {
        width: 110px;
        height: 82px;
        object-fit: cover;
        border-radius: 8px;
        flex-shrink: 0;
        background: #eee;
    }

    .related-content { flex: 1; }

    .related-h {
        font-size: 1rem;
        font-weight: 800;
        color: #222;
        margin: 0 0 6px;
        line-height: 1.2;
    }

    .related-p {
        font-size: 0.92rem;
        color: #555;
        line-height: 1.4;
        max-height: 2.8em;
        overflow: hidden;
        margin: 0;
    }

    /* ===== Voltar ===== */
    .back-wrap { margin-top: 40px; }

    .back-btn {
        display: inline-block;
        background: #00aaff;
        color: #fff;
        padding: 10px 20px;
        border-radius: 30px;
        font-weight: 500;
        text-decoration: none;
        transition: background 0.3s ease;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Links do conteúdo em nova aba
        const content = document.querySelector('.noticia-conteudo');
        if (content) {
            content.querySelectorAll('a').forEach(link => {
                link.setAttribute('target', '_blank');
                link.setAttribute('rel', 'noopener noreferrer');
            });
        }

        // Capa: quando carregar, remove skeleton + blur
        const heroMedia = document.querySelector('.hero-media');
        const heroImg = document.querySelector('.hero-img');

        if (heroMedia && heroImg) {
            const markLoaded = () => heroMedia.classList.add('is-loaded');

            if (heroImg.complete && heroImg.naturalWidth > 0) {
                markLoaded();
            } else {
                heroImg.addEventListener('load', markLoaded, { once: true });
                heroImg.addEventListener('error', markLoaded, { once: true });
            }
        }

        // Mantém seu padrão de imagens no corpo (alternando left/right)
        document.querySelectorAll('.noticia-conteudo img').forEach((img, index) => {
            img.style.maxWidth = '280px';
            img.style.borderRadius = '10px';
            img.style.objectFit = 'cover';
            img.style.margin = '0 20px 20px 0';
            img.style.float = (index % 2 === 0) ? 'left' : 'right';
        });
    });

    function copiarLinkInstagram(url) {
        navigator.clipboard.writeText(url).then(() => {
            alert('Link copiado! Agora é só colar no seu story ou feed do Instagram.');
        });
    }
</script>
@endpush
