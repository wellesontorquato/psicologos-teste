@extends('layouts.auth-layout')

@section('title', 'Redefinir Senha | PsiGestor')

@section('form')
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Token oculto -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- E-mail -->
        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input id="email" type="email" name="email" class="form-control rounded shadow-sm"
                value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
        </div>

        <!-- Nova Senha -->
        <div class="mb-3 position-relative">
            <label for="password" class="form-label">Nova Senha</label>
            <div class="position-relative">
                <input id="password" type="password" name="password"
                    class="form-control rounded shadow-sm pe-5" required autocomplete="new-password">
                <span onclick="togglePassword('password', 'togglePasswordIcon1')"
                    class="position-absolute top-50 end-0 translate-middle-y me-3" style="cursor: pointer;">
                    <i id="togglePasswordIcon1" class="fa-solid fa-eye-slash text-muted"></i>
                </span>
            </div>
        </div>

        <!-- Confirmar Senha -->
        <div class="mb-3 position-relative">
            <label for="password_confirmation" class="form-label">Confirmar Senha</label>
            <div class="position-relative">
                <input id="password_confirmation" type="password" name="password_confirmation"
                    class="form-control rounded shadow-sm pe-5" required autocomplete="new-password">
                <span onclick="togglePassword('password_confirmation', 'togglePasswordIcon2')"
                    class="position-absolute top-50 end-0 translate-middle-y me-3" style="cursor: pointer;">
                    <i id="togglePasswordIcon2" class="fa-solid fa-eye-slash text-muted"></i>
                </span>
                <div id="password-match-message" class="form-text ms-1 mt-1 text-danger d-none">
                    As senhas não coincidem.
                </div>
            </div>
        </div>

        <!-- Botão -->
        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary rounded shadow" id="submitBtn">
                Redefinir Senha
            </button>
        </div>
    </form>
@endsection

@section('scripts')
<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        const isPassword = input.type === 'password';

        input.type = isPassword ? 'text' : 'password';
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    }

    // Validação de senha em tempo real
    document.addEventListener('DOMContentLoaded', function () {
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('password_confirmation');
        const message = document.getElementById('password-match-message');
        const submitBtn = document.getElementById('submitBtn');

        function checkPasswordMatch() {
            if (confirmPassword.value && password.value !== confirmPassword.value) {
                message.classList.remove('d-none');
                submitBtn.disabled = true;
            } else {
                message.classList.add('d-none');
                submitBtn.disabled = false;
            }
        }

        password.addEventListener('input', checkPasswordMatch);
        confirmPassword.addEventListener('input', checkPasswordMatch);
    });
</script>
@endsection
