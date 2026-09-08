import {
    useEffect,
    useMemo,
    useState,
} from 'react';

import {
    motion,
    useReducedMotion,
} from 'motion/react';

function cleanText(value) {
    if (!value) {
        return '';
    }

    const element =
        document.createElement('div');

    element.innerHTML =
        String(value);

    return (
        element.textContent ||
        element.innerText ||
        ''
    ).trim();
}

function formatDate(value) {
    if (!value) {
        return null;
    }

    const date =
        new Date(value);

    if (
        Number.isNaN(
            date.getTime()
        )
    ) {
        return null;
    }

    return new Intl.DateTimeFormat(
        'pt-BR',
        {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        }
    )
        .format(date)
        .replace('.', '');
}

function ArticleImage({
    article,
    className = '',
    eager = false,
}) {
    if (!article.image_url) {
        return (
            <div
                className={`pgn-image-placeholder ${className}`}
                aria-hidden="true"
            >
                <div className="pgn-placeholder-grid" />

                <span>
                    <i className="bi bi-journal-richtext" />
                </span>

                <strong>
                    PsiGestor
                </strong>
            </div>
        );
    }

    return (
        <picture
            className={`pgn-picture ${className}`}
        >
            {article.image_webp_url &&
                article.image_webp_url !==
                    article.image_url && (
                    <source
                        srcSet={
                            article.image_webp_url
                        }
                        type="image/webp"
                    />
                )}

            <img
                src={article.image_url}
                alt={article.title}
                loading={
                    eager
                        ? 'eager'
                        : 'lazy'
                }
            />
        </picture>
    );
}

function ArticleCategory({
    article,
}) {
    return (
        <span className="pgn-category">
            {article.category ||
                'Conteúdo'}
        </span>
    );
}

function ArticleMeta({
    article,
}) {
    const date =
        formatDate(
            article.created_at
        );

    return (
        <div className="pgn-meta">
            <ArticleCategory
                article={article}
            />

            {date && (
                <>
                    <span className="pgn-meta-dot" />

                    <span>
                        {date}
                    </span>
                </>
            )}
        </div>
    );
}

function Skeleton() {
    return (
        <div
            className="pgn-layout pgn-skeleton-layout"
            aria-hidden="true"
        >
            <div className="pgn-skeleton pgn-skeleton-main">
                <div className="pgn-sk-image" />

                <div className="pgn-sk-content">
                    <span />
                    <strong />
                    <strong />
                    <small />
                    <small />
                </div>
            </div>

            <div className="pgn-skeleton-side">
                {[0, 1].map(
                    (item) => (
                        <div
                            className="pgn-skeleton pgn-skeleton-small"
                            key={item}
                        >
                            <div className="pgn-sk-image" />

                            <div className="pgn-sk-content">
                                <span />
                                <strong />
                                <small />
                            </div>
                        </div>
                    )
                )}
            </div>
        </div>
    );
}

function EmptyState({
    blogUrl,
    failed = false,
}) {
    return (
        <div className="pgn-empty">
            <span className="pgn-empty-icon">
                <i className="bi bi-journal-text" />
            </span>

            <div>
                <strong>
                    {failed
                        ? 'Não foi possível carregar os conteúdos agora.'
                        : 'Novos conteúdos estão a caminho.'}
                </strong>

                <p>
                    Enquanto isso, você pode
                    explorar o blog completo do
                    PsiGestor.
                </p>
            </div>

            <a
                href={blogUrl}
                className="pgn-empty-link"
            >
                Ir para o blog

                <i className="bi bi-arrow-right" />
            </a>
        </div>
    );
}

export default function NewsSection({
    endpoint,
    blogUrl,
}) {
    const [
        articles,
        setArticles,
    ] = useState([]);

    const [
        loading,
        setLoading,
    ] = useState(true);

    const [
        failed,
        setFailed,
    ] = useState(false);

    const reduceMotion =
        useReducedMotion();

    useEffect(() => {
        const controller =
            new AbortController();

        async function loadNews() {
            try {
                setLoading(true);
                setFailed(false);

                const response =
                    await fetch(
                        endpoint,
                        {
                            method: 'GET',
                            headers: {
                                Accept:
                                    'application/json',
                            },
                            cache:
                                'no-store',
                            signal:
                                controller.signal,
                        }
                    );

                if (!response.ok) {
                    throw new Error(
                        'Falha ao buscar conteúdos.'
                    );
                }

                const data =
                    await response.json();

                setArticles(
                    Array.isArray(data)
                        ? data.slice(
                              0,
                              3
                          )
                        : []
                );
            }
            catch (error) {
                if (
                    error.name !==
                    'AbortError'
                ) {
                    setFailed(true);
                    setArticles([]);
                }
            }
            finally {
                if (
                    !controller.signal
                        .aborted
                ) {
                    setLoading(false);
                }
            }
        }

        loadNews();

        return () => {
            controller.abort();
        };
    }, [endpoint]);

    const normalized =
        useMemo(
            () =>
                articles.map(
                    (article) => ({
                        ...article,

                        excerptText:
                            cleanText(
                                article.excerpt ||
                                    article.subtitle ||
                                    ''
                            ),
                    })
                ),
            [articles]
        );

    function getArticleUrl(
        article
    ) {
        const base =
            blogUrl.replace(
                /\/$/,
                ''
            );

        return (
            base +
            '/' +
            encodeURIComponent(
                article.slug
            )
        );
    }

    const primary =
        normalized[0];

    const secondary =
        normalized.slice(
            1,
            3
        );

    return (
        <section
            id="conteudos"
            className="section-news pgn-section"
            aria-labelledby="pgn-title"
        >
            <div
                className="pgn-orb pgn-orb-one"
                aria-hidden="true"
            />

            <div
                className="pgn-orb pgn-orb-two"
                aria-hidden="true"
            />

            <div className="pgn-container">

                {/* ============================
                    CABEÇALHO
                ============================ */}

                <motion.header
                    className="pgn-heading"
                    initial={{
                        opacity: 0,
                        y: 22,
                    }}
                    whileInView={{
                        opacity: 1,
                        y: 0,
                    }}
                    viewport={{
                        once: true,
                        amount: 0.45,
                    }}
                    transition={{
                        duration: 0.65,
                        ease: [
                            0.16,
                            1,
                            0.3,
                            1,
                        ],
                    }}
                >
                    <div className="pgn-eyebrow">
                        <span>
                            03
                        </span>

                        Ideias para explorar
                    </div>

                    <div className="pgn-heading-row">
                        <div>
                            <h2 id="pgn-title">
                                Conteúdos para descobrir,
                                <span>
                                    {' '}
                                    entender e ampliar perspectivas.
                                </span>
                            </h2>

                            <p>
                                Um espaço para ideias, informação
                                e temas diversos — dentro e fora
                                do universo PsiGestor.
                            </p>
                        </div>

                        <a
                            href={blogUrl}
                            className="pgn-heading-link"
                        >
                            Explorar o blog

                            <span>
                                <i className="bi bi-arrow-up-right" />
                            </span>
                        </a>
                    </div>
                </motion.header>

                {/* ============================
                    CARREGANDO
                ============================ */}

                {loading && (
                    <Skeleton />
                )}

                {/* ============================
                    VAZIO / ERRO
                ============================ */}

                {!loading &&
                    normalized.length ===
                        0 && (
                        <EmptyState
                            blogUrl={
                                blogUrl
                            }
                            failed={
                                failed
                            }
                        />
                    )}

                {/* ============================
                    CONTEÚDOS
                ============================ */}

                {!loading &&
                    primary && (
                        <div className="pgn-layout">

                            {/* PRINCIPAL */}

                            <motion.a
                                href={getArticleUrl(
                                    primary
                                )}
                                className="pgn-featured"
                                initial={{
                                    opacity: 0,
                                    y: 28,
                                }}
                                whileInView={{
                                    opacity: 1,
                                    y: 0,
                                }}
                                viewport={{
                                    once: true,
                                    amount: 0.2,
                                }}
                                transition={{
                                    duration: 0.72,
                                    ease: [
                                        0.16,
                                        1,
                                        0.3,
                                        1,
                                    ],
                                }}
                            >
                                <div className="pgn-featured-media">
                                    <ArticleImage
                                        article={
                                            primary
                                        }
                                        eager
                                    />

                                    <span className="pgn-featured-badge">
                                        Destaque
                                    </span>

                                    <span className="pgn-media-arrow">
                                        <i className="bi bi-arrow-up-right" />
                                    </span>
                                </div>

                                <div className="pgn-featured-content">
                                    <ArticleMeta
                                        article={
                                            primary
                                        }
                                    />

                                    <h3>
                                        {
                                            primary.title
                                        }
                                    </h3>

                                    {primary.excerptText && (
                                        <p>
                                            {
                                                primary.excerptText
                                            }
                                        </p>
                                    )}

                                    <span className="pgn-read">
                                        Ler conteúdo

                                        <i className="bi bi-arrow-right" />
                                    </span>
                                </div>
                            </motion.a>

                            {/* SECUNDÁRIAS */}

                            <div className="pgn-side">
                                {secondary.map(
                                    (
                                        article,
                                        index
                                    ) => (
                                        <motion.a
                                            href={getArticleUrl(
                                                article
                                            )}
                                            className="pgn-secondary"
                                            key={
                                                article.slug
                                            }
                                            initial={{
                                                opacity: 0,
                                                y: 24,
                                            }}
                                            whileInView={{
                                                opacity: 1,
                                                y: 0,
                                            }}
                                            viewport={{
                                                once: true,
                                                amount: 0.28,
                                            }}
                                            transition={{
                                                duration: 0.65,
                                                delay:
                                                    index *
                                                    0.09,
                                                ease: [
                                                    0.16,
                                                    1,
                                                    0.3,
                                                    1,
                                                ],
                                            }}
                                        >
                                            <div className="pgn-secondary-media">
                                                <ArticleImage
                                                    article={
                                                        article
                                                    }
                                                />

                                                <span className="pgn-media-arrow">
                                                    <i className="bi bi-arrow-up-right" />
                                                </span>
                                            </div>

                                            <div className="pgn-secondary-content">
                                                <ArticleMeta
                                                    article={
                                                        article
                                                    }
                                                />

                                                <h3>
                                                    {
                                                        article.title
                                                    }
                                                </h3>

                                                {article.excerptText && (
                                                    <p>
                                                        {
                                                            article.excerptText
                                                        }
                                                    </p>
                                                )}

                                                <span className="pgn-read">
                                                    Ler conteúdo

                                                    <i className="bi bi-arrow-right" />
                                                </span>
                                            </div>
                                        </motion.a>
                                    )
                                )}

                                {secondary.length ===
                                    0 && (
                                    <a
                                        href={
                                            blogUrl
                                        }
                                        className="pgn-blog-promo"
                                    >
                                        <span>
                                            <i className="bi bi-journal-richtext" />
                                        </span>

                                        <div>
                                            <small>
                                                Mais no blog
                                            </small>

                                            <strong>
                                                Explore todos
                                                os conteúdos
                                                do PsiGestor.
                                            </strong>
                                        </div>

                                        <i className="bi bi-arrow-up-right" />
                                    </a>
                                )}
                            </div>
                        </div>
                    )}

                {/* ============================
                    RODAPÉ
                ============================ */}

                {!loading &&
                    normalized.length >
                        0 && (
                        <motion.div
                            className="pgn-footer"
                            initial={{
                                opacity: 0,
                                y: 15,
                            }}
                            whileInView={{
                                opacity: 1,
                                y: 0,
                            }}
                            viewport={{
                                once: true,
                                amount: 0.6,
                            }}
                            transition={{
                                duration: 0.55,
                            }}
                        >
                            <span>
                                <i className="bi bi-book" />

                                Novos assuntos.
                                Diferentes perspectivas.
                            </span>

                            <a
                                href={
                                    blogUrl
                                }
                            >
                                Ver todos os conteúdos

                                <i className="bi bi-arrow-right" />
                            </a>
                        </motion.div>
                    )}
            </div>
        </section>
    );
}