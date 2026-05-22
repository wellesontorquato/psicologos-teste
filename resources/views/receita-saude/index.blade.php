@extends('layouts.app')

@section('title', 'Receita Saúde | PsiGestor')

@section('content')
<style>
    .rs-page {
        width: 100%;
    }

    .rs-card {
        background: rgba(255,255,255,.94);
        border: 1px solid rgba(226,232,240,.95);
        border-radius: 20px;
        box-shadow: 0 14px 38px rgba(15,23,42,.08);
    }

    .rs-muted {
        color: #64748b;
    }

    .rs-small {
        font-size: .86rem;
    }

    .rs-error-box {
        white-space: pre-line;
    }

    .rs-summary-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 14px;
    }

    .rs-summary-number {
        font-size: 2rem;
        line-height: 1;
        font-weight: 800;
        color: #020617;
    }

    .rs-receipt-list {
        display: grid;
        grid-template-columns: 1fr;
        gap: 14px;
    }

    .rs-receipt-card {
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 16px;
        background: #ffffff;
    }

    .rs-receipt-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }

    .rs-receipt-title {
        font-weight: 800;
        color: #020617;
        margin: 0;
        font-size: 1rem;
    }

    .rs-receipt-subtitle {
        color: #64748b;
        font-size: .84rem;
        margin-top: 2px;
    }

    .rs-info-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
    }

    .rs-info-item {
        border-radius: 14px;
        background: #f8fafc;
        padding: 10px 12px;
    }

    .rs-info-label {
        display: block;
        color: #64748b;
        font-size: .75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .03em;
        margin-bottom: 3px;
    }

    .rs-info-value {
        display: block;
        color: #0f172a;
        font-size: .95rem;
        font-weight: 700;
        word-break: break-word;
    }

    .rs-actions {
        display: grid;
        grid-template-columns: 1fr;
        gap: 8px;
        margin-top: 14px;
    }

    .rs-desktop-table {
        display: none;
    }

    .rs-table {
        margin-bottom: 0;
    }

    .rs-table th {
        white-space: nowrap;
        font-size: .82rem;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: .02em;
    }

    .rs-table td {
        vertical-align: middle;
    }

    .rs-toolbar {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .rs-toolbar-actions {
        display: grid;
        grid-template-columns: 1fr;
        gap: 8px;
    }

    .rs-btn-main {
        min-height: 42px;
        border-radius: 12px;
        font-weight: 700;
    }

    .rs-modal-section-title {
        font-size: .85rem;
        font-weight: 800;
        color: #334155;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: 10px;
    }

    .rs-modal-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .rs-modal-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 14px;
    }

    .rs-empty {
        text-align: center;
        padding: 42px 18px;
        color: #64748b;
    }

    @media (min-width: 576px) {
        .rs-info-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .rs-actions {
            grid-template-columns: auto auto;
            justify-content: end;
        }

        .rs-toolbar-actions {
            grid-template-columns: auto auto;
            justify-content: flex-start;
        }

        .rs-modal-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 992px) {
        .rs-summary-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .rs-mobile-list {
            display: none;
        }

        .rs-desktop-table {
            display: block;
        }

        .rs-toolbar {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }

        .rs-toolbar-actions {
            justify-content: end;
        }
    }
</style>

@php
    $formatarCpf = function ($cpf) {
        $cpf = preg_replace('/\D/', '', (string) $cpf);

        if (!$cpf) {
            return '—';
        }

        if (strlen($cpf) === 11) {
            return substr($cpf, 0, 3) . '.' .
                   substr($cpf, 3, 3) . '.' .
                   substr($cpf, 6, 3) . '-' .
                   substr($cpf, 9, 2);
        }

        return $cpf;
    };

    $formatarData = function ($data) {
        return $data ? optional($data)->format('d/m/Y') : '—';
    };

    $formatarValor = function ($valor) {
        return 'R$ ' . number_format((float) $valor, 2, ',', '.');
    };

    $badgeStatus = function ($status) {
        return match($status) {
            'rascunho' => 'text-bg-secondary',
            'exportado' => 'text-bg-primary',
            'emitido' => 'text-bg-success',
            'cancelado' => 'text-bg-danger',
            default => 'text-bg-light',
        };
    };
@endphp

<div class="container-fluid py-2 rs-page">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Receita Saúde</h1>
            <p class="rs-muted mb-0">
                Gere o CSV oficial para importar no Carnê-Leão Web/e-CAC e emitir recibos em lote.
            </p>
        </div>

        <form method="POST" action="{{ route('receita-saude.sincronizar') }}">
            @csrf
            <button class="btn btn-primary rs-btn-main">
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

    <div class="rs-summary-grid mb-4">
        <div class="rs-card p-4">
            <div class="rs-muted rs-small">Sessões pagas ainda sem rascunho</div>
            <div class="rs-summary-number mt-2">{{ $sessoesElegiveis }}</div>
            <div class="rs-muted rs-small mt-2">
                Somente sessões pagas, em BRL e com valor maior que zero.
            </div>
        </div>

        <div class="rs-card p-4">
            <div class="rs-muted rs-small">Código de ocupação do perfil</div>
            <div class="rs-summary-number mt-2">{{ $codigoOcupacao ?? '—' }}</div>
            <div class="rs-muted rs-small mt-2">
                Psicólogo(a): 255. Psiquiatra: 225. Psicanalista não entra no Receita Saúde.
            </div>
        </div>

        <div class="rs-card p-4">
            <div class="rs-muted rs-small">Fluxo recomendado</div>
            <div class="fw-semibold mt-2">
                1. gerar rascunhos → 2. revisar → 3. exportar CSV → 4. importar no e-CAC
            </div>
            <div class="rs-muted rs-small mt-2">
                Depois da importação, registre o número do recibo no histórico.
            </div>
        </div>
    </div>

    <div class="alert alert-info rs-small">
        <strong>Importante:</strong> o PsiGestor prepara o arquivo de integração. A emissão oficial ainda acontece no Carnê-Leão Web/e-CAC, em
        <strong>Escrituração &gt; Importar Escrituração</strong>. O arquivo gerado não possui cabeçalho e usa campos separados por ponto e vírgula.
    </div>

    <form id="formExportarCsv" method="POST" action="{{ route('receita-saude.exportar') }}">
        @csrf
    </form>

    <div class="rs-card p-3 p-lg-4">
        <div class="rs-toolbar mb-3">
            <div>
                <h2 class="h5 fw-bold mb-1">Rascunhos e histórico</h2>
                <p class="rs-muted mb-0 rs-small">
                    Selecione os recibos em rascunho/exportados para baixar o CSV.
                </p>
            </div>

            <div class="rs-toolbar-actions">
                <button type="submit" form="formExportarCsv" class="btn btn-success rs-btn-main">
                    <i class="bi bi-download me-1"></i>
                    Exportar CSV selecionados
                </button>
            </div>
        </div>

        <div class="rs-mobile-list">
            <div class="rs-receipt-list">
                @forelse($recibos as $recibo)
                    <div class="rs-receipt-card">
                        <div class="rs-receipt-head">
                            <div>
                                <p class="rs-receipt-title">
                                    {{ $recibo->paciente->nome ?? 'Paciente removido' }}
                                </p>
                                <div class="rs-receipt-subtitle">
                                    Sessão #{{ $recibo->sessao_id ?? '—' }}
                                </div>
                            </div>

                            <div class="text-end">
                                <span class="badge {{ $badgeStatus($recibo->status) }}">
                                    {{ $recibo->status_label }}
                                </span>
                            </div>
                        </div>

                        <div class="rs-info-grid">
                            <div class="rs-info-item">
                                <span class="rs-info-label">Pagamento</span>
                                <span class="rs-info-value">{{ $formatarData($recibo->data_pagamento) }}</span>
                            </div>

                            <div class="rs-info-item">
                                <span class="rs-info-label">Valor</span>
                                <span class="rs-info-value">{{ $formatarValor($recibo->valor_pagamento) }}</span>
                            </div>

                            <div class="rs-info-item">
                                <span class="rs-info-label">CPF pagador</span>
                                <span class="rs-info-value">{{ $formatarCpf($recibo->cpf_pagador) }}</span>
                            </div>

                            <div class="rs-info-item">
                                <span class="rs-info-label">CPF beneficiário</span>
                                <span class="rs-info-value">{{ $formatarCpf($recibo->cpf_beneficiario) }}</span>
                            </div>

                            <div class="rs-info-item">
                                <span class="rs-info-label">Nº recibo</span>
                                <span class="rs-info-value">{{ $recibo->numero_recibo ?: '—' }}</span>
                            </div>
                        </div>

                        <div class="rs-actions">
                            @if(in_array($recibo->status, ['rascunho', 'exportado']))
                                <label class="btn btn-outline-secondary rs-btn-main mb-0">
                                    <input type="checkbox"
                                           class="form-check-input me-1"
                                           data-recibo-checkbox
                                           value="{{ $recibo->id }}">
                                    Selecionar
                                </label>
                            @endif

                            <button type="button"
                                    class="btn btn-primary rs-btn-main"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalRecibo{{ $recibo->id }}">
                                Atualizar
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="rs-empty">
                        Nenhum rascunho gerado ainda.
                        <br>
                        Clique em <strong>Buscar sessões pagas</strong> para começar.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="rs-desktop-table">
            <div class="table-responsive">
                <table class="table align-middle rs-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" id="selecionarTodos">
                            </th>
                            <th>Status</th>
                            <th>Paciente</th>
                            <th>Pagamento</th>
                            <th>Valor</th>
                            <th>CPF pagador</th>
                            <th>CPF beneficiário</th>
                            <th>Nº recibo</th>
                            <th class="text-end">Ação</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($recibos as $recibo)
                            <tr>
                                <td>
                                    @if(in_array($recibo->status, ['rascunho', 'exportado']))
                                        <input type="checkbox"
                                               class="form-check-input"
                                               data-recibo-checkbox
                                               value="{{ $recibo->id }}">
                                    @endif
                                </td>

                                <td>
                                    <span class="badge {{ $badgeStatus($recibo->status) }}">
                                        {{ $recibo->status_label }}
                                    </span>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $recibo->paciente->nome ?? 'Paciente removido' }}
                                    </div>
                                    <div class="rs-muted rs-small">
                                        Sessão #{{ $recibo->sessao_id ?? '—' }}
                                    </div>
                                </td>

                                <td>{{ $formatarData($recibo->data_pagamento) }}</td>

                                <td>{{ $formatarValor($recibo->valor_pagamento) }}</td>

                                <td>{{ $formatarCpf($recibo->cpf_pagador) }}</td>

                                <td>{{ $formatarCpf($recibo->cpf_beneficiario) }}</td>

                                <td>{{ $recibo->numero_recibo ?: '—' }}</td>

                                <td class="text-end">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalRecibo{{ $recibo->id }}">
                                        Atualizar
                                    </button>
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
        </div>

        <div class="mt-3">
            {{ $recibos->links() }}
        </div>
    </div>

    @foreach($recibos as $recibo)
        <div class="modal fade" id="modalRecibo{{ $recibo->id }}" tabindex="-1" aria-labelledby="modalReciboLabel{{ $recibo->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title fw-bold" id="modalReciboLabel{{ $recibo->id }}">
                                Atualizar Receita Saúde
                            </h5>
                            <div class="rs-muted rs-small">
                                {{ $recibo->paciente->nome ?? 'Paciente removido' }} · Sessão #{{ $recibo->sessao_id ?? '—' }}
                            </div>
                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>

                    <form id="formAtualizarRecibo{{ $recibo->id }}"
                          method="POST"
                          action="{{ route('receita-saude.atualizar', $recibo) }}"
                          class="rs-recibo-form">
                        @csrf
                        @method('PATCH')

                        <div class="modal-body">
                            <div class="rs-modal-card mb-3">
                                <div class="rs-modal-section-title">Dados principais</div>

                                <div class="rs-modal-grid">
                                    <div>
                                        <label class="form-label">Data do pagamento</label>
                                        <input type="date"
                                               name="data_pagamento"
                                               class="form-control"
                                               value="{{ optional($recibo->data_pagamento)->format('Y-m-d') }}"
                                               required>
                                    </div>

                                    <div>
                                        <label class="form-label">Valor</label>
                                        <input type="number"
                                               step="0.01"
                                               min="0.01"
                                               name="valor_pagamento"
                                               class="form-control"
                                               value="{{ $recibo->valor_pagamento }}"
                                               required>
                                    </div>
                                </div>
                            </div>

                            <div class="rs-modal-card mb-3">
                                <div class="rs-modal-section-title">CPFs do recibo</div>

                                <div class="rs-modal-grid">
                                    <div>
                                        <label class="form-label">CPF pagador</label>
                                        <input type="text"
                                               name="cpf_pagador"
                                               class="form-control js-cpf-mask"
                                               value="{{ $formatarCpf($recibo->cpf_pagador) }}"
                                               maxlength="14"
                                               placeholder="000.000.000-00"
                                               inputmode="numeric"
                                               required>
                                    </div>

                                    <div>
                                        <label class="form-label">CPF beneficiário</label>
                                        <input type="text"
                                               name="cpf_beneficiario"
                                               class="form-control js-cpf-mask"
                                               value="{{ $formatarCpf($recibo->cpf_beneficiario) }}"
                                               maxlength="14"
                                               placeholder="000.000.000-00"
                                               inputmode="numeric"
                                               required>
                                    </div>
                                </div>

                                <div class="rs-muted rs-small mt-2">
                                    O CPF será exibido com máscara, mas enviado ao sistema apenas com números.
                                </div>
                            </div>

                            <div class="rs-modal-card mb-3">
                                <div class="rs-modal-section-title">Informações do recibo</div>

                                <div class="mb-3">
                                    <label class="form-label">Descrição</label>
                                    <input type="text"
                                           name="descricao"
                                           class="form-control"
                                           value="{{ $recibo->descricao }}"
                                           maxlength="255"
                                           placeholder="Ex: Atendimento psicológico em 22/05/2026">
                                </div>

                                <div class="rs-modal-grid">
                                    <div>
                                        <label class="form-label">Número do recibo</label>
                                        <input type="text"
                                               name="numero_recibo"
                                               class="form-control"
                                               value="{{ $recibo->numero_recibo }}"
                                               placeholder="Informe após importar no e-CAC">
                                    </div>

                                    <div>
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="rascunho" @selected($recibo->status === 'rascunho')>
                                                Rascunho
                                            </option>
                                            <option value="exportado" @selected($recibo->status === 'exportado')>
                                                Exportado
                                            </option>
                                            <option value="emitido" @selected($recibo->status === 'emitido')>
                                                Emitido
                                            </option>
                                            <option value="cancelado" @selected($recibo->status === 'cancelado')>
                                                Cancelado
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <label class="form-label">Observações internas</label>
                                    <textarea name="observacoes"
                                              class="form-control"
                                              rows="3"
                                              placeholder="Observações internas">{{ $recibo->observacoes }}</textarea>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="modal-footer d-flex flex-column flex-sm-row gap-2">
                        @if($recibo->status !== 'emitido')
                            <form method="POST"
                                  action="{{ route('receita-saude.excluir', $recibo) }}"
                                  class="w-100"
                                  onsubmit="return confirm('Remover este rascunho?')">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-outline-danger w-100">
                                    Remover rascunho
                                </button>
                            </form>
                        @endif

                        <button type="button" class="btn btn-outline-secondary w-100" data-bs-dismiss="modal">
                            Cancelar
                        </button>

                        <button type="submit"
                                form="formAtualizarRecibo{{ $recibo->id }}"
                                class="btn btn-primary w-100">
                            Salvar alterações
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selecionarTodos = document.getElementById('selecionarTodos');
    const exportForm = document.getElementById('formExportarCsv');
    const reciboChecks = document.querySelectorAll('[data-recibo-checkbox]');
    const reciboForms = document.querySelectorAll('.rs-recibo-form');
    const cpfInputs = document.querySelectorAll('.js-cpf-mask');

    function apenasNumeros(valor) {
        return String(valor || '').replace(/\D/g, '');
    }

    function aplicarMascaraCpf(valor) {
        valor = apenasNumeros(valor).slice(0, 11);

        if (valor.length <= 3) {
            return valor;
        }

        if (valor.length <= 6) {
            return valor.replace(/(\d{3})(\d+)/, '$1.$2');
        }

        if (valor.length <= 9) {
            return valor.replace(/(\d{3})(\d{3})(\d+)/, '$1.$2.$3');
        }

        return valor.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
    }

    function sincronizarCheckboxes(valor, marcado) {
        document.querySelectorAll('[data-recibo-checkbox][value="' + valor + '"]').forEach(function (check) {
            check.checked = marcado;
        });
    }

    function atualizarSelecionarTodos() {
        if (!selecionarTodos || reciboChecks.length === 0) {
            return;
        }

        const todosMarcados = Array.from(reciboChecks).every(function (check) {
            return check.checked;
        });

        selecionarTodos.checked = todosMarcados;
    }

    cpfInputs.forEach(function (input) {
        input.value = aplicarMascaraCpf(input.value);

        input.addEventListener('input', function () {
            input.value = aplicarMascaraCpf(input.value);
        });
    });

    reciboChecks.forEach(function (check) {
        check.addEventListener('change', function () {
            sincronizarCheckboxes(check.value, check.checked);
            atualizarSelecionarTodos();
        });
    });

    if (selecionarTodos) {
        selecionarTodos.addEventListener('change', function () {
            reciboChecks.forEach(function (check) {
                check.checked = selecionarTodos.checked;
            });
        });
    }

    if (exportForm) {
        exportForm.addEventListener('submit', function (event) {
            exportForm.querySelectorAll('.js-recibo-hidden').forEach(function (input) {
                input.remove();
            });

            const selecionados = Array.from(document.querySelectorAll('[data-recibo-checkbox]:checked'))
                .map(function (check) {
                    return check.value;
                });

            const selecionadosUnicos = Array.from(new Set(selecionados));

            if (selecionadosUnicos.length === 0) {
                event.preventDefault();
                alert('Selecione pelo menos um recibo para exportar.');
                return;
            }

            selecionadosUnicos.forEach(function (id) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'recibos[]';
                input.value = id;
                input.className = 'js-recibo-hidden';
                exportForm.appendChild(input);
            });
        });
    }

    reciboForms.forEach(function (form) {
        form.addEventListener('submit', function () {
            form.querySelectorAll('.js-cpf-mask').forEach(function (input) {
                input.value = apenasNumeros(input.value);
            });
        });
    });
});
</script>
@endsection