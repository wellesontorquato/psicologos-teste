@extends('layouts.landing')

@section('title', 'Funcionalidades | PsiGestor')

@section('content')

{{-- Título com fundo gradiente --}}
<section style="background: linear-gradient(to right, #00aaff, #00c4ff); padding: 60px 20px;">
    <div style="max-width: 1100px; margin: auto; text-align: center;">
        <h1 style="font-size: 2rem; color: white; margin-bottom: 10px;">
            Funcionalidades do PsiGestor
        </h1>
        <p style="font-size: 1.1rem; color: white;">
            Conheça as principais funcionalidades que tornam sua rotina clínica mais leve e eficiente.
        </p>
    </div>
</section>

{{-- Conteúdo em cards --}}
<section style="max-width: 1100px; margin: 50px auto; padding: 0 20px;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 30px;">
        @php
            $features = [
                [
                    'title' => 'Agenda Visual',
                    'desc'  => 'Drag & drop, semana/dia e notificações.',
                    'is_new'=> true,
                    'details_title' => 'Agenda Visual + Google Agenda + Google Meet',
                    'details_html' => '
                        <p>Organize sua agenda com <strong>drag &amp; drop</strong>, visualização por <strong>dia</strong> e <strong>semana</strong>, e fluxo rápido para criar, editar e remarcar sessões.</p>
                        <p>Com a <strong>sincronização com o Google Agenda</strong>, você visualiza as agendas já existentes e mantém tudo alinhado, evitando conflitos de horários.</p>
                        <p>Ao criar um atendimento, o sistema pode <strong>enviar automaticamente um e-mail</strong> ao paciente com o <strong>link da sala do Google Meet</strong>.</p>
                        <ul>
                            <li>Evita conflitos de agenda</li>
                            <li>Teleconsulta com link pronto</li>
                            <li>Fluxo rápido de remarcação</li>
                        </ul>
                    '
                ],
                [
                    'title' => 'Evoluções',
                    'desc'  => 'Prontuário com histórico completo.',
                    'is_new'=> false,
                    'details_title' => 'Evoluções e Registro Clínico',
                    'details_html' => '
                        <p>Registre evoluções em uma <strong>linha do tempo</strong> por paciente, com histórico de sessões sempre acessível.</p>
                        <p>Padronize anotações clínicas com mais segurança e organização.</p>
                        <ul>
                            <li>Histórico completo</li>
                            <li>Mais consistência no prontuário</li>
                            <li>Menos anotações dispersas</li>
                        </ul>
                    '
                ],
                [
                    'title' => 'Financeiro',
                    'desc'  => 'Pagamentos, pendências e controle.',
                    'is_new'=> true,
                    'details_title' => 'Financeiro + Multimoedas',
                    'details_html' => '
                        <p>Controle recebimentos por paciente e por período, com visão clara de <strong>pagos</strong>, <strong>pendentes</strong> e <strong>atrasos</strong>.</p>
                        <p>Envie notificações de sessões não pagas e acompanhe seu fluxo financeiro.</p>
                        <p><strong>Multimoedas</strong>: registre atendimentos e recebimentos em moedas diferentes — ideal para pacientes de fora do Brasil.</p>
                        <ul>
                            <li>Organização por período</li>
                            <li>Notificações de pendências</li>
                            <li>Recebimentos em moedas diferentes</li>
                        </ul>
                    '
                ],
                [
                    'title' => 'Arquivos',
                    'desc'  => 'Documentos e anexos organizados.',
                    'is_new'=> false,
                    'details_title' => 'Arquivos e Documentos',
                    'details_html' => '
                        <p>Centralize exames, relatórios, contratos, termos e anexos clínicos por paciente.</p>
                        <ul>
                            <li>Organização por tipo</li>
                            <li>Acesso rápido</li>
                            <li>Menos retrabalho</li>
                        </ul>
                    '
                ],
                [
                    'title' => 'Confirmação por WhatsApp',
                    'desc'  => 'Lembretes e confirmações automáticas.',
                    'is_new'=> false,
                    'details_title' => 'WhatsApp Automático',
                    'details_html' => '
                        <p>Envie confirmações e lembretes automáticos, reduzindo faltas e melhorando previsibilidade.</p>
                        <ul>
                            <li>Menos faltas</li>
                            <li>Mensagens padronizadas</li>
                            <li>Mais tempo livre</li>
                        </ul>
                    '
                ],
                [
                    'title' => 'Painel de Indicadores',
                    'desc'  => 'Métricas e visão por período.',
                    'is_new'=> false,
                    'details_title' => 'Indicadores e Estatísticas',
                    'details_html' => '
                        <p>Visualize métricas com gráficos por período, entendendo a evolução da clínica.</p>
                        <ul>
                            <li>Decisões baseadas em dados</li>
                            <li>Visão clara por período</li>
                            <li>Melhoria contínua</li>
                        </ul>
                    '
                ],
                [
                    'title' => 'Banco de dados robusto',
                    'desc'  => 'Segurança e estabilidade.',
                    'is_new'=> false,
                    'details_title' => 'Segurança e Confiabilidade',
                    'details_html' => '
                        <p>Centralize tudo em um ambiente confiável, com foco em estabilidade e organização.</p>
                        <ul>
                            <li>Confiabilidade</li>
                            <li>Organização</li>
                            <li>Segurança</li>
                        </ul>
                    '
                ],
                [
                    'title' => 'Exportações em PDF e Excel',
                    'desc'  => 'Relatórios com filtros e exportação.',
                    'is_new'=> false,
                    'details_title' => 'Relatórios e Exportações',
                    'details_html' => '
                        <p>Gere relatórios completos com filtros por data, status e situação de pagamento.</p>
                        <ul>
                            <li>PDF para arquivar</li>
                            <li>Excel para analisar</li>
                            <li>Fechamento mensal rápido</li>
                        </ul>
                    '
                ],
            ];
        @endphp

        @foreach($features as $index => $f)
            <div class="feature-card-page">
                <div class="feature-page-top">
                    <h4 class="feature-title-page">{{ $f['title'] }}</h4>
                    @if(!empty($f['is_new']))
                        <span class="feature-badge">NOVO</span>
                    @endif
                </div>

                <p class="feature-desc-page">{{ $f['desc'] }}</p>

                <button
                    type="button"
                    class="feature-more-btn-page"
                    data-bs-toggle="modal"
                    data-bs-target="#featureModalPage"
                    data-feature-index="{{ $index }}"
                    data-feature-title="{{ $f['details_title'] ?? $f['title'] }}"
                    data-feature-body-html="{{ e($f['details_html']) }}"
                >
                    Ver detalhes
                </button>
            </div>
        @endforeach
    </div>

    {{-- Modal Único --}}
    <div class="modal fade modal-soft" id="featureModalPage" tabindex="-1" aria-labelledby="featureModalPageLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content modal-soft-content">
                <div class="modal-header modal-soft-header">
                    <div class="modal-head-left">
                        <h5 class="modal-title" id="featureModalPageLabel">Detalhes</h5>
                        <p class="modal-subtitle">Navegue entre funcionalidades sem sair da página.</p>
                    </div>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <div class="modal-body">
                    <div id="featureModalPageBody" class="modal-soft-body"></div>
                </div>

                <div class="modal-footer modal-soft-footer">
                    <div class="modal-nav">
                        <button type="button" class="btn btn-outline-light modal-nav-btn" id="featurePrevBtnPage">
                            ← Anterior
                        </button>
                        <button type="button" class="btn btn-outline-light modal-nav-btn" id="featureNextBtnPage">
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

    {{-- Dataset --}}
    <script type="application/json" id="featureDatasetPage">
        {!! json_encode(array_map(function($f) {
            return [
                'title' => $f['details_title'] ?? $f['title'],
                'body'  => $f['details_html'],
            ];
        }, $features), JSON_UNESCAPED_UNICODE) !!}
    </script>
</section>

{{-- BOTÃO WHATSAPP FLOTANTE --}}
<a href="https://wa.me/5582991128022?text=Ol%C3%A1%2C%20tenho%20interesse%20no%20PsiGestor%20e%20gostaria%20de%20saber%20mais%20sobre%20os%20planos!" target="_blank" style="
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 999;
    display: flex;
    align-items: center;
    gap: 10px;
    background: #25d366;
    color: white;
    padding: 10px 15px;
    border-radius: 50px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s ease;
" onmouseover="this.style.background='#1ebd5a';" onmouseout="this.style.background='#25d366';">
    <img src="https://psicologos-teste-production.up.railway.app/images/whatsapp.png" alt="WhatsApp" style="width: 24px; height: 24px;">
    (82) 99112-8022
</a>

<style>
    .feature-card-page {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        text-align: left;

        display: flex;
        flex-direction: column;
        gap: 10px;
        min-height: 220px;
        transition: 0.28s ease;
    }
    .feature-card-page:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 34px rgba(0,0,0,0.06);
    }

    .feature-page-top{
        display:flex;
        align-items:center;
        justify-content: space-between;
        gap: 10px;
    }

    .feature-title-page {
        color: #00aaff;
        margin: 0;
        font-weight: 900;
        font-size: 1.1rem;
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
        white-space: nowrap;
    }

    .feature-desc-page {
        color: #555;
        margin: 0;
        line-height: 1.45;

        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .feature-more-btn-page {
        margin-top: auto;
        align-self: flex-start;

        border: 1px solid rgba(0,170,255,0.35);
        background: rgba(0,170,255,0.08);
        color: #008ecc;
        font-weight: 900;
        border-radius: 999px;
        padding: 8px 14px;
        cursor: pointer;
        transition: 0.22s ease;
    }
    .feature-more-btn-page:hover {
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

    /* ===== Seu tema do modal (mantido) ===== */
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
        display:flex;
        gap:10px;
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
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalEl = document.getElementById('featureModalPage');
        if (!modalEl) return;

        const dataEl = document.getElementById('featureDatasetPage');
        const features = dataEl ? JSON.parse(dataEl.textContent) : [];

        const titleEl = modalEl.querySelector('#featureModalPageLabel');
        const bodyEl  = modalEl.querySelector('#featureModalPageBody');
        const prevBtn = modalEl.querySelector('#featurePrevBtnPage');
        const nextBtn = modalEl.querySelector('#featureNextBtnPage');

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

@endsection
