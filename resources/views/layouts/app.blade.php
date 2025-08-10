<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <title>@yield('title', config('app.name', 'Sistema de Psicólogos/Psicanalistas/Psiquiatras'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Vite -->
    {{ Vite::useBuildDirectory('build')->withEntryPoints(['resources/css/app.css', 'resources/js/app.js']) }}
    @yield('styles')
    @stack('styles')

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        [x-cloak] { display: none !important; }
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            width: 240px;
            height: 100vh;
            background: #00aaff;
            color: white;
            position: fixed;
            padding: 20px 15px;
            transition: width 0.3s ease;
            overflow-y: auto;
            scrollbar-width: thin;
        }
        .sidebar a {
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            margin-bottom: 10px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sidebar a:hover,
        .sidebar .active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .sidebar::-webkit-scrollbar {
            width: 8px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 4px;
        }

        .main-content {
            margin-left: 260px;
            padding: 30px;
            transition: margin-left 0.3s ease;
        }

        /* Overlay para escurecer fundo */
        .overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.35); /* leve escurecimento */
            backdrop-filter: blur(6px);   /* aplica o blur */
            -webkit-backdrop-filter: blur(6px); /* suporte Safari */
            z-index: 1040;
            transition: opacity 0.3s ease;
            opacity: 0;
        }

        .overlay.active {
            display: block;
            opacity: 1;
        }

        .topbar {
            position: relative;
            z-index: 1100; /* garante que o botão hamburguer fica acima do overlay */
        }

        .topbar {
            background: white;
            padding: 15px 30px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .swal2-title-custom {
            text-align: center !important;
        }
        .swal2-html-custom ul {
            text-align: center !important;
            list-style-position: inside !important;
            padding-left: 0 !important;
        }
        .swal2-html-custom li {
            margin-bottom: 5px;
        }

        .lida {
            opacity: 0.6;
            transition: opacity 0.3s ease;
        }

        .trial-badge {
            margin-right: 10px;
            padding: 4px 10px;
            font-size: 0.8rem;
            background-color: #fff3cd;
            color: #856404;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 4px;
            font-weight: 500;
        }

        .modal {
            z-index: 1200 !important; /* maior que a topbar */
        }

        .modal-backdrop {
            z-index: 1190 !important; /* um pouco abaixo do modal */
        }

        @media (max-width: 992px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: 0; /* fixo no canto esquerdo */
                height: 100vh;
                z-index: 1050;
                transform: translateX(-260px); /* começa fora da tela */
                transition: transform 0.3s ease;
            }
            .sidebar.active {
                transform: translateX(0); /* desliza para dentro */
            }
            .main-content {
                margin-left: 0 !important;
                padding: 20px 15px;
            }
            .menu-toggle {
                display: inline-block;
            }
        }

        .btn-novo-paciente, .btn-nova-sessao, .btn-voltar-sessoes, .btn-nova-evolucao {
            width: 100%; /* mobile first */
        }

        @media (min-width: 768px) {
            .btn-novo-paciente, .btn-nova-sessao, .btn-voltar-sessoes, .btn-nova-evolucao {
                width: auto;
                display: inline-block;
            }
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
<body class="font-sans antialiased">
    <div class="sidebar">
        <div style="display: flex; justify-content: center; align-items: center; height: 80px;">
            <img src="{{ versao('images/logo-psigestor-branca.png') }}" alt="PsiGestor Logo" style="max-height: 50px;">
        </div>
        
        @auth
            <div style="text-align: center; margin-top: 25px; margin-bottom: 20px; background: rgba(255, 255, 255, 0.1); padding: 15px; border-radius: 12px;">
                <div style="display: flex; justify-content: center;">
                    <img src="{{ Auth::user()->profile_photo_url }}"
                        alt="Foto de Perfil"
                        class="sidebar-profile-photo"
                        style="width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 3px solid #fff; box-shadow: 0 0 8px rgba(0,0,0,0.2);" />
                </div>

                <div style="margin-top: 10px; font-weight: bold; font-size: 1rem; color: #fff;">
                    {{ Auth::user()->name }}
                </div>

                <div style="font-size: 0.9rem; color: #e6faff; margin-top: 4px;">
                @if (Auth::user()->is_admin)
                    <i class="bi bi-shield-lock-fill" style="color: #ffc107;"></i> Administrador
                @else
                    <i class="bi bi-person-circle" style="color: #ffffff;"></i>

                    @php
                        $tipo = strtolower(Auth::user()->tipo_profissional);
                        $genero = Auth::user()->genero;
                    @endphp

                    @if ($tipo === 'psiquiatra')
                        @if ($genero === 'feminino')
                            Psiquiatra
                        @else
                            Psiquiatra
                        @endif
                    @elseif ($tipo === 'psicologo')
                        @switch($genero)
                            @case('feminino')
                                Psicóloga
                                @break
                            @case('masculino')
                                Psicólogo
                                @break
                            @default
                                Psicólogo(a)
                        @endswitch
                    @elseif ($tipo === 'psicanalista')
                        Psicanalista
                    @else
                        Profissional
                    @endif
                @endif

                    {{-- Registro Profissional --}}
                    @php
                        $tipo = Auth::user()->tipo_profissional;
                        $registro = Auth::user()->registro_profissional;
                    @endphp

                    @if ($registro && $tipo)
                        @php $tipoLower = \Illuminate\Support\Str::lower($tipo); @endphp

                        @if ($tipoLower === 'psiquiatra')
                            <small class="d-block text-white-50">
                                CRM {{ preg_replace('/\D/', '', $registro) }}
                            </small>
                        @elseif ($tipoLower === 'psicologo')
                            <small class="d-block text-white-50">
                                CRP {{ preg_replace('/(\d{2})(\d{5})/', '$1/$2', preg_replace('/\D/', '', $registro)) }}
                            </small>
                        @endif
                        {{-- Psicanalista não exibe nada --}}
                    @endif
                </div>


                <div style="margin-top: 10px; text-align: center;">
                    <a href="{{ route('profile.edit') }}" style="color: #ffffff; font-size: 0.85rem; text-decoration: underline; display: inline-block;">
                        Meu perfil
                    </a>
                </div>
            </div>
        @endauth
        <hr>

        <a href="{{ route('dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
        </a>
        <a href="{{ route('pacientes.index') }}" class="{{ request()->is('pacientes*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> <span>Pacientes</span>
        </a>
        <a href="{{ route('sessoes.index') }}" class="{{ request()->is('sessoes*') ? 'active' : '' }}">
            <i class="bi bi-calendar-check-fill"></i> <span>Sessões</span>
        </a>
        <a href="{{ route('evolucoes.index') }}" class="{{ request()->is('evolucoes*') ? 'active' : '' }}">
            <i class="bi bi-journal-medical"></i> <span>Evoluções</span>
        </a>
        <a href="{{ route('agenda') }}" class="{{ request()->is('agenda*') ? 'active' : '' }}">
            <i class="bi bi-calendar4-week"></i> <span>Agenda</span>
        </a>
        @auth
            @if (auth()->user()->subscribed('default'))
                <a href="{{ route('assinaturas.minha') }}" class="{{ request()->is('minha-assinatura') ? 'active' : '' }}">
                    <i class="bi bi-credit-card-fill"></i> <span>Minha Assinatura</span>
                </a>
            @else
                <a href="{{ route('assinaturas.index') }}" class="{{ request()->is('assinaturas') ? 'active' : '' }}">
                    <i class="bi bi-credit-card-fill"></i> <span>Assinatura</span>
                    <span class="badge bg-light text-primary ms-2">Novo</span>
                </a>
            @endif
        @endauth

        @auth
            @if (Auth::user()->is_admin)
                <a href="{{ route('admin.auditoria.index') }}" class="{{ request()->is('auditoria*') ? 'active' : '' }}">
                    <i class="bi bi-shield-lock"></i> <span>Auditoria</span>
                </a>
                <a href="{{ route('admin.usuarios.index') }}" class="{{ request()->is('usuarios*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i> <span>Usuários</span>
                </a>
                <a href="{{ route('admin.news.index') }}" class="{{ request()->is('admin/news*') ? 'active' : '' }}">
                    <i class="bi bi-newspaper"></i> <span>Notícias do Blog</span>
                </a>
            @endif
        @endauth

        <hr>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-sm btn-light w-100">Sair</button>
        </form>
    </div>
    <div class="overlay"></div>

    <div class="main-content">
        <div class="topbar">
            <div class="d-flex align-items-center">
            <button class="menu-toggle btn btn-link text-dark d-lg-none">
                <i class="bi bi-list fs-3"></i>
            </button>
            <h2 class="h5 mb-0">
                @isset($header) {{ $header }} @else Painel @endisset
            </h2>
        </div>

            <div x-data="{ aberto: false }" class="relative">
                @php
                    use App\Models\Notificacao;
                    $user = Auth::user();
                    $notificacoes = Notificacao::where('user_id', $user->id ?? null)
                        ->orderByDesc('created_at')
                        ->take(10)
                        ->get();
                    $naoLidas = $notificacoes->where('lida', false)->count();
                @endphp

                <button @click="aberto = !aberto; $refs.badge?.remove();" class="relative p-2 text-gray-700 hover:text-blue-600 focus:outline-none">
                    <i class="bi bi-bell-fill fs-5"></i>
                    @if($naoLidas > 0)
                        <span x-ref="badge" class="absolute top-0 right-0 bg-red-600 text-white text-xs px-1.5 py-0.5 rounded-full">
                            {{ $naoLidas }}
                        </span>
                    @endif
                </button>
                
                <div x-show="aberto" x-cloak @click.away="aberto = false"
                    x-transition
                    class="absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-xl shadow-lg z-50">
                    <div class="p-3 border-b font-semibold text-gray-700 flex justify-between items-center">
                        <span>Notificações</span>
                        @if($naoLidas > 0)
                        <button
                            @click.prevent="
                                fetch('{{ route('notificacoes.ler.todas') }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    }
                                }).then(() => {
                                    $refs.badge?.remove();
                                    aberto = false;
                                    document.querySelectorAll('.notificacao-item').forEach(el => {
                                        el.classList.add('lida');
                                    });
                                    $el.style.display = 'none';
                                })"
                            class="text-xs text-blue-600 hover:underline">
                            Marcar todas como lidas
                        </button>
                        @endif
                    </div>

                    <ul class="max-h-96 overflow-y-auto">
                        @forelse($notificacoes as $notificacao)
                            <li class="px-4 py-3 hover:bg-gray-50 transition-all duration-150 border-b notificacao-item">
                                <a href="{{ route('notificacoes.acao', $notificacao->id) }}" data-tipo="{{ $notificacao->tipo }}" class="flex items-start gap-3 no-underline">
                                    @php
                                        $badgeColor = match($notificacao->tipo) {
                                            'sessao_nao_paga' => 'bg-red-600',
                                            'whatsapp_confirmado' => 'bg-green-600',
                                            'aniversario' => 'bg-gray-400',
                                            'whatsapp_remarcar' => 'bg-yellow-500',
                                            'whatsapp_cancelada' => 'bg-red-600',
                                            default => 'bg-blue-400',
                                        };
                                    @endphp
                                    <span class="inline-block w-2.5 h-2.5 mt-1 rounded-full {{ $badgeColor }}"></span>
                                    <div class="flex-1 text-sm leading-snug">
                                        <div class="text-gray-800 font-semibold">
                                            {{ $notificacao->titulo }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            {{ $notificacao->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @empty
                            <li class="px-4 py-3 text-sm text-gray-500 text-center">Sem novas notificações.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        @if (Auth::check() && !Auth::user()->hasVerifiedEmail())
            <div id="alert-verificacao-email" class="alert alert-warning d-flex justify-content-between align-items-center shadow-sm" role="alert" style="border-left: 5px solid #ffc107; padding: 10px 20px; margin-bottom: 20px;">
                <div>
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Atenção:</strong> Seu e-mail ainda não foi verificado.
                </div>
                <button id="btn-reenviar-email" class="btn btn-sm btn-warning text-white fw-bold">
                    Reenviar verificação
                </button>
            </div>

            <script>
                document.getElementById('btn-reenviar-email').addEventListener('click', async () => {
                    try {
                        const resposta = await fetch('{{ route('verification.send') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        });

                        const data = await resposta.json();

                        Swal.fire({
                            icon: 'success',
                            title: 'Verificação enviada!',
                            text: data.message || 'Um novo link de verificação foi enviado para seu e-mail.',
                            confirmButtonColor: '#00aaff'
                        });
                    } catch (e) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro ao reenviar verificação',
                            text: 'Tente novamente mais tarde.',
                            confirmButtonColor: '#00aaff'
                        });
                    }
                });
            </script>
        @endif

        <main>
            @yield('content')
        </main>
    </div>

    @include('components.modal-aniversariantes')
    @include('components.modal-sessao-confirmada')
    @include('components.modal-sessao-cancelada')
    @include('components.modal-sessao-reagendada')
    @include('components.modal-sessao-nao-paga')

    @yield('scripts')
    @stack('scripts')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Erro ao atualizar informações',
                customClass: {
                    title: 'swal2-title-custom',
                    htmlContainer: 'swal2-html-custom'
                },
                html: `<ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>`,
                confirmButtonColor: '#00aaff'
            });
        </script>
    @endif

    @if (session('status') === 'profile-updated')
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: 'Perfil atualizado com sucesso.',
                confirmButtonColor: '#00aaff'
            });
        </script>
    @endif

    @if (session('status') === 'password-updated')
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Senha atualizada!',
                text: 'Sua senha foi alterada com sucesso.',
                confirmButtonColor: '#00aaff'
            });
        </script>
    @endif

    @if (session('status') === 'photo-updated')
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Foto atualizada!',
                text: 'Sua foto de perfil foi atualizada com sucesso.',
                confirmButtonColor: '#00aaff'
            });
        </script>
    @endif
    <script>
    document.addEventListener('DOMContentLoaded', () => {
    const trialEndsAt = @json(Auth::user()?->trial_ends_at);
    if (trialEndsAt) {
        const end = new Date(trialEndsAt);
        const now = new Date();
        const diffTime = end - now;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays > 0) {
            // Só mostra o alerta se ainda restar 1 dia ou menos
            if (diffDays === 1) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Seu teste gratuito está acabando!',
                    text: 'Falta apenas 1 dia para o término do seu período de teste.',
                    confirmButtonColor: '#00aaff'
                });
            }

            // Cria o badge apenas se ainda tem dias restantes
            const badge = document.createElement('div');
            badge.className = 'trial-badge';
            badge.innerHTML = '<i class="bi bi-hourglass-split"></i> ' + diffDays + ' dias grátis restantes';

            const topbar = document.querySelector('.topbar > .relative');
            if (topbar) {
                topbar.insertAdjacentElement('beforebegin', badge);
            }
        } else {
            // Trial expirado: cria o badge de "Teste gratuito encerrado"
            const badge = document.createElement('div');
            badge.className = 'trial-badge';
            badge.innerHTML = '<i class="bi bi-x-circle-fill"></i> Teste gratuito encerrado';

            const topbar = document.querySelector('.topbar > .relative');
            if (topbar) {
                topbar.insertAdjacentElement('beforebegin', badge);
            }
        }
    }
});
</script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('a[data-tipo]').forEach(link => {
                link.addEventListener('click', async function (e) {
                    e.preventDefault();
                    const tipo = this.getAttribute('data-tipo');
                    const url = this.getAttribute('href');

                    try {
                        const resposta = await fetch(url, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        });

                        if (!resposta.ok) throw new Error('Erro ao processar notificação');

                        const data = await resposta.json();

                        // 🎂 Modal de Aniversário
                        if (data.tipo === 'aniversario') {
                            const lista = document.getElementById('lista-aniversariantes');
                            lista.innerHTML = '';

                            try {
                                const resposta = await fetch('/api/aniversariantes-hoje');
                                const aniversariantes = await resposta.json();

                                aniversariantes.forEach(p => {
                                    const li = document.createElement('li');
                                    li.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');
                                    li.innerHTML = `
                                        <span><i class="bi bi-heart-fill text-danger me-2"></i><strong>${p.nome}</strong></span>
                                        <span class="badge rounded-pill bg-success">
                                            🎂 Está fazendo ${p.idade} anos
                                        </span>`;
                                    lista.appendChild(li);
                                });

                                const modalEl = document.getElementById('modalAniversariantes');
                                const instance = bootstrap.Modal.getOrCreateInstance(modalEl);
                                instance.show();
                            } catch (error) {
                                console.error('Erro ao buscar aniversariantes:', error);
                                alert('Erro ao carregar os aniversariantes.');
                            } finally {
                                hideSpinner();
                            }

                            document.getElementById('modalAniversariantes').addEventListener('hidden.bs.modal', () => {
                                document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                                document.body.classList.remove('modal-open');
                                document.body.style = '';
                            });
                        }

                        // ✅ Modal de Sessão Confirmada
                        if (data.tipo === 'whatsapp_confirmado' && data.sessao) {
                            const texto = `
                                A sessão com <strong>${data.sessao.paciente}</strong> foi confirmada para o dia 
                                <strong>${data.sessao.data}</strong> às <strong>${data.sessao.hora}</strong>.
                            `;
                            document.getElementById('modalSessaoConfirmadaTexto').innerHTML = texto;

                            const modalEl = document.getElementById('modalSessaoConfirmada');
                            const instance = bootstrap.Modal.getOrCreateInstance(modalEl);
                            instance.show();
                            hideSpinner();
                        }

                        // ❌ Modal de Sessão Cancelada
                        if (data.tipo === 'whatsapp_cancelada' && data.sessao) {
                            const texto = `
                                A sessão com <strong>${data.sessao.paciente}</strong> marcada para o dia 
                                <strong>${data.sessao.data}</strong> às <strong>${data.sessao.hora}</strong> foi 
                                <span class="text-danger fw-bold">cancelada</span>.
                            `;
                            document.getElementById('modalSessaoCanceladaTexto').innerHTML = texto;

                            const modalEl = document.getElementById('modalSessaoCancelada');
                            const instance = bootstrap.Modal.getOrCreateInstance(modalEl);
                            instance.show();
                            hideSpinner();
                        }

                        // 🔄 Modal de Sessão Remarcada
                        if (data.tipo === 'whatsapp_remarcar' && data.sessao) {
                            const texto = `
                                A sessão com <strong>${data.sessao.paciente}</strong>, originalmente marcada para o dia 
                                <strong>${data.sessao.data}</strong> às <strong>${data.sessao.hora}</strong>, será reagendada.
                            `;
                            document.getElementById('modalSessaoReagendadaTexto').innerHTML = texto;

                            const btn = document.getElementById('btnReagendarSessao');
                            btn.href = `/sessoes/${data.sessao.id}/edit`;
                        
                            const modalEl = document.getElementById('modalSessaoReagendada');
                            const instance = bootstrap.Modal.getOrCreateInstance(modalEl);
                            instance.show();
                            hideSpinner();
                        }

                        // ⚠️ Modal de Sessão Não Paga
                        if (data.tipo === 'sessao_nao_paga' && data.sessao) {
                            const texto = `
                                <strong>${data.sessao.paciente}</strong><br>
                                Sessão realizada em <strong>${data.sessao.data}</strong> às <strong>${data.sessao.hora}</strong> ainda está <span class="text-danger fw-bold">pendente de pagamento</span>.
                            `;
                            document.getElementById('modalSessaoNaoPagaTexto').innerHTML = texto;

                            const modalEl = document.getElementById('modalSessaoNaoPaga');
                            const instance = bootstrap.Modal.getOrCreateInstance(modalEl);
                            instance.show();
                            hideSpinner();
                        }


                    } catch (error) {
                        console.error(error);
                        hideSpinner();
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro ao abrir notificação.',
                            text: 'Tente novamente.',
                            confirmButtonColor: '#00aaff'
                        });
                    }
                });
            });
        });
</script>

<!-- Spinner Global (Tailwind) -->
    <div id="global-spinner" class="hidden fixed inset-0 bg-white/70 z-[9999] flex items-center justify-center">
        <svg class="animate-spin h-16 w-16 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
    </div>

    <script>
    window.showSpinner = function() {
        document.getElementById('global-spinner')?.classList.remove('hidden');
    };
    window.hideSpinner = function() {
        document.getElementById('global-spinner')?.classList.add('hidden');
    };

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('form:not(.no-spinner)').forEach(form => {
            form.addEventListener('submit', function() {
                showSpinner();
            });
        });
    });
    document.addEventListener('click', function (e) {
        const link = e.target.closest('a');

        if (link && link.href && !link.classList.contains('no-spinner')) {
            const isSamePageAnchor = link.getAttribute('href').startsWith('#');
            const isNewTab = link.target === '_blank';
            const isExternal = link.hostname !== window.location.hostname;

            if (!isSamePageAnchor && !isNewTab && !isExternal) {
                // Se for link de download, NÃO mostra o spinner
                if (!link.classList.contains('no-spinner-on-download')) {
                    showSpinner();
                }
            }
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.querySelector('.sidebar');
        const toggleBtn = document.querySelector('.menu-toggle');
        const overlay = document.querySelector('.overlay');

        function closeSidebar() {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        }

        function openSidebar() {
            sidebar.classList.add('active');
            overlay.classList.add('active');
        }

        // abre/fecha no clique do botão
        toggleBtn?.addEventListener('click', () => {
            if (sidebar.classList.contains('active')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });

        // fecha se clicar em um link da sidebar
        sidebar.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 992) {
                    closeSidebar();
                }
            });
        });

        // fecha se clicar fora da sidebar (no overlay)
        overlay?.addEventListener('click', closeSidebar);

        // fallback: fecha se clicar fora de tudo
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 992 && sidebar.classList.contains('active')) {
                if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target) && !overlay.contains(e.target)) {
                    closeSidebar();
                }
            }
        });
    });
</script>

</body>
</html>
