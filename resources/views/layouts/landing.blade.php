<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="PsiGestor — plataforma para psicólogos, psicanalistas e psiquiatras. Agenda, prontuário, teleconsulta e muito mais.">
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
    @else
        <meta property="og:title" content="PsiGestor - Plataforma para Profissionais da Saúde Mental">
        <meta property="og:description" content="Facilite sua prática clínica com agendamento, prontuário e muito mais.">
        <meta property="og:image" content="{{ versao('images/default-og.png') }}">
        <meta property="og:url" content="{{ url()->current() }}">
    @endif

    {{-- Preconnect para reduzir latência --}}
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://unpkg.com" crossorigin>
    <link rel="preconnect" href="https://connect.facebook.net" crossorigin>

    {{-- CSS com carregamento otimizado --}}
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        media="print"
        onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"></noscript>

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        media="print"
        onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"></noscript>

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        media="print"
        onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"></noscript>

    <link rel="stylesheet"
        href="https://unpkg.com/aos@next/dist/aos.css"
        media="print"
        onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css"></noscript>

    {{-- Favicon e PWA --}}
    <link rel="icon" type="image/png" href="{{ versao('favicon.png') }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#00aaff">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="apple-touch-icon" href="/icon-192.png">

    {{-- Fontes do Google --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-PTGJTGFPC5">
    </script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-PTGJTGFPC5');
    </script>

    {{-- Estilos Globais da Landing Page --}}
    <style>
        html, body {
            overflow-x: hidden;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', 'Segoe UI', sans-serif;
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
            /* O padding-top agora é ajustado pela altura do .top-nav, que é 80px no desktop e 70px no mobile */
            /* O CSS dentro de nav.blade.php cuida da altura, este é um fallback */
            padding-top: 70px;
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
        
        /* Ícones e Fontes Externas */
        .bi-newspaper {
            font-size: 1.4rem;
            color: #333;
        }
        
        @font-face {
            font-family: 'Font Awesome 6 Free';
            font-style: normal;
            font-weight: 400;
            font-display: swap;
            src: url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/webfonts/fa-regular-400.woff2') format('woff2');
        }
    </style>

    @stack('styles')

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
    @include('layouts.partials.ga')
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

    {{-- JAVASCRIPTS --}}
    <script src="https://unpkg.com/aos@next/dist/aos.js" async></script>

    <!-- ✅ Bootstrap JS (necessário para Modal, Collapse, Dropdown etc.) -->
    <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    defer
    ></script>

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

    {{-- Funções de Cookies (Globais) --}}
    <script>
        (function () {
        const STORAGE_KEY = 'cookieConsent';
        const BANNER_ID = 'cookie-banner';
        const MODAL_ID  = 'cookie-modal';

        // Atualiza o consentimento nos tags (GA4 + opcional FB Pixel)
        function updateConsent({ analytics, marketing }) {
            if (window.gtag) {
            gtag('consent', 'update', {
                analytics_storage:   analytics ? 'granted' : 'denied',
                ad_storage:          marketing ? 'granted' : 'denied',
                ad_user_data:        marketing ? 'granted' : 'denied',
                ad_personalization:  marketing ? 'granted' : 'denied',
            });
            }
            // Se usar Facebook Pixel:
            if (window.fbq) {
            fbq('consent', marketing ? 'grant' : 'revoke');
            }
        }

        function show(id){ const el = document.getElementById(id); if (el) el.style.display = 'block'; }
        function hide(id){ const el = document.getElementById(id); if (el) el.style.display = 'none'; }

        // BOTÕES DO BANNER
        window.aceitarCookies = function () {
            localStorage.setItem(STORAGE_KEY, 'all');
            updateConsent({ analytics: true, marketing: true });
            hide(BANNER_ID); hide(MODAL_ID);
        };

        window.rejeitarCookies = function () {
            localStorage.setItem(STORAGE_KEY, 'none');
            updateConsent({ analytics: false, marketing: false });
            hide(BANNER_ID); hide(MODAL_ID);
        };

        // MODAL DE PREFERÊNCIAS
        window.abrirConfiguracoesCookies = function () { show(MODAL_ID); };
        window.fecharModalCookies = function () { hide(MODAL_ID); };

        window.salvarPreferenciasCookies = function () {
            const analytics = !!document.getElementById('cookie-analytics')?.checked;
            const marketing = !!document.getElementById('cookie-marketing')?.checked;
            localStorage.setItem(STORAGE_KEY, JSON.stringify({ analytics, marketing }));
            updateConsent({ analytics, marketing });
            hide(BANNER_ID); hide(MODAL_ID);
        };

        // Ao carregar: aplica o que foi salvo (ou mostra o banner se não houver decisão)
        document.addEventListener('DOMContentLoaded', function () {
            const saved = localStorage.getItem(STORAGE_KEY);
            if (!saved) { show(BANNER_ID); return; }

            let analytics = false, marketing = false;
            if (saved === 'all') {
            analytics = marketing = true;
            } else if (saved !== 'none') {
            try {
                const obj = JSON.parse(saved);
                analytics = !!obj.analytics;
                marketing = !!obj.marketing;
            } catch (_) {}
            }
            updateConsent({ analytics, marketing });
        });
        })();
    </script>
    
    {{-- Scripts que rodam após o HTML estar pronto (rápidos) --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Controla o menu de navegação mobile
            const toggleButton = document.getElementById('menu-toggle');
            const navLinks = document.getElementById('main-nav-links');

            if (toggleButton && navLinks) {
                toggleButton.addEventListener('click', () => {
                    toggleButton.classList.toggle('is-active');
                    navLinks.classList.toggle('is-open');
                });
            }

            // Verifica e exibe o banner de cookies inicialmente
            const consent = localStorage.getItem('cookieConsent');
            if (!consent) {
                const banner = document.getElementById('cookie-banner');
                if (banner) {
                   banner.style.display = 'block';
                }
            }
        });
    </script>

    {{-- Scripts que rodam após TUDO (imagens, outros scripts) estar carregado --}}
    <script>
        window.addEventListener('load', () => {
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 800,
                    once: true
                });
            }
            
            const consent = localStorage.getItem('cookieConsent');
            if (!consent) {
                const banner = document.getElementById('cookie-banner');
                if (banner) {
                    banner.style.opacity = '1';
                    banner.style.transform = 'none';
                }
            }
        });
    </script>
    
    {{-- Service Worker --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(() => console.log("✅ Service Worker registrado!"))
                    .catch(err => console.log("❌ Erro no Service Worker:", err));
            });
        }
    </script>
</body>
</html>