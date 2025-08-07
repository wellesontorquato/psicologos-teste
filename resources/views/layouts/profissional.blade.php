<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Perfil do Profissional | PsiGestor')</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    
    <style>
        :root {
            --primary-color: #00aaff;
            --secondary-color: #008ecc;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --bg-light: #f8f9fa;
            --card-bg: #ffffff;
            --border-color: #e5e7eb;
        }
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-light); color: var(--text-dark); margin: 0; line-height: 1.6; }
        .container { max-width: 1100px; margin: 0 auto; padding: 2rem 1rem; }
        
        /* Layout em Grid */
        .profile-grid { display: grid; grid-template-columns: 1fr; gap: 2rem; }
        
        /* Cabeçalho e Rodapé */
        header { text-align: center; padding: 1rem; background-color: var(--card-bg); border-bottom: 1px solid var(--border-color); }
        header img.logo { height: 40px; }
        footer { text-align: center; color: var(--text-light); font-size: 0.9rem; padding: 2rem 1rem; border-top: 1px solid var(--border-color); margin-top: 2rem; }
        
        /* Cards */
        .card { background-color: var(--card-bg); padding: 2rem; border-radius: 1rem; border: 1px solid var(--border-color); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05); margin-bottom: 2rem; animation: fadeIn 0.5s ease-out forwards; }
        .card h4 { display: flex; align-items: center; gap: 0.75rem; font-size: 1.25rem; font-weight: 700; margin-top: 0; margin-bottom: 1.5rem; color: var(--text-dark); border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem; }
        .card h4 .bi { color: var(--primary-color); }

        /* Perfil (Sidebar) */
        .profile-header-card { text-align: center; padding: 1.5rem; padding-top: 2.5rem; }
        .profile-photo { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 5px solid var(--card-bg); box-shadow: 0 0 0 4px var(--primary-color), 0 10px 30px -10px rgba(0,0,0,0.2); margin-top: -6rem; margin-bottom: 1rem; background-color: var(--bg-light); }
        .profile-header-card h2 { font-size: 2rem; font-weight: 800; margin: 0; }
        .profile-header-card .specialty { color: var(--text-light); font-size: 1.1rem; margin-top: 0.25rem; margin-bottom: 1.5rem; }
        
        /* --- NOVO: Estilos para a biografia --- */
        .bio-text {
            font-size: 1.05rem;
            line-height: 1.7;
            color: var(--text-light);
            white-space: pre-wrap; /* Preserva as quebras de linha do texto */
            padding-left: 1.5rem;
            border-left: 3px solid var(--primary-color);
        }

        /* --- NOVO: Espaçamento para os botões de contato na sidebar --- */
        .links-container {
            display: flex;
            flex-direction: column;
            gap: 1rem; /* Define o espaço vertical entre os botões */
            margin-top: 1.5rem; /* Espaço entre o nome/especialidade e o primeiro botão */
        }

        /* --- NOVO: Links Secundários (ícones) --- */
        .secondary-links {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        .secondary-links a {
            color: var(--text-light);
            font-size: 1.5rem; /* Tamanho do ícone */
            text-decoration: none;
            transition: color 0.2s, transform 0.2s;
        }
        .secondary-links a:hover {
            color: var(--primary-color);
            transform: scale(1.1);
        }

        /* Contato Info */
        .contact-info p { display: flex; align-items: center; gap: 0.75rem; margin: 0.5rem 0; }
        .contact-info a { color: var(--text-dark); text-decoration: none; word-break: break-all; }
        .contact-info a:hover { color: var(--primary-color); }
        
        /* Botão Principal */
        .btn-link { display: inline-flex; align-items: center; justify-content: center; gap: 0.75rem; width: 100%; padding: 1rem; color: white; font-weight: 700; font-size: 1rem; border-radius: 0.75rem; text-decoration: none; border: none; cursor: pointer; box-shadow: 0 4px 15px -5px rgba(0,0,0,0.3); transition: transform 0.2s, box-shadow 0.2s; }
        .btn-link:hover { transform: translateY(-3px); box-shadow: 0 8px 20px -6px rgba(0,0,0,0.4); }
        
        /* Classes de Botões por Rede Social */
        .btn-default { background-color: var(--primary-color); }
        .btn-whatsapp { background-color: #25D366; }
        .btn-instagram { background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%); }
        .btn-facebook { background-color: #1877F2; }
        .btn-linkedin { background-color: #0A66C2; }
        .btn-calendly { background-color: #0069A5; }
        .btn-telegram { background-color: #0088CC; }
        
        /* Listas */
        ul.areas-list { list-style: none; padding: 0; display: flex; flex-wrap: wrap; gap: 0.75rem; }
        ul.areas-list li { background-color: #f3f4f6; color: var(--text-dark); padding: 0.5rem 1rem; border-radius: 50px; font-size: 0.9rem; font-weight: 500; }
        
        /* Animações */
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .card:nth-child(2) { animation-delay: 0.1s; }
        .card:nth-child(3) { animation-delay: 0.2s; }
        
        /* --- ATUALIZADO: Media Queries --- */
        @media (min-width: 992px) {
            .profile-grid {
                grid-template-columns: 320px 1fr;
                .profile-sidebar { position: sticky; top: 2rem; }
            }
        }
        @media (max-width: 991px) {
            .profile-photo {
                width: 130px;
                height: 130px;
                margin-top: -4.5rem; /* Reduz o efeito flutuante no mobile */
            }
            .card {
                padding: 1.5rem; /* Reduz o padding dos cards no mobile */
            }
             .profile-header-card h2 {
                font-size: 1.75rem; /* Reduz o nome no mobile */
            }
        }
    </style>
</head>
<body>
    <header>
        <img src="{{ versao('images/logo-psigestor.png') }}" alt="PsiGestor" class="logo">
    </header>

    <div class="container">
        @yield('content')
    </div>

    <footer>
        © {{ date('Y') }} PsiGestor - Todos os direitos reservados
    </footer>
</body>
</html>