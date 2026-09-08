import {
    motion,
    useReducedMotion,
} from 'motion/react';

const reveal = {
    hidden: {
        opacity: 0,
        y: 26,
    },
    visible: {
        opacity: 1,
        y: 0,
        transition: {
            duration: 0.68,
            ease: [0.16, 1, 0.3, 1],
        },
    },
};

function CheckItem({
    children,
}) {
    return (
        <li>
            <span>
                <i className="bi bi-check2" />
            </span>

            {children}
        </li>
    );
}

function AgendaPreview() {
    const reduceMotion =
        useReducedMotion();

    const sessions = [
        {
            time: '09:00',
            initials: 'AM',
            name: 'Ana M.',
            type: 'Atendimento online',
            status: 'Confirmada',
            avatar: '',
        },
        {
            time: '10:30',
            initials: 'RL',
            name: 'Rafael L.',
            type: 'Consultório',
            status: 'Hoje',
            avatar: 'is-purple',
        },
        {
            time: '14:00',
            initials: 'CM',
            name: 'Carla M.',
            type: 'Atendimento online',
            status: 'Confirmada',
            avatar: 'is-green',
        },
    ];

    return (
        <motion.div
            className="pgf-agenda-shell"
            initial={{
                opacity: 0,
                y: 24,
                scale: 0.975,
            }}
            whileInView={{
                opacity: 1,
                y: 0,
                scale: 1,
            }}
            viewport={{
                once: true,
                amount: 0.35,
            }}
            transition={{
                duration: 0.78,
                ease: [0.16, 1, 0.3, 1],
            }}
        >
            <div className="pgf-agenda-toolbar">
                <div className="pgf-agenda-brand">
                    <span>
                        Ψ
                    </span>

                    <div>
                        <small>Agenda</small>
                        <strong>
                            Segunda-feira
                        </strong>
                    </div>
                </div>

                <div className="pgf-agenda-sync">
                    <motion.span
                        animate={
                            reduceMotion
                                ? undefined
                                : {
                                      scale: [
                                          1,
                                          1.35,
                                          1,
                                      ],
                                  }
                        }
                        transition={{
                            duration: 2.4,
                            repeat: Infinity,
                            ease: 'easeInOut',
                        }}
                    />

                    Google Agenda sincronizada
                </div>
            </div>

            <div className="pgf-week-strip">
                <div>
                    <small>SEG</small>
                    <strong>08</strong>
                </div>

                <div>
                    <small>TER</small>
                    <strong>09</strong>
                </div>

                <div className="is-active">
                    <small>QUA</small>
                    <strong>10</strong>
                </div>

                <div>
                    <small>QUI</small>
                    <strong>11</strong>
                </div>

                <div>
                    <small>SEX</small>
                    <strong>12</strong>
                </div>
            </div>

            <div className="pgf-agenda-body">
                <div className="pgf-agenda-hours">
                    <span>08:00</span>
                    <span>10:00</span>
                    <span>12:00</span>
                    <span>14:00</span>
                    <span>16:00</span>
                </div>

                <div className="pgf-agenda-sessions">
                    <div
                        className="pgf-current-line"
                        aria-hidden="true"
                    >
                        <span />
                    </div>

                    {sessions.map(
                        (session, index) => (
                            <motion.div
                                className="pgf-session"
                                key={session.time}
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
                                    amount: 0.5,
                                }}
                                transition={{
                                    duration: 0.52,
                                    delay:
                                        0.22 +
                                        index * 0.15,
                                    ease: [
                                        0.16,
                                        1,
                                        0.3,
                                        1,
                                    ],
                                }}
                            >
                                <span className="pgf-session-time">
                                    {session.time}
                                </span>

                                <span
                                    className={`pgf-session-avatar ${session.avatar}`}
                                >
                                    {session.initials}
                                </span>

                                <span className="pgf-session-person">
                                    <strong>
                                        {session.name}
                                    </strong>

                                    <small>
                                        {session.type}
                                    </small>
                                </span>

                                <span
                                    className={`pgf-session-status ${
                                        index === 1
                                            ? 'is-neutral'
                                            : ''
                                    }`}
                                >
                                    {session.status}
                                </span>
                            </motion.div>
                        )
                    )}
                </div>
            </div>

            <motion.div
                className="pgf-agenda-toast"
                initial={{
                    opacity: 0,
                    y: 12,
                    scale: 0.94,
                }}
                whileInView={{
                    opacity: 1,
                    y: 0,
                    scale: 1,
                }}
                viewport={{
                    once: true,
                }}
                transition={{
                    duration: 0.55,
                    delay: 0.95,
                    ease: [0.16, 1, 0.3, 1],
                }}
            >
                <span>
                    <i className="bi bi-whatsapp" />
                </span>

                <div>
                    <small>
                        Confirmação
                    </small>

                    <strong>
                        Lembrete enviado
                    </strong>
                </div>

                <i className="bi bi-check2-circle" />
            </motion.div>
        </motion.div>
    );
}

function PatientMiniUI() {
    return (
        <div className="pgf-patient-ui">
            <div className="pgf-patient-head">
                <span className="pgf-patient-avatar">
                    AM
                </span>

                <div>
                    <strong>
                        Ana Martins
                    </strong>

                    <small>
                        Paciente ativo
                    </small>
                </div>

                <span className="pgf-active-dot">
                    Ativo
                </span>
            </div>

            <div className="pgf-patient-tabs">
                <span className="is-active">
                    Histórico
                </span>

                <span>
                    Sessões
                </span>

                <span>
                    Arquivos
                </span>
            </div>

            <div className="pgf-timeline">
                <span />

                <div>
                    <small>
                        05 SET
                    </small>

                    <strong>
                        Evolução registrada
                    </strong>
                </div>
            </div>

            <div className="pgf-timeline">
                <span />

                <div>
                    <small>
                        29 AGO
                    </small>

                    <strong>
                        Sessão realizada
                    </strong>
                </div>
            </div>
        </div>
    );
}

function AiMiniUI() {
    return (
        <div className="pgf-ai-ui">
            <div className="pgf-ai-top">
                <span>
                    <i className="bi bi-stars" />
                </span>

                <div>
                    <small>
                        Copiloto clínico
                    </small>

                    <strong>
                        Organizar evolução
                    </strong>
                </div>
            </div>

            <div className="pgf-ai-lines">
                <motion.span
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
                        duration: 0.55,
                        delay: 0.15,
                    }}
                />

                <motion.span
                    initial={{
                        scaleX: 0,
                    }}
                    whileInView={{
                        scaleX: 0.86,
                    }}
                    viewport={{
                        once: true,
                    }}
                    transition={{
                        duration: 0.55,
                        delay: 0.28,
                    }}
                />

                <motion.span
                    initial={{
                        scaleX: 0,
                    }}
                    whileInView={{
                        scaleX: 0.72,
                    }}
                    viewport={{
                        once: true,
                    }}
                    transition={{
                        duration: 0.55,
                        delay: 0.41,
                    }}
                />
            </div>

            <div className="pgf-ai-footer">
                <span>
                    <i className="bi bi-shield-check" />
                    Você mantém o controle
                </span>

                <span className="pgf-ai-ready">
                    Pronto
                </span>
            </div>
        </div>
    );
}

function FinanceMiniUI() {
    const bars = [
        43,
        66,
        52,
        79,
        69,
        91,
    ];

    return (
        <div className="pgf-finance-ui">
            <div className="pgf-finance-head">
                <div>
                    <small>
                        Recebido no mês
                    </small>

                    <strong>
                        R$ 7.840
                    </strong>
                </div>

                <span>
                    +12,4%
                </span>
            </div>

            <div className="pgf-finance-chart">
                {bars.map(
                    (height, index) => (
                        <div key={index}>
                            <motion.span
                                style={{
                                    height: `${height}%`,
                                }}
                                initial={{
                                    scaleY: 0,
                                }}
                                whileInView={{
                                    scaleY: 1,
                                }}
                                viewport={{
                                    once: true,
                                }}
                                transition={{
                                    duration: 0.55,
                                    delay:
                                        0.12 +
                                        index * 0.07,
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

            <div className="pgf-finance-legend">
                <span>
                    Sessões pagas
                </span>

                <strong>
                    28
                </strong>
            </div>
        </div>
    );
}

export default function Features({
    featuresUrl,
}) {
    return (
        <section
            id="funcionalidades"
            className="section-features pgf-section"
            aria-labelledby="pgf-title"
        >
            <div className="pgf-background">
                <span className="pgf-background-orb" />
            </div>

            <div className="pgf-container">
                <motion.header
                    className="pgf-heading"
                    variants={reveal}
                    initial="hidden"
                    whileInView="visible"
                    viewport={{
                        once: true,
                        amount: 0.45,
                    }}
                >
                    <div className="pgf-eyebrow">
                        <span>
                            01
                        </span>

                        Uma rotina, um só fluxo
                    </div>

                    <h2 id="pgf-title">
                        Tudo conectado à sua
                        <span>
                            {' '}rotina clínica.
                        </span>
                    </h2>

                    <p>
                        Do primeiro horário do dia ao
                        acompanhamento financeiro, o PsiGestor
                        organiza sua prática em um único fluxo —
                        sem informações espalhadas.
                    </p>
                </motion.header>

                <motion.article
                    className="pgf-main-feature"
                    initial={{
                        opacity: 0,
                        y: 36,
                    }}
                    whileInView={{
                        opacity: 1,
                        y: 0,
                    }}
                    viewport={{
                        once: true,
                        amount: 0.18,
                    }}
                    transition={{
                        duration: 0.75,
                        ease: [0.16, 1, 0.3, 1],
                    }}
                >
                    <div className="pgf-main-copy">
                        <div className="pgf-feature-number">
                            01
                        </div>

                        <div className="pgf-feature-icon">
                            <i className="bi bi-calendar2-week" />
                        </div>

                        <span className="pgf-feature-label">
                            Agenda
                        </span>

                        <h3>
                            Seu dia organizado antes
                            mesmo de começar.
                        </h3>

                        <p>
                            Visualize atendimentos, acompanhe
                            confirmações e mantenha sua agenda
                            sincronizada sem depender de controles
                            paralelos.
                        </p>

                        <ul>
                            <CheckItem>
                                Integração com Google Agenda
                            </CheckItem>

                            <CheckItem>
                                Sessões recorrentes
                            </CheckItem>

                            <CheckItem>
                                Confirmações e lembretes
                            </CheckItem>
                        </ul>
                    </div>

                    <div className="pgf-main-demo">
                        <AgendaPreview />
                    </div>
                </motion.article>

                <div className="pgf-secondary-grid">
                    <motion.article
                        className="pgf-secondary-card pgf-card-patient"
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
                            duration: 0.65,
                            ease: [0.16, 1, 0.3, 1],
                        }}
                    >
                        <div className="pgf-secondary-copy">
                            <span className="pgf-small-number">
                                02
                            </span>

                            <span className="pgf-small-icon">
                                <i className="bi bi-person-vcard" />
                            </span>

                            <h3>
                                Pacientes e prontuário
                            </h3>

                            <p>
                                Histórico clínico, sessões e
                                informações importantes reunidos
                                para acompanhar cada processo com
                                mais contexto.
                            </p>
                        </div>

                        <PatientMiniUI />
                    </motion.article>

                    <motion.article
                        className="pgf-secondary-card pgf-card-ai"
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
                            duration: 0.65,
                            delay: 0.08,
                            ease: [0.16, 1, 0.3, 1],
                        }}
                    >
                        <div className="pgf-secondary-copy">
                            <span className="pgf-small-number">
                                03
                            </span>

                            <span className="pgf-small-icon">
                                <i className="bi bi-stars" />
                            </span>

                            <h3>
                                Evoluções com apoio de IA
                            </h3>

                            <p>
                                Organize sua escrita clínica a
                                partir dos seus próprios tópicos,
                                mantendo você no controle do
                                conteúdo.
                            </p>
                        </div>

                        <AiMiniUI />
                    </motion.article>

                    <motion.article
                        className="pgf-secondary-card pgf-card-finance"
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
                            duration: 0.65,
                            delay: 0.16,
                            ease: [0.16, 1, 0.3, 1],
                        }}
                    >
                        <div className="pgf-secondary-copy">
                            <span className="pgf-small-number">
                                04
                            </span>

                            <span className="pgf-small-icon">
                                <i className="bi bi-wallet2" />
                            </span>

                            <h3>
                                Financeiro sem planilhas
                            </h3>

                            <p>
                                Sessões, pagamentos e visão do
                                mês no mesmo lugar em que sua
                                rotina clínica acontece.
                            </p>
                        </div>

                        <FinanceMiniUI />
                    </motion.article>
                </div>

                <motion.div
                    className="pgf-more"
                    initial={{
                        opacity: 0,
                        y: 20,
                    }}
                    whileInView={{
                        opacity: 1,
                        y: 0,
                    }}
                    viewport={{
                        once: true,
                        amount: 0.4,
                    }}
                    transition={{
                        duration: 0.62,
                    }}
                >
                    <div className="pgf-more-copy">
                        <span>
                            E tem mais
                        </span>

                        <strong>
                            Recursos que acompanham o restante
                            da sua rotina.
                        </strong>
                    </div>

                    <div className="pgf-more-items">
                        <span>
                            <i className="bi bi-google" />
                            Google Agenda
                        </span>

                        <span>
                            <i className="bi bi-whatsapp" />
                            WhatsApp
                        </span>

                        <span>
                            <i className="bi bi-receipt" />
                            Receita Saúde
                        </span>

                        <span>
                            <i className="bi bi-folder2-open" />
                            Arquivos
                        </span>

                        <span>
                            <i className="bi bi-bar-chart-line" />
                            Indicadores
                        </span>
                    </div>

                    <a
                        href={featuresUrl}
                        className="pgf-more-link"
                    >
                        Ver todas

                        <i className="bi bi-arrow-right" />
                    </a>
                </motion.div>
            </div>
        </section>
    );
}