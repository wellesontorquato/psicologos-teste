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
                    'icon' => 'calendar-check',
                    'title' => 'Agenda Visual',
                    'desc'  => 'Drag & drop, notificações e visualização por semana ou dia.',
                    'details' => 'Sincronização com o Google Agenda (com acesso às agendas já existentes) e envio automático de e-mails ao paciente com o link da sala do Google Meet ao criar a sessão.'
                ],
                [
                    'icon' => 'file-lines',
                    'title' => 'Evoluções',
                    'desc'  => 'Linha do tempo com registros detalhados de cada sessão.',
                    'details' => null
                ],
                [
                    'icon' => 'coins',
                    'title' => 'Financeiro',
                    'desc'  => 'Controle de pagamentos e notificações de sessões anteriores não pagas.',
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
                    'desc'  => 'Gráficos com estatísticas por período e evolução da clínica.',
                    'details' => null
                ],
                [
                    'icon' => 'user-shield',
                    'title' => 'Banco de dados robusto',
                    'desc'  => 'Seus arquivos e dados sempre bem guardados e protegidos.',
                    'details' => null
                ],
                [
                    'icon' => 'file-export',
                    'title' => 'Exportações em PDF e Excel',
                    'desc'  => 'Relatórios completos com filtros por data, status e pagamento.',
                    'details' => null
                ],
            ];
        @endphp

        @foreach($features as $f)
            <div class="feature-card">
                <h4 class="feature-title">{{ $f['title'] }}</h4>

                <p class="feature-desc">{{ $f['desc'] }}</p>

                @if(!empty($f['details']))
                    <details class="feature-details">
                        <summary>Ver detalhes</summary>
                        <p>{{ $f['details'] }}</p>
                    </details>
                @endif
            </div>
        @endforeach
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

{{-- Estilos locais (mantém cards uniformes sem quebrar layout) --}}
<style>
    .feature-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        text-align: left;

        display: flex;
        flex-direction: column;
        gap: 10px;
        min-height: 190px; /* mantém padrão visual */
    }

    .feature-title {
        color: #00aaff;
        margin: 0;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .feature-desc {
        color: #555;
        margin: 0;
        line-height: 1.45;

        display: -webkit-box;
        -webkit-line-clamp: 3; /* evita “estourar” o card */
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .feature-details {
        margin-top: auto; /* joga o "Ver detalhes" pra base do card */
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
        font-size: 0.9rem;
        transform: translateY(-1px);
    }

    .feature-details[open] summary::after {
        content: "▴";
    }

    .feature-details p {
        margin: 10px 0 0;
        color: #555;
        line-height: 1.5;
    }
</style>

@endsection
