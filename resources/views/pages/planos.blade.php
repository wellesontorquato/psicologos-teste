@extends('layouts.landing')

@section('title', 'Nossos Planos | PsiGestor')

@section('content')
<section class="section-plans">
    <!-- Cabeçalho com degradê -->
    <div style="background: linear-gradient(to right, #00aaff, #00c4ff); padding: 60px 20px; text-align: center; color: white;">
        <h2 style="font-size: 2.2rem; margin-bottom: 15px; font-weight: bold;">
            Planos do PsiGestor
        </h2>
        <p style="max-width: 700px; margin: 0 auto; font-size: 1rem;">
            Escolha o plano ideal para sua clínica e tenha acesso a todas as funcionalidades que tornam sua rotina mais leve e eficiente.
        </p>
    </div>

    <!-- Cards dos planos -->
    <div style="max-width: 1200px; margin: 50px auto; padding: 0 20px;">
        <div style="
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 30px;
        ">
            @php
                $plans = [
                    [
                        'title' => 'Plano Mensal',
                        'price' => 'R$ 39,90',
                        'desc' => 'Assinatura mensal recorrente com <strong>10 dias grátis</strong>.',
                        'highlight' => 'primary',
                        'color' => '#007bff',
                        'savings' => null,
                    ],
                    [
                        'title' => 'Plano Trimestral',
                        'price' => 'R$ 104,90',
                        'desc' => 'Economize <strong>12,36%(R$ 14,80)</strong> com pagamento trimestral recorrente.',
                        'highlight' => 'success',
                        'color' => '#28a745',
                        'savings' => 'Mais popular',
                    ],
                    [
                        'title' => 'Plano Anual',
                        'price' => 'R$ 374,90',
                        'desc' => 'Economize <strong>21,71%(R$ 103,90)</strong> com pagamento anual recorrente.',
                        'highlight' => 'warning',
                        'color' => '#ffc107',
                        'savings' => null,
                    ],
                ];
            @endphp

            @foreach ($plans as $p)
                <div style="
                    background: #f9f9f9;
                    border-radius: 16px;
                    padding: 30px 20px;
                    text-align: center;
                    box-shadow: 0 3px 10px rgba(0,0,0,0.05);
                    border-top: 4px solid {{ $p['color'] }};
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                    position: relative;
                " onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 3px 10px rgba(0,0,0,0.05)'">
                    
                    @if($p['savings'])
                        <span style="
                            position: absolute;
                            top: -15px;
                            right: -15px;
                            background: {{ $p['color'] }};
                            color: white;
                            font-size: 0.75rem;
                            padding: 5px 12px;
                            border-radius: 20px;
                            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
                        ">
                            {{ $p['savings'] }}
                        </span>
                    @endif

                    <h4 style="font-size: 1.3rem; margin-bottom: 15px; color: {{ $p['color'] }}; font-weight: bold;">
                        {{ $p['title'] }}
                    </h4>
                    <h3 style="font-size: 2rem; margin-bottom: 15px; color: #222;">{{ $p['price'] }}</h3>
                    <p style="font-size: 0.95rem; color: #555; margin-bottom: 30px; line-height: 1.5;">{!! $p['desc'] !!}</p>
                    
                    <a href="{{ route('register') }}" style="
                        display: block;
                        width: 100%;
                        padding: 12px 20px;
                        font-size: 1rem;
                        border-radius: 10px;
                        background: {{ $p['color'] }};
                        color: white;
                        text-decoration: none;
                        transition: background 0.3s ease;
                        box-sizing: border-box;
                        margin-top: 10px;
                    " onmouseover="this.style.background='#0056b3'" onmouseout="this.style.background='{{ $p['color'] }}'">
                        Começar agora
                    </a>
                </div>
            @endforeach
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
@endsection
