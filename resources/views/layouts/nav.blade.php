{{-- resources/views/layouts/nav.blade.php --}}

<style>
    /* BASE DO HEADER */
    .top-nav {
        background-color: #ffffff;
        position: fixed;
        width: 100%;
        top: 0;
        z-index: 999;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        padding: 10px 20px;
        box-sizing: border-box;
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
    }

    /* ESTILOS DE NAVEGAÇÃO (DESKTOP POR PADRÃO) */
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
        transition: color 0.3s, background-color 0.3s;
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
    }

    #main-nav-links a.btn-cta:hover {
        background-color: #00aaff;
        color: white;
    }

    /* BOTÃO HAMBÚRGUER (ESCONDIDO NO DESKTOP) */
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

    /* ANIMAÇÃO DO HAMBÚRGUER ATIVO */
    .hamburger.is-active span:nth-child(1) {
        transform: rotate(45deg) translate(5px, 5px);
    }
    .hamburger.is-active span:nth-child(2) {
        opacity: 0;
    }
    .hamburger.is-active span:nth-child(3) {
        transform: rotate(-45deg) translate(5px, -5px);
    }


    /* LAYOUT PARA MOBILE (telas com até 768px de largura) */
    @media (max-width: 768px) {
        .top-nav .logo img {
            max-height: 40px;
        }

        .hamburger {
            display: flex; /* Mostra o botão apenas no mobile */
        }

        #main-nav-links {
            display: none; /* Esconde a navegação por padrão no mobile */
            position: absolute;
            top: 70px; /* Altura do header */
            left: 0;
            right: 0;
            width: 100%;
            background-color: #ffffff;
            flex-direction: column;
            padding: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            gap: 0;
        }

        /* CLASSE PARA MOSTRAR O MENU MOBILE QUANDO ATIVADO */
        #main-nav-links.is-open {
            display: flex;
        }

        #main-nav-links a {
            width: 100%;
            text-align: center;
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        #main-nav-links a:last-child {
            border-bottom: none;
        }

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
</style>

<header class="top-nav">
    <div class="nav-container">
        {{-- LOGO --}}
        <a href="https://www.psigestor.com/" class="logo">
            <img src="{{ versao('images/logo-psigestor.webp') }}" alt="PsiGestor Logo">
        </a>

        {{-- LINKS DE NAVEGAÇÃO (HTML ÚNICO) --}}
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