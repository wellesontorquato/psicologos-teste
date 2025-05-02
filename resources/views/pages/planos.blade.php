@extends('layouts.landing')

@section('title', 'Nossos planos | PsiGestor')

@section('content')
<section class="section-plans" style="background-color: #ffffff; padding: 80px 20px;">
    <div style="max-width: 1200px; margin: auto;">
        <h2 style="text-align: center; font-size: 2rem; margin-bottom: 20px; font-weight: bold;">
            Planos do PsiGestor
        </h2>
        <p style="text-align: center; color: #666; max-width: 700px; margin: 0 auto 40px;">
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
                        'savings' => null,
                    ],
                    [
                        'title' => 'Plano Trimestral',
                        'price' => 'R$ 149,90',
                        'desc' => 'Economize <strong>17%</strong> com pagamento trimestral.',
                        'highlight' => 'success',
                        'savings' => 'Mais popular',
                    ],
                    [
                        'title' => 'Plano Anual',
                        'price' => 'R$ 499,90',
                        'desc' => 'Economize <strong>30%</strong> com pagamento anual.',
                        'highlight' => 'warning',
                        'savings' => null,
                    ],
                ];
            @endphp

            @foreach ($plans as $p)
                <div style="background: #f8f9fa; border-radius: 16px; padding: 30px; text-align: center; box-shadow: 0 3px 8px rgba(0,0,0,0.05); border-top: 4px solid var(--bs-{{ $p['highlight'] }}); position: relative; transition: 0.3s ease;">
                    @if($p['savings'])
                        <span style="
                            position: absolute;
                            top: -15px;
                            right: -15px;
                            background: var(--bs-{{ $p['highlight'] }});
                            color: white;
                            font-size: 0.8rem;
                            padding: 5px 10px;
                            border-radius: 20px;
                            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
                        ">
                            {{ $p['savings'] }}
                        </span>
                    @endif
                    <h4 style="font-size: 1.3rem; margin-bottom: 15px; color: var(--bs-{{ $p['highlight'] }}); font-weight: bold;">
                        {{ $p['title'] }}
                    </h4>
                    <h3 style="font-size: 2rem; margin-bottom: 10px;">{{ $p['price'] }}</h3>
                    <p style="font-size: 0.95rem; color: #555; margin-bottom: 25px;">{!! $p['desc'] !!}</p>
                    <a href="{{ route('register') }}" class="btn btn-{{ $p['highlight'] }} w-100" style="padding: 10px 20px;">
                        Começar agora
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
