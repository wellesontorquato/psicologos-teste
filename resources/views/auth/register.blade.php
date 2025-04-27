@extends('layouts.auth-layout')

@section('title', 'Cadastrar-se')

@section('form')
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="row g-3">
            <div class="col-12">
                <div class="form-floating">
                    <input type="text" name="name" id="name" class="form-control rounded-xl" placeholder="Nome" value="{{ old('name') }}" required>
                    <label for="name">Nome</label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-floating">
                    <input type="email" name="email" id="email" class="form-control rounded-xl" placeholder="E-mail" value="{{ old('email') }}" required>
                    <label for="email">E-mail</label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-floating">
                    <input type="date" name="data_nascimento" id="data_nascimento" class="form-control rounded-xl" value="{{ old('data_nascimento') }}" required>
                    <label for="data_nascimento">Data de nascimento</label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" name="cpf" id="cpf" class="form-control rounded-xl" placeholder="CPF" value="{{ old('cpf') }}" required maxlength="14">
                    <label for="cpf">CPF</label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" name="crp" id="crp" class="form-control rounded-xl" placeholder="CRP" value="{{ old('crp') }}" required>
                    <label for="crp">CRP</label>
                </div>
            </div>

            <div class="col-12">
                <div class="form-floating">
                    <select name="genero" id="genero" class="form-select rounded-xl" required>
                        <option value="" disabled {{ old('genero') == '' ? 'selected' : '' }}>Selecione</option>
                        <option value="masculino" {{ old('genero') == 'masculino' ? 'selected' : '' }}>Masculino</option>
                        <option value="feminino" {{ old('genero') == 'feminino' ? 'selected' : '' }}>Feminino</option>
                        <option value="nao_binario" {{ old('genero') == 'nao_binario' ? 'selected' : '' }}>Não-binário</option>
                        <option value="outro" {{ old('genero') == 'outro' ? 'selected' : '' }}>Outro</option>
                        <option value="prefiro_nao_dizer" {{ old('genero') == 'prefiro_nao_dizer' ? 'selected' : '' }}>Prefiro não dizer</option>
                    </select>
                    <label for="genero">Gênero</label>
                </div>
            </div>

            <div class="col-md-6 position-relative">
                <div class="form-floating">
                    <input type="password" name="password" id="password" class="form-control rounded-xl pe-5" placeholder="Senha" required>
                    <label for="password">Senha</label>
                </div>
                <span onclick="togglePassword('password', 'togglePasswordIcon1')"
                    class="position-absolute top-50 end-0 translate-middle-y me-3"
                    style="cursor: pointer; z-index: 10;">
                    <i id="togglePasswordIcon1" class="fa-solid fa-eye-slash text-muted"></i>
                </span>
            </div>

            <div class="col-md-6 position-relative">
                <div class="form-floating">
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control rounded-xl pe-5" placeholder="Confirmar Senha" required>
                    <label for="password_confirmation">Confirmar Senha</label>
                </div>
                <span onclick="togglePassword('password_confirmation', 'togglePasswordIcon2')"
                    class="position-absolute top-50 end-0 translate-middle-y me-3"
                    style="cursor: pointer; z-index: 10;">
                    <i id="togglePasswordIcon2" class="fa-solid fa-eye-slash text-muted"></i>
                </span>
            </div>

            <div class="col-12 d-grid mt-3">
                <button type="submit" class="btn btn-primary btn-lg shadow rounded-xl">Cadastrar</button>
            </div>

            <div class="text-center text-muted mt-2">
                Já tem uma conta? <a href="{{ route('login') }}" class="auth-link">Entrar</a>
            </div>
        </div>
@endsection

@section('scripts')
<script>
    document.getElementById('cpf').addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        e.target.value = value;
    });
</script>
<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        const isPassword = input.type === 'password';

        input.type = isPassword ? 'text' : 'password';
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    }

    document.addEventListener('DOMContentLoaded', function () {
        const password = document.getElementById('password');
        const confirm = document.getElementById('password_confirmation');
        const submitBtn = document.querySelector('button[type="submit"]');

        const errorDiv = document.createElement('div');
        errorDiv.classList.add('text-danger', 'mt-1', 'small');
        errorDiv.innerText = 'As senhas não coincidem';
        errorDiv.style.display = 'none';
        confirm.parentNode.appendChild(errorDiv);

        function validarSenhas() {
            if (confirm.value.length > 0 && password.value === confirm.value) {
            confirm.classList.remove('is-invalid');
            errorDiv.style.display = 'none';
            submitBtn.disabled = false;
        } else {
            confirm.classList.add('is-invalid');
            errorDiv.style.display = 'block';
            submitBtn.disabled = true;
        }

        password.addEventListener('input', validarSenhas);
        confirm.addEventListener('input', validarSenhas);
    });
</script>
@if ($errors->any())
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Erro no cadastro',
            html: `{!! implode('<br>', $errors->all()) !!}`,
            confirmButtonText: 'Corrigir'
        });
    </script>
@endif
@endsection
