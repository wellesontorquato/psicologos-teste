@extends('layouts.app')

@section('title', 'Minha Assinatura | PsiGestor')

@section('content')
<div class="container py-5">
    <h2 class="mb-5 fw-bold text-center">🧾 Minha Assinatura</h2>

    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- CARD DE DETALHES --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <span class="fw-semibold text-primary">📄 Detalhes da Assinatura</span>

                    @php
                        $status = $assinatura->stripe_status;
                        $badgeClass = match($status) {
                            'active' => 'bg-success',
                            'canceled' => 'bg-danger',
                            default => 'bg-secondary'
                        };
                        $statusTraduzido = match($status) {
                            'active' => 'Ativa',
                            'canceled' => 'Cancelada',
                            default => ucfirst($status)
                        };
                        $nomesPlanos = [
                            'price_1RVxueC1nNYXXNDRXZRHr2N3' => 'Plano Mensal',
                            'price_1RVxv5C1nNYXXNDRYJlrrwG5' => 'Plano Trimestral',
                            'price_1RVxvdC1nNYXXNDR2URxfXFz' => 'Plano Anual',
                        ];
                    @endphp
                    <span class="badge rounded-pill px-3 py-2 text-white {{ $badgeClass }}">
                        {{ $statusTraduzido }}
                    </span>
                </div>
                <div class="card-body">

                    {{-- Plano --}}
                    <p class="mb-2"><strong>📌 Plano:</strong> {{ $nomesPlanos[$assinatura->stripe_price] ?? 'Desconhecido' }}</p>

                    {{-- Trial --}}
                    @if($assinatura->onTrial())
                        <p class="mb-2"><strong>🎁 Período de Teste até:</strong> {{ $assinatura->trial_ends_at->format('d/m/Y') }}</p>
                    @endif

                    {{-- Fim da assinatura --}}
                    @if($assinatura->ends_at)
                        <p class="mb-2 text-danger"><strong>📆 Assinatura se encerra em:</strong> {{ $assinatura->ends_at->format('d/m/Y') }}</p>
                    @endif

                    <hr class="my-4">

                    {{-- CANCELAR OU REATIVAR ASSINATURA --}}
                    @if($assinatura->ends_at && $assinatura->onGracePeriod())
                        {{-- Alerta de encerramento --}}
                        <div class="alert alert-warning d-flex align-items-center gap-2 mb-3" role="alert">
                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                            <div>
                                ⚠️ Sua assinatura está programada para encerrar em 
                                <strong>{{ $assinatura->ends_at->format('d/m/Y') }}</strong>, 
                                mas você pode reativá-la agora para não perder o acesso.
                            </div>
                        </div>

                        {{-- Botão de Reativar --}}
                        <form class="form-reativarassinatura no-spinner" action="{{ route('assinatura.reativar') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-arrow-repeat fs-5"></i> Reativar Assinatura
                            </button>
                        </form>
                    @else
                        {{-- Botão de Cancelar --}}
                        <form class="form-cancelarassinatura no-spinner" action="{{ route('assinatura.cancelar') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-x-circle-fill fs-5"></i> Cancelar Assinatura
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- HISTÓRICO DE PAGAMENTOS --}}
            @if($faturas && count($faturas) > 0)
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <strong>💰 Histórico de Pagamentos</strong>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped table-hover align-middle text-center m-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Data</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Recibo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($faturas as $fatura)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($fatura->created)->format('d/m/Y') }}</td>
                                        <td>R$ {{ number_format($fatura->amount_paid / 100, 2, ',', '.') }}</td>
                                        <td>
                                            <span class="badge px-3 py-2 rounded-pill {{ $fatura->paid ? 'bg-success' : 'bg-danger' }}">
                                                {{ $fatura->paid ? 'Pago' : 'Falhou' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ $fatura->invoice_pdf }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-receipt-cutoff me-1"></i> Ver Recibo
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
    </div>
</div>

{{-- MODAL DO CARTÃO --}}
<div class="modal fade" id="modalCartao" tabindex="-1" aria-labelledby="modalCartaoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalCartaoLabel"><i class="bi bi-shield-lock-fill me-2"></i> Gerenciar Cartão</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <p>Você será redirecionado para o portal seguro da Stripe, onde poderá:</p>
        <ul>
          <li>Alterar o cartão atual</li>
          <li>Adicionar novo método de pagamento</li>
          <li>Remover cartão existente</li>
        </ul>
        <p class="text-muted small">Essa operação é 100% segura e feita diretamente na plataforma Stripe.</p>
      </div>
      <div class="modal-footer">
        <a href="{{ route('billing.portal') }}" class="btn btn-primary">
            <i class="bi bi-box-arrow-up-right me-1"></i> Acessar Portal da Stripe
        </a>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Cancelar assinatura
    document.querySelectorAll('.form-cancelarassinatura').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Tem certeza?',
                text: "Você perderá o acesso ao sistema ao cancelar a assinatura.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
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
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#3085d6',
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
</script>
@endpush
