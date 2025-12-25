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
                    'desc'  => 'Drag & drop, notificações e visualização por semana ou dia.',
                    'details_title' => 'Agenda Visual + Google Agenda + Google Meet',
                    'details' => 'Sincronização com o Google Agenda (com acesso às agendas já existentes) e envio automático de e-mails ao paciente com o link da sala do Google Meet ao criar a sessão.'
                ],
                [
                    'title' => 'Evoluções',
                    'desc'  => 'Linha do tempo com registros detalhados de cada sessão.',
                    'details_title' => null,
                    'details' => null
                ],
                [
                    'title' => 'Financeiro',
                    'desc'  => 'Controle de pagamentos e notificações de sessões anteriores não pagas.',
                    'details_title' => 'Financeiro com Multimoedas',
                    'details' => 'Suporte a multimoedas para organizar atendimentos e recebimentos em diferentes moedas.'
                ],
                [
                    'title' => 'Arquivos',
                    'desc'  => 'Documentos organizados por tipo: exames, contratos e relatórios.',
                    'details_title' => null,
                    'details' => null
                ],
                [
                    'title' => 'Confirmação por WhatsApp',
                    'desc'  => 'Envio automático de lembretes e confirmações de sessão.',
                    'details_title' => null,
                    'details' => null
                ],
                [
                    'title' => 'Painel de Indicadores',
                    'desc'  => 'Gráficos com estatísticas por período e evolução da clínica.',
                    'details_title' => null,
                    'details' => null
                ],
                [
                    'title' => 'Banco de dados robusto',
                    'desc'  => 'Seus arquivos e dados sempre bem guardados e protegidos.',
                    'details_title' => null,
                    'details' => null
                ],
                [
                    'title' => 'Exportações em PDF e Excel',
                    'desc'  => 'Relatórios completos com filtros por data, status e pagamento.',
                    'details_title' => null,
                    'details' => null
                ],
            ];
        @endphp

        @foreach($features as $f)
            <div class="feature-card-page">
                <h4 class="feature-title-page">{{ $f['title'] }}</h4>

                <p class="feature-desc-page">{{ $f['desc'] }}</p>

                @if(!empty($f['details']))
                    <button
                        type="button"
                        class="feature-more-btn-page"
                        data-bs-toggle="modal"
                        data-bs-target="#featureModalPage"
                        data-feature-title="{{ $f['details_title'] ?? $f['title'] }}"
                        data-feature-body="{{ $f['details'] }}"
                    >
                        Ver detalhes
                    </button>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Modal Único --}}
    <div class="modal fade" id="featureModalPage" tabindex="-1" aria-labelledby="featureModalPageLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header" style="background: linear-gradient(to right, #00aaff, #00c4ff);">
                    <h5 class="modal-title" id="featureModalPageLabel" style="color: #fff; font-weight: 800; margin: 0;">
                        Detalhes
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <div class="modal-body" style="padding: 22px;">
                    <p id="featureModalPageBody" style="margin: 0; color: #333; line-height: 1.6;"></p>
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
        min-height: 190px;
        transition: 0.25s ease;
    }

    .feature-card-page:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 24px rgba(0,0,0,0.06);
    }

    .feature-title-page {
        color: #00aaff;
        margin: 0;
        font-weight: 800;
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
        font-weight: 800;
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
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalEl = document.getElementById('featureModalPage');
        if (!modalEl) return;

        modalEl.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const title = button?.getAttribute('data-feature-title') || 'Detalhes';
            const body  = button?.getAttribute('data-feature-body') || '';

            const titleEl = modalEl.querySelector('#featureModalPageLabel');
            const bodyEl  = modalEl.querySelector('#featureModalPageBody');

            if (titleEl) titleEl.textContent = title;
            if (bodyEl) bodyEl.textContent = body;
        });
    });
</script>

@endsection
