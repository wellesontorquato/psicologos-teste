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
        }
        .sidebar a {
            color: white;
            display: block;
            padding: 10px;
            margin-bottom: 10px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .sidebar a:hover,
        .sidebar .active {
            background-color: rgba(255, 255, 255, 0.2);
        }
        .main-content {
            margin-left: 260px;
            padding: 30px;
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
    </style>
</head>
<body class="font-sans antialiased">
    <div class="sidebar">
        <div style="text-align: center;">
            <img src="/images/logo-psigestor-branca.png" alt="PsiGestor Logo" style="max-height: 50px;">
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
                <div style="font-size: 0.9rem; color: #e6faff;">
                    @if (Auth::user()->is_admin)
                        <i class="bi bi-shield-lock-fill" style="color: #ffc107;"></i> Administrador
                    @else
                        <i class="bi bi-person-circle" style="color: #ffffff;"></i>
                        @switch(Auth::user()->genero)
                            @case('feminino')
                                Psicóloga
                                @break
                            @case('masculino')
                                Psicólogo
                                @break
                            @default
                                Psicólogue
                        @endswitch
                    @endif
                    @if(Auth::check() && Auth::user()->crp)
                        <small class="d-block text-white-50">
                            CRP {{ preg_replace('/(\d{2})(\d{5})/', '$1/$2', preg_replace('/\D/', '', Auth::user()->crp)) }}
                        </small>
                    @endif
                </div>
                <div style="margin-top: 6px;">
                    <a href="{{ route('profile.edit') }}" style="color: #ffffff; font-size: 0.85rem; text-decoration: underline;">
                        Meu perfil
                    </a>
                </div>
            </div>
        @endauth

        <hr>

        <a href="{{ route('dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('pacientes.index') }}" class="{{ request()->is('pacientes*') ? 'active' : '' }}">Pacientes</a>
        <a href="{{ route('sessoes.index') }}" class="{{ request()->is('sessoes*') ? 'active' : '' }}">Sessões</a>
        <a href="{{ route('evolucoes.index') }}" class="{{ request()->is('evolucoes*') ? 'active' : '' }}">Evoluções</a>
        <a href="{{ route('agenda') }}" class="{{ request()->is('agenda*') ? 'active' : '' }}">Agenda</a>
        <a href="{{ route('assinaturas.index') }}" class="{{ request()->is('assinaturas') ? 'active' : '' }}">
            <i class="bi bi-credit-card-fill"></i> Assinatura 
            <span class="badge bg-light text-primary ms-2">Novo</span>
        </a>
        
        @auth
            @if (Auth::user()->is_admin)
                <a href="{{ route('auditoria.index') }}" class="{{ request()->is('auditoria*') ? 'active' : '' }}">
                    <i class="bi bi-shield-lock"></i> Auditoria
                </a>
                <a href="{{ route('usuarios.index') }}" class="{{ request()->is('usuarios*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i> Usuários
                </a>
            @endif
        @endauth

        <hr>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-sm btn-light w-100">Sair</button>
        </form>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div>
                @isset($header)
                    <h2 class="h5">{{ $header }}</h2>
                @else
                    <h2 class="h5">Painel</h2>
                @endisset
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
                            document.getElementById('lista-aniversariantes').innerHTML = '';
                            const lista = document.getElementById('lista-aniversariantes');
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

                            new bootstrap.Modal(document.getElementById('modalAniversariantes')).show();
                            document.getElementById('modalAniversariantes').addEventListener('hidden.bs.modal', () => {
                                const backdrops = document.querySelectorAll('.modal-backdrop');
                                backdrops.forEach(b => b.remove());

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
                        }


                    } catch (error) {
                        console.error(error);
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
// Spinner global (Tailwind-friendly)
window.showSpinner = function() {
    document.getElementById('global-spinner')?.classList.remove('hidden');
};
window.hideSpinner = function() {
    document.getElementById('global-spinner')?.classList.add('hidden');
};
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            showSpinner();
        });
    });
});
</script>
</body>
</html>
