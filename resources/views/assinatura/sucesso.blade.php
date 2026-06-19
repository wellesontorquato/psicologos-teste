@extends('layouts.app')

@section('title', 'Sucesso! | PsiGestor')

@section('content')
<style>
    .succ-page {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 70vh;
        padding: 20px 15px;
    }

    .succ-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 28px;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.06);
        padding: 48px 32px;
        max-width: 520px;
        width: 100%;
        text-align: center;
        animation: slideUp 0.5s ease-out forwards;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .succ-icon-wrapper {
        width: 88px;
        height: 88px;
        background: #dcfce7;
        color: #166534;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        margin: 0 auto 24px auto;
        box-shadow: 0 12px 28px rgba(22, 101, 52, 0.18);
    }

    .succ-title {
        font-size: 1.8rem;
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 14px;
        letter-spacing: -0.5px;
        line-height: 1.2;
    }

    .succ-desc {
        font-size: 1.05rem;
        color: #64748b;
        margin-bottom: 36px;
        line-height: 1.6;
    }

    .succ-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        min-height: 56px;
        border-radius: 16px;
        background: #2563eb;
        color: #ffffff;
        font-weight: 800;
        font-size: 1.05rem;
        text-decoration: none;
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.25);
        transition: all 0.2s ease;
    }

    .succ-btn:hover {
        background: #1d4ed8;
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(37, 99, 235, 0.35);
    }

    .succ-btn i {
        font-size: 1.2rem;
    }
</style>

<div class="container-fluid succ-page">
    <div class="succ-card">
        <div class="succ-icon-wrapper">
            <i class="bi bi-check-lg"></i>
        </div>
        
        <h2 class="succ-title">Assinatura confirmada com sucesso!</h2>
        
        <p class="succ-desc">
            Bem-vindo(a) ao <strong>PsiGestor Premium</strong>. Sua conta foi atualizada e você já tem acesso completo a todas as ferramentas da plataforma.
        </p>
        
        <a href="{{ route('dashboard') }}" class="succ-btn">
            Ir para o meu painel
            <i class="bi bi-arrow-right"></i>
        </a>
    </div>
</div>
@endsection