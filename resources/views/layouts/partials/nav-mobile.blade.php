{{-- resources/views/layouts/partials/nav-mobile.blade.php --}}
<header class="top-nav nav-mobile">
    <div class="nav-container">
        <a href="https://www.psigestor.com/">
            <img src="/images/logo-psigestor.png" alt="PsiGestor Logo">
        </a>

        {{-- BOTÃO HAMBÚRGUER --}}
        <button id="menu-toggle" class="hamburger" aria-label="Abrir Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>

    {{-- MENU MOBILE --}}
    <nav id="mobile-menu" class="mobile-menu">
        <a href="{{ route('home') }}">Início</a>
        <a href="{{ route('funcionalidades') }}">Funcionalidades</a>
        <a href="{{ route('planos') }}">Nossos Planos</a>
        <a href="{{ route('contato') }}">Contato</a>
        <a href="{{ route('blog.index') }}">Blog</a>
        <a href="{{ route('login') }}" class="btn-cta">Login</a>
    </nav>
</header>
