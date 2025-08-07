@extends('layouts.app')

@section('title', 'Sucesso! | PsiGestor')

@section('content')
<div class="container py-5 text-center">
    <div class="alert alert-success shadow-sm rounded-4 p-4">
        <i class="bi bi-check-circle-fill fs-1 text-success"></i>
        <h2 class="mt-3">Assinatura confirmada com sucesso!</h2>
        <p class="lead mt-2">Bem-vindo(a) ao PsiGestor Premium. Você agora tem acesso completo à plataforma.</p>
        <a href="{{ route('dashboard') }}" class="btn btn-success mt-3">
            Ir para o painel
        </a>
    </div>
</div>
@endsection
