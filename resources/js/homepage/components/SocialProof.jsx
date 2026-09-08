import {
    useState,
} from 'react';

import {
    AnimatePresence,
    motion,
    useReducedMotion,
} from 'motion/react';

const testimonials = [
    {
        id: 'camila',
        name: 'Camila Ferreira',
        initials: 'CF',
        profession: 'Psicóloga',
        register: 'CRP 06/2314',
        text:
            'O PsiGestor trouxe leveza e praticidade para meu consultório. Estou encantada!',
        theme: 'Rotina mais leve',
        icon: 'bi-heart',
    },
    {
        id: 'juliana',
        name: 'Juliana Salles',
        initials: 'JS',
        profession: 'Psicóloga',
        register: 'CRP 06/2891',
        text:
            'Economizo tempo e reduzi os esquecimentos dos pacientes com os lembretes via WhatsApp!',
        theme: 'Agenda e lembretes',
        icon: 'bi-whatsapp',
    },
    {
        id: 'joana',
        name: 'Joana Silva',
        initials: 'JS',
        profession: 'Psicóloga',
        register: 'CRP 06/4510',
        text:
            'Minhas anotações de evolução nunca foram tão organizadas. A timeline é sensacional.',
        theme: 'Evoluções',
        icon: 'bi-journal-text',
    },
    {
        id: 'carlos',
        name: 'Carlos Lima',
        initials: 'CL',
        profession: 'Psiquiatra',
        register: 'CRM 0611987',
        text:
            'Com o PsiGestor, consegui parar de usar mil planilhas e centralizar tudo em um só lugar.',
        theme: 'Tudo centralizado',
        icon: 'bi-grid',
    },
];

const reveal = {
    hidden: {
        opacity: 0,
        y: 22,
    },

    visible: {
        opacity: 1,
        y: 0,

        transition: {
            duration: 0.65,
            ease: [0.16, 1, 0.3, 1],
        },
    },
};

function Avatar({
    testimonial,
    compact = false,
}) {
    return (
        <span
            className={`pgs2-avatar ${
                compact
                    ? 'is-compact'
                    : ''
            }`}
        >
            {testimonial.initials}
        </span>
    );
}

function Person({
    testimonial,
    compact = false,
}) {
    return (
        <div
            className={`pgs2-person ${
                compact
                    ? 'is-compact'
                    : ''
            }`}
        >
            <Avatar
                testimonial={testimonial}
                compact={compact}
            />

            <span className="pgs2-person-copy">
                <strong>
                    {testimonial.name}
                </strong>

                <small>
                    {testimonial.profession}

                    <span>
                        ·
                    </span>

                    {testimonial.register}
                </small>
            </span>
        </div>
    );
}

export default function SocialProof() {
    const [
        activeId,
        setActiveId,
    ] = useState(
        testimonials[0].id
    );

    const reduceMotion =
        useReducedMotion();

    const activeIndex =
        testimonials.findIndex(
            (testimonial) =>
                testimonial.id ===
                activeId
        );

    const active =
        testimonials[
            activeIndex >= 0
                ? activeIndex
                : 0
        ];

    return (
        <section
            id="depoimentos"
            className="pgs2-section"
            aria-labelledby="pgs2-title"
        >
            <div
                className="pgs2-grid"
                aria-hidden="true"
            />

            <div
                className="pgs2-glow pgs2-glow-one"
                aria-hidden="true"
            />

            <div
                className="pgs2-glow pgs2-glow-two"
                aria-hidden="true"
            />

            <div className="pgs2-container">

                {/* ==============================
                    HEADING
                ============================== */}

                <motion.header
                    className="pgs2-heading"
                    variants={reveal}
                    initial="hidden"
                    whileInView="visible"
                    viewport={{
                        once: true,
                        amount: 0.4,
                    }}
                >
                    <div className="pgs2-eyebrow">
                        <span>
                            02
                        </span>

                        Feito para quem cuida
                    </div>

                    <h2 id="pgs2-title">
                        A rotina fica mais leve
                        <span>
                            {' '}
                            quando tudo encontra seu lugar.
                        </span>
                    </h2>

                    <p>
                        Experiências de profissionais
                        que encontraram no PsiGestor uma
                        forma mais simples de organizar
                        o dia a dia clínico.
                    </p>
                </motion.header>

                {/* ==============================
                    STAGE
                ============================== */}

                <div className="pgs2-stage">

                    {/* ==============================
                        DEPOIMENTO PRINCIPAL
                    ============================== */}

                    <motion.article
                        className="pgs2-featured"
                        initial={{
                            opacity: 0,
                            y: 28,
                            scale: 0.985,
                        }}
                        whileInView={{
                            opacity: 1,
                            y: 0,
                            scale: 1,
                        }}
                        viewport={{
                            once: true,
                            amount: 0.28,
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
                        <div
                            className="pgs2-quote-mark"
                            aria-hidden="true"
                        >
                            “
                        </div>

                        <div className="pgs2-featured-top">
                            <span className="pgs2-featured-label">
                                <i
                                    className={`bi ${active.icon}`}
                                />

                                {active.theme}
                            </span>

                            <span className="pgs2-counter">
                                <strong>
                                    {String(
                                        activeIndex + 1
                                    ).padStart(
                                        2,
                                        '0'
                                    )}
                                </strong>

                                <span>
                                    /
                                </span>

                                {String(
                                    testimonials.length
                                ).padStart(
                                    2,
                                    '0'
                                )}
                            </span>
                        </div>

                        <div className="pgs2-featured-content">
                            <AnimatePresence
                                mode="wait"
                                initial={false}
                            >
                                <motion.div
                                    key={active.id}
                                    initial={
                                        reduceMotion
                                            ? {
                                                  opacity: 0,
                                              }
                                            : {
                                                  opacity: 0,
                                                  y: 11,
                                                  filter:
                                                      'blur(4px)',
                                              }
                                    }
                                    animate={{
                                        opacity: 1,
                                        y: 0,
                                        filter:
                                            'blur(0px)',
                                    }}
                                    exit={
                                        reduceMotion
                                            ? {
                                                  opacity: 0,
                                              }
                                            : {
                                                  opacity: 0,
                                                  y: -8,
                                                  filter:
                                                      'blur(3px)',
                                              }
                                    }
                                    transition={{
                                        duration: 0.3,
                                        ease: [
                                            0.16,
                                            1,
                                            0.3,
                                            1,
                                        ],
                                    }}
                                >
                                    <blockquote>
                                        “{active.text}”
                                    </blockquote>

                                    <Person
                                        testimonial={
                                            active
                                        }
                                    />
                                </motion.div>
                            </AnimatePresence>
                        </div>

                        <div className="pgs2-featured-bottom">
                            <div className="pgs2-progress">
                                {testimonials.map(
                                    (
                                        testimonial,
                                        index
                                    ) => (
                                        <button
                                            key={
                                                testimonial.id
                                            }
                                            type="button"
                                            className={
                                                testimonial.id ===
                                                activeId
                                                    ? 'is-active'
                                                    : ''
                                            }
                                            onClick={() =>
                                                setActiveId(
                                                    testimonial.id
                                                )
                                            }
                                            aria-label={`Ver depoimento de ${testimonial.name}`}
                                        >
                                            <span />
                                        </button>
                                    )
                                )}
                            </div>

                            <span className="pgs2-hint">
                                Selecione outro relato
                                <i className="bi bi-arrow-right" />
                            </span>
                        </div>
                    </motion.article>

                    {/* ==============================
                        NAVEGADOR
                    ============================== */}

                    <motion.aside
                        className="pgs2-browser"
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
                            amount: 0.25,
                        }}
                        transition={{
                            duration: 0.7,
                            delay: 0.08,
                            ease: [
                                0.16,
                                1,
                                0.3,
                                1,
                            ],
                        }}
                    >
                        <div className="pgs2-browser-heading">
                            <div>
                                <small>
                                    Outros relatos
                                </small>

                                <strong>
                                    Diferentes rotinas.
                                    O mesmo objetivo:
                                    simplificar.
                                </strong>
                            </div>

                            <span className="pgs2-browser-icon">
                                <i className="bi bi-chat-quote" />
                            </span>
                        </div>

                        <div className="pgs2-browser-list">
                            {testimonials.map(
                                (
                                    testimonial,
                                    index
                                ) => {
                                    const selected =
                                        testimonial.id ===
                                        activeId;

                                    return (
                                        <motion.button
                                            type="button"
                                            key={
                                                testimonial.id
                                            }
                                            className={`pgs2-browser-item ${
                                                selected
                                                    ? 'is-active'
                                                    : ''
                                            }`}
                                            onClick={() =>
                                                setActiveId(
                                                    testimonial.id
                                                )
                                            }
                                            aria-pressed={
                                                selected
                                            }
                                            initial={{
                                                opacity: 0,
                                                x: 12,
                                            }}
                                            whileInView={{
                                                opacity: 1,
                                                x: 0,
                                            }}
                                            viewport={{
                                                once: true,
                                                amount: 0.5,
                                            }}
                                            transition={{
                                                duration: 0.48,
                                                delay:
                                                    0.16 +
                                                    index *
                                                        0.07,
                                                ease: [
                                                    0.16,
                                                    1,
                                                    0.3,
                                                    1,
                                                ],
                                            }}
                                        >
                                            <Person
                                                testimonial={
                                                    testimonial
                                                }
                                                compact
                                            />

                                            <span className="pgs2-browser-theme">
                                                <i
                                                    className={`bi ${testimonial.icon}`}
                                                />

                                                {
                                                    testimonial.theme
                                                }
                                            </span>

                                            <span className="pgs2-browser-arrow">
                                                <i className="bi bi-chevron-right" />
                                            </span>
                                        </motion.button>
                                    );
                                }
                            )}
                        </div>

                        <div className="pgs2-browser-footer">
                            <small>
                                Temas que mais aparecem
                            </small>

                            <div className="pgs2-topic-list">
                                <span>
                                    <i className="bi bi-calendar2-check" />
                                    Agenda
                                </span>

                                <span>
                                    <i className="bi bi-whatsapp" />
                                    Lembretes
                                </span>

                                <span>
                                    <i className="bi bi-journal-text" />
                                    Evoluções
                                </span>

                                <span>
                                    <i className="bi bi-wallet2" />
                                    Financeiro
                                </span>
                            </div>
                        </div>
                    </motion.aside>
                </div>
            </div>
        </section>
    );
}