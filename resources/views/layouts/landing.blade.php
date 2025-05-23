<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'PsiGestor - Plataforma para Psicólogos, Psicanalistas e Psiquiatras' }}</title>

    {{-- Ícones e AOS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    {{-- Estilos personalizados da landing --}}
    <style>
        html, body {
            overflow-x: hidden;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #fff;
            width: 100%;
        }

        .page-wrapper {
            max-width: 100%;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .page-wrapper > main {
            flex: 1;
            padding-top: 65px;
        }

        .btn-cta {
            background: white;
            color: #00aaff;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: bold;
            border: 2px solid #00aaff;
            text-decoration: none;
            transition: 0.3s;
        }

        .btn-cta:hover {
            background: #f0f8ff;
        }

        .bi-newspaper {
            font-size: 1.4rem;
            color: #333;
        }
    </style>


    @stack('styles')
    <!-- Facebook Meta Pixel -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '1112759344219140');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=1112759344219140&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Facebook Meta Pixel -->
</head>
<body>
    <div class="page-wrapper">
        {{-- NAVBAR --}}
        @include('layouts.nav')

        {{-- CONTEÚDO PRINCIPAL --}}
        <main>
            @yield('content')
        </main>
    
        {{-- FOOTER --}}
        @include('layouts.footer')
    </div>

    {{-- JS --}}
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 1000 });
    </script>

    @stack('scripts')
</body>
</html>
