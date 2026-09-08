import React from 'react';
import {
    createRoot,
} from 'react-dom/client';

import {
    motion,
    useReducedMotion,
} from 'motion/react';

import '../../../css/public-plans.css';

const plans = [
    {
        id: 'mensal',
        eyebrow: 'Flexibilidade',
        title: 'Mensal',
        price: '39,90',
        period: 'por mês',
        equivalent: 'R$ 39,90/mês',
        billing:
            'Cobrança recorrente mensal.',
        savings: null,
        savingsValue: null,
        badge: null,
        tone: 'plain',
    },
    {
        id: 'trimestral',
        eyebrow: 'Equilíbrio',
        title: 'Trimestral',
        price: '104,90',
        period: 'a cada 3 meses',
        equivalent: 'R$ 34,97/mês equivalente',
        billing:
            'Cobrança recorrente trimestral.',
        savings: '12,36%',
        savingsValue: 'R$ 14,80',
        badge: 'Mais popular',
        tone: 'popular',
    },
    {
        id: 'anual',
        eyebrow: 'Maior economia',
        title: 'Anual',
        price: '374,90',
        period: 'por ano',
        equivalent: 'R$ 31,24/mês equivalente',
        billing:
            'Cobrança recorrente anual.',
        savings: '21,71%',
        savingsValue: 'R$ 103,90',
        badge: 'Maior economia',
        tone: 'annual',
    },
];

const features = [
    {
        icon: 'calendar',
        title: 'Agenda conectada',
        text:
            'Organize atendimentos, períodos, remarcações e sua rotina clínica em um único fluxo.',
    },
    {
        icon: 'patient',
        title: 'Pacientes e prontuário',
        text:
            'Informações, histórico e registros clínicos organizados no contexto certo.',
    },
    {
        icon: 'brain',
        title: 'Evoluções com IA',
        text:
            'Transforme os tópicos da sessão em uma evolução estruturada para revisar e registrar.',
    },
    {
        icon: 'wallet',
        title: 'Financeiro',
        text:
            'Acompanhe recebimentos, pendências e informações importantes da rotina financeira.',
    },
    {
        icon: 'link',
        title: 'Ecossistema integrado',
        text:
            'Google Agenda, Google Meet, WhatsApp, arquivos e outros recursos trabalhando juntos.',
    },
    {
        icon: 'chart',
        title: 'Indicadores e organização',
        text:
            'Tenha uma visão mais clara da operação sem espalhar informações por várias ferramentas.',
    },
];

const faq = [
    {
        question:
            'O que muda entre os três planos?',
        answer:
            'O PsiGestor continua completo. O que muda é o período da cobrança e a economia obtida nos ciclos mais longos.',
    },
    {
        question:
            'Como funcionam os 10 dias grátis?',
        answer:
            'Você começa pelo cadastro e recebe 10 dias para conhecer o PsiGestor antes de seguir com uma assinatura.',
    },
    {
        question:
            'As cobranças são recorrentes?',
        answer:
            'Sim. Os ciclos mensal, trimestral e anual são recorrentes de acordo com o plano escolhido.',
    },
    {
        question:
            'Posso cancelar minha assinatura?',
        answer:
            'Sim. Ao cancelar, o acesso permanece disponível até o encerramento do período já contratado.',
    },
];

function Icon({
    name,
    size = 24,
}) {
    const common = {
        width: size,
        height: size,
        viewBox: '0 0 24 24',
        fill: 'none',
        stroke: 'currentColor',
        strokeWidth: 1.8,
        strokeLinecap: 'round',
        strokeLinejoin: 'round',
        'aria-hidden': true,
    };

    if (name === 'calendar') {
        return (
            <svg {...common}>
                <path d="M7 2v3M17 2v3" />
                <path d="M3.5 8.5h17" />
                <rect
                    x="3.5"
                    y="4"
                    width="17"
                    height="17"
                    rx="3"
                />
                <path d="m8 14 2.2 2.2L16 11" />
            </svg>
        );
    }

    if (name === 'patient') {
        return (
            <svg {...common}>
                <circle
                    cx="12"
                    cy="8"
                    r="3.5"
                />
                <path d="M5.5 20c.8-4.1 3-6.2 6.5-6.2s5.7 2.1 6.5 6.2" />
            </svg>
        );
    }

    if (name === 'brain') {
        return (
            <svg {...common}>
                <path d="M9.5 4.2a3 3 0 0 0-5 2.2c0 .6.2 1.2.5 1.7A3.8 3.8 0 0 0 6 15.5v.3A3.2 3.2 0 0 0 9.2 19H10V5.2a2 2 0 0 0-.5-1Z" />
                <path d="M14.5 4.2a3 3 0 0 1 5 2.2c0 .6-.2 1.2-.5 1.7a3.8 3.8 0 0 1-1 7.4v.3a3.2 3.2 0 0 1-3.2 3.2H14V5.2a2 2 0 0 1 .5-1Z" />
                <path d="M7 9.5h3M14 9.5h3M7.5 14H10M14 14h2.5" />
            </svg>
        );
    }

    if (name === 'wallet') {
        return (
            <svg {...common}>
                <path d="M4 6.5A2.5 2.5 0 0 1 6.5 4H18a2 2 0 0 1 2 2v13H6a2 2 0 0 1-2-2Z" />
                <path d="M4 8h16" />
                <path d="M15 12h5v4h-5a2 2 0 1 1 0-4Z" />
            </svg>
        );
    }

    if (name === 'link') {
        return (
            <svg {...common}>
                <path d="M10 13.8 8.2 15.6a3.6 3.6 0 0 1-5.1-5.1l3-3A3.6 3.6 0 0 1 11.2 7" />
                <path d="m14 10.2 1.8-1.8a3.6 3.6 0 0 1 5.1 5.1l-3 3a3.6 3.6 0 0 1-5.1.5" />
                <path d="m8.5 15.5 7-7" />
            </svg>
        );
    }

    if (name === 'chart') {
        return (
            <svg {...common}>
                <path d="M4 20V10" />
                <path d="M10 20V4" />
                <path d="M16 20v-7" />
                <path d="M22 20H2" />
            </svg>
        );
    }

    if (name === 'check') {
        return (
            <svg {...common}>
                <path d="m5 12.5 4.2 4.2L19 7" />
            </svg>
        );
    }

    if (name === 'arrow') {
        return (
            <svg {...common}>
                <path d="M5 12h14" />
                <path d="m14 7 5 5-5 5" />
            </svg>
        );
    }

    if (name === 'spark') {
        return (
            <svg {...common}>
                <path d="M12 2.5 13.8 8l5.7 1.8-5.7 1.8L12 17l-1.8-5.4-5.7-1.8L10.2 8Z" />
                <path d="m18.5 15 .8 2.2 2.2.8-2.2.8-.8 2.2-.8-2.2-2.2-.8 2.2-.8Z" />
            </svg>
        );
    }

    if (name === 'shield') {
        return (
            <svg {...common}>
                <path d="M12 3 20 6v5.7c0 4.6-3.2 7.7-8 9.3-4.8-1.6-8-4.7-8-9.3V6Z" />
                <path d="m8.5 12 2.2 2.2 4.8-5" />
            </svg>
        );
    }

    if (name === 'clock') {
        return (
            <svg {...common}>
                <circle
                    cx="12"
                    cy="12"
                    r="9"
                />
                <path d="M12 7v5l3.5 2" />
            </svg>
        );
    }

    return null;
}

function Reveal({
    children,
    className = '',
    delay = 0,
    y = 24,
}) {
    const reduceMotion =
        useReducedMotion();

    return (
        <motion.div
            className={className}
            initial={
                reduceMotion
                    ? false
                    : {
                          opacity: 0,
                          y,
                      }
            }
            whileInView={
                reduceMotion
                    ? undefined
                    : {
                          opacity: 1,
                          y: 0,
                      }
            }
            viewport={{
                once: true,
                amount: 0.18,
            }}
            transition={{
                duration: 0.62,
                delay,
                ease: [
                    0.22,
                    1,
                    0.36,
                    1,
                ],
            }}
        >
            {children}
        </motion.div>
    );
}

function Hero({
    registerUrl,
}) {
    const reduceMotion =
        useReducedMotion();

    return (
        <section className="pgpl-hero">
            <div
                className="pgpl-hero-orb pgpl-hero-orb-one"
                aria-hidden="true"
            />

            <div
                className="pgpl-hero-orb pgpl-hero-orb-two"
                aria-hidden="true"
            />

            <div className="pgpl-shell pgpl-hero-grid">
                <motion.div
                    className="pgpl-hero-copy"
                    initial={
                        reduceMotion
                            ? false
                            : {
                                  opacity: 0,
                                  y: 28,
                              }
                    }
                    animate={
                        reduceMotion
                            ? undefined
                            : {
                                  opacity: 1,
                                  y: 0,
                              }
                    }
                    transition={{
                        duration: 0.72,
                        ease: [
                            0.22,
                            1,
                            0.36,
                            1,
                        ],
                    }}
                >
                    <span className="pgpl-eyebrow">
                        Planos do PsiGestor
                    </span>

                    <h1>
                        Um único sistema.
                        <span>
                            Três formas de acompanhar seu ritmo.
                        </span>
                    </h1>

                    <p className="pgpl-hero-description">
                        Escolha como prefere organizar a
                        cobrança. Em qualquer ciclo, você
                        continua com o PsiGestor completo
                        para cuidar da sua rotina clínica.
                    </p>

                    <div className="pgpl-hero-actions">
                        <a
                            className="pgpl-button pgpl-button-primary"
                            href={registerUrl}
                        >
                            Começar 10 dias grátis
                            <Icon
                                name="arrow"
                                size={18}
                            />
                        </a>

                        <a
                            className="pgpl-button pgpl-button-secondary"
                            href="#planos"
                        >
                            Comparar planos
                        </a>
                    </div>

                    <div className="pgpl-hero-trust">
                        <span>
                            <Icon
                                name="clock"
                                size={17}
                            />
                            10 dias para testar
                        </span>

                        <span>
                            <Icon
                                name="check"
                                size={17}
                            />
                            Todos os recursos
                        </span>

                        <span>
                            <Icon
                                name="shield"
                                size={17}
                            />
                            Cancele quando quiser
                        </span>
                    </div>
                </motion.div>

                <motion.div
                    className="pgpl-cycle-visual"
                    initial={
                        reduceMotion
                            ? false
                            : {
                                  opacity: 0,
                                  x: 26,
                                  scale: 0.98,
                              }
                    }
                    animate={
                        reduceMotion
                            ? undefined
                            : {
                                  opacity: 1,
                                  x: 0,
                                  scale: 1,
                              }
                    }
                    transition={{
                        duration: 0.8,
                        delay: 0.08,
                        ease: [
                            0.22,
                            1,
                            0.36,
                            1,
                        ],
                    }}
                >
                    <div className="pgpl-cycle-head">
                        <span>
                            O mesmo PsiGestor
                        </span>

                        <strong>
                            em ciclos diferentes
                        </strong>
                    </div>

                    <div className="pgpl-cycle-list">
                        {plans.map(
                            (
                                plan,
                                index
                            ) => (
                                <motion.div
                                    key={
                                        plan.id
                                    }
                                    className={`pgpl-cycle-row pgpl-cycle-row-${plan.id}`}
                                    initial={
                                        reduceMotion
                                            ? false
                                            : {
                                                  opacity: 0,
                                                  x: 28,
                                              }
                                    }
                                    animate={
                                        reduceMotion
                                            ? undefined
                                            : {
                                                  opacity: 1,
                                                  x: 0,
                                              }
                                    }
                                    transition={{
                                        delay:
                                            0.25 +
                                            index *
                                                0.1,
                                        duration:
                                            0.55,
                                    }}
                                >
                                    <div className="pgpl-cycle-meta">
                                        <span>
                                            {
                                                plan.title
                                            }
                                        </span>

                                        {plan.savings ? (
                                            <small>
                                                −
                                                {
                                                    plan.savings
                                                }
                                            </small>
                                        ) : (
                                            <small>
                                                base
                                            </small>
                                        )}
                                    </div>

                                    <div className="pgpl-cycle-track">
                                        <motion.span
                                            initial={
                                                reduceMotion
                                                    ? false
                                                    : {
                                                          scaleX: 0,
                                                      }
                                            }
                                            animate={
                                                reduceMotion
                                                    ? undefined
                                                    : {
                                                          scaleX: 1,
                                                      }
                                            }
                                            transition={{
                                                duration:
                                                    0.8,
                                                delay:
                                                    0.4 +
                                                    index *
                                                        0.1,
                                                ease: [
                                                    0.22,
                                                    1,
                                                    0.36,
                                                    1,
                                                ],
                                            }}
                                        />
                                    </div>

                                    <strong className="pgpl-cycle-price">
                                        R${' '}
                                        {
                                            plan.price
                                        }
                                    </strong>
                                </motion.div>
                            )
                        )}
                    </div>

                    <div className="pgpl-cycle-footer">
                        <span className="pgpl-cycle-spark">
                            <Icon
                                name="spark"
                                size={18}
                            />
                        </span>

                        <p>
                            Quanto maior o
                            ciclo, maior a
                            economia em relação
                            ao mensal.
                        </p>
                    </div>
                </motion.div>
            </div>
        </section>
    );
}

function Pricing({
    registerUrl,
}) {
    return (
        <section
            className="pgpl-pricing"
            id="planos"
        >
            <div className="pgpl-shell">
                <Reveal className="pgpl-section-heading">
                    <span className="pgpl-section-kicker">
                        Escolha seu ciclo
                    </span>

                    <h2>
                        Escolha a cobrança,
                        <span>
                            não o que você pode usar.
                        </span>
                    </h2>

                    <p>
                        Os três planos dão acesso ao
                        mesmo PsiGestor. O que muda é
                        o período de cobrança e a
                        economia.
                    </p>
                </Reveal>

                <div className="pgpl-price-grid">
                    {plans.map(
                        (
                            plan,
                            index
                        ) => (
                            <Reveal
                                key={
                                    plan.id
                                }
                                delay={
                                    index *
                                    0.08
                                }
                                className="pgpl-price-reveal"
                            >
                                <article
                                    className={`pgpl-price-card pgpl-price-card-${plan.tone}`}
                                >
                                    {plan.badge && (
                                        <span className="pgpl-price-badge">
                                            {
                                                plan.badge
                                            }
                                        </span>
                                    )}

                                    <div className="pgpl-price-card-top">
                                        <span className="pgpl-plan-eyebrow">
                                            {
                                                plan.eyebrow
                                            }
                                        </span>

                                        <h3>
                                            Plano{' '}
                                            {
                                                plan.title
                                            }
                                        </h3>

                                        <div className="pgpl-price-value">
                                            <span className="pgpl-price-currency">
                                                R$
                                            </span>

                                            <strong>
                                                {
                                                    plan.price
                                                }
                                            </strong>
                                        </div>

                                        <span className="pgpl-price-period">
                                            {
                                                plan.period
                                            }
                                        </span>

                                        <div className="pgpl-equivalent">
                                            {
                                                plan.equivalent
                                            }
                                        </div>
                                    </div>

                                    {plan.savings ? (
                                        <div className="pgpl-saving-box">
                                            <strong>
                                                Economize{' '}
                                                {
                                                    plan.savings
                                                }
                                            </strong>

                                            <span>
                                                São{' '}
                                                {
                                                    plan.savingsValue
                                                }{' '}
                                                a menos
                                                comparado ao
                                                mensal.
                                            </span>
                                        </div>
                                    ) : (
                                        <div className="pgpl-saving-box pgpl-saving-box-neutral">
                                            <strong>
                                                Sem compromisso
                                                longo
                                            </strong>

                                            <span>
                                                Um ciclo curto
                                                para manter
                                                mais
                                                flexibilidade.
                                            </span>
                                        </div>
                                    )}

                                    <ul className="pgpl-plan-list">
                                        <li>
                                            <span>
                                                <Icon
                                                    name="check"
                                                    size={
                                                        16
                                                    }
                                                />
                                            </span>
                                            PsiGestor
                                            completo
                                        </li>

                                        <li>
                                            <span>
                                                <Icon
                                                    name="check"
                                                    size={
                                                        16
                                                    }
                                                />
                                            </span>
                                            10 dias para
                                            testar
                                        </li>

                                        <li>
                                            <span>
                                                <Icon
                                                    name="check"
                                                    size={
                                                        16
                                                    }
                                                />
                                            </span>
                                            {
                                                plan.billing
                                            }
                                        </li>
                                    </ul>

                                    <a
                                        href={
                                            registerUrl
                                        }
                                        className="pgpl-plan-button"
                                    >
                                        Começar 10
                                        dias grátis
                                        <Icon
                                            name="arrow"
                                            size={
                                                17
                                            }
                                        />
                                    </a>

                                    <small className="pgpl-plan-caption">
                                        Comece pelo
                                        cadastro e
                                        escolha seu ciclo.
                                    </small>
                                </article>
                            </Reveal>
                        )
                    )}
                </div>
            </div>
        </section>
    );
}

function Included({
    featuresUrl,
}) {
    return (
        <section className="pgpl-included">
            <div className="pgpl-shell">
                <Reveal className="pgpl-included-heading">
                    <span className="pgpl-section-kicker">
                        Em todos os planos
                    </span>

                    <h2>
                        Você não precisa escolher
                        <span>
                            quais partes do PsiGestor terá.
                        </span>
                    </h2>

                    <p>
                        O ciclo muda. Sua experiência
                        com o sistema não.
                    </p>
                </Reveal>

                <div className="pgpl-feature-grid">
                    {features.map(
                        (
                            feature,
                            index
                        ) => (
                            <Reveal
                                key={
                                    feature.title
                                }
                                delay={
                                    index *
                                    0.055
                                }
                            >
                                <article className="pgpl-feature-card">
                                    <span className="pgpl-feature-icon">
                                        <Icon
                                            name={
                                                feature.icon
                                            }
                                            size={
                                                25
                                            }
                                        />
                                    </span>

                                    <div>
                                        <h3>
                                            {
                                                feature.title
                                            }
                                        </h3>

                                        <p>
                                            {
                                                feature.text
                                            }
                                        </p>
                                    </div>
                                </article>
                            </Reveal>
                        )
                    )}
                </div>

                <Reveal className="pgpl-features-link-wrap">
                    <a
                        href={featuresUrl}
                        className="pgpl-text-link"
                    >
                        Ver todas as funcionalidades
                        <Icon
                            name="arrow"
                            size={17}
                        />
                    </a>
                </Reveal>
            </div>
        </section>
    );
}

function SavingsStory() {
    return (
        <section className="pgpl-savings">
            <div className="pgpl-shell pgpl-savings-grid">
                <Reveal className="pgpl-savings-copy">
                    <span className="pgpl-dark-kicker">
                        A lógica é simples
                    </span>

                    <h2>
                        Mais tempo no ciclo.
                        <span>
                            Menor custo equivalente.
                        </span>
                    </h2>

                    <p>
                        Você não recebe menos ou mais
                        recursos conforme o plano.
                        A vantagem dos ciclos maiores
                        está na economia sobre o
                        valor mensal.
                    </p>

                    <div className="pgpl-dark-note">
                        <Icon
                            name="spark"
                            size={20}
                        />

                        <span>
                            No anual, a economia chega
                            a <strong>R$ 103,90</strong>.
                        </span>
                    </div>
                </Reveal>

                <Reveal
                    className="pgpl-savings-chart"
                    delay={0.08}
                >
                    <div className="pgpl-chart-head">
                        <span>
                            Custo equivalente por mês
                        </span>

                        <small>
                            comparação visual
                        </small>
                    </div>

                    <div className="pgpl-chart-row">
                        <div className="pgpl-chart-label">
                            <span>
                                Mensal
                            </span>
                            <strong>
                                R$ 39,90
                            </strong>
                        </div>

                        <div className="pgpl-chart-track">
                            <motion.span
                                className="pgpl-chart-bar pgpl-chart-bar-month"
                                initial={{
                                    scaleX: 0,
                                }}
                                whileInView={{
                                    scaleX: 1,
                                }}
                                viewport={{
                                    once: true,
                                }}
                                transition={{
                                    duration:
                                        0.75,
                                }}
                            />
                        </div>
                    </div>

                    <div className="pgpl-chart-row">
                        <div className="pgpl-chart-label">
                            <span>
                                Trimestral
                            </span>
                            <strong>
                                R$ 34,97
                            </strong>
                        </div>

                        <div className="pgpl-chart-track">
                            <motion.span
                                className="pgpl-chart-bar pgpl-chart-bar-quarter"
                                initial={{
                                    scaleX: 0,
                                }}
                                whileInView={{
                                    scaleX: 1,
                                }}
                                viewport={{
                                    once: true,
                                }}
                                transition={{
                                    duration:
                                        0.75,
                                    delay: 0.1,
                                }}
                            />
                        </div>

                        <small className="pgpl-chart-saving">
                            −12,36%
                        </small>
                    </div>

                    <div className="pgpl-chart-row">
                        <div className="pgpl-chart-label">
                            <span>
                                Anual
                            </span>
                            <strong>
                                R$ 31,24
                            </strong>
                        </div>

                        <div className="pgpl-chart-track">
                            <motion.span
                                className="pgpl-chart-bar pgpl-chart-bar-year"
                                initial={{
                                    scaleX: 0,
                                }}
                                whileInView={{
                                    scaleX: 1,
                                }}
                                viewport={{
                                    once: true,
                                }}
                                transition={{
                                    duration:
                                        0.75,
                                    delay: 0.2,
                                }}
                            />
                        </div>

                        <small className="pgpl-chart-saving pgpl-chart-saving-best">
                            −21,71%
                        </small>
                    </div>

                    <p className="pgpl-chart-footnote">
                        Valores equivalentes calculados
                        a partir do total de cada ciclo.
                    </p>
                </Reveal>
            </div>
        </section>
    );
}

function FAQ() {
    return (
        <section className="pgpl-faq">
            <div className="pgpl-shell pgpl-faq-grid">
                <Reveal className="pgpl-faq-heading">
                    <span className="pgpl-section-kicker">
                        Antes de começar
                    </span>

                    <h2>
                        Algumas respostas
                        <span>
                            sem letras miúdas.
                        </span>
                    </h2>

                    <p>
                        O essencial sobre teste,
                        cobrança e cancelamento.
                    </p>
                </Reveal>

                <Reveal
                    className="pgpl-faq-list"
                    delay={0.06}
                >
                    {faq.map(
                        (
                            item,
                            index
                        ) => (
                            <details
                                key={
                                    item.question
                                }
                                className="pgpl-faq-item"
                                open={
                                    index ===
                                    0
                                }
                            >
                                <summary>
                                    <span>
                                        {
                                            item.question
                                        }
                                    </span>

                                    <span
                                        className="pgpl-faq-plus"
                                        aria-hidden="true"
                                    >
                                        +
                                    </span>
                                </summary>

                                <p>
                                    {
                                        item.answer
                                    }
                                </p>
                            </details>
                        )
                    )}
                </Reveal>
            </div>
        </section>
    );
}

function FinalCTA({
    registerUrl,
    loginUrl,
    whatsappUrl,
}) {
    return (
        <section className="pgpl-final">
            <div className="pgpl-shell">
                <Reveal className="pgpl-final-card">
                    <div className="pgpl-final-copy">
                        <span className="pgpl-final-kicker">
                            Seu primeiro ciclo começa
                            antes da cobrança
                        </span>

                        <h2>
                            Experimente o PsiGestor
                            <span>
                                por 10 dias.
                            </span>
                        </h2>

                        <p>
                            Conheça a rotina dentro
                            do sistema e depois escolha
                            o ciclo que faz mais sentido
                            para você.
                        </p>

                        <div className="pgpl-final-actions">
                            <a
                                href={
                                    registerUrl
                                }
                                className="pgpl-button pgpl-button-light"
                            >
                                Começar agora
                                <Icon
                                    name="arrow"
                                    size={18}
                                />
                            </a>

                            <a
                                href={
                                    whatsappUrl
                                }
                                target="_blank"
                                rel="noreferrer"
                                className="pgpl-button pgpl-button-dark-outline"
                            >
                                Falar com o PsiGestor
                            </a>
                        </div>
                    </div>

                    <div className="pgpl-final-side">
                        <div className="pgpl-final-side-icon">
                            <Icon
                                name="shield"
                                size={30}
                            />
                        </div>

                        <strong>
                            Já tem cadastro?
                        </strong>

                        <p>
                            Entre na sua conta para
                            continuar de onde parou.
                        </p>

                        <a
                            href={loginUrl}
                            className="pgpl-final-login"
                        >
                            Entrar no PsiGestor
                            <Icon
                                name="arrow"
                                size={16}
                            />
                        </a>
                    </div>
                </Reveal>
            </div>
        </section>
    );
}

function PlansPage({
    registerUrl,
    loginUrl,
    featuresUrl,
    whatsappUrl,
}) {
    return (
        <main className="pgpl-page">
            <Hero
                registerUrl={
                    registerUrl
                }
            />

            <Pricing
                registerUrl={
                    registerUrl
                }
            />

            <Included
                featuresUrl={
                    featuresUrl
                }
            />

            <SavingsStory />

            <FAQ />

            <FinalCTA
                registerUrl={
                    registerUrl
                }
                loginUrl={
                    loginUrl
                }
                whatsappUrl={
                    whatsappUrl
                }
            />

            <a
                className="pgpl-whatsapp"
                href={whatsappUrl}
                target="_blank"
                rel="noreferrer"
                aria-label="Conversar com o PsiGestor pelo WhatsApp"
            >
                <span className="pgpl-whatsapp-dot" />
                <span>
                    WhatsApp
                </span>
            </a>
        </main>
    );
}

const mount =
    document.getElementById(
        'psigestor-plans-page'
    );

if (mount) {
    const {
        registerUrl = '/register',
        loginUrl = '/login',
        featuresUrl = '/funcionalidades',
        whatsappUrl = 'https://wa.me/5582991128022',
    } = mount.dataset;

    createRoot(mount).render(
        <PlansPage
            registerUrl={
                registerUrl
            }
            loginUrl={
                loginUrl
            }
            featuresUrl={
                featuresUrl
            }
            whatsappUrl={
                whatsappUrl
            }
        />
    );
}