@extends('layouts.landing')

@section('content')
<section class="section-plans" style="background-color: #ffffff; padding: 80px 20px;">
    <div style="max-width: 1200px; margin: auto;">
        <h2 style="text-align: center; font-size: 2.2rem; margin-bottom: 20px; font-weight: bold; color: #0077cc;">
            Planos do PsiGestor
        </h2>
        <p style="text-align: center; color: #666; max-width: 700px; margin: 0 auto 50px; font-size: 1rem;">
            Escolha o plano ideal para sua clínica e tenha acesso a todas as funcionalidades que tornam sua rotina mais leve e eficiente.
        </p>

        <div style="
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 30px;
        ">
            @php
                $plans = [
                    [
                        'title' => 'Plano Mensal',
                        'price' => 'R$ 59,90',
                        'desc' => 'Assinatura mensal com <strong>10 dias grátis</strong>.',
                        'highlight' => 'primary',
                        'color' => '#007bff',
                        'savings' => null,
                    ],
                    [
                        'title' => 'Plano Trimestral',
                        'price' => 'R$ 149,90',
                        'desc' => 'Economize <strong>17%</strong> com pagamento trimestral.',
                        'highlight' => 'success',
                        'color' => '#28a745',
                        'savings' => 'Mais popular',
                    ],
                    [
                        'title' => 'Plano Anual',
                        'price' => 'R$ 499,90',
                        'desc' => 'Economize <strong>30%</strong> com pagamento anual.',
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
                        display: inline-block;
                        width: 100%;
                        padding: 12px 20px;
                        font-size: 1rem;
                        border-radius: 8px;
                        background: {{ $p['color'] }};
                        color: white;
                        text-decoration: none;
                        transition: background 0.3s ease;
                    " onmouseover="this.style.background='#0056b3'" onmouseout="this.style.background='{{ $p['color'] }}'">
                        Começar agora
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
