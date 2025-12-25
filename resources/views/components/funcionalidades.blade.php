<section class="section-features" style="background-color: #ffffff; padding: 60px 20px;">
    <div style="max-width: 1200px; margin: auto;">
        <h2 style="text-align: center; font-size: 1.8rem; margin-bottom: 20px; font-weight: bold;">
            Funcionalidades do PsiGestor
        </h2>
        <p style="text-align: center; color: #666; max-width: 700px; margin: 0 auto 40px;">
            Descubra os recursos que tornam sua clínica mais eficiente, organizada e conectada.
        </p>

        <div style="
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 30px;
        ">
            @php
                $features = [
                    [
                        'icon' => 'calendar-check',
                        'title' => 'Agenda Visual',
                        'desc'  => 'Agenda com drag & drop e visualização por dia ou semana.',
                        'details' => 'Sincronização com o Google Agenda (com acesso às agendas já existentes) e envio automático de e-mails ao paciente com o link da sala do Google Meet ao criar o atendimento.'
                    ],
                    [
                        'icon' => 'file-lines',
                        'title' => 'Evoluções',
                        'desc'  => 'Linha do tempo completa com registros clínicos organizados.',
                        'details' => null
                    ],
                    [
                        'icon' => 'coins',
                        'title' => 'Financeiro',
                        'desc'  => 'Controle de pagamentos e notificações de sessões não pagas.',
                        'details' => 'Suporte a multimoedas para organizar atendimentos e recebimentos em diferentes moedas.'
                    ],
                    [
                        'icon' => 'cloud-upload-alt',
                        'title' => 'Arquivos',
                        'desc'  => 'Documentos organizados por tipo: exames, contratos e relatórios.',
                        'details' => null
                    ],
                    [
                        'icon' => 'whatsapp',
                        'title' => 'Confirmação por WhatsApp',
                        'desc'  => 'Envio automático de lembretes e confirmações de sessão.',
                        'details' => null
                    ],
                    [
                        'icon' => 'chart-line',
                        'title' => 'Painel de Indicadores',
                        'desc'  => 'Gráficos e estatísticas por período e evolução da clínica.',
                        'details' => null
                    ],
                    [
                        'icon' => 'user-shield',
                        'title' => 'Banco de dados robusto',
                        'desc'  => 'Seus dados protegidos com segurança e backups automáticos.',
                        'details' => null
                    ],
                    [
                        'icon' => 'file-export',
                        'title' => 'Exportações em PDF e Excel',
                        'desc'  => 'Relatórios com filtros por data, status e pagamento.',
                        'details' => null
                    ],
                ];
            @endphp

            @foreach ($features as $f)
                <div class="feature-card">
                    <i class="fa-solid fa-{{ $f['icon'] }} feature-icon"></i>

                    <h4 class="feature-title">
                        {{ $f['title'] }}
                    </h4>

                    <p class="feature-desc">
                        {{ $f['desc'] }}
                    </p>

                    @if(!empty($f['details']))
                        <details class="feature-details">
                            <summary>Ver detalhes</summary>
                            <p>{{ $f['details'] }}</p>
                        </details>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>

<style>
    .feature-card {
        background: #f8f9fa;
        border-radius: 16px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 3px 8px rgba(0,0,0,0.05);
        transition: 0.3s ease;

        display: flex;
        flex-direction: column;
        gap: 10px;
        min-height: 220px; /* mantém padrão visual */
    }

    .feature-icon {
        font-size: 1.8rem;
        color: #00aaff;
        margin-bottom: 5px;
    }

    .feature-title {
        font-size: 1.1rem;
        margin: 0;
        font-weight: 700;
    }

    .feature-desc {
        font-size: 0.95rem;
        color: #555;
        line-height: 1.45;

        display: -webkit-box;
        -webkit-line-clamp: 3; /* mantém altura uniforme */
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .feature-details {
        margin-top: auto; /* alinha o "Ver detalhes" no rodapé do card */
        text-align: left;
    }

    .feature-details summary {
        cursor: pointer;
        color: #008ecc;
        font-weight: 600;
        list-style: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        user-select: none;
    }

    .feature-details summary::-webkit-details-marker {
        display: none;
    }

    .feature-details summary::after {
        content: "▾";
        font-size: 0.85rem;
    }

    .feature-details[open] summary::after {
        content: "▴";
    }

    .feature-details p {
        margin: 8px 0 0;
        color: #555;
        font-size: 0.9rem;
        line-height: 1.5;
    }
</style>
