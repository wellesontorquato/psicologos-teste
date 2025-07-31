<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PsiGestor - Plataforma para Profissionais da Saúde Mental')</title>

    {{-- Open Graph (Facebook, WhatsApp, LinkedIn) --}}
    @if(isset($news) && $news instanceof \App\Models\News)
        <meta property="og:title" content="{{ $news->title }}">
        <meta property="og:description" content="{{ $news->subtitle ?? Str::limit(strip_tags($news->content), 150) }}">
        <meta property="og:image" content="{{ $news->image_url }}">
        <meta property="og:url" content="{{ route('blog.show', $news->slug) }}">
        <meta property="og:type" content="article">
        <meta property="og:locale" content="pt_BR">

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $news->title }}">
        <meta name="twitter:description" content="{{ $news->subtitle ?? Str::limit(strip_tags($news->content), 150) }}">
        <meta name="twitter:image" content="{{ $news->image_url }}">
    @endif

    {{-- Ícones e AOS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

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

    {{-- Banner de Cookies --}}
    <div id="cookie-banner" style="display: none; position: fixed; bottom: 20px; left: 20px; right: 20px; max-width: 600px; margin: auto; background: white; border: 1px solid #ccc; border-radius: 10px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 9999;">
        <p style="margin-bottom: 10px; font-size: 0.95rem; color: #333;">
            🍪 Este site utiliza cookies para melhorar a sua experiência de navegação, analisar o tráfego e personalizar conteúdos. Ao continuar, você concorda com nossa 
            <a href="/politica-de-privacidade" target="_blank" style="color: #00aaff; text-decoration: underline;">Política de Privacidade</a>.
        </p>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button onclick="aceitarCookies()" style="background-color: #00aaff; color: white; border: none; padding: 10px 16px; border-radius: 5px; cursor: pointer;">Aceitar Todos</button>
            <button onclick="rejeitarCookies()" style="background-color: #ddd; color: #111; border: none; padding: 10px 16px; border-radius: 5px; cursor: pointer;">Rejeitar</button>
            <button onclick="abrirConfiguracoesCookies()" style="background-color: transparent; color: #555; border: none; padding: 10px 16px; cursor: pointer; text-decoration: underline;">Cookies Personalizados</button>
        </div>
    </div>

    {{-- Modal de Personalização de Cookies --}}
    <div id="cookie-modal" style="display: none; position: fixed; z-index: 99999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6);">
        <div style="background: #fff; max-width: 500px; margin: 80px auto; padding: 30px; border-radius: 8px; position: relative;">
            <h4 style="margin-top: 0">Preferências de Cookies</h4>
            <p>Escolha os tipos de cookies que você deseja aceitar:</p>

            <div style="margin-top: 20px">
                <label><input type="checkbox" disabled checked> Cookies Necessários <small>(sempre ativos)</small></label>
            </div>
            <div style="margin-top: 10px">
                <label><input type="checkbox" id="cookie-analytics"> Cookies de Análise</label>
            </div>
            <div style="margin-top: 10px">
                <label><input type="checkbox" id="cookie-marketing"> Cookies de Marketing</label>
            </div>

            <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px">
                <button onclick="salvarPreferenciasCookies()" style="background-color: #00aaff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">Salvar</button>
                <button onclick="fecharModalCookies()" style="background: transparent; border: none; color: #666; cursor: pointer;">Cancelar</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const consent = localStorage.getItem('cookieConsent');
            if (!consent) {
                document.getElementById('cookie-banner').style.display = 'block';
            }
        });

        function aceitarCookies() {
            localStorage.setItem('cookieConsent', 'all');
            document.getElementById('cookie-banner').style.display = 'none';
        }

        function rejeitarCookies() {
            localStorage.setItem('cookieConsent', 'none');
            document.getElementById('cookie-banner').style.display = 'none';
        }

        function abrirConfiguracoesCookies() {
            document.getElementById('cookie-modal').style.display = 'block';
        }

        function fecharModalCookies() {
            document.getElementById('cookie-modal').style.display = 'none';
        }

        function salvarPreferenciasCookies() {
            const analytics = document.getElementById('cookie-analytics').checked;
            const marketing = document.getElementById('cookie-marketing').checked;

            const preferencias = {
                analytics,
                marketing
            };

            localStorage.setItem('cookieConsent', JSON.stringify(preferencias));
            document.getElementById('cookie-banner').style.display = 'none';
            fecharModalCookies();
        }
    </script>
</body>
</html>
