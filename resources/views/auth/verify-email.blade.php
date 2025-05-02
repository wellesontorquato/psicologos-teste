@extends('layouts.auth-layout')

@section('title', 'Verifique seu E-mail | PsiGestor')

@section('form')
    <div class="text-muted text-center mb-4">
        Obrigado por se cadastrar! Antes de começar, verifique seu e-mail clicando no link que acabamos de enviar.
        <br>Não recebeu o e-mail? Podemos enviar novamente.
    </div>

    @if (session('status') === 'verification-link-sent')
        <div class="alert alert-success text-center">
            Um novo link de verificação foi enviado para o e-mail informado no cadastro.
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="mb-3">
        @csrf
        <div class="d-grid">
            <button type="submit" class="btn btn-primary rounded shadow">
                Reenviar e-mail de verificação
            </button>
        </div>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <div class="d-grid">
            <button type="submit" class="btn btn-outline-secondary rounded shadow">
                Sair da conta
            </button>
        </div>
    </form>
@endsection
