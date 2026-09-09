@extends('layouts.landing')

@section('title', $news->title . ' | PsiGestor')

@push('styles')
    @vite(['resources/js/public-pages/blog-article/index.jsx'])
@endpush

@section('content')

<section class="pga-article">

    <div
        id="pg-article-motion-root"
        aria-hidden="true"
    ></div>

        {{-- ============================================================
         HERO EDITORIAL / PORTAL
         ============================================================ --}}

    @if($news->image)

        <header
            class="pga-story-hero hero-media hero-skeleton"
            data-pga-reveal
        >

            <picture class="pga-story-hero-picture">

                @if(
                    !empty($news->image_webp_url) &&
                    $news->image_webp_url !== $news->image_url
                )
                    <source
                        srcset="{{ $news->image_webp_url }}"
                        type="image/webp"
                    >
                @endif

                <img
                    src="{{ $news->image_url }}"
                    alt="{{ $news->title }}"
                    class="pga-story-hero-image hero-img"
                    loading="eager"
                    decoding="async"
                    fetchpriority="high"
                >

            </picture>

            <div
                class="pga-story-hero-overlay"
                aria-hidden="true"
            ></div>

            <div class="pga-story-hero-content pga-shell">

                <div class="pga-story-hero-copy">

                    <div class="pga-story-hero-meta">

                        @if($news->category)

                            <span class="pga-story-hero-category">
                                {{ $news->category }}
                            </span>

                            <span
                                class="pga-story-hero-dot"
                                aria-hidden="true"
                            >
                                •
                            </span>

                        @endif

                        <time
                            datetime="{{ $news->created_at->toIso8601String() }}"
                        >
                            {{ $news->created_at->format('d/m/Y') }}
                        </time>

                    </div>

                    <h1 class="pga-story-hero-title">
                        {{ $news->title }}
                    </h1>

                    @if($news->subtitle)

                        <p class="pga-story-hero-subtitle">
                            {{ $news->subtitle }}
                        </p>

                    @endif

                    <div class="pga-story-hero-byline">

                        <span>
                            Por
                            <strong>
                                {{ $news->author_name ?? 'Equipe PsiGestor' }}
                            </strong>
                        </span>

                        <span
                            class="pga-story-hero-separator"
                            aria-hidden="true"
                        >
                            •
                        </span>

                        <time
                            datetime="{{ $news->created_at->toIso8601String() }}"
                        >
                            {{ $news->created_at->format('d/m/Y \à\s H:i') }}
                        </time>

                        @if(
                            $news->updated_at &&
                            $news->updated_at->gt($news->created_at)
                        )

                            <span
                                class="pga-story-hero-separator"
                                aria-hidden="true"
                            >
                                •
                            </span>

                            <span>
                                Atualizado
                                {{ $news->updated_at->diffForHumans() }}
                            </span>

                        @endif

                    </div>

                </div>

            </div>

        </header>

    @else

        {{-- Artigos antigos ou futuros sem capa continuam validos. --}}
        <header
            class="pga-masthead pga-masthead--no-image"
            data-pga-reveal
        >

            <div class="pga-shell">

                <nav
                    class="pga-breadcrumb"
                    aria-label="Navegação do artigo"
                >

                    <a href="{{ route('blog.index') }}">
                        Blog
                    </a>

                    @if($news->category)

                        <span aria-hidden="true">
                            /
                        </span>

                        <span>
                            {{ $news->category }}
                        </span>

                    @endif

                </nav>

                <p class="pga-kicker">
                    Publicação PsiGestor
                </p>

                <div class="pga-publication-line">

                    @if($news->category)

                        <span class="pga-category">
                            {{ $news->category }}
                        </span>

                        <span
                            class="pga-publication-dot"
                            aria-hidden="true"
                        >
                            •
                        </span>

                    @endif

                    <time
                        datetime="{{ $news->created_at->toIso8601String() }}"
                    >
                        {{ $news->created_at->format('d/m/Y') }}
                    </time>

                </div>

                <h1 class="pga-title">
                    {{ $news->title }}
                </h1>

                @if($news->subtitle)

                    <p class="pga-subtitle">
                        {{ $news->subtitle }}
                    </p>

                @endif

                <div class="pga-byline">

                    <span>
                        Por
                        <strong>
                            {{ $news->author_name ?? 'Equipe PsiGestor' }}
                        </strong>
                    </span>

                    <span
                        class="pga-byline-separator"
                        aria-hidden="true"
                    >
                        •
                    </span>

                    <time
                        datetime="{{ $news->created_at->toIso8601String() }}"
                    >
                        {{ $news->created_at->format('d/m/Y \à\s H:i') }}
                    </time>

                    @if(
                        $news->updated_at &&
                        $news->updated_at->gt($news->created_at)
                    )

                        <span
                            class="pga-byline-separator"
                            aria-hidden="true"
                        >
                            •
                        </span>

                        <span>
                            Atualizado
                            {{ $news->updated_at->diffForHumans() }}
                        </span>

                    @endif

                </div>

            </div>

        </header>

    @endif
@php
        $shareUrl = urlencode(
            route(
                'blog.show',
                $news->slug
            )
        );

        $shareTitle =
            urlencode(
                $news->title
            );
    @endphp

    {{-- ============================================================
         LEITURA
         ============================================================ --}}
    <div class="pga-shell pga-reading-layout">

        {{-- Compartilhamento lateral no desktop.
             No mobile, o CSS leva este bloco para depois do texto. --}}
        <aside
            class="pga-share-rail"
            aria-label="Compartilhar publicação"
        >
            <div class="pga-share-sticky">

                <p class="pga-share-label">
                    Compartilhar
                </p>

                <div class="pga-share-buttons">

                    <a
                        href="https://wa.me/?text={{ $shareTitle }}%0A{{ $shareUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="pga-share-button"
                        title="Compartilhar no WhatsApp"
                        aria-label="Compartilhar no WhatsApp"
                    >
                        <i class="bi bi-whatsapp"></i>
                    </a>

                    <a
                        href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="pga-share-button"
                        title="Compartilhar no Facebook"
                        aria-label="Compartilhar no Facebook"
                    >
                        <i class="bi bi-facebook"></i>
                    </a>

                    <a
                        href="https://twitter.com/intent/tweet?text={{ $shareTitle }}&url={{ $shareUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="pga-share-button"
                        title="Compartilhar no X"
                        aria-label="Compartilhar no X"
                    >
                        <i class="bi bi-twitter-x"></i>
                    </a>

                    <button
                        type="button"
                        onclick="copiarLinkInstagram('{{ route('blog.show', $news->slug) }}')"
                        class="pga-share-button"
                        title="Copiar link para compartilhar no Instagram"
                        aria-label="Copiar link para compartilhar no Instagram"
                    >
                        <i class="bi bi-instagram"></i>
                    </button>

                </div>

            </div>
        </aside>

        <article
            class="pga-reading-column"
            data-pga-reveal
        >

            <div class="noticia-conteudo">
                {!! $news->content !!}
            </div>

        </article>

        <div
            class="pga-reading-balance"
            aria-hidden="true"
        ></div>

    </div>

    {{-- ============================================================
         RELACIONADOS
         ============================================================ --}}
    @if($related->count())

        <section
            class="pga-related pga-shell"
            aria-labelledby="pga-related-title"
            data-pga-reveal
        >

            <div class="pga-related-heading">

                <div>
                    <p class="pga-related-kicker">
                        Continue lendo
                    </p>

                    <h2 id="pga-related-title">
                        Mais para ler
                    </h2>
                </div>

                <a
                    href="{{ route('blog.index') }}"
                    class="pga-related-all"
                >
                    Ver todas
                    <span aria-hidden="true">→</span>
                </a>

            </div>

            <div class="pga-related-grid">

                @foreach($related as $item)

                    <a
                        href="{{ route('blog.show', $item->slug) }}"
                        class="pga-related-card"
                    >

                        @if($item->image)

                            <div class="pga-related-media">

                                <img
                                    src="{{ $item->image_url }}"
                                    alt="{{ $item->title }}"
                                    loading="lazy"
                                    decoding="async"
                                >

                            </div>

                        @else

                            <div
                                class="pga-related-media pga-related-media--empty"
                                aria-hidden="true"
                            >
                                PsiGestor
                            </div>

                        @endif

                        <div class="pga-related-meta">

                            @if($item->category)
                                <span>
                                    {{ $item->category }}
                                </span>

                                <i aria-hidden="true">•</i>
                            @endif

                            <time
                                datetime="{{ $item->created_at->toIso8601String() }}"
                            >
                                {{ $item->created_at->format('d/m/Y') }}
                            </time>

                        </div>

                        <h3>
                            {{ $item->title }}
                        </h3>

                        <div class="pga-related-excerpt">
                            {!! $item->excerpt !!}
                        </div>

                        <span class="pga-related-action">
                            Ler publicação
                            <span aria-hidden="true">↗</span>
                        </span>

                    </a>

                @endforeach

            </div>

        </section>

    @endif

    {{-- ============================================================
         RETORNO
         ============================================================ --}}
    <div
        class="pga-shell pga-back-section"
        data-pga-reveal
    >

        <a
            href="{{ route('blog.index') }}"
            class="pga-back-link"
        >
            <span aria-hidden="true">←</span>
            Todas as publicações
        </a>

    </div>

</section>

@endsection

@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {

        /*
         * Links criados dentro do conteudo editorial continuam
         * abrindo em nova aba.
         */
        const content =
            document.querySelector(
                '.noticia-conteudo'
            );

        if (content) {

            content
                .querySelectorAll('a')
                .forEach(link => {

                    link.setAttribute(
                        'target',
                        '_blank'
                    );

                    link.setAttribute(
                        'rel',
                        'noopener noreferrer'
                    );
                });

            /*
             * Preserva o comportamento existente:
             * imagens internas alternam esquerda/direita
             * no desktop.
             */
            content
                .querySelectorAll('img')
                .forEach((img, index) => {

                    /*
                     * Mantem o comportamento editorial legado
                     * no desktop.
                     */
                    img.style.maxWidth =
                        '280px';

                    img.style.borderRadius =
                        '10px';

                    img.style.objectFit =
                        'cover';

                    img.style.margin =
                        '0 20px 20px 0';

                    img.style.float =
                        (index % 2 === 0)
                            ? 'left'
                            : 'right';

                    /*
                     * Imagens de artigos antigos podem apontar
                     * para hosts externos que deixaram de existir.
                     *
                     * Nesse caso nao exibimos o icone quebrado
                     * nem reservamos espaco visual para a imagem.
                     *
                     * O conteudo textual permanece intacto.
                     */
                    const hideBrokenImage = () => {

                        img.style.setProperty(
                            'display',
                            'none',
                            'important'
                        );

                        img.style.setProperty(
                            'width',
                            '0',
                            'important'
                        );

                        img.style.setProperty(
                            'height',
                            '0',
                            'important'
                        );

                        img.style.setProperty(
                            'margin',
                            '0',
                            'important'
                        );

                        img.style.setProperty(
                            'padding',
                            '0',
                            'important'
                        );

                        img.style.setProperty(
                            'float',
                            'none',
                            'important'
                        );

                        img.setAttribute(
                            'aria-hidden',
                            'true'
                        );

                        img.dataset.pgaBrokenImage =
                            'true';
                    };

                    img.addEventListener(
                        'error',
                        hideBrokenImage,
                        {
                            once: true
                        }
                    );

                    /*
                     * Quando o erro ocorreu antes de o listener
                     * ser registrado, complete sera true e
                     * naturalWidth sera zero.
                     */
                    if (
                        img.complete &&
                        img.naturalWidth === 0
                    ) {
                        hideBrokenImage();
                    }
                });
        }

        /*
         * Preserva skeleton / carregamento da capa.
         */
        const heroMedia =
            document.querySelector(
                '.hero-media'
            );

        const heroImg =
            document.querySelector(
                '.hero-img'
            );

        if (
            heroMedia &&
            heroImg
        ) {

            const markLoaded = () => {
                heroMedia.classList.add(
                    'is-loaded'
                );
            };

            if (heroImg.complete) {
                markLoaded();
            }
            else {

                heroImg.addEventListener(
                    'load',
                    markLoaded,
                    {
                        once: true
                    }
                );

                heroImg.addEventListener(
                    'error',
                    markLoaded,
                    {
                        once: true
                    }
                );
            }
        }
    });

    function copiarLinkInstagram(url) {

        navigator.clipboard
            .writeText(url)
            .then(() => {

                alert(
                    'Link copiado! Agora é só colar no seu story ou feed do Instagram.'
                );
            });
    }
</script>

@endpush
