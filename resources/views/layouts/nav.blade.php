{{-- resources/views/layouts/nav.blade.php --}}

@php
    $isModernHome = request()->routeIs('home');
@endphp

<style>
    /* =========================================================
       BASE
    ========================================================= */

    .top-nav {
        position: fixed;
        top: 0;
        z-index: 999;
        width: 100%;
        box-sizing: border-box;
        padding: 10px 20px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        background: #ffffff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        transition:
            background 0.3s ease,
            box-shadow 0.3s ease,
            border-color 0.3s ease,
            backdrop-filter 0.3s ease;
    }

    .nav-container {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .top-nav .logo {
        position: relative;
        z-index: 4;
        display: inline-flex;
        align-items: center;
        flex: 0 0 auto;
        text-decoration: none;
    }

    .top-nav .logo img {
        display: block;
        width: auto;
        max-height: 50px;
        transition: transform 0.22s ease;
    }

    .top-nav .logo:hover img {
        transform: translateY(-1px);
    }

    /* =========================================================
       NAVEGAÇÃO TRADICIONAL - OUTRAS PÁGINAS
    ========================================================= */

    .top-nav:not(.top-nav--home) #main-nav-links {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .top-nav:not(.top-nav--home) #main-nav-links > a {
        padding: 10px 20px;
        border-radius: 25px;
        color: #333;
        font-weight: 500;
        text-decoration: none;
        white-space: nowrap;
        transition:
            color 0.3s,
            background-color 0.3s,
            border-color 0.3s;
    }

    .top-nav:not(.top-nav--home) #main-nav-links > a:hover {
        color: #00aaff;
        background: #f0f8ff;
    }

    .top-nav:not(.top-nav--home)
        #main-nav-links > a.btn-cta {
        color: #00aaff;
        border: 2px solid #00aaff;
        background: transparent;
        font-weight: 700;
    }

    .top-nav:not(.top-nav--home)
        #main-nav-links > a.btn-cta:hover {
        color: #fff;
        background: #00aaff;
    }

    /* =========================================================
       HOMEPAGE V2
    ========================================================= */

    .top-nav--home {
        min-height: 70px;
        display: flex;
        align-items: center;
        padding: 9px 24px;
        border-bottom-color: transparent;
        background: rgba(255, 255, 255, 0.82);
        box-shadow: none;
        -webkit-backdrop-filter: blur(16px);
        backdrop-filter: blur(16px);
    }

    .top-nav--home.is-scrolled {
        border-bottom-color: rgba(31, 64, 96, 0.08);
        background: rgba(255, 255, 255, 0.93);
        box-shadow:
            0 10px 30px rgba(27, 65, 101, 0.07);
    }

    .top-nav--home .nav-container {
        max-width: 1340px;
        min-height: 52px;
        gap: 30px;
    }

    .top-nav--home .logo img {
        max-height: 44px;
    }

    .top-nav--home #main-nav-links {
        min-width: 0;
        flex: 1;
        display: grid;
        grid-template-columns:
            1fr
            auto;
        align-items: center;
        gap: 28px;
    }

    .pg-nav-center {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }

    .pg-nav-center > a {
        position: relative;
        display: inline-flex;
        align-items: center;
        min-height: 38px;
        padding: 0 13px;
        border-radius: 10px;
        color: #40556c;
        font-size: 0.79rem;
        font-weight: 500;
        text-decoration: none;
        white-space: nowrap;
        transition:
            color 0.18s ease,
            background 0.18s ease;
    }

    .pg-nav-center > a:hover {
        color: #172d46;
        background: rgba(238, 247, 253, 0.8);
    }

    .pg-nav-center > a[aria-current="page"] {
        color: #0878ed;
        font-weight: 600;
    }

    .pg-nav-center > a[aria-current="page"]::after {
        position: absolute;
        content: "";
        left: 13px;
        right: 13px;
        bottom: 2px;
        height: 2px;
        border-radius: 999px;
        background:
            linear-gradient(
                90deg,
                #0aaaf5,
                #0878ed
            );
    }

    .pg-nav-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
    }

    .pg-nav-login {
        min-height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 13px;
        border-radius: 11px;
        color: #40556c;
        font-size: 0.79rem;
        font-weight: 600;
        text-decoration: none;
        transition:
            color 0.18s ease,
            background 0.18s ease;
    }

    .pg-nav-login:hover {
        color: #172d46;
        background: #f4f8fb;
    }

    .pg-nav-primary {
        min-height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 16px;
        border-radius: 12px;
        color: #fff;
        background:
            linear-gradient(
                135deg,
                #08abf6,
                #0878ed
            );
        box-shadow:
            0 8px 20px
            rgba(0, 132, 225, 0.18);
        font-size: 0.77rem;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
        transition:
            transform 0.18s ease,
            box-shadow 0.18s ease;
    }

    .pg-nav-primary:hover {
        color: #fff;
        transform: translateY(-1px);
        box-shadow:
            0 11px 25px
            rgba(0, 132, 225, 0.24);
    }

    .pg-nav-primary i {
        font-size: 0.8rem;
        transition: transform 0.18s ease;
    }

    .pg-nav-primary:hover i {
        transform: translateX(2px);
    }

    /* =========================================================
       HAMBURGER
    ========================================================= */

    .hamburger {
        position: relative;
        z-index: 1001;
        display: none;
        flex-direction: column;
        gap: 5px;
        padding: 7px;
        border: 0;
        background: none;
        cursor: pointer;
    }

    .hamburger span {
        width: 24px;
        height: 2px;
        border-radius: 999px;
        background: #31475f;
        transition:
            transform 0.25s ease,
            opacity 0.25s ease;
    }

    .hamburger.is-active span:nth-child(1) {
        transform:
            rotate(45deg)
            translate(5px, 5px);
    }

    .hamburger.is-active span:nth-child(2) {
        opacity: 0;
    }

    .hamburger.is-active span:nth-child(3) {
        transform:
            rotate(-45deg)
            translate(5px, -5px);
    }

    /* =========================================================
       MOBILE
    ========================================================= */

    @media (max-width: 900px) {
        .hamburger {
            display: flex;
        }

        .top-nav .logo img {
            max-height: 40px;
        }

        .top-nav #main-nav-links,
        .top-nav--home #main-nav-links {
            position: absolute;
            top: calc(100% + 8px);
            left: 14px;
            right: 14px;
            width: auto;
            display: none;
            padding: 10px;
            border:
                1px solid
                rgba(35, 68, 100, 0.09);
            border-radius: 17px;
            background:
                rgba(255, 255, 255, 0.97);
            box-shadow:
                0 22px 50px
                rgba(30, 69, 105, 0.14);
            -webkit-backdrop-filter: blur(18px);
            backdrop-filter: blur(18px);
        }

        .top-nav #main-nav-links.is-open,
        .top-nav--home #main-nav-links.is-open {
            display: block;
        }

        .top-nav:not(.top-nav--home)
            #main-nav-links > a {
            width: 100%;
            display: flex;
            justify-content: center;
            margin: 2px 0;
            border-bottom: 0;
        }

        .pg-nav-center {
            display: grid;
            gap: 2px;
        }

        .pg-nav-center > a {
            width: 100%;
            min-height: 44px;
            justify-content: flex-start;
            padding: 0 13px;
            border-radius: 10px;
        }

        .pg-nav-center
            > a[aria-current="page"]::after {
            display: none;
        }

        .pg-nav-actions {
            display: grid;
            grid-template-columns:
                1fr
                1fr;
            gap: 8px;
            margin-top: 8px;
            padding-top: 9px;
            border-top:
                1px solid
                rgba(35, 68, 100, 0.07);
        }

        .pg-nav-login,
        .pg-nav-primary {
            width: 100%;
        }

        .pg-nav-login {
            background: #f4f8fb;
        }
    }

    @media (max-width: 520px) {
        .top-nav,
        .top-nav--home {
            min-height: 64px;
            padding:
                8px
                14px;
        }

        .top-nav--home .nav-container {
            min-height: 48px;
        }

        .top-nav #main-nav-links,
        .top-nav--home #main-nav-links {
            left: 10px;
            right: 10px;
        }

        .pg-nav-actions {
            grid-template-columns: 1fr;
        }
    }

    /* =========================================================
       TEMA ANO NOVO - PRESERVADO PARA OUTRAS PÁGINAS
    ========================================================= */

    body:has(.hero-newyear)
        .top-nav:not(.top-nav--home),
    body.theme-newyear-fallback
        .top-nav:not(.top-nav--home) {
        background:
            rgba(6, 26, 58, 0.7);
        border-bottom:
            1px solid
            rgba(255, 255, 255, 0.16);
        box-shadow:
            0 12px 30px
            rgba(0, 0, 0, 0.18);
        -webkit-backdrop-filter:
            blur(12px);
        backdrop-filter:
            blur(12px);
    }

    body:has(.hero-newyear)
        .top-nav:not(.top-nav--home)
        #main-nav-links > a,
    body.theme-newyear-fallback
        .top-nav:not(.top-nav--home)
        #main-nav-links > a {
        color: rgba(255, 255, 255, 0.92);
    }

    body:has(.hero-newyear)
        .top-nav:not(.top-nav--home)
        #main-nav-links > a:hover,
    body.theme-newyear-fallback
        .top-nav:not(.top-nav--home)
        #main-nav-links > a:hover {
        color: #fff;
        background:
            rgba(255, 255, 255, 0.1);
    }

    body:has(.hero-newyear)
        .top-nav:not(.top-nav--home)
        #main-nav-links > a.btn-cta,
    body.theme-newyear-fallback
        .top-nav:not(.top-nav--home)
        #main-nav-links > a.btn-cta {
        color:
            rgba(255, 255, 255, 0.95);
        border-color:
            rgba(255, 215, 0, 0.85);
        background:
            rgba(255, 215, 0, 0.1);
    }

    body:has(.hero-newyear)
        .top-nav:not(.top-nav--home)
        #main-nav-links > a.btn-cta:hover,
    body.theme-newyear-fallback
        .top-nav:not(.top-nav--home)
        #main-nav-links > a.btn-cta:hover {
        color: #061a3a;
        border-color:
            rgba(255, 215, 0, 0.95);
        background:
            rgba(255, 215, 0, 0.95);
    }

    body:has(.hero-newyear)
        .top-nav:not(.top-nav--home)
        .hamburger span,
    body.theme-newyear-fallback
        .top-nav:not(.top-nav--home)
        .hamburger span {
        background:
            rgba(255, 255, 255, 0.92);
    }

    @media (max-width: 900px) {
        body:has(.hero-newyear)
            .top-nav:not(.top-nav--home)
            #main-nav-links,
        body.theme-newyear-fallback
            .top-nav:not(.top-nav--home)
            #main-nav-links {
            background:
                rgba(6, 26, 58, 0.96);
            border-color:
                rgba(255, 255, 255, 0.12);
        }
    }
</style>

<header
    class="top-nav{{ $isModernHome ? ' top-nav--home' : '' }}"
>
    <div class="nav-container">

        {{-- LOGO --}}
        <a
            href="{{ route('home') }}"
            class="logo"
            aria-label="PsiGestor - Página inicial"
        >
            <img
                src="{{ versao('images/logo-psigestor.webp') }}"
                alt="PsiGestor"
            >
        </a>

        {{-- HOMEPAGE MODERNA --}}
        @if($isModernHome)

            <nav
                id="main-nav-links"
                aria-label="Navegação principal"
            >
                <div class="pg-nav-center">
                    <a
                        href="#inicio"
                        aria-current="page"
                    >
                        Início
                    </a>

                    <a href="{{ route('funcionalidades') }}">
                        Funcionalidades
                    </a>

                    <a href="{{ route('planos') }}">
                        Planos
                    </a>

                    <a href="{{ route('blog.index') }}">
                        Blog
                    </a>

                    <a href="{{ route('contato') }}">
                        Contato
                    </a>
                </div>

                <div class="pg-nav-actions">
                    <a
                        href="{{ route('login') }}"
                        class="pg-nav-login"
                    >
                        Entrar
                    </a>

                    <a
                        href="{{ route('register') }}"
                        class="pg-nav-primary"
                    >
                        Começar grátis

                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </nav>

        @else

            {{-- NAVEGAÇÃO ORIGINAL DAS OUTRAS PÁGINAS --}}
            <nav
                id="main-nav-links"
                aria-label="Navegação principal"
            >
                <a href="{{ route('home') }}">
                    Início
                </a>

                <a href="{{ route('funcionalidades') }}">
                    Funcionalidades
                </a>

                <a href="{{ route('planos') }}">
                    Nossos Planos
                </a>

                <a href="{{ route('contato') }}">
                    Contato
                </a>

                <a href="{{ route('blog.index') }}">
                    Blog
                </a>

                <a
                    href="{{ route('login') }}"
                    class="btn-cta"
                >
                    Login
                </a>
            </nav>

        @endif

        {{-- HAMBURGER --}}
        <button
            id="menu-toggle"
            class="hamburger"
            type="button"
            aria-label="Abrir ou fechar menu"
            aria-controls="main-nav-links"
            aria-expanded="false"
        >
            <span></span>
            <span></span>
            <span></span>
        </button>

    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function () {
    try {
        if (document.querySelector('.hero-newyear')) {
            document.body.classList.add(
                'theme-newyear-fallback'
            );
        }
    } catch (e) {
        // Fallback silencioso.
    }

    const homeHeader =
        document.querySelector(
            '.top-nav.top-nav--home'
        );

    if (homeHeader) {
        const updateHomeHeader = function () {
            homeHeader.classList.toggle(
                'is-scrolled',
                window.scrollY > 12
            );
        };

        updateHomeHeader();

        window.addEventListener(
            'scroll',
            updateHomeHeader,
            {
                passive: true
            }
        );
    }

    const menuButton =
        document.getElementById(
            'menu-toggle'
        );

    const navLinks =
        document.getElementById(
            'main-nav-links'
        );

    if (
        menuButton &&
        navLinks
    ) {
        const syncExpandedState =
            function () {
                menuButton.setAttribute(
                    'aria-expanded',
                    navLinks.classList.contains(
                        'is-open'
                    )
                        ? 'true'
                        : 'false'
                );
            };

        const observer =
            new MutationObserver(
                syncExpandedState
            );

        observer.observe(
            navLinks,
            {
                attributes: true,
                attributeFilter: [
                    'class'
                ]
            }
        );

        syncExpandedState();
    }
});
</script>