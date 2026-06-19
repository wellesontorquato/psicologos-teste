@extends('layouts.app')

@section('title', 'Minha Assinatura | PsiGestor')

@section('content')
<style>
    .sub-page {
        width: 100%;
        max-width: 900px;
        margin: 0 auto;
        padding: 20px 15px;
    }

    .sub-header {
        text-align: center;
        margin-bottom: 32px;
    }

    .sub-title {
        font-size: 2rem;
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 8px;
        letter-spacing: -0.5px;
    }

    .sub-subtitle {
        color: #64748b;
        font-size: 1.05rem;
    }

    .sub-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
        padding: 24px;
        margin-bottom: 24px;
    }

    .sub-card-header {
        display: flex;
        flex-direction: column;
        gap: 12px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 16px;
        margin-bottom: 20px;
    }

    .sub-card-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sub-badge {
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: fit-content;
    }

    .badge-gray { background: #f1f5f9; color: #475569; }
    .badge-green { background: #dcfce7; color: #166534; }
    .badge-yellow { background: #fef3c7; color: #92400e; }
    .badge-red { background: #fee2e2; color: #b91c1c; }

    /* Grid de Informações */
    .sub-info-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
        margin-bottom: 24px;
    }

    .sub-info-box {
        background: #f8fafc;
        border-radius: 14px;
        padding: 16px 20px;
        border: 1px solid #f1f5f9;
    }

    .sub-info-label {
        font-size: 0.8rem;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
        display: block;
    }

    .sub-info-val {
        font-size: 1.1rem;
        font-weight: 800;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sub-info-val i {
        color: #2563eb;
        font-size: 1.2rem;
    }

    /* Alertas */
    .sub-alert {
        padding: 16px 20px;
        border-radius: 14px;
        display: flex;
        align-items: flex-start;
        gap: 14px;
        font-size: 0.95rem;
        font-weight: 600;
        margin-bottom: 24px;
        line-height: 1.5;
    }

    .sub-alert i { font-size: 1.3rem; margin-top: -2px; }
    .alert-blue { background: #eff6ff; color: #1e3a8a; border: 1px solid #bfdbfe; }
    .alert-yellow { background: #fefce8; color: #854d0e; border: 1px solid #fef08a; }
    .alert-red { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

    /* Botões */
    .sub-actions {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .sub-btn {
        min-height: 48px;
        border-radius: 14px;
        font-weight: 800;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 20px;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        width: 100%;
        text-decoration: none;
    }

    .btn-primary-custom { background: #2563eb; color: #fff; box-shadow: 0 4px 12px rgba(37,99,235,0.2); }
    .btn-primary-custom:hover { background: #1d4ed8; color: #fff; transform: translateY(-1px); }
    
    .btn-success-custom { background: #10b981; color: #fff; box-shadow: 0 4px 12px rgba(16,185,129,0.2); }
    .btn-success-custom:hover { background: #059669; color: #fff; transform: translateY(-1px); }
    
    .btn-danger-outline { background: #fff; border: 2px solid #fee2e2; color: #ef4444; }
    .btn-danger-outline:hover { background: #fef2f2; border-color: #fca5a5; }

    .btn-outline-custom { background: #fff; border: 2px solid #e2e8f0; color: #475569; }
    .btn-outline-custom:hover { background: #f8fafc; border-color: #cbd5e1; color: #0f172a; }

    /* Tabela de Histórico */
    .sub-table-wrapper {
        overflow-x: auto;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
    }

    .sub-table {
        width: 100%;
        min-width: 600px;
        border-collapse: collapse;
        margin: 0;
    }

    .sub-table th {
        background: #f8fafc;
        padding: 14px 20px;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #e2e8f0;
    }

    .sub-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.95rem;
        color: #0f172a;
        font-weight: 600;
        vertical-align: middle;
    }

    .sub-table tr:hover td { background: #f8fafc; }
    .sub-table tr:last-child td { border-bottom: none; }

    .btn-receipt {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.85rem;
        font-weight: 700;
        color: #2563eb;
        background: #eff6ff;
        padding: 6px 12px;
        border-radius: 10px;
        text-decoration: none;
        transition: background 0.2s;
    }
    .btn-receipt:hover { background: #dbeafe; color: #1d4ed8; }

    @media (min-width: 768px) {
        .sub-card-header {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }
        .sub-info-grid {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }
        .sub-actions {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<div class="container-fluid sub-page">
    <div class="sub-header">
        <h2 class="sub-title">Minha Assinatura</h2>
        <p class="sub-subtitle">Gerencie seu plano, método de pagamento e visualize seu histórico financeiro.</p>
    </div>

    {{-- ✅ CASO NÃO TENHA ASSINATURA (trial / pós-trial / nunca assinou) --}}
    @if(!$assinatura)
        <div class="sub-card">
            <div class="sub-card-header">
                <h3 class="sub-card-title"><i class="bi bi-file-earmark-text text-primary"></i> Detalhes da Assinatura</h3>
                <span class="sub-badge badge-gray">Sem assinatura</span>
            </div>

            @if(auth()->user()->onTrial())
                <div class="sub-alert alert-blue">
                    <i class="bi bi-gift-fill"></i>
                    <div>
                        Você está aproveitando o <strong>período de teste gratuito</strong>. Para garantir acesso ininterrupto após o teste, escolha um plano agora.
                    </div>
                </div>
            @else
                <div class="sub-alert alert-yellow">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>
                        Você <strong>não possui</strong> uma assinatura ativa no momento. Escolha um plano para voltar a utilizar a plataforma.
                    </div>
                </div>
            @endif

            <a href="{{ route('assinaturas.index') }}" class="sub-btn btn-primary-custom w-100">
                <i class="bi bi-bag-check-fill"></i> Ver planos e assinar
            </a>
        </div>

    @else
        {{-- ✅ CASO TENHA ASSINATURA (ativa/cancelada/expirada) --}}
        @php
            $agora = now();
            $expirada = $assinatura->ends_at && $agora->gte($assinatura->ends_at);

            $nomesPlanos = [
                'price_1RVxueC1nNYXXNDRXZRHr2N3' => 'Plano Mensal',
                'price_1RVxv5C1nNYXXNDRYJlrrwG5' => 'Plano Trimestral',
                'price_1RVxvdC1nNYXXNDR2URxfXFz' => 'Plano Anual',
            ];

            if ($expirada) {
                $badgeClass = 'badge-red';
                $statusTraduzido = 'Expirada';
            } elseif ($assinatura->onGracePeriod() || $assinatura->canceled() || $assinatura->ends_at) {
                $badgeClass = 'badge-yellow';
                $statusTraduzido = 'Cancelada';
            } else {
                $badgeClass = 'badge-green';
                $statusTraduzido = 'Ativa';
            }
        @endphp

        <div class="sub-card">
            <div class="sub-card-header">
                <h3 class="sub-card-title"><i class="bi bi-file-earmark-text text-primary"></i> Detalhes da Assinatura</h3>
                <span class="sub-badge {{ $badgeClass }}">{{ $statusTraduzido }}</span>
            </div>

            <div class="sub-info-grid">
                <div class="sub-info-box">
                    <span class="sub-info-label">Plano Atual</span>
                    <span class="sub-info-val"><i class="bi bi-star-fill"></i> {{ $nomesPlanos[$assinatura->stripe_price] ?? 'Desconhecido' }}</span>
                </div>

                @if($assinatura->onTrial())
                    <div class="sub-info-box">
                        <span class="sub-info-label">Período de Teste</span>
                        <span class="sub-info-val"><i class="bi bi-gift-fill text-success"></i> Até {{ optional($assinatura->trial_ends_at)->format('d/m/Y') }}</span>
                    </div>
                @endif

                @if($assinatura->ends_at)
                    <div class="sub-info-box">
                        <span class="sub-info-label">{{ $expirada ? 'Encerrada em' : 'Encerra em' }}</span>
                        <span class="sub-info-val {{ $expirada ? 'text-danger' : '' }}"><i class="bi bi-calendar-x {{ $expirada ? 'text-danger' : 'text-warning' }}"></i> {{ $assinatura->ends_at->format('d/m/Y') }}</span>
                    </div>
                @endif
            </div>

            {{-- AÇÕES E ALERTAS --}}
            @if($assinatura->onGracePeriod() && !$expirada)
                <div class="sub-alert alert-yellow">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>
                        Sua assinatura está programada para encerrar em <strong>{{ $assinatura->ends_at?->format('d/m/Y') }}</strong>. Você pode reativá-la agora para não perder o acesso.
                    </div>
                </div>

                <div class="sub-actions">
                    <form class="form-reativarassinatura no-spinner w-100" action="{{ route('assinatura.reativar') }}" method="POST">
                        @csrf
                        <button type="submit" class="sub-btn btn-success-custom w-100">
                            <i class="bi bi-arrow-repeat"></i> Reativar Assinatura
                        </button>
                    </form>
                    @if(auth()->user()->stripe_id)
                        <button type="button" class="sub-btn btn-outline-custom w-100" data-bs-toggle="modal" data-bs-target="#modalCartao">
                            <i class="bi bi-credit-card"></i> Gerenciar Cartão
                        </button>
                    @endif
                </div>

            @elseif($expirada)
                <div class="sub-alert alert-red">
                    <i class="bi bi-x-octagon-fill"></i>
                    <div>
                        Sua assinatura expirou. Para voltar a usar o sistema, selecione um novo plano.
                    </div>
                </div>

                <a href="{{ route('assinaturas.index') }}" class="sub-btn btn-primary-custom w-100">
                    <i class="bi bi-bag-check-fill"></i> Assinar novamente
                </a>

            @else
                <div class="sub-actions">
                    @if(auth()->user()->stripe_id)
                        <button type="button" class="sub-btn btn-outline-custom w-100" data-bs-toggle="modal" data-bs-target="#modalCartao">
                            <i class="bi bi-credit-card"></i> Gerenciar Cartão
                        </button>
                    @endif
                    <form class="form-cancelarassinatura no-spinner w-100" action="{{ route('assinatura.cancelar') }}" method="POST">
                        @csrf
                        <button type="submit" class="sub-btn btn-danger-outline w-100">
                            <i class="bi bi-x-circle-fill"></i> Cancelar Assinatura
                        </button>
                    </form>
                </div>
            @endif
        </div>
    @endif

    {{-- HISTÓRICO DE PAGAMENTOS --}}
    @if($faturas && count($faturas) > 0)
        <div class="sub-card">
            <div class="sub-card-header">
                <h3 class="sub-card-title"><i class="bi bi-receipt text-primary"></i> Histórico de Pagamentos</h3>
            </div>
            
            <div class="sub-table-wrapper">
                <table class="sub-table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th style="text-align: right;">Recibo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($faturas as $fatura)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($fatura->created)->format('d/m/Y') }}</td>
                                <td>R$ {{ number_format($fatura->amount_paid / 100, 2, ',', '.') }}</td>
                                <td>
                                    <span class="sub-badge {{ $fatura->paid ? 'badge-green' : 'badge-red' }}">
                                        {{ $fatura->paid ? 'Pago' : 'Falhou' }}
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ $fatura->invoice_pdf }}" target="_blank" class="btn-receipt">
                                        <i class="bi bi-download"></i> PDF
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>

{{-- MODAL DO CARTÃO --}}
@if(auth()->user()->stripe_id)
    <div class="modal fade" id="modalCartao" tabindex="-1" aria-labelledby="modalCartaoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.1);">
                <div class="modal-header border-0 pb-0 mt-3 px-4">
                    <h5 class="modal-title fw-bold text-dark" id="modalCartaoLabel">
                        <i class="bi bi-shield-lock text-primary me-2"></i> Gerenciar Cartão
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body px-4 pt-3 pb-4 text-muted">
                    <p class="mb-3" style="font-size: 0.95rem;">Você será redirecionado para o portal seguro da <strong>Stripe</strong>. Lá você poderá:</p>
                    <ul class="mb-4" style="font-size: 0.95rem; font-weight: 600; color: #334155; line-height: 1.8;">
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Alterar o cartão atual</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Adicionar novo método de pagamento</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Remover cartão existente</li>
                    </ul>
                    <div class="d-flex align-items-center gap-2 p-3 rounded-3" style="background: #f8fafc; font-size: 0.85rem;">
                        <i class="bi bi-lock-fill text-primary fs-5"></i>
                        <span>Operação 100% criptografada e segura, gerida diretamente pela Stripe.</span>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 d-flex flex-column gap-2">
                    <a href="{{ route('billing.portal') }}" class="sub-btn btn-primary-custom w-100">
                        Acessar Portal da Stripe <i class="bi bi-box-arrow-up-right ms-1"></i>
                    </a>
                    <button type="button" class="sub-btn btn-outline-custom w-100" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Cancelar assinatura
    document.querySelectorAll('.form-cancelarassinatura').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Tem certeza?',
                text: "Você continuará com acesso até o fim do período já pago. Deseja cancelar mesmo assim?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sim, cancelar',
                cancelButtonText: 'Manter assinatura'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.close();
                    setTimeout(() => {
                        const btn = form.querySelector('button[type="submit"]');
                        btn.disabled = true;
                        btn.innerHTML = 'Cancelando... <span class="spinner-border spinner-border-sm ms-2"></span>';
                        form.submit();
                    }, 300);
                }
            });
        });
    });

    // Reativar assinatura
    document.querySelectorAll('.form-reativarassinatura').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Reativar assinatura?',
                text: "Você continuará com acesso ao sistema normalmente.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sim, reativar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.close();
                    setTimeout(() => {
                        const btn = form.querySelector('button[type="submit"]');
                        btn.disabled = true;
                        btn.innerHTML = 'Reativando... <span class="spinner-border spinner-border-sm ms-2"></span>';
                        form.submit();
                    }, 300);
                }
            });
        });
    });
});
</script>
@endpush