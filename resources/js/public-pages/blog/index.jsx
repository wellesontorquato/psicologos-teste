import React, {
    useEffect,
} from 'react';

import {
    createRoot,
} from 'react-dom/client';

import {
    animate,
    inView,
} from 'motion';

import '../../../css/public-blog.css';

/**
 * Enhancement visual somente.
 *
 * Blade continua responsável por:
 * - dados;
 * - busca;
 * - artigos;
 * - imagens;
 * - links;
 * - paginação.
 */
function BlogMotionController() {
    useEffect(() => {
        const reduceMotion =
            window.matchMedia(
                '(prefers-reduced-motion: reduce)'
            ).matches;

        if (reduceMotion) {
            return undefined;
        }

        const cleanupFunctions = [];

        /*
         * Entradas editoriais.
         */
        const revealItems =
            Array.from(
                document.querySelectorAll(
                    '[data-blog-reveal]'
                )
            );

        revealItems.forEach(
            (
                item,
                index
            ) => {
                item.style.opacity = '0';
                item.style.transform =
                    'translateY(14px)';

                const stop =
                    inView(
                        item,
                        () => {
                            animate(
                                item,
                                {
                                    opacity: [
                                        0,
                                        1,
                                    ],
                                    y: [
                                        14,
                                        0,
                                    ],
                                },
                                {
                                    duration:
                                        0.48,
                                    delay:
                                        Math.min(
                                            index *
                                                0.022,
                                            0.09
                                        ),
                                    ease: [
                                        0.16,
                                        1,
                                        0.3,
                                        1,
                                    ],
                                }
                            );
                        },
                        {
                            amount: 0.08,
                        }
                    );

                cleanupFunctions.push(
                    stop
                );
            }
        );

        /*
         * Linhas editoriais.
         */
        const editorialLines =
            Array.from(
                document.querySelectorAll(
                    [
                        '.pgb-masthead-rule',
                        '.pgb-feed-line',
                    ].join(',')
                )
            );

        editorialLines.forEach(
            (
                line,
                index
            ) => {
                line.style.transformOrigin =
                    'left center';

                const stop =
                    inView(
                        line,
                        () => {
                            animate(
                                line,
                                {
                                    scaleX: [
                                        0.15,
                                        1,
                                    ],
                                    opacity: [
                                        0.4,
                                        1,
                                    ],
                                },
                                {
                                    duration:
                                        0.72,
                                    delay:
                                        index *
                                        0.05,
                                    ease: [
                                        0.16,
                                        1,
                                        0.3,
                                        1,
                                    ],
                                }
                            );
                        },
                        {
                            amount: 0.2,
                        }
                    );

                cleanupFunctions.push(
                    stop
                );
            }
        );

        return () => {
            cleanupFunctions.forEach(
                (cleanup) => {
                    if (
                        typeof cleanup ===
                        'function'
                    ) {
                        cleanup();
                    }
                }
            );
        };
    }, []);

    return null;
}

const rootElement =
    document.getElementById(
        'psigestor-blog-motion'
    );

if (rootElement) {
    createRoot(
        rootElement
    ).render(
        <BlogMotionController />
    );
}