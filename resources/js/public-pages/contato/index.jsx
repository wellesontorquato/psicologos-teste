import React from 'react';
import {
    createRoot,
} from 'react-dom/client';

import {
    motion,
    useReducedMotion,
} from 'motion/react';

import '../../../css/public-contact.css';

function Icon({
    name,
    size = 22,
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

    if (name === 'message') {
        return (
            <svg {...common}>
                <path d="M21 15a4 4 0 0 1-4 4H8l-5 3v-7a4 4 0 0 1-1-2.7V7a4 4 0 0 1 4-4h11a4 4 0 0 1 4 4Z" />
                <path d="M7 8h10M7 12h7" />
            </svg>
        );
    }

    if (name === 'monitor') {
        return (
            <svg {...common}>
                <rect
                    x="3"
                    y="4"
                    width="18"
                    height="13"
                    rx="2"
                />
                <path d="M8 21h8M12 17v4" />
            </svg>
        );
    }

    if (name === 'life') {
        return (
            <svg {...common}>
                <circle
                    cx="12"
                    cy="12"
                    r="9"
                />
                <circle
                    cx="12"
                    cy="12"
                    r="3"
                />
                <path d="m5.6 5.6 4.3 4.3M14.1 14.1l4.3 4.3M18.4 5.6l-4.3 4.3M9.9 14.1l-4.3 4.3" />
            </svg>
        );
    }

    if (name === 'card') {
        return (
            <svg {...common}>
                <rect
                    x="3"
                    y="5"
                    width="18"
                    height="14"
                    rx="2.5"
                />
                <path d="M3 9h18M7 15h3" />
            </svg>
        );
    }

    if (name === 'user') {
        return (
            <svg {...common}>
                <circle
                    cx="12"
                    cy="8"
                    r="3.2"
                />
                <path d="M5.5 20c.8-4 3-6 6.5-6s5.7 2 6.5 6" />
            </svg>
        );
    }

    if (name === 'spark') {
        return (
            <svg {...common}>
                <path d="M12 2.5 13.8 8l5.7 1.8-5.7 1.8L12 17l-1.8-5.4-5.7-1.8L10.2 8Z" />
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

    if (name === 'check') {
        return (
            <svg {...common}>
                <path d="m5 12.5 4.1 4.1L19 7" />
            </svg>
        );
    }

    return null;
}

function Reveal({
    children,
    className = '',
    delay = 0,
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
                          y: 22,
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
                amount: 0.16,
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

const contactReasons = [
    {
        icon: 'monitor',
        title: 'Conhecer o PsiGestor',
        text:
            'Demonstração, dúvidas sobre o sistema ou ajuda para começar.',
    },
    {
        icon: 'life',
        title: 'Precisando de ajuda',
        text:
            'Suporte técnico, cadastro ou alguma dúvida durante o uso.',
    },
    {
        icon: 'card',
        title: 'Pagamento e parceria',
        text:
            'Questões financeiras, interesse em parceria ou outros assuntos.',
    },
];

function ContactIntro({
    whatsappUrl,
}) {
    const reduceMotion =
        useReducedMotion();

    return (
        <motion.div
            className="pgct-intro"
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
            <span className="pgct-eyebrow">
                Fale com o PsiGestor
            </span>

            <h1>
                Você explica
                <span>
                    o contexto.
                </span>
                A gente entende
                por onde começar.
            </h1>

            <p className="pgct-intro-description">
                Seja para conhecer melhor o sistema,
                pedir suporte, resolver uma dúvida ou
                enviar uma sugestão, escolha o assunto
                e conte o que está acontecendo.
            </p>

            <div className="pgct-reason-list">
                {contactReasons.map(
                    (
                        reason,
                        index
                    ) => (
                        <motion.div
                            key={
                                reason.title
                            }
                            className="pgct-reason"
                            initial={
                                reduceMotion
                                    ? false
                                    : {
                                          opacity: 0,
                                          x: -18,
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
                                    0.2 +
                                    index *
                                        0.09,
                                duration:
                                    0.5,
                            }}
                        >
                            <span className="pgct-reason-icon">
                                <Icon
                                    name={
                                        reason.icon
                                    }
                                />
                            </span>

                            <div>
                                <strong>
                                    {
                                        reason.title
                                    }
                                </strong>

                                <p>
                                    {
                                        reason.text
                                    }
                                </p>
                            </div>
                        </motion.div>
                    )
                )}
            </div>

            <div className="pgct-direct">
                <div className="pgct-direct-icon">
                    <Icon
                        name="message"
                        size={20}
                    />
                </div>

                <div>
                    <span>
                        Prefere uma conversa direta?
                    </span>

                    <a
                        href={whatsappUrl}
                        target="_blank"
                        rel="noreferrer"
                    >
                        Chamar no WhatsApp
                        <Icon
                            name="arrow"
                            size={16}
                        />
                    </a>
                </div>
            </div>
        </motion.div>
    );
}

const steps = [
    {
        number: '01',
        title: 'Escolha o assunto',
        text:
            'A categoria ajuda a contextualizar o motivo do seu contato.',
    },
    {
        number: '02',
        title: 'Conte o que aconteceu',
        text:
            'Compartilhe as informações necessárias para entendermos melhor sua mensagem.',
    },
    {
        number: '03',
        title: 'Envie com segurança',
        text:
            'O formulário utiliza verificação reCAPTCHA antes de encaminhar a mensagem.',
    },
];

function ContactFlow({
    whatsappUrl,
}) {
    return (
        <div className="pgct-shell">
            <Reveal className="pgct-flow-heading">
                <span className="pgct-section-kicker">
                    Um contato mais claro
                </span>

                <h2>
                    Menos ida e volta.
                    <span>
                        Mais contexto desde o início.
                    </span>
                </h2>

                <p>
                    O formulário organiza as informações
                    essenciais para sua mensagem chegar
                    mais completa.
                </p>
            </Reveal>

            <div className="pgct-step-grid">
                {steps.map(
                    (
                        step,
                        index
                    ) => (
                        <Reveal
                            key={
                                step.number
                            }
                            delay={
                                index *
                                0.08
                            }
                        >
                            <article className="pgct-step-card">
                                <span className="pgct-step-number">
                                    {
                                        step.number
                                    }
                                </span>

                                <h3>
                                    {
                                        step.title
                                    }
                                </h3>

                                <p>
                                    {
                                        step.text
                                    }
                                </p>
                            </article>
                        </Reveal>
                    )
                )}
            </div>

            <Reveal className="pgct-flow-cta">
                <div>
                    <span className="pgct-flow-cta-icon">
                        <Icon
                            name="spark"
                            size={23}
                        />
                    </span>

                    <div>
                        <strong>
                            Ainda prefere falar pelo WhatsApp?
                        </strong>

                        <p>
                            O canal continua disponível como
                            alternativa ao formulário.
                        </p>
                    </div>
                </div>

                <a
                    href={whatsappUrl}
                    target="_blank"
                    rel="noreferrer"
                >
                    Abrir WhatsApp
                    <Icon
                        name="arrow"
                        size={17}
                    />
                </a>
            </Reveal>
        </div>
    );
}

const introMount =
    document.getElementById(
        'psigestor-contact-intro'
    );

if (introMount) {
    const {
        whatsappUrl,
    } = introMount.dataset;

    createRoot(
        introMount
    ).render(
        <ContactIntro
            whatsappUrl={
                whatsappUrl
            }
        />
    );
}

const flowMount =
    document.getElementById(
        'psigestor-contact-flow'
    );

if (flowMount) {
    const {
        whatsappUrl,
    } = flowMount.dataset;

    createRoot(
        flowMount
    ).render(
        <ContactFlow
            whatsappUrl={
                whatsappUrl
            }
        />
    );
}