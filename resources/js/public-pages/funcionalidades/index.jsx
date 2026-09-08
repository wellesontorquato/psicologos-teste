import React, { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';
import {
    motion,
    useReducedMotion,
} from 'motion/react';

import '../../../css/public-features.css';

const agendaDays = [
    {
        day: 'SEG',
        date: '08',
        sessions: [
            ['08:00', 'Marina Costa', 'Confirmada'],
            ['10:30', 'Lucas Almeida', 'Confirmada'],
            ['15:00', 'Ana Beatriz', 'Pendente'],
        ],
    },
    {
        day: 'TER',
        date: '09',
        sessions: [
            ['09:00', 'Clara Santos', 'Confirmada'],
            ['13:30', 'Rafael Lima', 'Confirmada'],
        ],
    },
    {
        day: 'QUA',
        date: '10',
        sessions: [
            ['08:30', 'João Martins', 'Confirmada'],
            ['11:00', 'Fernanda Melo', 'Pendente'],
            ['16:30', 'Paulo Vieira', 'Confirmada'],
        ],
    },
];

const patientTimeline = [
    {
        label: 'Hoje',
        title: 'Evolução registrada',
        text: 'Registro da sessão organizado na timeline.',
        icon: 'bi-file-earmark-text',
    },
    {
        label: '02 set',
        title: 'Sessão realizada',
        text: 'Atendimento concluído e vinculado ao paciente.',
        icon: 'bi-check2-circle',
    },
    {
        label: '28 ago',
        title: 'Documento anexado',
        text: 'Arquivo armazenado no prontuário.',
        icon: 'bi-paperclip',
    },
];

const ecosystem = [
    {
        icon: 'bi-calendar3',
        title: 'Google Agenda',
        text: 'Sincronize compromissos e mantenha sua agenda alinhada.',
    },
    {
        icon: 'bi-camera-video',
        title: 'Google Meet',
        text: 'Apoio ao fluxo de teleatendimento e reuniões online.',
    },
    {
        icon: 'bi-whatsapp',
        title: 'WhatsApp',
        text: 'Lembretes e comunicação integrados à rotina.',
    },
    {
        icon: 'bi-receipt',
        title: 'Receita Saúde',
        text: 'Organize dados para o fluxo de emissão dos recibos.',
    },
    {
        icon: 'bi-folder2-open',
        title: 'Arquivos',
        text: 'Documentos e anexos centralizados por paciente.',
    },
    {
        icon: 'bi-graph-up-arrow',
        title: 'Indicadores',
        text: 'Visualize dados da clínica por período.',
    },
    {
        icon: 'bi-file-earmark-arrow-down',
        title: 'Exportações',
        text: 'Relatórios e informações em PDF e Excel.',
    },
    {
        icon: 'bi-shield-check',
        title: 'Organização e segurança',
        text: 'Informações centralizadas em uma estrutura confiável.',
    },
];

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
                          y: 24,
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
                ease: [0.22, 1, 0.36, 1],
            }}
        >
            {children}
        </motion.div>
    );
}

function Icon({
    name,
}) {
    return (
        <i
            className={`bi ${name}`}
            aria-hidden="true"
        />
    );
}

function CheckItem({
    children,
}) {
    return (
        <li className="pgfx-check">
            <span className="pgfx-check-icon">
                <Icon name="bi-check2" />
            </span>

            <span>{children}</span>
        </li>
    );
}

function AgendaMockup() {
    return (
        <div
            className="pgfx-window pgfx-agenda-window"
            aria-hidden="true"
        >
            <div className="pgfx-window-top">
                <div className="pgfx-window-title">
                    <span className="pgfx-window-icon">
                        <Icon name="bi-calendar3" />
                    </span>

                    <div>
                        <strong>Agenda</strong>
                        <small>Semana atual</small>
                    </div>
                </div>

                <div className="pgfx-window-actions">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>

            <div className="pgfx-agenda-toolbar">
                <div>
                    <button type="button">
                        <Icon name="bi-chevron-left" />
                    </button>

                    <strong>Setembro</strong>

                    <button type="button">
                        <Icon name="bi-chevron-right" />
                    </button>
                </div>

                <span className="pgfx-view-pill">
                    Semana
                </span>
            </div>

            <div className="pgfx-agenda-grid">
                {agendaDays.map(
                    (
                        day,
                        dayIndex
                    ) => (
                        <motion.div
                            className="pgfx-day"
                            key={day.day}
                            initial={{
                                opacity: 0,
                                y: 12,
                            }}
                            whileInView={{
                                opacity: 1,
                                y: 0,
                            }}
                            viewport={{
                                once: true,
                            }}
                            transition={{
                                delay:
                                    0.12 +
                                    dayIndex *
                                        0.08,
                            }}
                        >
                            <div className="pgfx-day-head">
                                <span>
                                    {day.day}
                                </span>

                                <strong>
                                    {day.date}
                                </strong>
                            </div>

                            <div className="pgfx-day-events">
                                {day.sessions.map(
                                    (
                                        session,
                                        index
                                    ) => (
                                        <motion.div
                                            className={`pgfx-event ${
                                                session[2] ===
                                                'Pendente'
                                                    ? 'is-pending'
                                                    : ''
                                            }`}
                                            key={`${day.day}-${session[0]}`}
                                            initial={{
                                                opacity: 0,
                                                scale:
                                                    0.97,
                                            }}
                                            whileInView={{
                                                opacity: 1,
                                                scale: 1,
                                            }}
                                            viewport={{
                                                once: true,
                                            }}
                                            transition={{
                                                delay:
                                                    0.24 +
                                                    index *
                                                        0.07 +
                                                    dayIndex *
                                                        0.05,
                                            }}
                                        >
                                            <span className="pgfx-event-time">
                                                {
                                                    session[0]
                                                }
                                            </span>

                                            <strong>
                                                {
                                                    session[1]
                                                }
                                            </strong>

                                            <small>
                                                {
                                                    session[2]
                                                }
                                            </small>
                                        </motion.div>
                                    )
                                )}
                            </div>
                        </motion.div>
                    )
                )}
            </div>

            <motion.div
                className="pgfx-agenda-toast"
                initial={{
                    opacity: 0,
                    x: 14,
                }}
                whileInView={{
                    opacity: 1,
                    x: 0,
                }}
                viewport={{
                    once: true,
                }}
                transition={{
                    delay: 0.65,
                    duration: 0.4,
                }}
            >
                <span>
                    <Icon name="bi-check2" />
                </span>

                <div>
                    <strong>
                        Sessão confirmada
                    </strong>
                    <small>
                        Agenda atualizada
                    </small>
                </div>
            </motion.div>
        </div>
    );
}

function PatientMockup() {
    return (
        <div
            className="pgfx-window pgfx-patient-window"
            aria-hidden="true"
        >
            <div className="pgfx-patient-header">
                <div className="pgfx-avatar">
                    MC
                </div>

                <div className="pgfx-patient-meta">
                    <strong>
                        Marina Costa
                    </strong>

                    <span>
                        Prontuário do paciente
                    </span>
                </div>

                <span className="pgfx-active-pill">
                    Ativo
                </span>
            </div>

            <div className="pgfx-patient-tabs">
                <span className="is-active">
                    Timeline
                </span>

                <span>
                    Evoluções
                </span>

                <span>
                    Arquivos
                </span>
            </div>

            <div className="pgfx-timeline">
                {patientTimeline.map(
                    (
                        item,
                        index
                    ) => (
                        <motion.div
                            className="pgfx-timeline-item"
                            key={item.title}
                            initial={{
                                opacity: 0,
                                x: 18,
                            }}
                            whileInView={{
                                opacity: 1,
                                x: 0,
                            }}
                            viewport={{
                                once: true,
                            }}
                            transition={{
                                delay:
                                    0.12 +
                                    index *
                                        0.1,
                            }}
                        >
                            <div className="pgfx-timeline-rail">
                                <span>
                                    <Icon
                                        name={
                                            item.icon
                                        }
                                    />
                                </span>
                            </div>

                            <div className="pgfx-timeline-content">
                                <small>
                                    {item.label}
                                </small>

                                <strong>
                                    {
                                        item.title
                                    }
                                </strong>

                                <p>
                                    {item.text}
                                </p>
                            </div>
                        </motion.div>
                    )
                )}
            </div>

            <div className="pgfx-patient-bottom">
                <span>
                    <Icon name="bi-clock-history" />
                    Histórico centralizado
                </span>

                <span>
                    <Icon name="bi-folder2" />
                    Arquivos organizados
                </span>
            </div>
        </div>
    );
}

function AiMockup() {
    return (
        <div
            className="pgfx-ai-demo"
            aria-hidden="true"
        >
            <div className="pgfx-ai-input">
                <div className="pgfx-ai-input-head">
                    <span>
                        Tópicos da sessão
                    </span>

                    <span className="pgfx-ai-live">
                        <i></i>
                        IA
                    </span>
                </div>

                <div className="pgfx-ai-chips">
                    <motion.span
                        initial={{
                            opacity: 0,
                            scale: 0.9,
                        }}
                        whileInView={{
                            opacity: 1,
                            scale: 1,
                        }}
                        viewport={{
                            once: true,
                        }}
                    >
                        rotina de sono
                    </motion.span>

                    <motion.span
                        initial={{
                            opacity: 0,
                            scale: 0.9,
                        }}
                        whileInView={{
                            opacity: 1,
                            scale: 1,
                        }}
                        viewport={{
                            once: true,
                        }}
                        transition={{
                            delay: 0.12,
                        }}
                    >
                        ansiedade
                    </motion.span>

                    <motion.span
                        initial={{
                            opacity: 0,
                            scale: 0.9,
                        }}
                        whileInView={{
                            opacity: 1,
                            scale: 1,
                        }}
                        viewport={{
                            once: true,
                        }}
                        transition={{
                            delay: 0.22,
                        }}
                    >
                        estratégias
                    </motion.span>
                </div>

                <div className="pgfx-ai-generate">
                    <Icon name="bi-stars" />
                    Gerar evolução
                </div>
            </div>

            <div className="pgfx-ai-flow">
                <span></span>
                <Icon name="bi-arrow-right" />
                <span></span>
            </div>

            <motion.div
                className="pgfx-ai-output"
                initial={{
                    opacity: 0,
                    y: 16,
                }}
                whileInView={{
                    opacity: 1,
                    y: 0,
                }}
                viewport={{
                    once: true,
                }}
                transition={{
                    delay: 0.42,
                    duration: 0.55,
                }}
            >
                <div className="pgfx-ai-output-head">
                    <div>
                        <span className="pgfx-doc-icon">
                            <Icon name="bi-file-earmark-text" />
                        </span>

                        <strong>
                            Evolução estruturada
                        </strong>
                    </div>

                    <span className="pgfx-ready">
                        Pronta para revisar
                    </span>
                </div>

                <div className="pgfx-ai-lines">
                    <span className="w-100"></span>
                    <span className="w-92"></span>
                    <span className="w-96"></span>
                    <span className="w-76"></span>
                </div>

                <div className="pgfx-ai-footer">
                    <span>
                        <Icon name="bi-check-circle" />
                        Organizada
                    </span>

                    <span>
                        <Icon name="bi-pencil-square" />
                        Editável
                    </span>
                </div>
            </motion.div>
        </div>
    );
}

function FinanceMockup() {
    const bars = [
        42,
        58,
        48,
        72,
        64,
        84,
        76,
        92,
    ];

    return (
        <div
            className="pgfx-window pgfx-finance-window"
            aria-hidden="true"
        >
            <div className="pgfx-finance-head">
                <div>
                    <span>
                        Visão financeira
                    </span>
                    <strong>
                        Setembro
                    </strong>
                </div>

                <span className="pgfx-finance-filter">
                    Este mês
                    <Icon name="bi-chevron-down" />
                </span>
            </div>

            <div className="pgfx-finance-metrics">
                <div>
                    <span>
                        Recebimentos
                    </span>
                    <strong>
                        R$ 8.420
                    </strong>
                    <small>
                        registrados
                    </small>
                </div>

                <div>
                    <span>
                        Pendentes
                    </span>
                    <strong>
                        R$ 680
                    </strong>
                    <small>
                        a acompanhar
                    </small>
                </div>
            </div>

            <div className="pgfx-chart">
                <div className="pgfx-chart-top">
                    <strong>
                        Movimento do período
                    </strong>

                    <span>
                        8 semanas
                    </span>
                </div>

                <div className="pgfx-bars">
                    {bars.map(
                        (
                            value,
                            index
                        ) => (
                            <motion.span
                                key={`${value}-${index}`}
                                initial={{
                                    height: '12%',
                                }}
                                whileInView={{
                                    height: `${value}%`,
                                }}
                                viewport={{
                                    once: true,
                                }}
                                transition={{
                                    duration: 0.55,
                                    delay:
                                        index *
                                        0.055,
                                    ease: [
                                        0.22,
                                        1,
                                        0.36,
                                        1,
                                    ],
                                }}
                            />
                        )
                    )}
                </div>
            </div>

            <div className="pgfx-finance-list">
                <div>
                    <span className="pgfx-status is-paid"></span>
                    <strong>
                        Marina Costa
                    </strong>
                    <span>
                        Pago
                    </span>
                </div>

                <div>
                    <span className="pgfx-status is-pending"></span>
                    <strong>
                        Lucas Almeida
                    </strong>
                    <span>
                        Pendente
                    </span>
                </div>

                <div>
                    <span className="pgfx-status is-paid"></span>
                    <strong>
                        Clara Santos
                    </strong>
                    <span>
                        Pago
                    </span>
                </div>
            </div>
        </div>
    );
}

function SectionLabel({
    number,
    children,
}) {
    return (
        <div className="pgfx-section-label">
            <span>
                {number}
            </span>
            {children}
        </div>
    );
}

function FeaturesPage({
    homeUrl,
    registerUrl,
    loginUrl,
    plansUrl,
    whatsappUrl,
}) {
    const reduceMotion =
        useReducedMotion();
    const [
        activeSection,
        setActiveSection,
    ] = useState('agenda');


    useEffect(() => {
        const sectionIds = [
            'agenda',
            'prontuario',
            'ia',
            'financeiro',
            'ecossistema',
        ];

        const sections =
            sectionIds
                .map((id) =>
                    document.getElementById(id)
                )
                .filter(Boolean);

        if (!sections.length) {
            return undefined;
        }

        let animationFrameId =
            null;

        const updateActiveSection =
            () => {
                animationFrameId =
                    null;

                /*
                 * Navbar:
                 * aproximadamente 70 px.
                 *
                 * Anchorbar:
                 * aproximadamente 57 px.
                 *
                 * As seções usam scroll-margin-top
                 * próximo de 136 px.
                 *
                 * 150 px coloca a linha de ativação
                 * imediatamente abaixo das barras.
                 */
                const activationLine =
                    150;

                let nextSection =
                    sections[0].id;

                for (
                    const section
                    of sections
                ) {
                    const sectionTop =
                        section
                            .getBoundingClientRect()
                            .top;

                    if (
                        sectionTop <=
                        activationLine
                    ) {
                        nextSection =
                            section.id;
                    }
                    else {
                        break;
                    }
                }

                /*
                 * Garante a última seção quando
                 * chegamos ao final do documento.
                 */
                const bottomReached =
                    Math.ceil(
                        window.innerHeight +
                        window.scrollY
                    ) >=
                    document.documentElement
                        .scrollHeight -
                        4;

                if (bottomReached) {
                    nextSection =
                        sections[
                            sections.length - 1
                        ].id;
                }

                setActiveSection(
                    (currentSection) =>
                        currentSection ===
                        nextSection
                            ? currentSection
                            : nextSection
                );
            };

        const requestUpdate =
            () => {
                if (
                    animationFrameId !==
                    null
                ) {
                    return;
                }

                animationFrameId =
                    window.requestAnimationFrame(
                        updateActiveSection
                    );
            };

        updateActiveSection();

        window.addEventListener(
            'scroll',
            requestUpdate,
            {
                passive: true,
            }
        );

        window.addEventListener(
            'resize',
            requestUpdate
        );

        return () => {
            window.removeEventListener(
                'scroll',
                requestUpdate
            );

            window.removeEventListener(
                'resize',
                requestUpdate
            );

            if (
                animationFrameId !==
                null
            ) {
                window.cancelAnimationFrame(
                    animationFrameId
                );
            }
        };
    }, []);

    function handleSectionClick(
        event,
        sectionId
    ) {
        event.preventDefault();

        const section =
            document.getElementById(
                sectionId
            );

        if (!section) {
            return;
        }

        /*
         * O clique muda o estado imediatamente.
         * Durante e após o movimento, o scroll spy
         * baseado na posição real mantém o estado.
         */
        setActiveSection(
            sectionId
        );

        window.history.replaceState(
            null,
            '',
            `#${sectionId}`
        );

        section.scrollIntoView({
            behavior:
                reduceMotion
                    ? 'auto'
                    : 'smooth',
            block: 'start',
        });
    }

    function scrollToResources() {
        document
            .getElementById(
                'agenda'
            )
            ?.scrollIntoView({
                behavior:
                    reduceMotion
                        ? 'auto'
                        : 'smooth',
                block: 'start',
            });
    }

    return (
        <div className="pgfx-page">
            <section className="pgfx-hero">
                <div
                    className="pgfx-hero-orb pgfx-hero-orb-one"
                    aria-hidden="true"
                ></div>

                <div
                    className="pgfx-hero-orb pgfx-hero-orb-two"
                    aria-hidden="true"
                ></div>

                <div className="pgfx-container pgfx-hero-grid">
                    <motion.div
                        className="pgfx-hero-copy"
                        initial={
                            reduceMotion
                                ? false
                                : {
                                      opacity: 0,
                                      y: 24,
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
                            duration: 0.68,
                            ease: [
                                0.22,
                                1,
                                0.36,
                                1,
                            ],
                        }}
                    >
                        <span className="pgfx-eyebrow">
                            Recursos do PsiGestor
                        </span>

                        <h1>
                            Tudo o que organiza
                            sua rotina,
                            <span>
                                trabalhando junto.
                            </span>
                        </h1>

                        <p>
                            Da primeira sessão ao
                            acompanhamento financeiro,
                            o PsiGestor conecta as
                            informações que fazem parte
                            do seu dia a dia clínico em
                            um único fluxo.
                        </p>

                        <div className="pgfx-hero-actions">
                            <a
                                href={
                                    registerUrl
                                }
                                className="pgfx-btn pgfx-btn-primary"
                            >
                                Começar 10 dias
                                grátis

                                <Icon name="bi-arrow-right" />
                            </a>

                            <button
                                type="button"
                                className="pgfx-btn pgfx-btn-secondary"
                                onClick={
                                    scrollToResources
                                }
                            >
                                Explorar recursos

                                <Icon name="bi-arrow-down" />
                            </button>
                        </div>

                        <div className="pgfx-hero-trust">
                            <span>
                                <Icon name="bi-check-circle" />
                                Sem cartão de
                                crédito
                            </span>

                            <span>
                                <Icon name="bi-check-circle" />
                                Acesso imediato
                            </span>

                            <span>
                                <Icon name="bi-check-circle" />
                                Tudo conectado
                            </span>
                        </div>
                    </motion.div>

                    <motion.div
                        className="pgfx-hero-visual"
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
                            duration: 0.75,
                            delay: 0.12,
                            ease: [
                                0.22,
                                1,
                                0.36,
                                1,
                            ],
                        }}
                    >
                        <div className="pgfx-flow-card">
                            <div className="pgfx-flow-head">
                                <div>
                                    <span>
                                        Seu dia no
                                        PsiGestor
                                    </span>
                                    <strong>
                                        Uma rotina,
                                        um fluxo.
                                    </strong>
                                </div>

                                <span className="pgfx-flow-status">
                                    <i></i>
                                    Conectado
                                </span>
                            </div>

                            <div className="pgfx-flow-path">
                                <div className="pgfx-flow-item">
                                    <span>
                                        <Icon name="bi-calendar3" />
                                    </span>
                                    <div>
                                        <small>
                                            08:00
                                        </small>
                                        <strong>
                                            Agenda
                                        </strong>
                                    </div>
                                </div>

                                <span className="pgfx-flow-line"></span>

                                <div className="pgfx-flow-item">
                                    <span>
                                        <Icon name="bi-person" />
                                    </span>
                                    <div>
                                        <small>
                                            09:00
                                        </small>
                                        <strong>
                                            Paciente
                                        </strong>
                                    </div>
                                </div>

                                <span className="pgfx-flow-line"></span>

                                <div className="pgfx-flow-item is-highlighted">
                                    <span>
                                        <Icon name="bi-stars" />
                                    </span>
                                    <div>
                                        <small>
                                            Pós-sessão
                                        </small>
                                        <strong>
                                            Evolução
                                        </strong>
                                    </div>
                                </div>

                                <span className="pgfx-flow-line"></span>

                                <div className="pgfx-flow-item">
                                    <span>
                                        <Icon name="bi-wallet2" />
                                    </span>
                                    <div>
                                        <small>
                                            Fechamento
                                        </small>
                                        <strong>
                                            Financeiro
                                        </strong>
                                    </div>
                                </div>
                            </div>

                            <div className="pgfx-flow-bottom">
                                <span>
                                    <Icon name="bi-arrow-repeat" />
                                </span>

                                <p>
                                    A informação acompanha
                                    a rotina sem precisar
                                    ser refeita em vários
                                    lugares.
                                </p>
                            </div>
                        </div>
                    </motion.div>
                </div>
            </section>

            <nav
                className="pgfx-anchorbar"
                aria-label="Recursos da página"
            >
                <div className="pgfx-container">
                    <a
                        href="#agenda"
                        onClick={(event) =>
                            handleSectionClick(
                                event,
                                'agenda'
                            )
                        }
                        className={
                            activeSection === 'agenda'
                                ? 'is-active'
                                : undefined
                        }
                        aria-current={
                            activeSection === 'agenda'
                                ? 'location'
                                : undefined
                        }
                    >
                        Agenda
                    </a>

                    <a
                        href="#prontuario"
                        onClick={(event) =>
                            handleSectionClick(
                                event,
                                'prontuario'
                            )
                        }
                        className={
                            activeSection === 'prontuario'
                                ? 'is-active'
                                : undefined
                        }
                        aria-current={
                            activeSection === 'prontuario'
                                ? 'location'
                                : undefined
                        }
                    >
                        Pacientes
                    </a>

                    <a
                        href="#ia"
                        onClick={(event) =>
                            handleSectionClick(
                                event,
                                'ia'
                            )
                        }
                        className={
                            activeSection === 'ia'
                                ? 'is-active'
                                : undefined
                        }
                        aria-current={
                            activeSection === 'ia'
                                ? 'location'
                                : undefined
                        }
                    >
                        IA
                    </a>

                    <a
                        href="#financeiro"
                        onClick={(event) =>
                            handleSectionClick(
                                event,
                                'financeiro'
                            )
                        }
                        className={
                            activeSection === 'financeiro'
                                ? 'is-active'
                                : undefined
                        }
                        aria-current={
                            activeSection === 'financeiro'
                                ? 'location'
                                : undefined
                        }
                    >
                        Financeiro
                    </a>

                    <a
                        href="#ecossistema"
                        onClick={(event) =>
                            handleSectionClick(
                                event,
                                'ecossistema'
                            )
                        }
                        className={
                            activeSection === 'ecossistema'
                                ? 'is-active'
                                : undefined
                        }
                        aria-current={
                            activeSection === 'ecossistema'
                                ? 'location'
                                : undefined
                        }
                    >
                        Ecossistema
                    </a>
                </div>
            </nav>

            <section
                id="agenda"
                className="pgfx-story-section"
            >
                <div className="pgfx-container pgfx-story-grid">
                    <Reveal className="pgfx-story-copy">
                        <SectionLabel number="01">
                            Agenda
                        </SectionLabel>

                        <h2>
                            Organize o dia sem
                            transformar organização em
                            mais uma tarefa.
                        </h2>

                        <p>
                            Visualize seus atendimentos,
                            crie e reorganize sessões e
                            mantenha os compromissos
                            clínicos em um fluxo mais
                            claro.
                        </p>

                        <ul className="pgfx-check-list">
                            <CheckItem>
                                Visualização de agenda
                                por período
                            </CheckItem>

                            <CheckItem>
                                Remarcações de forma
                                prática
                            </CheckItem>

                            <CheckItem>
                                Integração com Google
                                Agenda e Google Meet
                            </CheckItem>

                            <CheckItem>
                                Lembretes ligados à
                                rotina dos atendimentos
                            </CheckItem>
                        </ul>
                    </Reveal>

                    <Reveal
                        className="pgfx-story-visual"
                        delay={0.08}
                    >
                        <AgendaMockup />
                    </Reveal>
                </div>
            </section>

            <section
                id="prontuario"
                className="pgfx-story-section pgfx-story-section-soft"
            >
                <div className="pgfx-container pgfx-story-grid pgfx-story-grid-reverse">
                    <Reveal
                        className="pgfx-story-visual"
                        delay={0.06}
                    >
                        <PatientMockup />
                    </Reveal>

                    <Reveal className="pgfx-story-copy">
                        <SectionLabel number="02">
                            Pacientes e prontuário
                        </SectionLabel>

                        <h2>
                            O histórico certo, no
                            contexto certo.
                        </h2>

                        <p>
                            Mantenha informações,
                            evoluções, sessões e
                            documentos do paciente
                            organizados em uma linha do
                            tempo que acompanha o
                            atendimento.
                        </p>

                        <ul className="pgfx-check-list">
                            <CheckItem>
                                Histórico do paciente
                                centralizado
                            </CheckItem>

                            <CheckItem>
                                Evoluções vinculadas ao
                                prontuário
                            </CheckItem>

                            <CheckItem>
                                Documentos e anexos
                                organizados
                            </CheckItem>

                            <CheckItem>
                                Acesso rápido durante a
                                rotina clínica
                            </CheckItem>
                        </ul>
                    </Reveal>
                </div>
            </section>

            <section
                id="ia"
                className="pgfx-ai-section"
            >
                <div
                    className="pgfx-ai-glow"
                    aria-hidden="true"
                ></div>

                <div className="pgfx-container">
                    <Reveal className="pgfx-ai-heading">
                        <SectionLabel number="03">
                            Evoluções com IA
                        </SectionLabel>

                        <h2>
                            Dos tópicos da sessão a um
                            registro estruturado.
                        </h2>

                        <p>
                            Registre os pontos
                            principais do atendimento e
                            use a inteligência
                            artificial como apoio para
                            organizar uma evolução que
                            você pode revisar e editar.
                        </p>
                    </Reveal>

                    <Reveal
                        className="pgfx-ai-wrap"
                        delay={0.08}
                    >
                        <AiMockup />
                    </Reveal>

                    <Reveal className="pgfx-ai-benefits">
                        <div>
                            <span>
                                <Icon name="bi-pencil-square" />
                            </span>

                            <strong>
                                Você mantém o controle
                            </strong>

                            <p>
                                O conteúdo permanece
                                revisável e editável.
                            </p>
                        </div>

                        <div>
                            <span>
                                <Icon name="bi-file-earmark-text" />
                            </span>

                            <strong>
                                Registro organizado
                            </strong>

                            <p>
                                Menos esforço com
                                estruturação manual.
                            </p>
                        </div>

                        <div>
                            <span>
                                <Icon name="bi-clock" />
                            </span>

                            <strong>
                                Fluxo mais ágil
                            </strong>

                            <p>
                                Reduza tarefas
                                repetitivas após o
                                atendimento.
                            </p>
                        </div>
                    </Reveal>
                </div>
            </section>

            <section
                id="financeiro"
                className="pgfx-story-section"
            >
                <div className="pgfx-container pgfx-story-grid">
                    <Reveal className="pgfx-story-copy">
                        <SectionLabel number="04">
                            Financeiro
                        </SectionLabel>

                        <h2>
                            Veja o que entrou, o que
                            falta e o que precisa da sua
                            atenção.
                        </h2>

                        <p>
                            Acompanhe recebimentos e
                            pendências por paciente e
                            período sem depender de
                            planilhas espalhadas.
                        </p>

                        <ul className="pgfx-check-list">
                            <CheckItem>
                                Pagamentos e pendências
                                em um só lugar
                            </CheckItem>

                            <CheckItem>
                                Organização por paciente
                                e período
                            </CheckItem>

                            <CheckItem>
                                Suporte a diferentes
                                moedas
                            </CheckItem>

                            <CheckItem>
                                Indicadores para
                                acompanhar sua rotina
                            </CheckItem>
                        </ul>
                    </Reveal>

                    <Reveal
                        className="pgfx-story-visual"
                        delay={0.08}
                    >
                        <FinanceMockup />
                    </Reveal>
                </div>
            </section>

            <section
                id="ecossistema"
                className="pgfx-ecosystem"
            >
                <div className="pgfx-container">
                    <Reveal className="pgfx-section-heading">
                        <SectionLabel number="05">
                            Ecossistema
                        </SectionLabel>

                        <h2>
                            Mais recursos, sem criar
                            mais lugares para você
                            administrar.
                        </h2>

                        <p>
                            Recursos complementares
                            ajudam o PsiGestor a
                            acompanhar outras partes da
                            rotina do consultório.
                        </p>
                    </Reveal>

                    <div className="pgfx-ecosystem-grid">
                        {ecosystem.map(
                            (
                                item,
                                index
                            ) => (
                                <Reveal
                                    key={
                                        item.title
                                    }
                                    delay={
                                        Math.min(
                                            index *
                                                0.045,
                                            0.22
                                        )
                                    }
                                >
                                    <article className="pgfx-ecosystem-card">
                                        <span className="pgfx-ecosystem-icon">
                                            <Icon
                                                name={
                                                    item.icon
                                                }
                                            />
                                        </span>

                                        <div>
                                            <strong>
                                                {
                                                    item.title
                                                }
                                            </strong>

                                            <p>
                                                {
                                                    item.text
                                                }
                                            </p>
                                        </div>
                                    </article>
                                </Reveal>
                            )
                        )}
                    </div>

                    <Reveal className="pgfx-connect-banner">
                        <div className="pgfx-connect-icon">
                            <Icon name="bi-diagram-3" />
                        </div>

                        <div>
                            <span>
                                A diferença está na
                                conexão
                            </span>

                            <strong>
                                Agenda, paciente,
                                prontuário e financeiro
                                fazem parte da mesma
                                rotina.
                            </strong>
                        </div>

                        <a href={registerUrl}>
                            Experimentar
                            <Icon name="bi-arrow-right" />
                        </a>
                    </Reveal>
                </div>
            </section>

            <section className="pgfx-final">
                <div className="pgfx-container">
                    <Reveal>
                        <div className="pgfx-final-card">
                            <div
                                className="pgfx-final-glow"
                                aria-hidden="true"
                            ></div>

                            <div className="pgfx-final-copy">
                                <span>
                                    Pronto para
                                    experimentar?
                                </span>

                                <h2>
                                    Sua rotina pode
                                    começar mais
                                    organizada hoje.
                                </h2>

                                <p>
                                    Tenha acesso ao
                                    PsiGestor por 10 dias
                                    e conheça o fluxo
                                    completo no seu
                                    próprio ritmo.
                                </p>

                                <div className="pgfx-final-actions">
                                    <a
                                        href={
                                            registerUrl
                                        }
                                        className="pgfx-btn pgfx-btn-white"
                                    >
                                        Começar 10
                                        dias grátis

                                        <Icon name="bi-arrow-right" />
                                    </a>

                                    <a
                                        href={
                                            plansUrl
                                        }
                                        className="pgfx-final-link"
                                    >
                                        Ver planos
                                    </a>
                                </div>
                            </div>

                            <div className="pgfx-final-side">
                                <div>
                                    <Icon name="bi-check-circle" />
                                    <span>
                                        Sem cartão de
                                        crédito
                                    </span>
                                </div>

                                <div>
                                    <Icon name="bi-lightning-charge" />
                                    <span>
                                        Acesso
                                        imediato
                                    </span>
                                </div>

                                <div>
                                    <Icon name="bi-box-arrow-in-right" />
                                    <span>
                                        Já possui uma
                                        conta?
                                        <a
                                            href={
                                                loginUrl
                                            }
                                        >
                                            Entrar
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </Reveal>
                </div>
            </section>

            <a
                className="pgfx-whatsapp"
                href={whatsappUrl}
                target="_blank"
                rel="noopener noreferrer"
                aria-label="Falar com o PsiGestor pelo WhatsApp"
            >
                <Icon name="bi-whatsapp" />

                <span>
                    Fale conosco
                </span>
            </a>
        </div>
    );
}

const element =
    document.getElementById(
        'psigestor-features-page'
    );

if (element) {
    const {
        homeUrl,
        registerUrl,
        loginUrl,
        plansUrl,
        whatsappUrl,
    } = element.dataset;

    createRoot(element).render(
        <FeaturesPage
            homeUrl={
                homeUrl || '/'
            }
            registerUrl={
                registerUrl ||
                '/register'
            }
            loginUrl={
                loginUrl ||
                '/login'
            }
            plansUrl={
                plansUrl ||
                '/planos'
            }
            whatsappUrl={
                whatsappUrl ||
                'https://wa.me/5582991128022'
            }
        />
    );
}