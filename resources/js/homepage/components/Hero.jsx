import {
    motion,
    useMotionValue,
    useReducedMotion,
    useSpring,
    useTransform,
} from 'motion/react';

const copyContainer = {
    hidden: {},
    visible: {
        transition: {
            staggerChildren: 0.09,
            delayChildren: 0.06,
        },
    },
};

const copyItem = {
    hidden: {
        opacity: 0,
        y: 22,
    },
    visible: {
        opacity: 1,
        y: 0,
        transition: {
            duration: 0.62,
            ease: [0.16, 1, 0.3, 1],
        },
    },
};

const productEntrance = {
    hidden: {
        opacity: 0,
        y: 30,
        scale: 0.975,
    },
    visible: {
        opacity: 1,
        y: 0,
        scale: 1,
        transition: {
            duration: 0.82,
            delay: 0.18,
            ease: [0.16, 1, 0.3, 1],
        },
    },
};

function StoryCard({
    className = '',
    icon,
    eyebrow,
    children,
    delay,
    float = 4,
}) {
    const reduceMotion = useReducedMotion();

    return (
        <motion.div
            className={`pg-story-card ${className}`}
            initial={{
                opacity: 0,
                y: 16,
                scale: 0.94,
            }}
            animate={{
                opacity: 1,
                y: 0,
                scale: 1,
            }}
            transition={{
                duration: 0.58,
                delay,
                ease: [0.16, 1, 0.3, 1],
            }}
        >
            <motion.div
                className="pg-story-card-surface"
                animate={
                    reduceMotion
                        ? undefined
                        : {
                              y: [0, -float, 0],
                          }
                }
                transition={{
                    duration: 5.2,
                    delay,
                    repeat: Infinity,
                    ease: 'easeInOut',
                }}
            >
                <span className="pg-story-card-icon">
                    <i className={`bi ${icon}`} />
                </span>

                <span className="pg-story-card-copy">
                    <small>{eyebrow}</small>
                    <strong>{children}</strong>
                </span>
            </motion.div>
        </motion.div>
    );
}

function MetricCard({
    title,
    icon,
    value,
    note,
    noteClassName = '',
    delay,
}) {
    return (
        <motion.div
            className="pg-metric-card"
            initial={{
                opacity: 0,
                y: 10,
            }}
            animate={{
                opacity: 1,
                y: 0,
            }}
            transition={{
                duration: 0.5,
                delay,
                ease: [0.16, 1, 0.3, 1],
            }}
        >
            <div className="pg-metric-head">
                <span>{title}</span>
                <i className={`bi ${icon}`} />
            </div>

            <strong>{value}</strong>

            <small className={noteClassName}>
                {note}
            </small>
        </motion.div>
    );
}

function ScheduleItem({
    time,
    initials,
    name,
    mode,
    avatarClassName = '',
    status,
    delay,
}) {
    return (
        <motion.div
            className={`pg-schedule-item ${
                status ? 'is-current' : ''
            }`}
            initial={{
                opacity: 0,
                x: 12,
            }}
            animate={{
                opacity: 1,
                x: 0,
            }}
            transition={{
                duration: 0.5,
                delay,
                ease: [0.16, 1, 0.3, 1],
            }}
        >
            <span className="pg-schedule-time">
                {time}
            </span>

            <span
                className={`pg-patient-avatar ${avatarClassName}`}
            >
                {initials}
            </span>

            <span className="pg-patient-info">
                <strong>{name}</strong>
                <small>{mode}</small>
            </span>

            {status ? (
                <motion.span
                    className="pg-session-status"
                    initial={{
                        opacity: 0,
                        scale: 0.9,
                    }}
                    animate={{
                        opacity: 1,
                        scale: 1,
                    }}
                    transition={{
                        duration: 0.4,
                        delay: delay + 0.25,
                    }}
                >
                    {status}
                </motion.span>
            ) : (
                <span className="pg-session-more">
                    •••
                </span>
            )}
        </motion.div>
    );
}

function ProductPreview() {
    const reduceMotion = useReducedMotion();

    const pointerX = useMotionValue(0);
    const pointerY = useMotionValue(0);

    const smoothX = useSpring(pointerX, {
        stiffness: 115,
        damping: 24,
        mass: 0.6,
    });

    const smoothY = useSpring(pointerY, {
        stiffness: 115,
        damping: 24,
        mass: 0.6,
    });

    const rotateY = useTransform(
        smoothX,
        [-0.5, 0.5],
        [-2.6, 2.6]
    );

    const rotateX = useTransform(
        smoothY,
        [-0.5, 0.5],
        [2.4, -2.4]
    );

    const translateX = useTransform(
        smoothX,
        [-0.5, 0.5],
        [-3, 3]
    );

    function handlePointerMove(event) {
        if (reduceMotion) {
            return;
        }

        const rect =
            event.currentTarget.getBoundingClientRect();

        pointerX.set(
            (event.clientX - rect.left) /
                rect.width -
                0.5
        );

        pointerY.set(
            (event.clientY - rect.top) /
                rect.height -
                0.5
        );
    }

    function resetPointer() {
        pointerX.set(0);
        pointerY.set(0);
    }

    const chartHeights = [
        42,
        57,
        48,
        71,
        62,
        86,
        77,
    ];

    return (
        <div
            className="pg-preview-stage"
            onPointerMove={handlePointerMove}
            onPointerLeave={resetPointer}
        >
            <div
                className="pg-product-halo"
                aria-hidden="true"
            />

            <motion.div
                className="pg-dashboard-perspective"
                variants={productEntrance}
                initial="hidden"
                animate="visible"
                style={
                    reduceMotion
                        ? undefined
                        : {
                              rotateX,
                              rotateY,
                              x: translateX,
                          }
                }
            >
                <div className="pg-dashboard">
                    <aside className="pg-dashboard-sidebar">
                        <div className="pg-dashboard-brand">
                            <span className="pg-dashboard-brand-mark">
                                Ψ
                            </span>
                        </div>

                        <div className="pg-dashboard-nav">
                            <span className="is-active">
                                <i className="bi bi-grid-1x2-fill" />
                            </span>

                            <span>
                                <i className="bi bi-calendar3" />
                            </span>

                            <span>
                                <i className="bi bi-people" />
                            </span>

                            <span>
                                <i className="bi bi-journal-medical" />
                            </span>

                            <span>
                                <i className="bi bi-wallet2" />
                            </span>
                        </div>

                        <div className="pg-dashboard-avatar">
                            P
                        </div>
                    </aside>

                    <main className="pg-dashboard-content">
                        <header className="pg-dashboard-header">
                            <div>
                                <span className="pg-dashboard-overline">
                                    Visão geral
                                </span>

                                <h3>
                                    Sua clínica hoje
                                </h3>
                            </div>

                            <div className="pg-dashboard-header-actions">
                                <motion.span
                                    className="pg-sync-pill"
                                    initial={{
                                        opacity: 0,
                                        scale: 0.92,
                                    }}
                                    animate={{
                                        opacity: 1,
                                        scale: 1,
                                    }}
                                    transition={{
                                        duration: 0.5,
                                        delay: 1.9,
                                        ease: [
                                            0.16,
                                            1,
                                            0.3,
                                            1,
                                        ],
                                    }}
                                >
                                    <motion.span
                                        className="pg-sync-dot"
                                        initial={{
                                            scale: 0,
                                        }}
                                        animate={{
                                            scale: 1,
                                        }}
                                        transition={{
                                            type: 'spring',
                                            stiffness: 400,
                                            damping: 18,
                                            delay: 2.05,
                                        }}
                                    />

                                    Google Agenda
                                </motion.span>

                                <span className="pg-dashboard-bell">
                                    <i className="bi bi-bell" />
                                </span>
                            </div>
                        </header>

                        <div className="pg-dashboard-metrics">
                            <MetricCard
                                title="Sessões hoje"
                                icon="bi-calendar-check"
                                value="06"
                                note="2 atendimentos pela manhã"
                                delay={0.72}
                            />

                            <MetricCard
                                title="Recebido no mês"
                                icon="bi-graph-up-arrow"
                                value="R$ 7.840"
                                note="+12,4% neste período"
                                noteClassName="is-positive"
                                delay={0.86}
                            />

                            <MetricCard
                                title="Pacientes ativos"
                                icon="bi-people"
                                value="32"
                                note="Histórico centralizado"
                                delay={1}
                            />
                        </div>

                        <div className="pg-dashboard-grid">
                            <section className="pg-schedule-card">
                                <div className="pg-panel-heading">
                                    <div>
                                        <span>
                                            Agenda
                                        </span>

                                        <strong>
                                            Próximas sessões
                                        </strong>
                                    </div>

                                    <span className="pg-panel-link">
                                        Hoje
                                    </span>
                                </div>

                                <div className="pg-schedule-list">
                                    <ScheduleItem
                                        time="09:00"
                                        initials="AM"
                                        name="Ana M."
                                        mode="Atendimento online"
                                        status="Em breve"
                                        delay={1.12}
                                    />

                                    <ScheduleItem
                                        time="10:30"
                                        initials="RL"
                                        name="Rafael L."
                                        mode="Consultório"
                                        avatarClassName="is-purple"
                                        delay={1.3}
                                    />

                                    <ScheduleItem
                                        time="14:00"
                                        initials="CM"
                                        name="Carla M."
                                        mode="Atendimento online"
                                        avatarClassName="is-green"
                                        delay={1.48}
                                    />
                                </div>
                            </section>

                            <motion.section
                                className="pg-finance-card"
                                initial={{
                                    opacity: 0,
                                    y: 10,
                                }}
                                animate={{
                                    opacity: 1,
                                    y: 0,
                                }}
                                transition={{
                                    duration: 0.55,
                                    delay: 1.55,
                                    ease: [
                                        0.16,
                                        1,
                                        0.3,
                                        1,
                                    ],
                                }}
                            >
                                <div className="pg-panel-heading">
                                    <div>
                                        <span>
                                            Financeiro
                                        </span>

                                        <strong>
                                            Recebimentos
                                        </strong>
                                    </div>

                                    <motion.span
                                        className="pg-growth"
                                        initial={{
                                            opacity: 0,
                                            scale: 0.85,
                                        }}
                                        animate={{
                                            opacity: 1,
                                            scale: 1,
                                        }}
                                        transition={{
                                            delay: 2.15,
                                            type: 'spring',
                                            stiffness: 320,
                                            damping: 18,
                                        }}
                                    >
                                        +12%
                                    </motion.span>
                                </div>

                                <div className="pg-chart">
                                    {chartHeights.map(
                                        (height, index) => (
                                            <div
                                                className="pg-chart-column"
                                                key={index}
                                            >
                                                <motion.span
                                                    initial={{
                                                        height: 0,
                                                    }}
                                                    animate={{
                                                        height: `${height}%`,
                                                    }}
                                                    transition={{
                                                        duration: 0.62,
                                                        delay:
                                                            1.75 +
                                                            index *
                                                                0.07,
                                                        ease: [
                                                            0.16,
                                                            1,
                                                            0.3,
                                                            1,
                                                        ],
                                                    }}
                                                />
                                            </div>
                                        )
                                    )}
                                </div>

                                <div className="pg-chart-labels">
                                    <span>Seg</span>
                                    <span>Ter</span>
                                    <span>Qua</span>
                                    <span>Qui</span>
                                    <span>Sex</span>
                                    <span>Sáb</span>
                                    <span>Dom</span>
                                </div>

                                <motion.div
                                    className="pg-finance-footer"
                                    initial={{
                                        opacity: 0,
                                    }}
                                    animate={{
                                        opacity: 1,
                                    }}
                                    transition={{
                                        duration: 0.5,
                                        delay: 2.4,
                                    }}
                                >
                                    <span>
                                        <i className="bi bi-check-circle-fill" />
                                        Pagamentos organizados
                                    </span>
                                </motion.div>
                            </motion.section>
                        </div>
                    </main>
                </div>
            </motion.div>

            <StoryCard
                className="pg-story-card-ai"
                icon="bi-stars"
                eyebrow="Evolução"
                delay={2.55}
                float={4}
            >
                Texto organizado com IA
            </StoryCard>

            <StoryCard
                className="pg-story-card-calendar"
                icon="bi-calendar2-check"
                eyebrow="Agenda"
                delay={2.2}
                float={3}
            >
                Sincronizada
            </StoryCard>

            <StoryCard
                className="pg-story-card-payment"
                icon="bi-check2-circle"
                eyebrow="Sessão"
                delay={2.95}
                float={3}
            >
                Pagamento confirmado
            </StoryCard>
        </div>
    );
}

export default function Hero({
    registerUrl,
}) {
    const reduceMotion =
        useReducedMotion();

    function trackLead() {
        if (
            typeof window !== 'undefined' &&
            typeof window.fbq === 'function'
        ) {
            window.fbq(
                'track',
                'Lead'
            );
        }
    }

    function scrollToFeatures() {
        const section =
            document.querySelector(
                '.section-features'
            );

        if (!section) {
            return;
        }

        section.scrollIntoView({
            behavior: reduceMotion
                ? 'auto'
                : 'smooth',
            block: 'start',
        });
    }

    return (
        <section
            id="inicio"
            className="pg-home-hero"
            aria-labelledby="pg-home-title"
        >
            <div
                className="pg-hero-grid"
                aria-hidden="true"
            />

            <div
                className="pg-hero-glow pg-hero-glow-left"
                aria-hidden="true"
            />

            <div
                className="pg-hero-glow pg-hero-glow-right"
                aria-hidden="true"
            />

            <div className="pg-home-container">
                <motion.div
                    className="pg-hero-copy"
                    variants={copyContainer}
                    initial="hidden"
                    animate="visible"
                >
                    <motion.div
                        className="pg-hero-eyebrow"
                        variants={copyItem}
                    >
                        <span className="pg-eyebrow-icon">
                            <i className="bi bi-heart-pulse" />
                        </span>

                        Gestão clínica para
                        profissionais da saúde mental
                    </motion.div>

                    <motion.h1
                        id="pg-home-title"
                        variants={copyItem}
                    >
                        <span className="pg-title-line">
                            Mais tempo para{' '}
                            <em>cuidar.</em>
                        </span>

                        <span className="pg-title-line pg-title-line-second">
                            Menos tempo{' '}
                            <strong>
                                organizando.
                            </strong>
                        </span>
                    </motion.h1>

                    <motion.p
                        className="pg-hero-description"
                        variants={copyItem}
                    >
                        Agenda, pacientes, prontuário
                        e financeiro conectados em um
                        só lugar — sem planilhas,
                        retrabalho ou informações
                        espalhadas.
                    </motion.p>

                    <motion.div
                        className="pg-hero-actions"
                        variants={copyItem}
                    >
                        <a
                            className="pg-primary-cta"
                            href={registerUrl}
                            onClick={trackLead}
                        >
                            <span>
                                Começar 10 dias grátis
                            </span>

                            <i className="bi bi-arrow-right" />
                        </a>

                        <button
                            className="pg-secondary-cta"
                            type="button"
                            onClick={scrollToFeatures}
                        >
                            <span className="pg-play-icon">
                                <i className="bi bi-play-fill" />
                            </span>

                            Ver como funciona
                        </button>
                    </motion.div>

                    <motion.div
                        className="pg-hero-trust"
                        variants={copyItem}
                    >
                        <span>
                            <i className="bi bi-check2" />
                            10 dias grátis
                        </span>

                        <span>
                            <i className="bi bi-check2" />
                            Sem cartão de crédito
                        </span>

                        <span>
                            <i className="bi bi-check2" />
                            Acesso imediato
                        </span>
                    </motion.div>
                </motion.div>

                <div className="pg-hero-product">
                    <ProductPreview />
                </div>
            </div>
        </section>
    );
}