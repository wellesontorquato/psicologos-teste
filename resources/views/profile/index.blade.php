@extends('layouts.app')
@section('title', 'Perfil | PsiGestor')
@section('content')
<style>
    /* Novo container principal com largura maior */
    .profile-container {
        max-width: 1024px; /* Aumentado para acomodar duas colunas */
        margin: 2rem auto;
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
</style>

<div class="profile-container">
    <h2 class="section-title">Editar Perfil</h2>

    <div class="profile-grid">
        
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
            
            <div class="profile-card">
                @include('profile.partials.delete-user-form')
            </div>
        </div>

        <div class="profile-main-content">
            <div class="profile-card">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="profile-card">
                @include('profile.partials.public-page-form')
            </div>

            <div class="profile-card">
                @include('profile.partials.update-password-form')
            </div>
        </div>

    </div>
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
</script>
@endsection
