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
@endphp

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Autenticação')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <style>
        body {
            background-color: #f0f4f8;
            font-family: 'Segoe UI', sans-serif;
        }

        .auth-container {
            display: flex;
            min-height: 100vh;
        }

        .auth-image {
            flex: 1;
            background: url("{{ asset($image) }}") center/60% no-repeat;
            background-color: #00aaff;
        }

        .auth-form {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background-color: #ffffff;
        }

        .auth-box {
            width: 100%;
            max-width: 600px; /* ← aqui aumentamos a largura */
            padding: 40px 50px; /* ← mais espaçamento horizontal */
            background-color: #fff;
            border-radius: 16px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
        }
        
        .auth-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 25px;
            text-align: center;
        }

        .form-check-label, .form-label {
            font-weight: 500;
        }

        .btn-primary {
            background-color: #00aaff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #008ecc;
        }

        .text-muted a {
            text-decoration: none;
        }

        .text-muted a:hover {
            text-decoration: underline;
        }

        .auth-link {
            color: #00aaff;
        }

        .auth-link:hover {
            text-decoration: underline;
        }

        .auth-alert {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-image"></div>
        <div class="auth-form">
            <div class="auth-box">
                <h2 class="auth-title">@yield('title')</h2>
                @yield('form')
            </div>
        </div>
    </div>
    @yield('scripts')
</body>
</html>
