{{-- resources/views/layouts/nav.blade.php --}}

<style>
    /* =========================
       BASE DO HEADER
    ========================= */
    .top-nav {
        background-color: #ffffff;
        position: fixed;
        width: 100%;
        top: 0;
        z-index: 999;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        padding: 10px 20px;
        box-sizing: border-box;
        transition: background .35s ease, box-shadow .35s ease, border-color .35s ease;
        border-bottom: 1px solid rgba(0,0,0,0.06);
    }

    .nav-container {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .top-nav .logo img {
        max-height: 50px;
        width: auto;
        display: block;
        transition: transform .25s ease;
    }

    .top-nav .logo:hover img {
        transform: translateY(-1px);
    }

    /* =========================
       LINKS (DESKTOP)
    ========================= */
    #main-nav-links {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    #main-nav-links a {
        color: #333;
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: 500;
        text-decoration: none;
        transition: color 0.3s, background-color 0.3s, border-color .3s;
        white-space: nowrap;
    }

    #main-nav-links a:hover {
        color: #00aaff;
        background-color: #f0f8ff;
    }

    #main-nav-links a.btn-cta {
        color: #00aaff;
        border: 2px solid #00aaff;
        font-weight: bold;
        background: transparent;
    }

    #main-nav-links a.btn-cta:hover {
        background-color: #00aaff;
        color: white;
    }

    /* =========================
       HAMBÚRGUER
    ========================= */
    .hamburger {
        display: none;
        background: none;
        border: none;
        cursor: pointer;
        padding: 5px;
        flex-direction: column;
        gap: 5px;
        z-index: 1000;
    }

    .hamburger span {
        width: 25px;
        height: 3px;
        background: #333;
        border-radius: 2px;
        transition: all 0.3s ease-in-out;
    }

    .hamburger.is-active span:nth-child(1) {
        transform: rotate(45deg) translate(5px, 5px);
    }
    .hamburger.is-active span:nth-child(2) {
        opacity: 0;
    }
    .hamburger.is-active span:nth-child(3) {
        transform: rotate(-45deg) translate(5px, -5px);
    }

    /* =========================
       MOBILE
    ========================= */
    @media (max-width: 768px) {
        .top-nav .logo img { max-height: 40px; }
        .hamburger { display: flex; }

        #main-nav-links {
            display: none;
            position: absolute;
            top: 70px;
            left: 0;
            right: 0;
            width: 100%;
            background-color: #ffffff;
            flex-direction: column;
            padding: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            gap: 0;
        }

        #main-nav-links.is-open { display: flex; }

        #main-nav-links a {
            width: 100%;
            text-align: center;
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        #main-nav-links a:last-child { border-bottom: none; }

        #main-nav-links a.btn-cta {
            margin-top: 10px;
            border: 2px solid #00aaff;
            background: transparent;
            color: #00aaff;
        }
        #main-nav-links a.btn-cta:hover {
            background-color: #00aaff;
            color: white;
        }
    }

    /* =========================================================
       THEME ANO NOVO (aplica só quando existir .hero-newyear na página)
       - Primeira opção: :has() (Chrome/Edge/Safari modernos)
       - Fallback: body.theme-newyear-fallback (JS abaixo adiciona)
    ========================================================= */

    /* :has() */
    body:has(.hero-newyear) .top-nav,
    body.theme-newyear-fallback .top-nav {
        background: rgba(6, 26, 58, 0.70);
        border-bottom: 1px solid rgba(255,255,255,0.16);
        box-shadow: 0 12px 30px rgba(0,0,0,0.18);
        -webkit-backdrop-filter: blur(12px);
        backdrop-filter: blur(12px);
    }

    body:has(.hero-newyear) #main-nav-links a,
    body.theme-newyear-fallback #main-nav-links a {
        color: rgba(255,255,255,0.92);
    }

    body:has(.hero-newyear) #main-nav-links a:hover,
    body.theme-newyear-fallback #main-nav-links a:hover {
        color: rgba(255,255,255,1);
        background: rgba(255,255,255,0.10);
    }

    body:has(.hero-newyear) #main-nav-links a.btn-cta,
    body.theme-newyear-fallback #main-nav-links a.btn-cta {
        color: rgba(255,255,255,0.95);
        border-color: rgba(255,215,0,0.85);
        background: rgba(255,215,0,0.10);
    }

    body:has(.hero-newyear) #main-nav-links a.btn-cta:hover,
    body.theme-newyear-fallback #main-nav-links a.btn-cta:hover {
        background: rgba(255,215,0,0.95);
        color: #061a3a;
        border-color: rgba(255,215,0,0.95);
    }

    body:has(.hero-newyear) .hamburger span,
    body.theme-newyear-fallback .hamburger span {
        background: rgba(255,255,255,0.92);
    }

    /* Menu mobile quando aberto */
    @media (max-width: 768px) {
        body:has(.hero-newyear) #main-nav-links,
        body.theme-newyear-fallback #main-nav-links {
            background: rgba(6, 26, 58, 0.92);
            border-top: 1px solid rgba(255,255,255,0.12);
        }

        body:has(.hero-newyear) #main-nav-links a,
        body.theme-newyear-fallback #main-nav-links a {
            border-bottom: 1px solid rgba(255,255,255,0.10);
        }
    }

    /* Um brilho discreto no header */
    body:has(.hero-newyear) .top-nav::after,
    body.theme-newyear-fallback .top-nav::after {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: radial-gradient(800px 120px at 20% 0%, rgba(255,215,0,0.18), transparent 60%);
        opacity: .85;
    }
</style>

<header class="top-nav">
    <div class="nav-container">
        {{-- LOGO --}}
        <a href="https://www.psigestor.com/" class="logo">
            <img src="{{ versao('images/logo-psigestor.webp') }}" alt="PsiGestor Logo">
        </a>

        {{-- LINKS --}}
        <nav id="main-nav-links">
            <a href="{{ route('home') }}">Início</a>
            <a href="{{ route('funcionalidades') }}">Funcionalidades</a>
            <a href="{{ route('planos') }}">Nossos Planos</a>
            <a href="{{ route('contato') }}">Contato</a>
            <a href="{{ route('blog.index') }}">Blog</a>
            <a href="{{ route('login') }}" class="btn-cta">Login</a>
        </nav>

        {{-- BOTÃO HAMBÚRGUER --}}
        <button id="menu-toggle" class="hamburger" aria-label="Abrir ou fechar menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</header>

<script>
/**
 * Fallback do tema:
 * Se existir .hero-newyear, adiciona uma classe no body
 * (para browsers sem suporte ao :has())
 */
document.addEventListener('DOMContentLoaded', function () {
    try {
        if (document.querySelector('.hero-newyear')) {
            document.body.classList.add('theme-newyear-fallback');
        }
    } catch (e) {}
});
</script>
