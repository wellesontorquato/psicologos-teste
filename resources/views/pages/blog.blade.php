@php
    /*
    |--------------------------------------------------------------------------
    | BLOG V3 — DIREÇÃO EDITORIAL
    |--------------------------------------------------------------------------
    |
    | A lógica permanece server-first.
    |
    | O BlogController continua responsável por:
    | - busca por título;
    | - ordenação;
    | - paginação;
    | - dados dos artigos.
    |
    | Esta view apenas reorganiza visualmente a coleção recebida.
    |
    */

    $title = 'Blog | PsiGestor';

    $hasSearch = request()->filled('search');
    $searchTerm = request('search');

    $currentArticles = $news->getCollection()->values();

    $leadArticle = $currentArticles->get(0);

    $sideArticles = $currentArticles
        ->slice(1, 2)
        ->values();

    $feedArticles = $currentArticles
        ->slice(3)
        ->values();

    /*
     * Apenas categorias que realmente aparecem na pagina atual.
     * Nao cria categoria nova nem altera a busca.
     */
    $visibleCategories = $currentArticles
        ->pluck('category')
        ->filter()
        ->unique()
        ->values();
@endphp

@extends('layouts.landing')

@section('content')

<main
    class="pgb-page"
    data-blog-page
>

    {{-- =========================================================
        ROOT REACT / MOTION
    ========================================================== --}}

    <div
        id="psigestor-blog-motion"
        class="pgb-motion-root"
        aria-hidden="true"
    ></div>

    {{-- =========================================================
        MASTHEAD
    ========================================================== --}}

    <header class="pgb-masthead">

        <div class="pgb-shell">

            <div
                class="pgb-masthead-meta"
                data-blog-reveal
            >

                <span>
                    Publicação PsiGestor
                </span>

            </div>

            <div
                class="pgb-brand-row"
                data-blog-reveal
            >

                <h1>Blog</h1>

                <div class="pgb-brand-side">

                    <p>
                        Saúde mental, prática clínica, tecnologia,
                        trabalho e sociedade em perspectiva.
                    </p>

                    <form
                        method="GET"
                        action="{{ route('blog.index') }}"
                        class="pgb-search"
                        role="search"
                    >

                        <div class="pgb-search-field">

                            <span
                                class="pgb-search-icon"
                                aria-hidden="true"
                            >
                                <i class="bi bi-search"></i>
                            </span>

                            <label
                                for="pgb-search-input"
                                class="visually-hidden"
                            >
                                Buscar publicação pelo título
                            </label>

                            <input
                                id="pgb-search-input"
                                type="search"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Buscar no blog"
                                autocomplete="off"
                            >

                            @if($hasSearch)

                                <a
                                    href="{{ route('blog.index') }}"
                                    class="pgb-search-clear"
                                    aria-label="Limpar busca"
                                    title="Limpar busca"
                                >
                                    <i class="bi bi-x-lg"></i>
                                </a>

                            @endif

                        </div>

                        <button
                            type="submit"
                            class="pgb-search-submit"
                        >
                            Buscar
                        </button>

                    </form>

                </div>

            </div>

            <div class="pgb-masthead-rule"></div>

            @if($visibleCategories->isNotEmpty())

                <div
                    class="pgb-topic-rail"
                    data-blog-reveal
                    aria-label="Temas presentes nesta página"
                >

                    <span class="pgb-topic-rail-label">
                        Nesta página
                    </span>

                    @foreach($visibleCategories as $category)

                        <span class="pgb-topic">
                            {{ $category }}
                        </span>

                    @endforeach

                </div>

            @endif

        </div>

    </header>

    {{-- =========================================================
        RESULTADOS
    ========================================================== --}}

    @if($news->count())

        <section class="pgb-editorial">

            <div class="pgb-shell">

                <div
                    class="pgb-section-heading"
                    data-blog-reveal
                >

                    <div>

                        <span class="pgb-section-label">

                            @if($hasSearch)
                                Busca
                            @else
                                Mais recentes
                            @endif

                        </span>

                        <h2>

                            @if($hasSearch)

                                Resultados para
                                <span>“{{ $searchTerm }}”</span>

                            @else

                                Últimas publicações

                            @endif

                        </h2>

                    </div>

                    <div class="pgb-section-index pgb-section-index-final">

    @if($hasSearch)

        <strong>
            {{ $news->total() }}
        </strong>

        <span>
            {{ $news->total() === 1 ? 'resultado' : 'resultados' }}
        </span>

    @else

        <strong>
            {{ $news->count() }}
        </strong>

        <span>
            nesta página
        </span>

        @if($news->total() > $news->count())

            <small>
                {{ $news->total() }} no blog
            </small>

        @endif

    @endif

</div>

                </div>

                {{-- =================================================
                    BLOCO PRINCIPAL
                ================================================== --}}

                @if($leadArticle)

                    <div
                        class="
                            pgb-lead-grid
                            {{ $sideArticles->isEmpty() ? 'pgb-lead-grid--solo' : '' }}
                                {{ $currentArticles->count() === 2 ? 'pgb-lead-grid--pair' : '' }}
                        "
                    >

                        {{-- MANCHETE PRINCIPAL --}}

                        <article
                            class="pgb-main-story"
                            data-blog-reveal
                        >

                            <a
                                href="{{ route('blog.show', $leadArticle->slug) }}"
                                class="pgb-main-story-link"
                            >

                                <div class="pgb-main-media">

                                    @if($leadArticle->image)

                                        <picture>

                                            @if(
                                                $leadArticle->image_webp_url &&
                                                $leadArticle->image_webp_url !== $leadArticle->image_url
                                            )

                                                <source
                                                    srcset="{{ $leadArticle->image_webp_url }}"
                                                    type="image/webp"
                                                >

                                            @endif

                                            <img
                                                src="{{ $leadArticle->image_url }}"
                                                alt="{{ $leadArticle->title }}"
                                                loading="eager"
                                                decoding="async"
                                            >

                                        </picture>

                                    @else

                                        <div
                                            class="pgb-image-fallback"
                                            aria-hidden="true"
                                        >
                                            <i class="bi bi-journal-richtext"></i>
                                            <span>PsiGestor</span>
                                        </div>

                                    @endif

                                </div>

                                <div class="pgb-main-copy">

                                    <div class="pgb-story-meta">

                                        @if($leadArticle->category)

                                            <span class="pgb-story-category">
                                                {{ $leadArticle->category }}
                                            </span>

                                            <span
                                                class="pgb-story-dot"
                                                aria-hidden="true"
                                            ></span>

                                        @endif

                                        <time
                                            datetime="{{ $leadArticle->created_at->toDateString() }}"
                                        >
                                            {{ $leadArticle->created_at->format('d/m/Y') }}
                                        </time>

                                    </div>

                                    <h2>
                                        {{ $leadArticle->title }}
                                    </h2>

                                    @if($leadArticle->excerpt)

                                        <div class="pgb-story-excerpt">
                                            {!! $leadArticle->excerpt !!}
                                        </div>

                                    @endif

                                    <span class="pgb-story-action">
                                        Ler publicação

                                        <i
                                            class="bi bi-arrow-up-right"
                                            aria-hidden="true"
                                        ></i>
                                    </span>

                                </div>

                            </a>

                        </article>

                        {{-- COLUNA LATERAL --}}

                        @if($sideArticles->isNotEmpty())

                            <aside
                                class="pgb-side-column"
                                aria-label="Outras publicações"
                            >

                                <div
                                    class="pgb-side-heading"
                                    data-blog-reveal
                                >

                                    <span>
                                        @if($hasSearch)
                                            Outros resultados
                                        @else
                                            Outras leituras
                                        @endif
                                    </span>

                                </div>

                                @foreach($sideArticles as $article)

                                    <article
                                        class="pgb-side-story"
                                        data-blog-reveal
                                    >

                                        <a
                                            href="{{ route('blog.show', $article->slug) }}"
                                            class="pgb-side-story-link"
                                        >

                                            <div class="pgb-side-media">

                                                @if($article->image)

                                                    <picture>

                                                        @if(
                                                            $article->image_webp_url &&
                                                            $article->image_webp_url !== $article->image_url
                                                        )

                                                            <source
                                                                srcset="{{ $article->image_webp_url }}"
                                                                type="image/webp"
                                                            >

                                                        @endif

                                                        <img
                                                            src="{{ $article->image_url }}"
                                                            alt="{{ $article->title }}"
                                                            loading="lazy"
                                                            decoding="async"
                                                        >

                                                    </picture>

                                                @else

                                                    <div
                                                        class="pgb-image-fallback pgb-image-fallback-small"
                                                        aria-hidden="true"
                                                    >
                                                        <i class="bi bi-journal-text"></i>
                                                    </div>

                                                @endif

                                            </div>

                                            <div class="pgb-side-copy">

                                                <div class="pgb-story-meta">

                                                    @if($article->category)

                                                        <span class="pgb-story-category">
                                                            {{ $article->category }}
                                                        </span>

                                                        <span
                                                            class="pgb-story-dot"
                                                            aria-hidden="true"
                                                        ></span>

                                                    @endif

                                                    <time
                                                        datetime="{{ $article->created_at->toDateString() }}"
                                                    >
                                                        {{ $article->created_at->format('d/m/Y') }}
                                                    </time>

                                                </div>

                                                <h3>
                                                    {{ $article->title }}
                                                </h3>

                                                @if($article->excerpt)

                                                    <div class="pgb-side-excerpt">
                                                        {!! $article->excerpt !!}
                                                    </div>

                                                @endif

                                                <span class="pgb-side-action">
                                                    Ler
                                                    <i
                                                        class="bi bi-arrow-right"
                                                        aria-hidden="true"
                                                    ></i>
                                                </span>

                                            </div>

                                        </a>

                                    </article>

                                @endforeach

                            </aside>

                        @endif

                    </div>

                @endif

            </div>

        </section>

        {{-- =========================================================
            GRADE SECUNDARIA
        ========================================================== --}}

        @if($feedArticles->isNotEmpty())

            <section class="pgb-feed">

                <div class="pgb-shell">

                    <div
                        class="pgb-feed-heading"
                        data-blog-reveal
                    >

                        <span>
                            @if($hasSearch)
                                Mais resultados
                            @else
                                Mais publicações
                            @endif
                        </span>

                        <div class="pgb-feed-line"></div>

                    </div>

                    <div class="pgb-feed-grid">

                        @foreach($feedArticles as $article)

                            <article
                                class="pgb-feed-story"
                                data-blog-reveal
                            >

                                <a
                                    href="{{ route('blog.show', $article->slug) }}"
                                    class="pgb-feed-story-link"
                                >

                                    <div class="pgb-feed-media">

                                        @if($article->image)

                                            <picture>

                                                @if(
                                                    $article->image_webp_url &&
                                                    $article->image_webp_url !== $article->image_url
                                                )

                                                    <source
                                                        srcset="{{ $article->image_webp_url }}"
                                                        type="image/webp"
                                                    >

                                                @endif

                                                <img
                                                    src="{{ $article->image_url }}"
                                                    alt="{{ $article->title }}"
                                                    loading="lazy"
                                                    decoding="async"
                                                >

                                            </picture>

                                        @else

                                            <div
                                                class="pgb-image-fallback"
                                                aria-hidden="true"
                                            >
                                                <i class="bi bi-journal-text"></i>
                                            </div>

                                        @endif

                                    </div>

                                    <div class="pgb-feed-copy">

                                        <div class="pgb-story-meta">

                                            @if($article->category)

                                                <span class="pgb-story-category">
                                                    {{ $article->category }}
                                                </span>

                                                <span
                                                    class="pgb-story-dot"
                                                    aria-hidden="true"
                                                ></span>

                                            @endif

                                            <time
                                                datetime="{{ $article->created_at->toDateString() }}"
                                            >
                                                {{ $article->created_at->format('d/m/Y') }}
                                            </time>

                                        </div>

                                        <h3>
                                            {{ $article->title }}
                                        </h3>

                                        @if($article->excerpt)

                                            <div class="pgb-story-excerpt">
                                                {!! $article->excerpt !!}
                                            </div>

                                        @endif

                                        <span class="pgb-story-action">
                                            Ler publicação

                                            <i
                                                class="bi bi-arrow-up-right"
                                                aria-hidden="true"
                                            ></i>
                                        </span>

                                    </div>

                                </a>

                            </article>

                        @endforeach

                    </div>

                </div>

            </section>

        @endif

        {{-- =========================================================
            PAGINACAO
            Mantida sem appends() propositalmente.
        ========================================================== --}}

        <div class="pgb-pagination-section">

            <div class="pgb-shell">

                <div
                    class="pgb-pagination"
                    data-blog-reveal
                >
                    {{ $news->links() }}
                </div>

            </div>

        </div>

    @else

        {{-- =========================================================
            EMPTY STATE
        ========================================================== --}}

        <section class="pgb-empty-section">

            <div class="pgb-shell">

                <div
                    class="pgb-empty"
                    data-blog-reveal
                >

                    <span class="pgb-empty-index">
                        00
                    </span>

                    <div>

                        <span class="pgb-section-label">
                            Sem resultados
                        </span>

                        <h2>
                            Nenhuma publicação encontrada.
                        </h2>

                        <p>
                            Não encontramos um conteúdo com esse título.
                            Tente outra busca ou volte para todas as publicações.
                        </p>

                        @if($hasSearch)

                            <a
                                href="{{ route('blog.index') }}"
                                class="pgb-empty-link"
                            >
                                Ver todas as publicações

                                <i
                                    class="bi bi-arrow-right"
                                    aria-hidden="true"
                                ></i>
                            </a>

                        @endif

                    </div>

                </div>

            </div>

        </section>

    @endif

</main>

@endsection

@push('scripts')
    @vite('resources/js/public-pages/blog/index.jsx')
@endpush