@extends('layouts.app')

@section('title', 'Assinaturas | PsiGestor')

@section('content')
<style>
    .plan-page {
        width: 100%;
        max-width: 1100px;
        margin: 0 auto;
        padding: 20px 15px;
    }

    .plan-header {
        text-align: center;
        margin-bottom: 50px;
    }

    .plan-title {
        font-size: 2.2rem;
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 12px;
        letter-spacing: -0.5px;
    }

    .plan-subtitle {
        font-size: 1.1rem;
        color: #64748b;
        max-width: 600px;
        margin: 0 auto;
    }

    .plan-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 32px;
        align-items: center;
        padding-top: 16px;
    }

    .plan-card {
        background: #ffffff;
        border-radius: 24px;
        padding: 36px 28px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px rgba(15,23,42,0.04);
        position: relative;
        display: flex;
        flex-direction: column;
        height: 100%;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .plan-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(15,23,42,0.08);
    }

    /* Card Destaque (Anual) */
    .plan-card-highlight {
        border: 2px solid #2563eb;
        box-shadow: 0 20px 40px rgba(37,99,235,0.12);
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    }

    .plan-card-highlight:hover {
        box-shadow: 0 24px 48px rgba(37,99,235,0.18);
    }

    .plan-badge {
        position: absolute;
        top: -14px;
        left: 50%;
        transform: translateX(-50%);
        padding: 6px 16px;
        border-radius: 999px;
        font-size: .75rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
        z-index: 2;
    }

    .badge-free { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .badge-save { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .badge-best { background: #2563eb; color: #ffffff; box-shadow: 0 4px 12px rgba(37,99,235,0.3); }

    .plan-name {
        font-size: 1.2rem;
        font-weight: 800;
        margin-bottom: 16px;
        color: #0f172a;
        text-align: center;
    }

    .plan-price-box {
        text-align: center;
        margin-bottom: 24px;
    }

    .plan-price {
        font-size: 2.8rem;
        font-weight: 900;
        color: #0f172a;
        line-height: 1;
        letter-spacing: -1px;
    }

    .plan-currency {
        font-size: 1.2rem;
        font-weight: 700;
        vertical-align: super;
        color: #475569;
    }

    .plan-period {
        font-size: 1rem;
        color: #64748b;
        font-weight: 600;
    }

    .plan-desc-box {
        flex-grow: 1;
        margin-bottom: 32px;
        padding-top: 24px;
        border-top: 1px solid #e2e8f0;
    }

    .plan-desc-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 12px;
        color: #475569;
        font-size: .95rem;
        line-height: 1.5;
    }

    .plan-desc-item i {
        color: #2563eb;
        font-size: 1.1rem;
        margin-top: -1px;
    }

    .plan-desc-item strong {
        color: #0f172a;
        font-weight: 800;
    }

    .plan-btn {
        width: 100%;
        min-height: 52px;
        border-radius: 14px;
        font-weight: 800;
        font-size: 1.05rem;
        border: none;
        transition: all 0.2s ease;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
    }

    .btn-outline-custom {
        background: #f8fafc;
        color: #0f172a;
        border: 2px solid #e2e8f0;
    }

    .btn-outline-custom:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    .btn-primary-custom {
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(37,99,235,0.25);
    }

    .btn-primary-custom:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    @media (min-width: 992px) {
        .plan-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
        
        .plan-card-highlight {
            transform: scale(1.05);
            z-index: 10;
        }

        .plan-card-highlight:hover {
            transform: scale(1.05) translateY(-5px);
        }
    }
</style>

<div class="container-fluid plan-page">
    <div class="plan-header">
        <h2 class="plan-title">Escolha seu plano de assinatura</h2>
        <p class="plan-subtitle">Tenha acesso completo a todas as ferramentas do PsiGestor para organizar e alavancar a sua prática clínica.</p>
    </div>

    <div class="plan-grid">
        
        <div class="plan-card">
            <div class="plan-badge badge-free">
                <i class="bi bi-gift-fill me-1"></i> 10 dias grátis
            </div>
            
            <div class="plan-name">Plano Mensal</div>
            
            <div class="plan-price-box">
                <span class="plan-currency">R$</span><span class="plan-price">39,90</span>
                <span class="plan-period">/mês</span>
            </div>
            
            <div class="plan-desc-box">
                <div class="plan-desc-item">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Acesso completo ao sistema.</span>
                </div>
                <div class="plan-desc-item">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Renovação automática a cada 30 dias.</span>
                </div>
                <div class="plan-desc-item">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Cancele quando quiser.</span>
                </div>
            </div>
            
            <form action="{{ route('assinatura.checkout') }}" method="POST" class="mt-auto m-0 p-0">
                @csrf
                <input type="hidden" name="price_id" value="{{ $precos['mensal'] }}">
                <button type="submit" class="plan-btn btn-outline-custom">Assinar Mensal</button>
            </form>
        </div>

        <div class="plan-card">
            <div class="plan-badge badge-save">
                <i class="bi bi-tags-fill me-1"></i> Economize 12,36%
            </div>

            <div class="plan-name">Plano Trimestral</div>
            
            <div class="plan-price-box">
                <span class="plan-currency">R$</span><span class="plan-price">104,90</span>
                <span class="plan-period">/trimestre</span>
            </div>
            
            <div class="plan-desc-box">
                <div class="plan-desc-item">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Acesso completo ao sistema.</span>
                </div>
                <div class="plan-desc-item">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Renovação automática a cada 3 meses.</span>
                </div>
                <div class="plan-desc-item">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Desconto de <strong>R$ 14,80</strong> no trimestre comparado ao mensal.</span>
                </div>
            </div>
            
            <form action="{{ route('assinatura.checkout') }}" method="POST" class="mt-auto m-0 p-0">
                @csrf
                <input type="hidden" name="price_id" value="{{ $precos['trimestral'] }}">
                <button type="submit" class="plan-btn btn-outline-custom">Assinar Trimestral</button>
            </form>
        </div>

        <div class="plan-card plan-card-highlight">
            <div class="plan-badge badge-best">
                <i class="bi bi-star-fill me-1"></i> Mais Vantajoso
            </div>

            <div class="plan-name text-primary">Plano Anual</div>
            
            <div class="plan-price-box">
                <span class="plan-currency text-primary">R$</span><span class="plan-price text-primary">374,90</span>
                <span class="plan-period">/ano</span>
            </div>
            
            <div class="plan-desc-box border-primary border-opacity-25">
                <div class="plan-desc-item">
                    <i class="bi bi-check-circle-fill text-primary"></i>
                    <span>Acesso completo ao sistema.</span>
                </div>
                <div class="plan-desc-item">
                    <i class="bi bi-check-circle-fill text-primary"></i>
                    <span>Renovação automática anual.</span>
                </div>
                <div class="plan-desc-item">
                    <i class="bi bi-check-circle-fill text-primary"></i>
                    <span>Maior economia: poupe <strong>R$ 103,90 (21,71%)</strong> por ano.</span>
                </div>
            </div>
            
            <form action="{{ route('assinatura.checkout') }}" method="POST" class="mt-auto m-0 p-0">
                @csrf
                <input type="hidden" name="price_id" value="{{ $precos['anual'] }}">
                <button type="submit" class="plan-btn btn-primary-custom">Assinar Anual</button>
            </form>
        </div>

    </div>
</div>
@endsection