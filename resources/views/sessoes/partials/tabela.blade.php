@php
    $simbolosMoeda = [
        'BRL' => 'R$',
        'USD' => 'US$',
        'EUR' => '€',
        'GBP' => '£',
        'ARS' => 'AR$',
        'CLP' => 'CLP$',
        'MXN' => 'MX$',
        'CAD' => 'C$',
        'AUD' => 'A$',
    ];
@endphp

{{-- DESKTOP --}}
<div class="d-none d-xl-block">
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table sessoes-table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 22%;">Paciente</th>
                        <th style="width: 15%;">Data</th>
                        <th style="width: 10%;">Valor</th>
                        <th style="width: 9%;">Pago</th>
                        <th style="width: 17%;">Confirmação</th>
                        <th style="width: 12%;">Lembrete</th>
                        <th style="width: 15%;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessoes as $sessao)
                        @php
                            $status = $sessao->status_confirmacao ?? 'PENDENTE';

                            $statusInfo = match($status) {
                                'CONFIRMADA' => ['✅', 'Confirmada', 'success'],
                                'CANCELADA'  => ['❌', 'Cancelada', 'danger'],
                                'REMARCAR'   => [is_null($sessao->data_hora) ? '🔄' : '📅', is_null($sessao->data_hora) ? 'Remarcar' : 'Remarcado', is_null($sessao->data_hora) ? 'warning' : 'info'],
                                default      => ['⏳', 'Pendente', 'secondary'],
                            };

                            $moeda = $sessao->moeda ?? 'BRL';
                            $simbolo = $simbolosMoeda[$moeda] ?? $moeda;

                            $telefoneNormalizado = preg_replace('/\D+/', '', $sessao->paciente->telefone ?? '');
                            $temTelefone = !empty($telefoneNormalizado);
                            $dataBase = $sessao->data_hora_original ?: $sessao->data_hora;
                            $lembreteEnviado = (int) $sessao->lembrete_enviado === 1;
                        @endphp

                        <tr>
                            <td>
                                <div class="fw-semibold text-dark nome-paciente">{{ $sessao->paciente->nome }}</div>
                                @if(!empty($sessao->paciente->telefone))
                                    <div class="small text-muted mt-1">{{ $sessao->paciente->telefone }}</div>
                                @endif
                            </td>

                            <td>
                                @if(is_null($sessao->data_hora))
                                    @if($sessao->status_confirmacao === 'REMARCAR')
                                        <div class="fw-semibold text-warning">Reagendar consulta</div>
                                    @elseif($sessao->status_confirmacao === 'CANCELADA')
                                        <div class="fw-semibold text-danger">Consulta cancelada</div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                @else
                                    <div class="fw-semibold">{{ \Carbon\Carbon::parse($sessao->data_hora)->format('d/m/Y') }}</div>
                                    <div class="small text-muted">
                                        {{ \Carbon\Carbon::parse($sessao->data_hora)->format('H:i') }} • {{ $sessao->duracao }} min
                                    </div>
                                @endif
                            </td>

                            <td>
                                <div class="fw-bold">{{ $simbolo }} {{ number_format((float) $sessao->valor, 2, ',', '.') }}</div>
                                <div class="small text-muted">{{ $moeda }}</div>
                            </td>

                            <td>
                                <span class="badge rounded-pill {{ $sessao->foi_pago ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $sessao->foi_pago ? 'Sim' : 'Não' }}
                                </span>
                            </td>

                            <td>
                                <div class="mb-2">
                                    <span class="badge rounded-pill text-bg-{{ $statusInfo[2] }}">
                                        {{ $statusInfo[0] }} {{ $statusInfo[1] }}
                                    </span>
                                </div>

                                <form action="{{ route('sessoes.atualizar-status-confirmacao', $sessao->id) }}"
                                      method="POST"
                                      class="form-status-confirmacao">
                                    @csrf
                                    <select name="status_confirmacao"
                                            class="form-select form-select-sm"
                                            data-current="{{ $sessao->status_confirmacao ?? 'PENDENTE' }}">
                                        <option value="PENDENTE" {{ ($sessao->status_confirmacao ?? 'PENDENTE') === 'PENDENTE' ? 'selected' : '' }}>Pendente</option>
                                        <option value="CONFIRMADA" {{ $sessao->status_confirmacao === 'CONFIRMADA' ? 'selected' : '' }}>Confirmada</option>
                                        <option value="CANCELADA" {{ $sessao->status_confirmacao === 'CANCELADA' ? 'selected' : '' }}>Cancelada</option>
                                        <option value="REMARCAR" {{ $sessao->status_confirmacao === 'REMARCAR' ? 'selected' : '' }}>Remarcar</option>
                                    </select>
                                </form>
                            </td>

                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <span class="badge rounded-pill {{ $lembreteEnviado ? 'text-bg-success' : 'text-bg-light text-dark border' }}">
                                        {{ $lembreteEnviado ? 'Enviado' : 'Pendente' }}
                                    </span>

                                    @if($lembreteEnviado && $dataBase)
                                        <span class="small text-muted">
                                            {{ \Carbon\Carbon::parse($dataBase)->format('d/m H:i') }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td>
                                <div class="acoes-stack">
                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                        <a href="{{ route('sessoes.edit', $sessao) }}" class="btn btn-sm btn-warning">Editar</a>

                                        @if($temTelefone && $dataBase)
                                            <a href="{{ route('sessoes.whatsapp', $sessao->id) }}"
                                               target="_blank"
                                               class="btn btn-sm btn-success btn-whatsapp-lembrete">
                                                WhatsApp
                                            </a>
                                        @else
                                            <button type="button" class="btn btn-sm btn-success" disabled>WhatsApp</button>
                                        @endif
                                    </div>

                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalRecorrencia"
                                                data-sessao-id="{{ $sessao->id }}">
                                            Recorrências
                                        </button>

                                        <a href="{{ route('evolucoes.create', [
                                                'paciente' => $sessao->paciente_id,
                                                'sessao_id' => $sessao->id,
                                                'data' => now()->format('Y-m-d')
                                            ]) }}"
                                           class="btn btn-sm btn-outline-success">
                                            Evolução
                                        </a>

                                        <form action="{{ route('sessoes.destroy', $sessao) }}"
                                              method="POST"
                                              class="form-excluir d-inline no-spinner">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="query_string" value="">
                                            <input type="hidden" name="aba" value="">
                                            <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Nenhuma sessão encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- TABLET --}}
<div class="d-none d-md-block d-xl-none">
    <div class="row g-3">
        @forelse($sessoes as $sessao)
            @php
                $status = $sessao->status_confirmacao ?? 'PENDENTE';

                $statusInfo = match($status) {
                    'CONFIRMADA' => ['✅', 'Confirmada', 'success'],
                    'CANCELADA'  => ['❌', 'Cancelada', 'danger'],
                    'REMARCAR'   => [is_null($sessao->data_hora) ? '🔄' : '📅', is_null($sessao->data_hora) ? 'Remarcar' : 'Remarcado', is_null($sessao->data_hora) ? 'warning' : 'info'],
                    default      => ['⏳', 'Pendente', 'secondary'],
                };

                $moeda = $sessao->moeda ?? 'BRL';
                $simbolo = $simbolosMoeda[$moeda] ?? $moeda;

                $telefoneNormalizado = preg_replace('/\D+/', '', $sessao->paciente->telefone ?? '');
                $temTelefone = !empty($telefoneNormalizado);
                $dataBase = $sessao->data_hora_original ?: $sessao->data_hora;
                $lembreteEnviado = (int) $sessao->lembrete_enviado === 1;
            @endphp

            <div class="col-12">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                            <div>
                                <h5 class="mb-1 fs-6 fw-semibold">{{ $sessao->paciente->nome }}</h5>
                                @if(!empty($sessao->paciente->telefone))
                                    <div class="small text-muted">{{ $sessao->paciente->telefone }}</div>
                                @endif
                            </div>

                            <span class="badge rounded-pill text-bg-{{ $statusInfo[2] }}">
                                {{ $statusInfo[0] }} {{ $statusInfo[1] }}
                            </span>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <div class="info-box">
                                    <div class="info-label">Data</div>
                                    <div class="info-value">
                                        @if(is_null($sessao->data_hora))
                                            @if($sessao->status_confirmacao === 'REMARCAR')
                                                <span class="text-warning">Reagendar consulta</span>
                                            @elseif($sessao->status_confirmacao === 'CANCELADA')
                                                <span class="text-danger">Consulta cancelada</span>
                                            @else
                                                —
                                            @endif
                                        @else
                                            {{ \Carbon\Carbon::parse($sessao->data_hora)->format('d/m/Y H:i') }}
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="info-box">
                                    <div class="info-label">Valor</div>
                                    <div class="info-value">{{ $simbolo }} {{ number_format((float) $sessao->valor, 2, ',', '.') }}</div>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="info-box">
                                    <div class="info-label">Pago</div>
                                    <div class="info-value">
                                        <span class="badge rounded-pill {{ $sessao->foi_pago ? 'text-bg-success' : 'text-bg-secondary' }}">
                                            {{ $sessao->foi_pago ? 'Sim' : 'Não' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <div class="small text-muted fw-semibold mb-1">Confirmação</div>
                                <form action="{{ route('sessoes.atualizar-status-confirmacao', $sessao->id) }}"
                                      method="POST"
                                      class="form-status-confirmacao">
                                    @csrf
                                    <select name="status_confirmacao"
                                            class="form-select form-select-sm"
                                            data-current="{{ $sessao->status_confirmacao ?? 'PENDENTE' }}">
                                        <option value="PENDENTE" {{ ($sessao->status_confirmacao ?? 'PENDENTE') === 'PENDENTE' ? 'selected' : '' }}>Pendente</option>
                                        <option value="CONFIRMADA" {{ $sessao->status_confirmacao === 'CONFIRMADA' ? 'selected' : '' }}>Confirmada</option>
                                        <option value="CANCELADA" {{ $sessao->status_confirmacao === 'CANCELADA' ? 'selected' : '' }}>Cancelada</option>
                                        <option value="REMARCAR" {{ $sessao->status_confirmacao === 'REMARCAR' ? 'selected' : '' }}>Remarcar</option>
                                    </select>
                                </form>
                            </div>

                            <div class="col-md-3">
                                <div class="small text-muted fw-semibold mb-1">Lembrete</div>
                                <span class="badge rounded-pill {{ $lembreteEnviado ? 'text-bg-success' : 'text-bg-light text-dark border' }}">
                                    {{ $lembreteEnviado ? 'Enviado' : 'Pendente' }}
                                </span>
                            </div>

                            <div class="col-md-4">
                                <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                                    <a href="{{ route('sessoes.edit', $sessao) }}" class="btn btn-sm btn-warning">Editar</a>

                                    @if($temTelefone && $dataBase)
                                        <a href="{{ route('sessoes.whatsapp', $sessao->id) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-success btn-whatsapp-lembrete">
                                            WhatsApp
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-sm btn-success" disabled>WhatsApp</button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <button type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalRecorrencia"
                                    data-sessao-id="{{ $sessao->id }}">
                                Recorrências
                            </button>

                            <a href="{{ route('evolucoes.create', [
                                    'paciente' => $sessao->paciente_id,
                                    'sessao_id' => $sessao->id,
                                    'data' => now()->format('Y-m-d')
                                ]) }}"
                               class="btn btn-sm btn-outline-success">
                                Evolução
                            </a>

                            <form action="{{ route('sessoes.destroy', $sessao) }}"
                                  method="POST"
                                  class="form-excluir d-inline no-spinner">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="query_string" value="">
                                <input type="hidden" name="aba" value="">
                                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">Nenhuma sessão encontrada.</div>
            </div>
        @endforelse
    </div>
</div>

{{-- MOBILE --}}
<div class="d-md-none">
    @forelse($sessoes as $sessao)
        @php
            $status = $sessao->status_confirmacao ?? 'PENDENTE';

            $statusInfo = match($status) {
                'CONFIRMADA' => ['✅', 'Confirmada', 'success'],
                'CANCELADA'  => ['❌', 'Cancelada', 'danger'],
                'REMARCAR'   => [is_null($sessao->data_hora) ? '🔄' : '📅', is_null($sessao->data_hora) ? 'Remarcar' : 'Remarcado', is_null($sessao->data_hora) ? 'warning' : 'info'],
                default      => ['⏳', 'Pendente', 'secondary'],
            };

            $moeda = $sessao->moeda ?? 'BRL';
            $simbolo = $simbolosMoeda[$moeda] ?? $moeda;

            $telefoneNormalizado = preg_replace('/\D+/', '', $sessao->paciente->telefone ?? '');
            $temTelefone = !empty($telefoneNormalizado);
            $dataBase = $sessao->data_hora_original ?: $sessao->data_hora;
            $lembreteEnviado = (int) $sessao->lembrete_enviado === 1;
        @endphp

        <div class="card border-0 shadow-sm mb-3 sessao-mobile-card">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                    <div>
                        <h5 class="card-title mb-1 fs-6 fw-semibold">{{ $sessao->paciente->nome }}</h5>
                        @if(!empty($sessao->paciente->telefone))
                            <div class="small text-muted">{{ $sessao->paciente->telefone }}</div>
                        @endif
                    </div>

                    <span class="badge rounded-pill text-bg-{{ $statusInfo[2] }}">
                        {{ $statusInfo[0] }}
                    </span>
                </div>

                <div class="info-stack mb-3">
                    <div class="info-row">
                        <span class="info-row-label">Data</span>
                        <span class="info-row-value">
                            @if(is_null($sessao->data_hora))
                                @if($sessao->status_confirmacao === 'REMARCAR')
                                    <span class="text-warning">Reagendar consulta</span>
                                @elseif($sessao->status_confirmacao === 'CANCELADA')
                                    <span class="text-danger">Consulta cancelada</span>
                                @else
                                    —
                                @endif
                            @else
                                {{ \Carbon\Carbon::parse($sessao->data_hora)->format('d/m/Y H:i') }}
                            @endif
                        </span>
                    </div>

                    <div class="info-row">
                        <span class="info-row-label">Valor</span>
                        <span class="info-row-value">{{ $simbolo }} {{ number_format((float) $sessao->valor, 2, ',', '.') }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-row-label">Duração</span>
                        <span class="info-row-value">{{ $sessao->duracao }} min</span>
                    </div>

                    <div class="info-row">
                        <span class="info-row-label">Pago</span>
                        <span class="info-row-value">
                            <span class="badge rounded-pill {{ $sessao->foi_pago ? 'text-bg-success' : 'text-bg-secondary' }}">
                                {{ $sessao->foi_pago ? 'Sim' : 'Não' }}
                            </span>
                        </span>
                    </div>

                    <div class="info-row">
                        <span class="info-row-label">Lembrete</span>
                        <span class="info-row-value">
                            <span class="badge rounded-pill {{ $lembreteEnviado ? 'text-bg-success' : 'text-bg-light text-dark border' }}">
                                {{ $lembreteEnviado ? 'Enviado' : 'Pendente' }}
                            </span>
                        </span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted fw-semibold mb-1">Status de confirmação</label>
                    <form action="{{ route('sessoes.atualizar-status-confirmacao', $sessao->id) }}"
                          method="POST"
                          class="form-status-confirmacao">
                        @csrf
                        <select name="status_confirmacao"
                                class="form-select form-select-sm"
                                data-current="{{ $sessao->status_confirmacao ?? 'PENDENTE' }}">
                            <option value="PENDENTE" {{ ($sessao->status_confirmacao ?? 'PENDENTE') === 'PENDENTE' ? 'selected' : '' }}>Pendente</option>
                            <option value="CONFIRMADA" {{ $sessao->status_confirmacao === 'CONFIRMADA' ? 'selected' : '' }}>Confirmada</option>
                            <option value="CANCELADA" {{ $sessao->status_confirmacao === 'CANCELADA' ? 'selected' : '' }}>Cancelada</option>
                            <option value="REMARCAR" {{ $sessao->status_confirmacao === 'REMARCAR' ? 'selected' : '' }}>Remarcar</option>
                        </select>
                    </form>
                </div>

                <div class="mobile-actions-grid">
                    <a href="{{ route('sessoes.edit', $sessao) }}" class="btn btn-sm btn-warning">Editar</a>

                    @if($temTelefone && $dataBase)
                        <a href="{{ route('sessoes.whatsapp', $sessao->id) }}"
                           target="_blank"
                           class="btn btn-sm btn-success btn-whatsapp-lembrete">
                            WhatsApp
                        </a>
                    @else
                        <button type="button" class="btn btn-sm btn-success" disabled>WhatsApp</button>
                    @endif

                    <button type="button"
                            class="btn btn-sm btn-outline-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#modalRecorrencia"
                            data-sessao-id="{{ $sessao->id }}">
                        Recorrências
                    </button>

                    <a href="{{ route('evolucoes.create', [
                            'paciente' => $sessao->paciente_id,
                            'data' => now()->format('Y-m-d'),
                            'sessao_id' => $sessao->id
                        ]) }}"
                       class="btn btn-sm btn-outline-success">
                        Evolução
                    </a>
                </div>

                <form action="{{ route('sessoes.destroy', $sessao) }}"
                      method="POST"
                      class="form-excluir no-spinner mt-2">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="query_string" value="">
                    <input type="hidden" name="aba" value="">
                    <button type="submit" class="btn btn-sm btn-danger w-100">Excluir</button>
                </form>
            </div>
        </div>
    @empty
        <div class="alert alert-info text-center">Nenhuma sessão encontrada.</div>
    @endforelse
</div>

<style>
    .sessoes-table thead th {
        font-size: .88rem;
        font-weight: 700;
        color: #212529;
        white-space: nowrap;
        padding: 1rem .85rem;
    }

    .sessoes-table tbody td {
        padding: 1rem .85rem;
        vertical-align: middle;
    }

    .sessoes-table tbody tr:not(:last-child) td {
        border-bottom: 1px solid #edf0f2;
    }

    .nome-paciente {
        line-height: 1.35;
    }

    .acoes-stack .btn {
        min-width: 92px;
    }

    .info-box {
        background: #f8f9fa;
        border-radius: .85rem;
        padding: .85rem .9rem;
        height: 100%;
    }

    .info-label {
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #6c757d;
        font-weight: 700;
        margin-bottom: .35rem;
    }

    .info-value {
        color: #212529;
        font-weight: 600;
    }

    .sessao-mobile-card {
        border-radius: 1rem;
    }

    .info-stack {
        display: flex;
        flex-direction: column;
        gap: .65rem;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        padding: .7rem .85rem;
        border-radius: .8rem;
        background: #f8f9fa;
    }

    .info-row-label {
        font-size: .8rem;
        color: #6c757d;
        font-weight: 700;
    }

    .info-row-value {
        font-size: .9rem;
        color: #212529;
        font-weight: 600;
        text-align: right;
    }

    .mobile-actions-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .6rem;
    }

    @media (max-width: 575.98px) {
        .mobile-actions-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
