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

    <!-- (Opcional) Tailwind + Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

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
            max-width: 600px;
            padding: 40px 50px;
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

        /* Spinner */
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
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
    <!-- Facebook Meta Pixel -->
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
    <!-- End Facebook Meta Pixel -->
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

        <!-- Spinner Global -->
        <div id="global-spinner">
        <svg class="spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity: 0.25;"></circle>
            <path fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" style="opacity: 0.75;"></path>
        </svg>
    </div>

    <style>
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
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
        window.showSpinner = function() {
            document.getElementById('global-spinner')?.style.setProperty('display', 'flex', 'important');
        };
        window.hideSpinner = function() {
            document.getElementById('global-spinner')?.style.setProperty('display', 'none', 'important');
        };

        document.addEventListener('DOMContentLoaded', function() {
            hideSpinner();

            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    showSpinner();
                });
            });

            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (link && link.href && !link.classList.contains('no-spinner')) {
                    const isSamePageAnchor = link.getAttribute('href')?.startsWith('#');
                    const isNewTab = link.target === '_blank';
                    const isExternal = link.hostname !== window.location.hostname;

                    if (!isSamePageAnchor && !isNewTab && !isExternal) {
                        showSpinner();
                    }
                }
            });

            window.addEventListener('beforeunload', function() {
                showSpinner();
            });
        });
    </script>

    @yield('scripts')
</body>
</html>
