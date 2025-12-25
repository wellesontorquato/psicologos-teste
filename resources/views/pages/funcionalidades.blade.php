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

{{-- Conteúdo em cards, fora do gradiente --}}
<section style="max-width: 1100px; margin: 50px auto; padding: 0 20px;">
    <div class="features-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 30px;">
        @php
            $features = [
                [
                    'title' => 'Agenda Visual',
                    'desc'  => 'Drag & drop, semana/dia e notificações.',
                    'details_title' => 'Agenda Visual + Google Agenda + Google Meet',
                    'details_html' => '
                        <p>Organize sua agenda com <strong>drag &amp; drop</strong>, visualização por <strong>dia</strong> e <strong>semana</strong>, e fluxo rápido para criar, editar e remarcar sessões.</p>
                        <p>Com a <strong>sincronização com o Google Agenda</strong>, você visualiza as agendas já existentes e mantém tudo alinhado, evitando conflitos de horários.</p>
                        <p>Ao criar um atendimento, o sistema pode <strong>enviar automaticamente um e-mail</strong> ao paciente com o <strong>link da sala do Google Meet</strong>, facilitando teleatendimentos.</p>
                    '
                ],
                [
                    'title' => 'Evoluções',
                    'desc'  => 'Prontuário com histórico completo.',
                    'details_title' => 'Evoluções e Registro Clínico',
                    'details_html' => '
                        <p>Registre evoluções em uma <strong>linha do tempo</strong> por paciente, com histórico de sessões sempre acessível.</p>
                        <p>Mantenha consistência clínica com informações estruturadas: progresso, intervenções, observações e metas terapêuticas.</p>
                        <p>Mais segurança, organização e agilidade no atendimento, sem depender de anotações soltas.</p>
                    '
                ],
                [
                    'title' => 'Financeiro',
                    'desc'  => 'Pagamentos, pendências e controle.',
                    'details_title' => 'Financeiro + Multimoedas',
                    'details_html' => '
                        <p>Controle recebimentos por paciente e por período, com visão clara de <strong>pagos</strong>, <strong>pendentes</strong> e <strong>atrasados</strong>.</p>
                        <p>Automatize notificações de sessões não pagas e mantenha sua rotina financeira mais simples.</p>
                        <p>Com <strong>multimoedas</strong>, registre atendimentos e recebimentos em moedas diferentes — ideal para pacientes de outros países.</p>
                    '
                ],
                [
                    'title' => 'Arquivos',
                    'desc'  => 'Documentos e anexos organizados.',
                    'details_title' => 'Arquivos e Documentos',
                    'details_html' => '
                        <p>Guarde exames, relatórios, contratos, termos e anexos clínicos por paciente.</p>
                        <p>Organização por tipo e acesso rápido, reduzindo risco de perda e retrabalho.</p>
                        <p>Mais agilidade para encontrar documentos durante o atendimento.</p>
                    '
                ],
                [
                    'title' => 'Confirmação por WhatsApp',
                    'desc'  => 'Lembretes e confirmações automáticas.',
                    'details_title' => 'WhatsApp Automático',
                    'details_html' => '
                        <p>Envie confirmações e lembretes automáticos, reduzindo faltas e atrasos.</p>
                        <p>Padronize a comunicação com o paciente, mantendo profissionalismo e consistência.</p>
                        <p>Mais previsibilidade na agenda e menos esforço manual no dia a dia.</p>
                    '
                ],
                [
                    'title' => 'Painel de Indicadores',
                    'desc'  => 'Métricas e visão por período.',
                    'details_title' => 'Indicadores e Estatísticas',
                    'details_html' => '
                        <p>Visualize métricas com gráficos por período, entendendo a evolução da clínica.</p>
                        <p>Acompanhe tendências, volume de sessões e padrões de agenda para tomar decisões melhores.</p>
                        <p>Transforme dados em clareza: enxergue o que está funcionando e onde otimizar.</p>
                    '
                ],
                [
                    'title' => 'Banco de dados robusto',
                    'desc'  => 'Segurança e estabilidade.',
                    'details_title' => 'Segurança e Confiabilidade',
                    'details_html' => '
                        <p>Estrutura pensada para manter seus dados protegidos e sua operação estável.</p>
                        <p>Centralize registros e arquivos em um ambiente confiável, reduzindo riscos de perda.</p>
                        <p>Ideal para migrar de planilhas e anotações para um fluxo mais profissional.</p>
                    '
                ],
                [
                    'title' => 'Exportações em PDF e Excel',
                    'desc'  => 'Relatórios com filtros e exportação.',
                    'details_title' => 'Relatórios e Exportações',
                    'details_html' => '
                        <p>Gere relatórios completos com filtros por data, status e situação de pagamento.</p>
                        <p>Exporte em <strong>PDF</strong> para arquivo e em <strong>Excel</strong> para análises e fechamento financeiro.</p>
                        <p>Ganhe tempo no fechamento mensal e no acompanhamento de resultados.</p>
                    '
                ],
            ];
        @endphp

        @foreach($features as $f)
            <div class="feature-card-page">
                <h4 class="feature-title-page">{{ $f['title'] }}</h4>
                <p class="feature-desc-page">{{ $f['desc'] }}</p>

                <button
                    type="button"
                    class="feature-more-btn-page"
                    data-bs-toggle="modal"
                    data-bs-target="#featureModalPage"
                    data-feature-title="{{ $f['details_title'] ?? $f['title'] }}"
                    data-feature-body-html="{{ e($f['details_html']) }}"
                >
                    Ver detalhes
                </button>
            </div>
        @endforeach
    </div>

    {{-- Modal Único --}}
    <div class="modal fade" id="featureModalPage" tabindex="-1" aria-labelledby="featureModalPageLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header" style="background: linear-gradient(to right, #00aaff, #00c4ff);">
                    <h5 class="modal-title" id="featureModalPageLabel" style="color: #fff; font-weight: 900; margin: 0;">
                        Detalhes
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <div class="modal-body" style="padding: 22px;">
                    <div id="featureModalPageBody" style="color: #333; line-height: 1.7;"></div>
                </div>

                <div class="modal-footer" style="border-top: 1px solid #eee;">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>
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
        min-height: 210px;
        transition: 0.25s ease;
    }

    .feature-card-page:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 24px rgba(0,0,0,0.06);
    }

    .feature-title-page {
        color: #00aaff;
        margin: 0;
        font-weight: 900;
        font-size: 1.1rem;
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
        transition: 0.2s ease;
    }

    .feature-more-btn-page:hover {
        background: rgba(0,170,255,0.14);
        border-color: rgba(0,170,255,0.55);
        transform: translateY(-1px);
    }

    #featureModalPageBody p { margin: 0 0 10px; }
    #featureModalPageBody p:last-child { margin-bottom: 0; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalEl = document.getElementById('featureModalPage');
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

            const titleEl = modalEl.querySelector('#featureModalPageLabel');
            const bodyEl  = modalEl.querySelector('#featureModalPageBody');

            if (titleEl) titleEl.textContent = title;
            if (bodyEl) bodyEl.innerHTML = bodyHtml;
        });
    });
</script>

@endsection
