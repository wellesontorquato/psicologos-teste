@extends('layouts.auth-layout')

@section('title', 'Entrar | PsiGestor')

@section('form')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input id="email" type="email" name="email"
                   class="form-control rounded shadow-sm"
                   value="{{ old('email') }}" required
                   autofocus autocomplete="username">
        </div>

        <div class="mb-3 position-relative">
            <label for="password" class="form-label">Senha</label>
            <div class="position-relative">
                <input id="password" type="password" name="password"
                    class="form-control rounded shadow-sm pe-5"
                    required autocomplete="current-password">
                <span onclick="togglePassword()"
                    class="position-absolute top-50 end-0 translate-middle-y me-3"
                    style="cursor: pointer;">
                    <i id="togglePasswordIcon" class="fa-solid fa-eye-slash text-muted"></i>
                </span>
            </div>
        </div>

        <div class="mb-3 d-flex justify-content-between align-items-center">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                <label class="form-check-label" for="remember_me">Lembrar-me</label>
            </div>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="auth-link">Esqueceu a senha?</a>
            @endif
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary rounded shadow">Entrar</button>
        </div>

        <div class="text-center text-muted">
            Não tem uma conta?
            <a href="{{ route('register') }}" class="auth-link">Cadastrar-se</a>
        </div>
    </form>

    <!-- 🔐 SCRIPT para alternar visibilidade da senha -->
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }
    </script>

    <!-- 🧁 SweetAlert2 para erro de login -->
    @if ($errors->has('email'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Erro ao entrar',
            text: '{{ __("auth.failed") }}',
            confirmButtonText: 'OK'
        });
    </script>
    @endif
@endsection
