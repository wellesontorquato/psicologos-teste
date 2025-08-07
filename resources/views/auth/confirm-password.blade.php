@extends('layouts.auth-layout')

@section('title', 'Confirmar Senha | PsiGestor')

@section('form')
    <div class="text-muted mb-4 text-sm">
        Esta é uma área segura do sistema. Confirme sua senha antes de continuar.
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-3 position-relative">
            <label for="password" class="form-label">Senha</label>
            <div class="position-relative">
                <input id="password" type="password" name="password"
                    class="form-control rounded shadow-sm pe-5" required autocomplete="current-password">
                <span onclick="togglePassword('password', 'togglePasswordIcon')"
                      class="position-absolute top-50 end-0 translate-middle-y me-3"
                      style="cursor: pointer;">
                    <i id="togglePasswordIcon" class="fa-solid fa-eye-slash text-muted"></i>
                </span>
            </div>
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary rounded shadow">Confirmar</button>
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
</script>
@endsection
