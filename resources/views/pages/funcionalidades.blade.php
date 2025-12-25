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
                    'desc'  => 'Drag & drop, visualização por semana ou dia, sincronização com o Google Agenda (com acesso às agendas já existentes), e envio automático de e-mails ao paciente com o link da sala do Google Meet ao criar a sessão.'
                ],
                ['icon' => 'file-lines', 'title' => 'Evoluções', 'desc' => 'Linha do tempo com registros detalhados de cada sessão.'],
                [
                    'icon' => 'coins',
                    'title' => 'Financeiro',
                    'desc'  => 'Controle de pagamentos, notificações de sessões anteriores não pagas e suporte a multimoedas para organizar atendimentos e recebimentos em diferentes moedas.'
                ],
                ['icon' => 'cloud-upload-alt', 'title' => 'Arquivos', 'desc' => 'Documentos organizados por tipo: exames, contratos, relatórios.'],
                ['icon' => 'whatsapp', 'title' => 'Confirmação por WhatsApp', 'desc' => 'Envio automático de lembretes e confirmações de sessão.'],
                ['icon' => 'chart-line', 'title' => 'Painel de Indicadores', 'desc' => 'Gráficos com estatísticas por período e evolução da clínica.'],
                ['icon' => 'user-shield', 'title' => 'Banco de dados robusto', 'desc' => 'Não se preocupe, conosco seus arquivos estarão sempre bem guardados.'],
                ['icon' => 'file-export', 'title' => 'Exportações em PDF e Excel', 'desc' => 'Relatórios completos com filtros por data, status e pagamento.'],
            ];
        @endphp

        @foreach($features as $f)
            <div style="background: #f8f9fa; border-radius: 12px; padding: 25px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03); text-align: left;">
                <h4 style="color: #00aaff; margin-bottom: 10px;">{{ $f['title'] }}</h4>
                <p style="color: #555;">{{ $f['desc'] }}</p>
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

@endsection
