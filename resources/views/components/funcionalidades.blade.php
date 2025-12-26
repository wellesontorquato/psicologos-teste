<section class="section-features" style="background-color: #ffffff; padding: 60px 20px;">
    <div style="max-width: 1200px; margin: auto;">
        <h2 style="text-align: center; font-size: 1.8rem; margin-bottom: 20px; font-weight: bold;">
            Funcionalidades do PsiGestor
        </h2>
        <p style="text-align: center; color: #666; max-width: 700px; margin: 0 auto 40px;">
            Descubra os recursos que tornam sua clínica mais eficiente, organizada e conectada.
        </p>

        <div class="features-grid" style="
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 30px;
        ">
            @php
                $features = [
                    [
                        'icon' => 'calendar-check',
                        'title' => 'Agenda Visual',
                        'desc'  => 'Drag & drop, semana/dia e notificações.',
                        'details_title' => 'Agenda Visual + Google Agenda + Google Meet',
                        'details_html' => '
                            <p>Organize sua agenda com <strong>drag &amp; drop</strong>, visualização por <strong>dia</strong> e <strong>semana</strong>, e fluxo rápido para criar, editar e remarcar sessões.</p>
                            <p>Com a <strong>sincronização com o Google Agenda</strong>, você consegue visualizar as agendas já existentes e manter tudo alinhado em um só lugar, evitando conflitos.</p>
                            <p>Ao criar um atendimento, o sistema pode <strong>disparar automaticamente um e-mail</strong> para o paciente com o <strong>link da sala do Google Meet</strong>.</p>
                        '
                    ],
                    [
                        'icon' => 'file-lines',
                        'title' => 'Evoluções',
                        'desc'  => 'Linha do tempo do prontuário e histórico.',
                        'details_title' => 'Evoluções e Registro Clínico',
                        'details_html' => '
                            <p>Registre evoluções em uma <strong>linha do tempo</strong> por paciente, com histórico de sessões sempre acessível.</p>
                            <p>Mantenha informações clínicas estruturadas: progresso, intervenções, observações e objetivos terapêuticos.</p>
                            <p>Mais segurança e organização, reduzindo retrabalho e anotações soltas.</p>
                        '
                    ],
                    [
                        'icon' => 'coins',
                        'title' => 'Financeiro',
                        'desc'  => 'Pagamentos, pendências e controle.',
                        'details_title' => 'Financeiro + Multimoedas',
                        'details_html' => '
                            <p>Controle recebimentos por paciente e por período, com visão clara de <strong>pagos</strong>, <strong>pendentes</strong> e <strong>atrasos</strong>.</p>
                            <p>Envie notificações de sessões anteriores não pagas e acompanhe seu fluxo financeiro com praticidade.</p>
                            <p>Com <strong>multimoedas</strong>, registre atendimentos e recebimentos em diferentes moedas — ideal para pacientes de outros países.</p>
                        '
                    ],
                    [
                        'icon' => 'cloud-upload-alt',
                        'title' => 'Arquivos',
                        'desc'  => 'Documentos e anexos do paciente.',
                        'details_title' => 'Arquivos e Documentos',
                        'details_html' => '
                            <p>Centralize exames, relatórios, contratos, termos e anexos clínicos por paciente.</p>
                            <p>Organização por tipo e acesso rápido, evitando perda de arquivos e pastas soltas.</p>
                            <p>Mais agilidade durante o atendimento e no acompanhamento do histórico.</p>
                        '
                    ],
                    [
                        'icon' => 'whatsapp',
                        'title' => 'Confirmação por WhatsApp',
                        'desc'  => 'Lembretes e confirmações automáticas.',
                        'details_title' => 'WhatsApp Automático',
                        'details_html' => '
                            <p>Envie confirmações e lembretes automáticos, reduzindo faltas e melhorando a previsibilidade da agenda.</p>
                            <p>Padronize mensagens e mantenha comunicação profissional sem depender de envio manual.</p>
                            <p>O paciente recebe as informações no canal mais usado do dia a dia.</p>
                        '
                    ],
                    [
                        'icon' => 'chart-line',
                        'title' => 'Painel de Indicadores',
                        'desc'  => 'Métricas e visão por período.',
                        'details_title' => 'Indicadores e Estatísticas',
                        'details_html' => '
                            <p>Acompanhe a evolução da sua clínica com gráficos e métricas por período.</p>
                            <p>Entenda tendências de atendimentos, recorrência e comportamento de agenda.</p>
                            <p>Transforme dados em decisões: enxergue padrões e oportunidades de melhoria.</p>
                        '
                    ],
                    [
                        'icon' => 'user-shield',
                        'title' => 'Banco de dados robusto',
                        'desc'  => 'Segurança, estabilidade e backups.',
                        'details_title' => 'Segurança e Confiabilidade',
                        'details_html' => '
                            <p>Estrutura pensada para estabilidade e proteção de dados, com foco em continuidade e confiança.</p>
                            <p>Centralize informações em um ambiente confiável, reduzindo riscos de perda e inconsistências.</p>
                            <p>Ideal para sair de planilhas e anotações soltas e ganhar padrão de atendimento.</p>
                        '
                    ],
                    [
                        'icon' => 'file-export',
                        'title' => 'Exportações em PDF e Excel',
                        'desc'  => 'Relatórios com filtros e exportação.',
                        'details_title' => 'Relatórios e Exportações',
                        'details_html' => '
                            <p>Gere relatórios com filtros por data, status de sessão e situação de pagamento.</p>
                            <p>Exporte em <strong>PDF</strong> para arquivar e em <strong>Excel</strong> para análises e fechamento.</p>
                            <p>Economize tempo no controle mensal e na gestão financeira.</p>
                        '
                    ],
                ];
            @endphp

            @foreach ($features as $f)
                <div class="feature-card">
                    <i class="fa-solid fa-{{ $f['icon'] }} feature-icon"></i>

                    <h4 class="feature-title">{{ $f['title'] }}</h4>

                    <p class="feature-desc">{{ $f['desc'] }}</p>

                    <button
                        type="button"
                        class="feature-more-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#featureModal"
                        data-feature-title="{{ $f['details_title'] ?? $f['title'] }}"
                        data-feature-body-html="{{ e($f['details_html']) }}"
                    >
                        Ver detalhes
                    </button>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Modal Único --}}
    <div class="modal fade modal-soft" id="featureModal" tabindex="-1" aria-labelledby="featureModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content modal-soft-content">
                <div class="modal-header modal-soft-header">
                    <h5 class="modal-title" id="featureModalLabel">Detalhes</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <div class="modal-body">
                    <div id="featureModalBody" class="modal-soft-body"></div>
                </div>

                <div class="modal-footer modal-soft-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Cards */
    .feature-card {
        background: #f8f9fa;
        border-radius: 16px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 3px 8px rgba(0,0,0,0.05);
        transition: 0.25s ease;

        display: flex;
        flex-direction: column;
        gap: 10px;
        min-height: 240px;
    }
    .feature-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(0,0,0,0.08);
    }
    .feature-icon {
        font-size: 1.8rem;
        color: #00aaff;
        margin-bottom: 5px;
    }
    .feature-title {
        font-size: 1.1rem;
        margin: 0;
        font-weight: 900;
        color: #111;
    }
    .feature-desc {
        font-size: 0.95rem;
        color: #555;
        line-height: 1.45;

        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin: 0;
    }
    .feature-more-btn {
        margin-top: auto;
        align-self: center;

        border: 1px solid rgba(0,170,255,0.35);
        background: rgba(0,170,255,0.08);
        color: #008ecc;
        font-weight: 900;
        border-radius: 999px;
        padding: 8px 14px;
        cursor: pointer;
        transition: 0.2s ease;
    }
    .feature-more-btn:hover {
        background: rgba(0,170,255,0.14);
        border-color: rgba(0,170,255,0.55);
        transform: translateY(-1px);
    }

    /* ===== Modal suave (animação premium) ===== */
    .modal-soft .modal-dialog {
        transform: translateY(10px) scale(0.98);
        transition: transform .28s ease, opacity .28s ease;
        opacity: 0;
    }
    .modal-soft.show .modal-dialog {
        transform: translateY(0) scale(1);
        opacity: 1;
    }

    /* Backdrop mais suave */
    .modal-backdrop.show {
        opacity: .55;
        transition: opacity .25s ease;
    }

    .modal-soft-content {
        border-radius: 16px;
        overflow: hidden;
        border: 0;
        box-shadow: 0 20px 60px rgba(0,0,0,0.25);
    }
    .modal-soft-header {
        background: linear-gradient(to right, #00aaff, #00c4ff);
        border: 0;
        padding: 16px 18px;
    }
    .modal-soft-header .modal-title {
        color: #fff;
        font-weight: 900;
        margin: 0;
    }
    .modal-soft-body {
        color: #333;
        line-height: 1.75;
    }
    .modal-soft-body p { margin: 0 0 10px; }
    .modal-soft-body p:last-child { margin-bottom: 0; }
    .modal-soft-footer {
        border-top: 1px solid #eee;
        padding: 14px 18px;
    }

    /* Respeita acessibilidade */
    @media (prefers-reduced-motion: reduce) {
        .modal-soft .modal-dialog,
        .modal-backdrop.show {
            transition: none !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalEl = document.getElementById('featureModal');
        if (!modalEl) return;

        modalEl.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const title = button?.getAttribute('data-feature-title') || 'Detalhes';

            const escapedHtml = button?.getAttribute('data-feature-body-html') || '';
            const bodyHtml = escapedHtml
                .replaceAll('&lt;', '<')
                .replaceAll('&gt;', '>')
                .replaceAll('&amp;', '&')
                .replaceAll('&quot;', '"')
                .replaceAll('&#039;', "'");

            modalEl.querySelector('#featureModalLabel').textContent = title;
            modalEl.querySelector('#featureModalBody').innerHTML = bodyHtml;
        });
    });
</script>
