@extends('layouts.app')
@section('title', 'Perfil | PsiGestor')
@section('content')
<style>
    /* Novo container principal com largura maior */
    .profile-container {
        max-width: 1024px;
        margin: 0 auto; /* Zera o espaço no topo e na base */
        padding: 2rem;
    }

    /* Novo layout em Grid */
    .profile-grid {
        display: grid;
        grid-template-columns: 1fr 2fr; /* Coluna da esquerda (1fr) e da direita (2fr) */
        gap: 2rem; /* Espaçamento entre as colunas */
    }

    /* Card genérico para cada seção */
    .profile-card {
        background-color: #fff;
        padding: 1.5rem;
        border-radius: 1rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem; /* Espaçamento entre cards na mesma coluna */
    }

    /* Deixa o card da foto com conteúdo centralizado */
    .photo-card {
        text-align: center;
    }

    .profile-photo {
        width: 128px; /* Um pouco maior para a nova sidebar */
        height: 128px;
        border-radius: 9999px;
        object-fit: cover;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        border: 3px solid #00aaff;
        margin: 0 auto 1.5rem auto;
        display: block;
    }

    .section-title {
        font-size: 1.75rem;
        font-weight: bold;
        margin-bottom: 2rem;
        color: #1f2937;
        text-align: left; /* Alinha o título principal à esquerda */
    }

    /* Ajustes responsivos para telas menores (ex: celulares) */
    @media (max-width: 768px) {
        .profile-grid {
            grid-template-columns: 1fr; /* Volta para uma única coluna */
        }
        .profile-container {
            padding: 1rem;
        }
    }

    /* --- Estilos de formulário e botões (mantidos e ajustados) --- */
    .form-group {
        margin-bottom: 1rem;
        text-align: left; /* Garante alinhamento à esquerda nos formulários */
    }
    
    .form-group label {
        display: block;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .input-style {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 0.5rem;
        outline: none;
        transition: all 0.2s ease-in-out;
    }

    .input-style:focus {
        border-color: #00aaff;
        box-shadow: 0 0 0 2px rgba(0, 170, 255, 0.2);
    }

    .file-input { display: none; }

    .file-input-label {
        display: inline-block;
        padding: 10px 20px;
        background-color: white;
        color: #00aaff;
        border: 1px solid #00aaff;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.3s ease;
        margin-bottom: 10px;
    }

    .file-input-label:hover {
        background-color: #00aaff;
        color: white;
    }

    .btn-primary {
        display: block;
        width: 100%;
        padding: 12px;
        background-color: #00aaff;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-top: 1rem;
        text-align: center;
    }
    .btn-primary:hover { background-color: #008ecc; }

    .btn-danger {
        background-color: #ff4d4d;
        color: white;
        padding: 10px 16px;
        border-radius: 0.5rem;
        font-weight: 500;
        border: none;
        cursor: pointer;
        width: 100%;
        text-align: center;
        margin-top: 1rem;
    }
    .btn-danger:hover { background-color: #e60000; }

    /* Adicione estas classes ao seu CSS principal */

    .card-summary {
        font-size: 1.25rem;
        font-weight: 600;
        color: #374151;
        cursor: pointer;
        list-style: none; /* Remove o marcador padrão (triângulo) */
        display: flex;
        align-items: center;
    }

    .card-summary::-webkit-details-marker {
        display: none; /* Remove o marcador no Chrome/Safari */
    }

    /* Adiciona um ícone de "seta" para indicar que é expansível */
    .card-summary::before {
        content: '▶';
        font-size: 0.7em;
        margin-right: 0.75rem;
        color: #00aaff;
        transition: transform 0.2s ease-in-out;
    }

    details[open] > summary::before {
        transform: rotate(90deg);
    }

    /* Estilos para validação de erro */
    .is-invalid {
        border-color: #dc3545 !important; /* Cor vermelha para o erro */
    }

    .is-invalid:focus {
        box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.25);
    }

    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }

    .card-summary.text-danger {
        color: #dc3545; /* Cor vermelha para o texto */
    }

    /* A seta do summary também fica vermelha */
    .card-summary.text-danger::before {
        color: #dc3545;
    }

    .form-text-warning {
        font-size: 0.9em;
        color: #666;
        line-height: 1.5;
        padding: 0.75rem;
        background-color: #fff3cd; /* Fundo amarelo claro para aviso */
        border-left: 4px solid #ffc107; /* Borda amarela de destaque */
        border-radius: 0.25rem;
    }

    /* --- Estilos para o Sistema de Abas --- */
    .tabs-nav {
        display: flex;
        position: relative;
        margin-bottom: 2rem;
        list-style: none;
        padding: 0;
        flex-wrap: wrap;
        border-bottom: none; /* remove a linha cinza geral */
    }

    .tab-link {
        padding: 0.75rem 1.5rem;
        cursor: pointer;
        font-weight: 500;
        color: #6b7280;
        text-decoration: none;
        white-space: nowrap;
        position: relative;
    }

    /* Linha cinza de fundo para todas as abas */
    .tab-link::after {
        content: '';
        position: absolute;
        bottom: 0; 
        left: 0;
        right: 0;
        height: 3px;
        background-color: #e5e7eb; /* linha cinza padrão */
    }

    /* Linha azul quando ativa, substitui a cinza */
    .tab-link.active::after {
        background-color: #00aaff; /* azul */
    }

    .mobile-tabs-container {
        display: none; /* Escondido por padrão (no desktop) */
        margin-bottom: 2rem;
    }
    .custom-select {
        position: relative;
    }
    .custom-select-trigger {
        width: 100%;
        padding: 0.8rem 1rem;
        font-size: 1rem;
        font-weight: 500;
        color: #374151;
        text-align: left;
        background-color: #fff;
        border: 1px solid #e0e6ed;
        border-radius: 0.5rem;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s ease;
    }
    .custom-select-trigger:focus, .custom-select.is-open .custom-select-trigger {
        outline: none;
        border-color: #00aaff;
        box-shadow: 0 0 0 2px rgba(0, 170, 255, 0.2);
    }
    .custom-select-trigger .arrow {
        transition: transform 0.2s ease;
    }
    .custom-select.is-open .custom-select-trigger .arrow {
        transform: rotate(180deg);
    }
    .custom-options {
        display: none; /* Escondido por padrão */
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        right: 0;
        background-color: #fff;
        border-radius: 0.5rem;
        border: 1px solid #e0e6ed;
        box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.1);
        z-index: 10;
        overflow: hidden;
    }
    .custom-select.is-open .custom-options {
        display: block; /* Mostra o menu quando aberto */
    }
    .custom-option {
        display: block;
        padding: 0.8rem 1rem;
        color: #374151;
        text-decoration: none;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    .custom-option:hover {
        background-color: #f3f4f6;
    }
    .custom-option.is-selected {
        background-color: #e6f7ff;
        color: #00aaff;
        font-weight: 600;
    }

    /* Modifique sua media query existente para isto */
    @media (max-width: 768px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }
        .profile-container {
            padding: 1rem;
        }

        /* Esconde as abas de desktop e mostra o select de mobile */
        .tabs-nav-container {
            display: none;
        }
        .mobile-tabs-container {
            display: block;
        }
    }
</style>

<div class="profile-container">
    <h2 class="section-title">Editar Perfil</h2>

    <nav class="tabs-nav-container">
        <ul class="tabs-nav">
            <li>
                <a href="{{ route('profile.edit') }}" 
                   class="tab-link {{ !request('tab') || request('tab') == 'perfil' ? 'active' : '' }}">
                   Perfil
                </a>
            </li>
            <li>
                <a href="{{ route('profile.edit', ['tab' => 'pagina-publica']) }}" 
                   class="tab-link {{ request('tab') == 'pagina-publica' ? 'active' : '' }}">
                   Página Pública
                </a>
            </li>
            <li>
                <a href="{{ route('profile.edit', ['tab' => 'seguranca']) }}" 
                   class="tab-link {{ request('tab') == 'seguranca' ? 'active' : '' }}">
                   Segurança
                </a>
            </li>
            <li>
                <a href="{{ route('profile.edit', ['tab' => 'conta']) }}" 
                   class="tab-link {{ request('tab') == 'conta' ? 'active' : '' }}">
                   Conta
                </a>
            </li>
        </ul>
    </nav>
    
    <div class="mobile-tabs-container">
        @php
            $tabs = [
                'perfil' => 'Perfil',
                'pagina-publica' => 'Página Pública',
                'seguranca' => 'Segurança',
                'conta' => 'Conta'
            ];
            $currentTabText = $tabs[request('tab', 'perfil')] ?? 'Perfil';
        @endphp
        <div class="custom-select" id="custom-mobile-tabs">
            <button class="custom-select-trigger" type="button">
                <span>{{ $currentTabText }}</span>
                <svg class="arrow" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 5L8 11L14 5" stroke="#6B7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div class="custom-options">
                <a href="{{ route('profile.edit') }}" class="custom-option {{ (request('tab', 'perfil') == 'perfil') ? 'is-selected' : '' }}">Perfil</a>
                <a href="{{ route('profile.edit', ['tab' => 'pagina-publica']) }}" class="custom-option {{ request('tab') == 'pagina-publica' ? 'is-selected' : '' }}">Página Pública</a>
                <a href="{{ route('profile.edit', ['tab' => 'seguranca']) }}" class="custom-option {{ request('tab') == 'seguranca' ? 'is-selected' : '' }}">Segurança</a>
                <a href="{{ route('profile.edit', ['tab' => 'conta']) }}" class="custom-option {{ request('tab') == 'conta' ? 'is-selected' : '' }}">Conta</a>
            </div>
        </div>
    </div>

    @php $currentTab = request('tab', 'perfil'); @endphp

    {{-- ABA: PERFIL --}}
    @if ($currentTab == 'perfil')
        <div class="profile-grid">
            {{-- Coluna da Esquerda --}}
            <div class="profile-sidebar">
                <div class="profile-card photo-card">
                    <img id="preview" src="{{ auth()->user()->profile_photo_url }}" class="profile-photo" alt="Foto de Perfil" />
                    
                    <form id="photo-upload-form" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label for="photo" class="file-input-label">
                            Escolher nova foto
                            <input type="file" name="photo" id="photo" accept="image/*" class="file-input" onchange="previewAndUploadPhoto()">
                        </label>
                    </form>

                    <form method="POST" action="{{ route('profile.photo.delete') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger">Remover Foto</button>
                    </form>
                </div>
            </div>
            {{-- Coluna da Direita --}}
            <div class="profile-main-content">
                <div class="profile-card">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>
    @endif

    {{-- ABA: PÁGINA PÚBLICA --}}
    @if ($currentTab == 'pagina-publica')
        <div class="profile-card">
            {{-- Assumi que o nome do seu partial é landing-page-form. Ajuste se for diferente --}}
            @include('profile.partials.landing-page-form')
        </div>
    @endif

    {{-- ABA: SEGURANÇA --}}
    @if ($currentTab == 'seguranca')
        <div class="profile-card">
            @include('profile.partials.update-password-form')
        </div>
    @endif

    {{-- ABA: CONTA --}}
    @if ($currentTab == 'conta')
        <div class="profile-card">
            @include('profile.partials.delete-user-form')
        </div>
    @endif

</div>

<script>
    function previewAndUploadPhoto() {
        const input = document.getElementById('photo');
        const preview = document.getElementById('preview');
        const form = document.getElementById('photo-upload-form');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => preview.src = e.target.result;
            reader.readAsDataURL(input.files[0]);

            const formData = new FormData(form);
            fetch("{{ route('profile.update.photo') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error();
                return response.json();
            })
            .then(response => {
                Swal.fire('Foto atualizada!', 'Sua nova imagem foi salva com sucesso.', 'success');
                preview.src = response.url + '?t=' + new Date().getTime();

                const menuPhoto = document.querySelector('.sidebar-profile-photo');
                if (menuPhoto) {
                    menuPhoto.src = response.url + '?t=' + new Date().getTime();
                }
            })
            .catch(() => {
                Swal.fire('Erro!', 'Não foi possível atualizar a foto.', 'error');
            });
        }
    }

    @if (session('status') === 'profile-updated')
        Swal.fire('Sucesso!', 'Perfil atualizado com sucesso!', 'success');
    @elseif (session('status') === 'password-updated')
        Swal.fire('Senha Atualizada!', 'Sua nova senha foi salva.', 'success');
    @elseif (session('status') === 'photo-updated')
        Swal.fire('Foto Atualizada!', 'Sua imagem de perfil foi atualizada.', 'success');
    @elseif (session('status') === 'slug-updated')
        Swal.fire('Link Atualizado!', 'O link público do seu perfil foi atualizado.', 'success');
    @endif

    document.addEventListener('DOMContentLoaded', function() {
        const customSelect = document.getElementById('custom-mobile-tabs');
        if (customSelect) {
            const trigger = customSelect.querySelector('.custom-select-trigger');
            
            trigger.addEventListener('click', function() {
                customSelect.classList.toggle('is-open');
            });

            // Fecha o dropdown se clicar fora dele
            window.addEventListener('click', function(e) {
                if (!customSelect.contains(e.target)) {
                    customSelect.classList.remove('is-open');
                }
            });
        }
    });
</script>
@endsection
