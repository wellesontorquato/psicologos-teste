@extends('layouts.app')

@section('title', 'Receita Saúde | PsiGestor')

@section('content')
<style>
    .rs-card {
        background: rgba(255,255,255,.92);
        border: 1px solid rgba(226,232,240,.9);
        border-radius: 20px;
        box-shadow: 0 14px 38px rgba(15,23,42,.08);
    }
    .rs-muted { color: #64748b; }
    .rs-table th { white-space: nowrap; }
    .rs-small { font-size: .86rem; }
    .rs-error-box { white-space: pre-line; }
</style>

<div class="container-fluid py-2">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Receita Saúde</h1>
            <p class="rs-muted mb-0">
                Gere o CSV oficial para importar no Carnê-Leão Web/e-CAC e emitir recibos em lote.
            </p>
        </div>

        <form method="POST" action="{{ route('receita-saude.sincronizar') }}">
            @csrf
            <button class="btn btn-primary fw-semibold">
                <i class="bi bi-arrow-repeat me-1"></i>
                Buscar sessões pagas
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger rs-error-box">
            @foreach($errors->all() as $erro)
                {{ $erro }}
            @endforeach
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="rs-card p-4 h-100">
                <div class="rs-muted rs-small">Sessões pagas ainda sem rascunho</div>
                <div class="display-6 fw-bold">{{ $sessoesElegiveis }}</div>
                <div class="rs-muted rs-small">Somente sessões pagas, em BRL e com valor maior que zero.</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="rs-card p-4 h-100">
                <div class="rs-muted rs-small">Código de ocupação do perfil</div>
                <div class="display-6 fw-bold">{{ $codigoOcupacao ?? '—' }}</div>
                <div class="rs-muted rs-small">
                    Psicólogo(a): 255. Psiquiatra: 225. Psicanalista não entra no Receita Saúde.
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="rs-card p-4 h-100">
                <div class="rs-muted rs-small">Fluxo recomendado</div>
                <div class="fw-semibold mt-1">1. gerar rascunhos → 2. revisar → 3. exportar CSV → 4. importar no e-CAC</div>
                <div class="rs-muted rs-small mt-2">Depois da importação, registre o número do recibo no histórico.</div>
            </div>
        </div>
    </div>

    <div class="alert alert-info rs-small">
        <strong>Importante:</strong> o PsiGestor prepara o arquivo de integração. A emissão oficial ainda acontece no Carnê-Leão Web/e-CAC, em
        <strong>Escrituração &gt; Importar Escrituração</strong>. O arquivo gerado não possui cabeçalho e usa campos separados por ponto e vírgula.
    </div>

    <div class="rs-card p-3 p-lg-4">
        <form method="POST" action="{{ route('receita-saude.exportar') }}">
            @csrf

            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                <div>
                    <h2 class="h5 fw-bold mb-1">Rascunhos e histórico</h2>
                    <p class="rs-muted mb-0 rs-small">Selecione recibos em rascunho/exportados para baixar o CSV.</p>
                </div>
                <button class="btn btn-success fw-semibold">
                    <i class="bi bi-download me-1"></i>
                    Exportar CSV selecionados
                </button>
            </div>

            <div class="table-responsive">
                <table class="table align-middle rs-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;"><input type="checkbox" id="selecionarTodos"></th>
                            <th>Status</th>
                            <th>Paciente</th>
                            <th>Pagamento</th>
                            <th>Valor</th>
                            <th>CPF pagador</th>
                            <th>CPF beneficiário</th>
                            <th>Nº recibo</th>
                            <th style="min-width: 320px;">Revisar / atualizar</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($recibos as $recibo)
                        <tr>
                            <td>
                                @if(in_array($recibo->status, ['rascunho', 'exportado']))
                                    <input type="checkbox" class="check-recibo" name="recibos[]" value="{{ $recibo->id }}">
                                @endif
                            </td>
                            <td>
                                @php
                                    $badge = match($recibo->status) {
                                        'rascunho' => 'text-bg-secondary',
                                        'exportado' => 'text-bg-primary',
                                        'emitido' => 'text-bg-success',
                                        'cancelado' => 'text-bg-danger',
                                        default => 'text-bg-light',
                                    };
                                @endphp
                                <span class="badge {{ $badge }}">{{ $recibo->status_label }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $recibo->paciente->nome ?? 'Paciente removido' }}</div>
                                <div class="rs-muted rs-small">Sessão #{{ $recibo->sessao_id ?? '—' }}</div>
                            </td>
                            <td>{{ optional($recibo->data_pagamento)->format('d/m/Y') }}</td>
                            <td>R$ {{ number_format((float) $recibo->valor_pagamento, 2, ',', '.') }}</td>
                            <td>{{ $recibo->cpf_pagador }}</td>
                            <td>{{ $recibo->cpf_beneficiario }}</td>
                            <td>{{ $recibo->numero_recibo ?: '—' }}</td>
                            <td>
                                <form method="POST" action="{{ route('receita-saude.atualizar', $recibo) }}" class="d-grid gap-2">
                                    @csrf
                                    @method('PATCH')

                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="date" name="data_pagamento" class="form-control form-control-sm"
                                                   value="{{ optional($recibo->data_pagamento)->format('Y-m-d') }}" required>
                                        </div>
                                        <div class="col-6">
                                            <input type="number" step="0.01" min="0.01" name="valor_pagamento" class="form-control form-control-sm"
                                                   value="{{ $recibo->valor_pagamento }}" required>
                                        </div>
                                    </div>

                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="text" name="cpf_pagador" class="form-control form-control-sm"
                                                   value="{{ $recibo->cpf_pagador }}" maxlength="20" placeholder="CPF pagador" required>
                                        </div>
                                        <div class="col-6">
                                            <input type="text" name="cpf_beneficiario" class="form-control form-control-sm"
                                                   value="{{ $recibo->cpf_beneficiario }}" maxlength="20" placeholder="CPF beneficiário" required>
                                        </div>
                                    </div>

                                    <input type="text" name="descricao" class="form-control form-control-sm"
                                           value="{{ $recibo->descricao }}" maxlength="255" placeholder="Descrição">

                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="text" name="numero_recibo" class="form-control form-control-sm"
                                                   value="{{ $recibo->numero_recibo }}" placeholder="Número do recibo">
                                        </div>
                                        <div class="col-6">
                                            <select name="status" class="form-select form-select-sm">
                                                <option value="rascunho" @selected($recibo->status === 'rascunho')>Rascunho</option>
                                                <option value="exportado" @selected($recibo->status === 'exportado')>Exportado</option>
                                                <option value="emitido" @selected($recibo->status === 'emitido')>Emitido</option>
                                                <option value="cancelado" @selected($recibo->status === 'cancelado')>Cancelado</option>
                                            </select>
                                        </div>
                                    </div>

                                    <textarea name="observacoes" class="form-control form-control-sm" rows="1" placeholder="Observações internas">{{ $recibo->observacoes }}</textarea>

                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-primary flex-fill">Salvar</button>
                                    </div>
                                </form>

                                @if($recibo->status !== 'emitido')
                                    <form method="POST" action="{{ route('receita-saude.excluir', $recibo) }}" class="mt-2" onsubmit="return confirm('Remover este rascunho?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger w-100">Remover rascunho</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 rs-muted">
                                Nenhum rascunho gerado ainda. Clique em <strong>Buscar sessões pagas</strong> para começar.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        <div class="mt-3">
            {{ $recibos->links() }}
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selecionarTodos = document.getElementById('selecionarTodos');
        const checks = document.querySelectorAll('.check-recibo');

        if (selecionarTodos) {
            selecionarTodos.addEventListener('change', function () {
                checks.forEach(check => check.checked = selecionarTodos.checked);
            });
        }
    });
</script>
@endsection
