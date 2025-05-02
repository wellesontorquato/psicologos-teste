@extends('layouts.app')

@section('title', 'Assinatura Cancelada | PsiGestor')

@section('content')
<div class="container py-5 text-center">
    <div class="alert alert-warning shadow-sm rounded-4 p-4">
        <i class="bi bi-exclamation-triangle-fill fs-1 text-warning"></i>
        <h2 class="mt-3">Assinatura cancelada</h2>
        <p class="lead mt-2">Você cancelou o processo de assinatura. Se quiser tentar novamente, é só escolher um plano.</p>
        <a href="{{ route('assinaturas.index') }}" class="btn btn-warning text-white mt-3">
            Escolher um plano
        </a>
    </div>
</div>
@endsection
