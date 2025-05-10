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
            transition: max-height 0.3s ease-out;
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

        .menu-toggle {
            display: none;
            flex-direction: column;
            cursor: pointer;
        }

        .menu-toggle span {
            height: 3px;
            width: 25px;
            background: #333;
            margin: 4px 0;
            border-radius: 3px;
            transition: 0.3s;
        }

        @media (max-width: 768px) {
            .top-nav {
                flex-wrap: wrap;
                padding: 15px 20px;
            }

            .menu-toggle {
                display: flex;
            }

            .top-nav nav {
                flex-direction: column;
                width: 100%;
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s ease-out;
            }

            .top-nav nav.open {
                max-height: 500px; /* Suficiente para mostrar os links */
            }

            .top-nav nav a {
                margin: 10px 0;
                width: 100%;
            }
        }
    </style>

    <img src="/images/logo-psigestor.png" alt="PsiGestor Logo">

    <div class="menu-toggle" id="menu-toggle">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <nav id="nav-menu">
        <a href="{{ route('home') }}">Início</a>
        <a href="{{ route('funcionalidades') }}">Funcionalidades</a>
        <a href="{{ route('planos') }}">Nossos Planos</a>
        <a href="{{ route('quem-somos') }}">Quem somos</a>
        <a href="{{ route('contato') }}">Contato</a>
        <a href="{{ route('login') }}" class="btn-cta">Login</a>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menuToggle = document.getElementById('menu-toggle');
            const navMenu = document.getElementById('nav-menu');

            menuToggle.addEventListener('click', function () {
                navMenu.classList.toggle('open');
            });
        });
    </script>
</header>
