@php
    $routeName = Route::currentRouteName();

    $image = match ($routeName) {
        'login' => 'images/auth/tela-login.png',
        'register' => 'images/auth/register.png',
        'password.request' => 'images/auth/forgot-password.png',
        'password.reset' => 'images/auth/tela-login.png',
        'verification.notice' => 'images/auth/verify-email.png',
        'password.confirm' => 'images/auth/forgot-password.png',
        default => 'images/auth/tela-login.png',
    };

    $imageWebp = preg_replace('/\.(png|jpg|jpeg)$/i', '.webp', $image);
@endphp

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Autenticação')</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Preconnect para reduzir latência -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>

    <!-- Bootstrap -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        media="print"
        onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"></noscript>

    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        media="print"
        onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"></noscript>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>

    <style>
        body {
            background-color: #f0f4f8;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
        }

        .auth-container {
            display: flex;
            flex-direction: column; /* Mobile-first */
            min-height: 100vh;
        }

        .auth-image {
            width: 100%;
            height: 180px;
            background-color: #00aaff;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-image picture, 
        .auth-image img {
            max-width: 90%;
            height: auto;
        }

        .auth-form {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 30px 20px;
            background-color: #ffffff;
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .auth-logo img {
            max-width: 180px;
            height: auto;
        }

        .auth-box {
            width: 100%;
            max-width: 420px;
            padding: 30px 25px;
            background-color: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.08);
        }

        .auth-title {
            font-size: 1.6rem;
            font-weight: bold;
            margin-bottom: 25px;
            text-align: center;
            color: #111;
        }

        .btn-primary {
            background-color: #00aaff;
            border: none;
            font-weight: 600;
            padding: 10px;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background-color: #008ecc;
        }

        .auth-link {
            color: #00aaff;
            font-weight: 500;
        }

        .auth-link:hover {
            color: #008ecc;
            text-decoration: underline;
        }

        /* Garante que o texto apareça sem atraso mesmo carregando fontes externas */
        @font-face {
        font-family: 'Font Awesome 6 Free';
        font-style: normal;
        font-weight: 400;
        font-display: swap;
        src: url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/webfonts/fa-regular-400.woff2') format('woff2');
        }

        /* Desktop layout */
        @media (min-width: 768px) {
            .auth-container {
                flex-direction: row;
            }

            .auth-image {
                flex: 1;
                height: auto;
                min-height: 100vh;
            }

            .auth-form {
                flex: 1;
                padding: 60px;
                justify-content: center;
            }

            .auth-logo {
                margin-bottom: 30px;
            }

            .auth-box {
                max-width: 500px;
                padding: 45px 40px;
            }

            .auth-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        {{-- Imagem lateral (somente desktop) --}}
        <div class="auth-image d-none d-md-flex">
            <picture>
                <source srcset="{{ asset($imageWebp) }}" type="image/webp">
                <img src="{{ asset($image) }}" alt="Tela de autenticação" loading="lazy" width="500" height="500">
            </picture>
        </div>

        {{-- Formulário --}}
        <div class="auth-form">
            {{-- Logo clicável --}}
            <div class="auth-logo">
                <a href="{{ url('/') }}">
                    <picture>
                        <source srcset="{{ versao('images/logo-psigestor.webp') }}" type="image/webp">
                        <img src="{{ versao('images/logo-psigestor.png') }}" alt="PsiGestor Logo" loading="lazy" width="180" height="auto">
                    </picture>
                </a>
            </div>

            <div class="auth-box">
                <h2 class="auth-title">@yield('title')</h2>
                @yield('form')
            </div>
        </div>
    </div>

    {{-- Spinner Global --}}
    <div id="global-spinner">
        <svg class="spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity: 0.25;"></circle>
            <path fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" style="opacity: 0.75;"></path>
        </svg>
    </div>

    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
        #global-spinner {
            display: none;
            position: fixed;
            inset: 0;
            background-color: rgba(255, 255, 255, 0.7);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        #global-spinner .spin {
            animation: spin 1s linear infinite;
            height: 64px;
            width: 64px;
            color: #00aaff;
        }
    </style>

    <script>
        window.showSpinner = () => document.getElementById('global-spinner').style.display = 'flex';
        window.hideSpinner = () => document.getElementById('global-spinner').style.display = 'none';

        document.addEventListener('DOMContentLoaded', () => {
            hideSpinner();
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', () => showSpinner());
            });
        });
    </script>

    @yield('scripts')
</body>
</html>
