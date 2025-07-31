<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ $user->name }} | Psicólogo(a)</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body style="background: #f9f9f9;">
    <header class="bg-white shadow-sm py-3">
        <div class="container text-center">
            <h1 class="text-primary">PsiGestor</h1>
        </div>
    </header>
    <main>
        @yield('content')
    </main>
    <footer class="bg-light text-center py-3 mt-4">
        <small>© {{ date('Y') }} PsiGestor - Todos os direitos reservados</small>
    </footer>
</body>
</html>
