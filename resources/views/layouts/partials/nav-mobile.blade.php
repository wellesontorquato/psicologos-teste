{{-- resources/views/layouts/partials/nav-mobile.blade.php --}}
<header class="top-nav nav-mobile">
    <div class="nav-container">
        <a href="https://www.psigestor.com/">
            <img src="/images/logo-psigestor.png" alt="PsiGestor Logo">
        </a>
        <a href="{{ route('login') }}" class="btn-cta">Login</a>
        <a href="{{ route('blog.index') }}" class="btn-cta">Blog</a>
    </div>
</header>
