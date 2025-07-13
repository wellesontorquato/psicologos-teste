{{-- resources/views/layouts/partials/nav-desktop.blade.php --}}
<header class="top-nav nav-desktop">
    <div class="nav-container">
        <a href="https://www.psigestor.com/">
            <img src="/images/logo-psigestor.png" alt="PsiGestor Logo">
        </a>
        <nav>
            <a href="{{ route('home') }}">Início</a>
            <a href="{{ route('funcionalidades') }}">Funcionalidades</a>
            <a href="{{ route('planos') }}">Nossos Planos</a>
            <a href="{{ route('contato') }}">Contato</a>
            <a href="{{ route('blog.index') }}">Blog</a>
            <a href="{{ route('login') }}" class="btn-cta">Login</a>
        </nav>
    </div>
</header>
