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
                        'id'    => 'agenda',
                        'icon'  => 'calendar-check',
                        'title' => 'Agenda Visual',
                        'desc'  => 'Drag & drop, semana/dia e notificações.',
                        'is_new'=> true,
                        'details_title' => 'Agenda Visual + Google Agenda + Google Meet',
                        'details_html' => '
                            <p>Organize sua agenda com <strong>drag &amp; drop</strong>, visualização por <strong>dia</strong> e <strong>semana</strong>, e fluxo rápido para criar, editar e remarcar sessões.</p>
                            <p>Com a <strong>sincronização com o Google Agenda</strong>, você consegue visualizar as agendas já existentes e manter tudo alinhado em um só lugar, evitando conflitos.</p>
                            <p>Ao criar um atendimento, o sistema pode <strong>disparar automaticamente um e-mail</strong> para o paciente com o <strong>link da sala do Google Meet</strong>.</p>
                            <ul>
                                <li>Visualização clara do dia/semana</li>
                                <li>Remarcações sem bagunça</li>
                                <li>Teleatendimento com link automático</li>
                            </ul>
                        '
                    ],
                    [
                        'id'    => 'evolucoes',
                        'icon'  => 'file-lines',
                        'title' => 'Evoluções',
                        'desc'  => 'Linha do tempo do prontuário e histórico.',
                        'is_new'=> false,
                        'details_title' => 'Evoluções e Registro Clínico',
                        'details_html' => '
                            <p>Registre evoluções em uma <strong>linha do tempo</strong> por paciente, com histórico de sessões sempre acessível.</p>
                            <p>Mantenha informações clínicas estruturadas: progresso, intervenções, observações e objetivos terapêuticos.</p>
                            <ul>
                                <li>Histórico organizado</li>
                                <li>Mais consistência nos registros</li>
                                <li>Menos retrabalho e anotações soltas</li>
                            </ul>
                        '
                    ],
                    [
                        'id'    => 'financeiro',
                        'icon'  => 'coins',
                        'title' => 'Financeiro',
                        'desc'  => 'Pagamentos, pendências e controle.',
                        'is_new'=> true,
                        'details_title' => 'Financeiro + Multimoedas',
                        'details_html' => '
                            <p>Controle recebimentos por paciente e por período, com visão clara de <strong>pagos</strong>, <strong>pendentes</strong> e <strong>atrasos</strong>.</p>
                            <p>Envie notificações de sessões não pagas e acompanhe seu fluxo financeiro com praticidade.</p>
                            <p>Com <strong>multimoedas</strong>, registre atendimentos e recebimentos em diferentes moedas — ideal para pacientes de outros países.</p>
                            <ul>
                                <li>Organização financeira por período</li>
                                <li>Notificações de pendências</li>
                                <li>Recebimentos em moedas diferentes</li>
                            </ul>
                        '
                    ],
                    [
                        'id'    => 'arquivos',
                        'icon'  => 'cloud-upload-alt',
                        'title' => 'Arquivos',
                        'desc'  => 'Documentos e anexos do paciente.',
                        'is_new'=> false,
                        'details_title' => 'Arquivos e Documentos',
                        'details_html' => '
                            <p>Centralize exames, relatórios, contratos, termos e anexos clínicos por paciente.</p>
                            <p>Organização por tipo e acesso rápido, evitando perda de arquivos e pastas soltas.</p>
                            <ul>
                                <li>Documentos centralizados</li>
                                <li>Organização por tipo</li>
                                <li>Acesso rápido durante o atendimento</li>
                            </ul>
                        '
                    ],
                    [
                        'id'    => 'whatsapp',
                        'icon'  => 'whatsapp',
                        'title' => 'Confirmação por WhatsApp',
                        'desc'  => 'Lembretes e confirmações automáticas.',
                        'is_new'=> false,
                        'details_title' => 'WhatsApp Automático',
                        'details_html' => '
                            <p>Envie confirmações e lembretes automáticos, reduzindo faltas e melhorando a previsibilidade da agenda.</p>
                            <p>Padronize mensagens e mantenha comunicação profissional sem depender de envio manual.</p>
                            <ul>
                                <li>Menos faltas</li>
                                <li>Comunicação padronizada</li>
                                <li>Mais tempo para atender</li>
                            </ul>
                        '
                    ],
                    [
                        'id'    => 'indicadores',
                        'icon'  => 'chart-line',
                        'title' => 'Painel de Indicadores',
                        'desc'  => 'Métricas e visão por período.',
                        'is_new'=> false,
                        'details_title' => 'Indicadores e Estatísticas',
                        'details_html' => '
                            <p>Acompanhe a evolução da sua clínica com gráficos e métricas por período.</p>
                            <p>Entenda tendências de atendimentos, recorrência e comportamento de agenda.</p>
                            <ul>
                                <li>Visão por período</li>
                                <li>Tendências e padrões</li>
                                <li>Decisões com base em dados</li>
                            </ul>
                        '
                    ],
                    [
                        'id'    => 'seguranca',
                        'icon'  => 'user-shield',
                        'title' => 'Banco de dados robusto',
                        'desc'  => 'Segurança, estabilidade e backups.',
                        'is_new'=> false,
                        'details_title' => 'Segurança e Confiabilidade',
                        'details_html' => '
                            <p>Estrutura pensada para estabilidade e proteção de dados, com foco em continuidade e confiança.</p>
                            <p>Centralize informações em um ambiente confiável, reduzindo riscos de perda e inconsistências.</p>
                            <ul>
                                <li>Estabilidade</li>
                                <li>Organização</li>
                                <li>Confiabilidade</li>
                            </ul>
                        '
                    ],
                    [
                        'id'    => 'exportacoes',
                        'icon'  => 'file-export',
                        'title' => 'Exportações em PDF e Excel',
                        'desc'  => 'Relatórios com filtros e exportação.',
                        'is_new'=> false,
                        'details_title' => 'Relatórios e Exportações',
                        'details_html' => '
                            <p>Gere relatórios com filtros por data, status de sessão e situação de pagamento.</p>
                            <p>Exporte em <strong>PDF</strong> para arquivar e em <strong>Excel</strong> para análises e fechamento.</p>
                            <ul>
                                <li>Relatórios com filtros</li>
                                <li>PDF para arquivo</li>
                                <li>Excel para análise</li>
                            </ul>
                        '
                    ],
                ];
            @endphp

            @foreach ($features as $index => $f)
                <div class="feature-card">
                    <div class="feature-top">
                        <i class="fa-solid fa-{{ $f['icon'] }} feature-icon"></i>

                        @if(!empty($f['is_new']))
                            <span class="feature-badge">NOVO</span>
                        @endif
                    </div>

                    <h4 class="feature-title">{{ $f['title'] }}</h4>
                    <p class="feature-desc">{{ $f['desc'] }}</p>

                    <button
                        type="button"
                        class="feature-more-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#featureModal"
                        data-feature-index="{{ $index }}"
                        data-feature-title="{{ $f['details_title'] ?? $f['title'] }}"
                        data-feature-body-html="{{ e($f['details_html']) }}"
                    >
                        Ver detalhes
                    </button>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Modal Único (com Next/Prev) --}}
    <div class="modal fade modal-soft" id="featureModal" tabindex="-1" aria-labelledby="featureModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content modal-soft-content">
                <div class="modal-header modal-soft-header">
                    <div class="modal-head-left">
                        <h5 class="modal-title" id="featureModalLabel">Detalhes</h5>
                        <p class="modal-subtitle">Explore os recursos e entenda como cada funcionalidade melhora sua rotina.</p>
                    </div>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <div class="modal-body">
                    <div id="featureModalBody" class="modal-soft-body"></div>
                </div>

                <div class="modal-footer modal-soft-footer">
                    <div class="modal-nav">
                        <button type="button" class="btn btn-outline-light modal-nav-btn" id="featurePrevBtn">
                            ← Anterior
                        </button>
                        <button type="button" class="btn btn-outline-light modal-nav-btn" id="featureNextBtn">
                            Próximo →
                        </button>
                    </div>

                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 12px; font-weight: 900;">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Dataset (JS usa isso pro carrossel) --}}
    <script type="application/json" id="featureDataset">
        {!! json_encode(array_map(function($f) {
            return [
                'title' => $f['details_title'] ?? $f['title'],
                'body'  => $f['details_html'],
            ];
        }, $features), JSON_UNESCAPED_UNICODE) !!}
    </script>
</section>

<style>
    /* Cards */
    .feature-card {
        background: #f8f9fa;
        border-radius: 16px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 3px 8px rgba(0,0,0,0.05);
        transition: 0.28s ease;

        display: flex;
        flex-direction: column;
        gap: 10px;
        min-height: 250px;
        position: relative;
        overflow: hidden;
    }
    .feature-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 34px rgba(0,0,0,0.09);
    }

    .feature-top{
        display:flex;
        align-items:center;
        justify-content:center;
        gap:10px;
        margin-bottom: 2px;
    }

    .feature-icon {
        font-size: 1.8rem;
        color: #00aaff;
    }

    .feature-badge{
        font-size: .72rem;
        font-weight: 900;
        letter-spacing: .5px;
        padding: 5px 10px;
        border-radius: 999px;
        color: #003a52;
        background: rgba(0,170,255,0.18);
        border: 1px solid rgba(0,170,255,0.28);
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
        transition: 0.22s ease;
    }
    .feature-more-btn:hover {
        background: rgba(0,170,255,0.14);
        border-color: rgba(0,170,255,0.55);
        transform: translateY(-1px);
    }

    /* ===== Modal UX suave (override Bootstrap) ===== */
    .modal.fade .modal-dialog {
        transform: translateY(30px) scale(0.96);
        opacity: 0;
        transition:
            transform 0.35s cubic-bezier(.4,0,.2,1),
            opacity 0.35s ease;
    }

    .modal.show .modal-dialog {
        transform: translateY(0) scale(1);
        opacity: 1;
    }

    .modal-backdrop {
        background-color: rgba(0,0,0,.55);
    }

    .modal-backdrop.fade {
        opacity: 0;
    }

    .modal-backdrop.show {
        opacity: 1;
    }

    @media (prefers-reduced-motion: reduce) {
        .modal.fade .modal-dialog {
            transition: none;
            transform: none;
        }
    }

    /* ===== Tema do modal ===== */
    .modal-soft-content{
        border-radius: 18px;
        overflow: hidden;
        border: 0;
        box-shadow: 0 28px 90px rgba(0,0,0,0.32);
    }

    .modal-soft-header{
        background: linear-gradient(to right, #00aaff, #00c4ff);
        border: 0;
        padding: 16px 18px;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
    }

    .modal-head-left{ display:flex; flex-direction:column; gap:4px; }
    .modal-soft-header .modal-title{
        color:#fff;
        font-weight: 900;
        margin: 0;
        line-height: 1.15;
    }
    .modal-subtitle{
        margin: 0;
        color: rgba(255,255,255,.9);
        font-size: .92rem;
        line-height: 1.3;
        max-width: 70ch;
    }

    /* Scroll interno suave */
    .modal-dialog-scrollable .modal-body{
        max-height: calc(100vh - 210px);
        overflow: auto;
        scroll-behavior: smooth;
        padding: 18px;
    }

    .modal-soft-body{
        color: #222;
        line-height: 1.75;
        font-size: 1rem;
    }
    .modal-soft-body p{ margin: 0 0 10px; }
    .modal-soft-body ul{ margin: 10px 0 0; padding-left: 20px; }
    .modal-soft-body li{ margin: 6px 0; }

    /* Footer */
    .modal-soft-footer{
        border-top: 1px solid rgba(255,255,255,.15);
        background: #0b1620;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .modal-nav{
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .modal-nav-btn{
        border-radius: 12px;
        font-weight: 900;
        padding: 8px 14px;
    }

    @media (max-width: 576px){
        .modal-soft-footer{
            flex-direction: column;
            align-items: stretch;
        }
        .modal-nav{
            width: 100%;
            justify-content: space-between;
        }
        .modal-nav-btn{
            flex: 1;
        }
    }

    @media (prefers-reduced-motion: reduce){
        .modal-dialog-scrollable .modal-body{
            scroll-behavior: auto !important;
        }
    }

    /* ===============================
   MOBILE: Features como carrossel
   =============================== */
    @media (max-width: 768px) {

        .features-grid {
            display: flex !important;
            flex-wrap: nowrap !important;
            gap: 16px;
            overflow-x: auto;
            padding-bottom: 10px;
            padding-left: 8px;
            padding-right: 8px;

            /* swipe suave */
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;

            /* evita scroll vertical acidental */
            overscroll-behavior-x: contain;
        }

        .features-grid::-webkit-scrollbar {
            display: none;
        }

        .feature-card {
            flex: 0 0 82%;
            scroll-snap-align: start;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            min-height: 260px;
        }

        .feature-card:active {
            transform: scale(0.98);
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalEl = document.getElementById('featureModal');
        if (!modalEl) return;

        const dataEl = document.getElementById('featureDataset');
        const features = dataEl ? JSON.parse(dataEl.textContent) : [];

        const titleEl = modalEl.querySelector('#featureModalLabel');
        const bodyEl  = modalEl.querySelector('#featureModalBody');
        const prevBtn = modalEl.querySelector('#featurePrevBtn');
        const nextBtn = modalEl.querySelector('#featureNextBtn');

        let currentIndex = 0;

        function decodeHtml(escapedHtml){
            return (escapedHtml || '')
                .replaceAll('&lt;', '<')
                .replaceAll('&gt;', '>')
                .replaceAll('&amp;', '&')
                .replaceAll('&quot;', '"')
                .replaceAll('&#039;', "'");
        }

        function render(index){
            if (!features.length) return;

            currentIndex = (index + features.length) % features.length;

            titleEl.textContent = features[currentIndex].title || 'Detalhes';
            bodyEl.innerHTML = decodeHtml(features[currentIndex].body || '');

            const modalBody = modalEl.querySelector('.modal-body');
            if (modalBody) modalBody.scrollTo({ top: 0, behavior: 'smooth' });

            const disabled = features.length <= 1;
            prevBtn.disabled = disabled;
            nextBtn.disabled = disabled;
        }

        modalEl.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const idx = parseInt(button?.getAttribute('data-feature-index') || '0', 10);
            render(Number.isFinite(idx) ? idx : 0);
        });

        prevBtn.addEventListener('click', function(){ render(currentIndex - 1); });
        nextBtn.addEventListener('click', function(){ render(currentIndex + 1); });

        modalEl.addEventListener('keydown', function(e){
            if (e.key === 'ArrowLeft')  { e.preventDefault(); render(currentIndex - 1); }
            if (e.key === 'ArrowRight') { e.preventDefault(); render(currentIndex + 1); }
        });
    });
</script>
