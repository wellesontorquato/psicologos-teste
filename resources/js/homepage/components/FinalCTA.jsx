import {
    motion,
    useReducedMotion,
} from 'motion/react';

const benefits = [
    {
        icon: 'bi-calendar2-check',
        label: 'Agenda',
        detail: 'Sua rotina organizada',
    },
    {
        icon: 'bi-people',
        label: 'Pacientes',
        detail: 'Histórico centralizado',
    },
    {
        icon: 'bi-journal-text',
        label: 'Evoluções',
        detail: 'Tudo no mesmo fluxo',
    },
    {
        icon: 'bi-wallet2',
        label: 'Financeiro',
        detail: 'Mais clareza no mês',
    },
];

export default function FinalCTA({
    registerUrl,
    loginUrl,
}) {
    const reduceMotion =
        useReducedMotion();

    return (
        <section
            className="pgcta-section"
            aria-labelledby="pgcta-title"
        >
            <div
                className="pgcta-background-orb pgcta-background-orb-one"
                aria-hidden="true"
            />

            <div
                className="pgcta-background-orb pgcta-background-orb-two"
                aria-hidden="true"
            />

            <div className="pgcta-container">
                <motion.div
                    className="pgcta-card"
                    initial={
                        reduceMotion
                            ? {
                                  opacity: 0,
                              }
                            : {
                                  opacity: 0,
                                  y: 32,
                                  scale: 0.985,
                              }
                    }
                    whileInView={{
                        opacity: 1,
                        y: 0,
                        scale: 1,
                    }}
                    viewport={{
                        once: true,
                        amount: 0.22,
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
                        className="pgcta-grid"
                        aria-hidden="true"
                    />

                    <div
                        className="pgcta-glow"
                        aria-hidden="true"
                    />

                    <div className="pgcta-copy">
                        <div className="pgcta-eyebrow">
                            <span>
                                04
                            </span>

                            Comece no seu ritmo
                        </div>

                        <h2 id="pgcta-title">
                            Sua rotina pode ser
                            <span>
                                {' '}
                                mais simples
                            </span>
                            {' '}
                            a partir de hoje.
                        </h2>

                        <p>
                            Organize agenda, pacientes,
                            prontuário, evoluções e
                            financeiro em um só lugar —
                            reduzindo o retrabalho sem
                            complicar a sua rotina.
                        </p>

                        <div className="pgcta-actions">
                            <motion.a
                                href={registerUrl}
                                className="pgcta-primary"
                                whileHover={
                                    reduceMotion
                                        ? undefined
                                        : {
                                              y: -2,
                                          }
                                }
                                whileTap={
                                    reduceMotion
                                        ? undefined
                                        : {
                                              scale: 0.985,
                                          }
                                }
                            >
                                Começar 10 dias grátis

                                <span>
                                    <i className="bi bi-arrow-right" />
                                </span>
                            </motion.a>

                            <a
                                href={loginUrl}
                                className="pgcta-login"
                            >
                                Já usa o PsiGestor?

                                <strong>
                                    Entrar
                                </strong>

                                <i className="bi bi-chevron-right" />
                            </a>
                        </div>

                        <div className="pgcta-trust">
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
                        </div>
                    </div>

                    <motion.div
                        className="pgcta-product"
                        initial={
                            reduceMotion
                                ? {
                                      opacity: 0,
                                  }
                                : {
                                      opacity: 0,
                                      x: 24,
                                  }
                        }
                        whileInView={{
                            opacity: 1,
                            x: 0,
                        }}
                        viewport={{
                            once: true,
                            amount: 0.35,
                        }}
                        transition={{
                            duration: 0.7,
                            delay: 0.12,
                            ease: [
                                0.16,
                                1,
                                0.3,
                                1,
                            ],
                        }}
                    >
                        <div className="pgcta-product-header">
                            <div>
                                <small>
                                    Tudo conectado
                                </small>

                                <strong>
                                    Seu consultório,
                                    mais organizado.
                                </strong>
                            </div>

                            <span className="pgcta-product-mark">
                                Ψ
                            </span>
                        </div>

                        <div className="pgcta-benefits">
                            {benefits.map(
                                (
                                    benefit,
                                    index
                                ) => (
                                    <motion.div
                                        className="pgcta-benefit"
                                        key={
                                            benefit.label
                                        }
                                        initial={
                                            reduceMotion
                                                ? {
                                                      opacity: 0,
                                                  }
                                                : {
                                                      opacity: 0,
                                                      y: 10,
                                                  }
                                        }
                                        whileInView={{
                                            opacity: 1,
                                            y: 0,
                                        }}
                                        viewport={{
                                            once: true,
                                        }}
                                        transition={{
                                            duration: 0.45,
                                            delay:
                                                0.2 +
                                                index *
                                                    0.06,
                                        }}
                                    >
                                        <span className="pgcta-benefit-icon">
                                            <i
                                                className={`bi ${benefit.icon}`}
                                            />
                                        </span>

                                        <span className="pgcta-benefit-copy">
                                            <strong>
                                                {
                                                    benefit.label
                                                }
                                            </strong>

                                            <small>
                                                {
                                                    benefit.detail
                                                }
                                            </small>
                                        </span>

                                        <span className="pgcta-benefit-check">
                                            <i className="bi bi-check2" />
                                        </span>
                                    </motion.div>
                                )
                            )}
                        </div>

                        <div className="pgcta-product-footer">
                            <span>
                                <span className="pgcta-status-dot" />

                                Pronto para começar
                            </span>

                            <strong>
                                Acesso imediato
                            </strong>
                        </div>
                    </motion.div>
                </motion.div>
            </div>
        </section>
    );
}