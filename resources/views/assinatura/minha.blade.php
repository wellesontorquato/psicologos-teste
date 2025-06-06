@extends('layouts.app')

@section('title', 'Minha Assinatura | PsiGestor')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 fw-bold text-center">Minha Assinatura</h2>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <span class="fw-semibold text-primary">Detalhes da Assinatura</span>
                    <span class="badge {{ $assinatura->stripe_status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                        {{ ucfirst($assinatura->stripe_status) }}
                    </span>
                </div>

                <div class="card-body">
                    @php
                        $nomesPlanos = [
                            'price_1RVxueC1nNYXXNDRXZRHr2N3' => 'Plano Mensal',
                            'price_1RVxv5C1nNYXXNDRYJlrrwG5' => 'Plano Trimestral',
                            'price_1RVxvdC1nNYXXNDR2URxfXFz' => 'Plano Anual',
                        ];
                    @endphp

                    <p><strong>Plano:</strong> {{ $nomesPlanos[$assinatura->stripe_price] ?? 'Desconhecido' }}</p>

                    @if($assinatura->onTrial())
                        <p><strong>Período de Teste até:</strong> {{ $assinatura->trial_ends_at->format('d/m/Y') }}</p>
                    @endif

                    @if($assinatura->ends_at)
                        <p><strong>Assinatura se encerra em:</strong> {{ $assinatura->ends_at->format('d/m/Y') }}</p>
                    @endif

                    <hr>

                    <form id="cancelar-assinatura-form" action="{{ route('assinatura.cancelar') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-x-circle me-1"></i> Cancelar Assinatura
                        </button>
                    </form>
                </div>
            </div>

            @if($faturas && count($faturas) > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <strong>Histórico de Pagamentos</strong>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered m-0">
                            <thead>
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
                                            <span class="badge {{ $fatura->paid ? 'bg-success' : 'bg-danger' }}">
                                                {{ $fatura->paid ? 'Pago' : 'Falhou' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ $fatura->invoice_pdf }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                Ver Recibo
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
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('cancelar-assinatura-form').addEventListener('submit', function(e) {
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
                this.submit();
            }
        });
    });
</script>
@endpush
