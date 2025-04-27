<header class="top-nav">
    <style>
        .top-nav {
            background-color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 40px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .top-nav img {
            max-height: 55px;
        }

        .top-nav nav {
            display: flex;
            align-items: center;
        }

        .top-nav nav a {
            margin-left: 25px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
        }

        .top-nav nav a:hover {
            color: #00aaff;
        }

        .top-nav .btn-cta {
            background: white;
            color: #00aaff;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: bold;
            border: none;
            transition: 0.3s;
            cursor: pointer;
        }

        .top-nav .btn-cta:hover {
            background: #f0f8ff;
        }

        @media (max-width: 768px) {
            .top-nav {
                flex-direction: column;
                align-items: center;
                text-align: center;
                padding: 15px 20px;
            }

            .top-nav nav {
                flex-wrap: wrap;
                justify-content: center;
                margin-top: 10px;
            }

            .top-nav nav a {
                margin: 8px;
            }
        }
    </style>

    <img src="/images/logo-psigestor.png" alt="PsiGestor Logo">
    <nav>
        <a href="{{ route('home') }}">Início</a>
        <a href="{{ route('funcionalidades') }}">Funcionalidades</a>
        <a href="{{ route('quem-somos') }}">Quem somos</a>
        <a href="{{ route('contato') }}">Contato</a>
        <a href="{{ route('login') }}" class="btn-cta">Login</a>
    </nav>
</header>
