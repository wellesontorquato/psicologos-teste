@extends('layouts.app')

@section('title', 'Assinaturas | PsiGestor')

@section('content')
<div class="container py-5">
    <h2 class="mb-5 text-center fw-bold">Escolha seu plano de assinatura</h2>

    <div class="row justify-content-center gy-4">
        <!-- Plano Mensal -->
        <div class="col-md-4">
            <div class="card border-primary shadow h-100">
                <div class="card-header bg-primary text-white text-center fw-semibold">
                    Plano Mensal
                </div>
                <div class="card-body text-center">
                    <h3 class="card-title mb-3">R$ 39,90</h3>
                    <p class="text-muted">Assinatura mensal recorrente com <strong>10 dias grátis</strong></p>
                    <form action="{{ route('assinatura.checkout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="price_id" value="{{ $precos['mensal'] }}">
                        <button class="btn btn-primary w-100">Assinar agora</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Plano Trimestral -->
        <div class="col-md-4">
            <div class="card border-success shadow h-100">
                <div class="card-header bg-success text-white text-center fw-semibold">
                    Plano Trimestral
                </div>
                <div class="card-body text-center">
                    <h3 class="card-title mb-3">R$ 104,90</h3>
                    <p class="text-muted">Economize <strong>12,36%(R$ 14,80)</strong> com pagamento trimestral recorrente</p>
                    <form action="{{ route('assinatura.checkout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="price_id" value="{{ $precos['trimestral'] }}">
                        <button class="btn btn-success w-100">Assinar agora</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Plano Anual -->
        <div class="col-md-4">
            <div class="card border-warning shadow h-100">
                <div class="card-header bg-warning text-white text-center fw-semibold">
                    Plano Anual
                </div>
                <div class="card-body text-center">
                    <h3 class="card-title mb-3">R$ 374,90</h3>
                    <p class="text-muted">Economize <strong>21,71%(R$ 103,90)</strong> por ano, assinatura recorrente</p>
                    <form action="{{ route('assinatura.checkout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="price_id" value="{{ $precos['anual'] }}">
                        <button class="btn btn-warning text-white w-100">Assinar agora</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
