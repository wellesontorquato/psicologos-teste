@extends('layouts.auth-layout')

@section('title', 'Recuperar Senha | PsiGestor')

@section('form')
    @if (session('status'))
        <div class="alert alert-success text-center">
            {{ session('status') }}
        </div>
    @endif

    <div class="text-muted mb-4 text-center">
        Esqueceu sua senha? Sem problemas. Informe seu e-mail e enviaremos um link para redefinir.
    </div>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input id="email" type="email" name="email"
                class="form-control rounded shadow-sm"
                value="{{ old('email') }}" required autofocus>
            @error('email')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary rounded shadow">
                Enviar link de redefinição
            </button>
        </div>

        <div class="text-center text-muted mt-3">
            <a href="{{ route('login') }}" class="auth-link">Voltar para login</a>
        </div>
    </form>
@endsection
