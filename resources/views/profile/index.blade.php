@extends('layouts.app')

@section('content')
<style>
    .profile-container {
        max-width: 640px;
        margin: 0 auto;
        padding: 2rem;
        background-color: white;
        border-radius: 16px;
        box-shadow: 0 0 30px rgba(0, 0, 0, 0.05);
    }

    .profile-photo {
        width: 96px;
        height: 96px;
        border-radius: 9999px;
        object-fit: cover;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        border: 3px solid #00aaff;
        margin: 0 auto 1rem auto;
        display: block;
    }

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

    .file-input {
        display: none;
    }

    .form-button,
    .btn-primary {
        display: block;
        width: 100%;
        padding: 10px;
        background-color: #00aaff;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-top: 10px;
        text-align: center;
    }

    .form-button:hover,
    .btn-primary:hover {
        background-color: #008ecc;
    }

    .btn-danger {
        background-color: #ff4d4d;
        color: white;
        padding: 10px 16px;
        border-radius: 0.5rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        justify-content: center;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: bold;
        text-align: center;
        margin-bottom: 1.5rem;
        color: #1f2937;
    }

    .form-section {
        margin-bottom: 2.5rem;
        text-align: center;
        background-color: #fff;
        padding: 20px;
        border-radius: 1rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group input {
        width: 100%;
        padding: 10px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 0.95rem;
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
</style>

<div class="profile-container">
    <h2 class="section-title">Editar Perfil</h2>

    <!-- Foto de Perfil -->
    <div class="form-section text-center">
        <img id="preview"
            src="{{ auth()->user()->profile_photo_url }}"
            class="profile-photo"
            alt="Foto de Perfil" />


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
            <button type="submit" class="btn-danger mt-2">Remover Foto</button>
        </form>
    </div>

    <!-- Informações -->
    @include('profile.partials.update-profile-information-form')

    <!-- Senha -->
    @include('profile.partials.update-password-form')

    <!-- Excluir Conta -->
    @include('profile.partials.delete-user-form')
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
                'Accept': 'application/json' // 👈 Ajuda Laravel a entender que queremos JSON
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) throw new Error();
            return response.json();
        })
        .then(response => {
            Swal.fire('Foto atualizada!', 'Sua nova imagem foi salva com sucesso.', 'success');
            // Força o recarregamento da imagem sem reload da página
            preview.src = response.url + '?t=' + new Date().getTime();
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
    @endif
</script>
@endsection
