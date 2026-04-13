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
<div class="d-none d-lg-block">
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0 sessoes-table">
                <thead class="table-light">
                    <tr>
                        <th style="min-width: 240px;">Paciente</th>
                        <th style="min-width: 170px;">Data</th>
                        <th style="width: 90px;">Duração</th>
                        <th style="width: 130px;">Valor</th>
                        <th style="width: 90px;">Pago?</th>
                        <th style="min-width: 190px;">Confirmação</th>
                        <th style="min-width: 170px;">Lembrete</th>
                        <th style="min-width: 250px;">Ações</th>
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

                            $dataFormatada = null;
                            if (!is_null($sessao->data_hora)) {
                                $dataFormatada = \Carbon\Carbon::parse($sessao->data_hora)->format('d/m/Y');
                                $horaFormatada = \Carbon\Carbon::parse($sessao->data_hora)->format('H:i');
                            }

                            $lembreteEnviado = (int) $sessao->lembrete_enviado === 1;
                        @endphp

                        <tr>
                            <td>
                                <div class="fw-semibold text-dark mb-1">{{ $sessao->paciente->nome }}</div>
                                @if(!empty($sessao->paciente->telefone))
                                    <div class="small text-muted">{{ $sessao->paciente->telefone }}</div>
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
                                    <div class="fw-semibold">{{ $dataFormatada }}</div>
                                    <div class="small text-muted">{{ $horaFormatada }}</div>
                                @endif
                            </td>

                            <td>
                                <span class="text-dark fw-medium">{{ $sessao->duracao }} min</span>
                            </td>

                            <td>
                                <div class="fw-bold text-dark">{{ $simbolo }} {{ number_format((float) $sessao->valor, 2, ',', '.') }}</div>
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
                                            Base: {{ \Carbon\Carbon::parse($dataBase)->format('d/m/Y H:i') }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('sessoes.edit', $sessao) }}" class="btn btn-sm btn-warning">Editar</a>

                                    @if($temTelefone && $dataBase)
                                        <a href="{{ route('sessoes.whatsapp', $sessao->id) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-success btn-whatsapp-lembrete">
                                            WhatsApp
                                        </a>
                                    @else
                                        <button type="button"
                                                class="btn btn-sm btn-success"
                                                disabled>
                                            WhatsApp
                                        </button>
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
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Nenhuma sessão encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MOBILE + TABLET --}}
<div class="d-lg-none">
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

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                    <div>
                        <h5 class="card-title mb-1 fs-6 fw-semibold">{{ $sessao->paciente->nome }}</h5>
                        @if(!empty($sessao->paciente->telefone))
                            <div class="small text-muted">{{ $sessao->paciente->telefone }}</div>
                        @endif
                    </div>

                    <span class="badge rounded-pill text-bg-{{ $statusInfo[2] }}">
                        {{ $statusInfo[0] }} {{ $statusInfo[1] }}
                    </span>
                </div>

                <div class="row g-2 small mb-3">
                    <div class="col-12">
                        <div class="p-2 rounded bg-light">
                            <div class="text-muted mb-1">Data</div>
                            <div class="fw-medium">
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

                    <div class="col-4">
                        <div class="p-2 rounded bg-light h-100">
                            <div class="text-muted mb-1">Duração</div>
                            <div class="fw-medium">{{ $sessao->duracao }} min</div>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="p-2 rounded bg-light h-100">
                            <div class="text-muted mb-1">Valor</div>
                            <div class="fw-medium">{{ $simbolo }} {{ number_format((float) $sessao->valor, 2, ',', '.') }}</div>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="p-2 rounded bg-light h-100">
                            <div class="text-muted mb-1">Pago?</div>
                            <div>
                                <span class="badge rounded-pill {{ $sessao->foi_pago ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $sessao->foi_pago ? 'Sim' : 'Não' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="small text-muted mb-1">Lembrete</div>
                    <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
                        <span class="badge rounded-pill {{ $lembreteEnviado ? 'text-bg-success' : 'text-bg-light text-dark border' }}">
                            {{ $lembreteEnviado ? 'Enviado' : 'Pendente' }}
                        </span>

                        @if($lembreteEnviado && $dataBase)
                            <span class="small text-muted">
                                Base: {{ \Carbon\Carbon::parse($dataBase)->format('d/m/Y H:i') }}
                            </span>
                        @endif
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

                <div class="d-grid gap-2">
                    <div class="d-grid grid-actions-2 gap-2">
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
                          class="form-excluir no-spinner">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="query_string" value="">
                        <input type="hidden" name="aba" value="">
                        <button type="submit" class="btn btn-sm btn-danger w-100">Excluir</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info text-center">Nenhuma sessão encontrada.</div>
    @endforelse
</div>

<style>
    .sessoes-table thead th {
        font-size: 0.92rem;
        font-weight: 700;
        color: #212529;
        white-space: nowrap;
    }

    .sessoes-table tbody td {
        vertical-align: middle;
        padding: 1rem 0.75rem;
    }

    .grid-actions-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }

    @media (max-width: 575.98px) {
        .grid-actions-2 {
            grid-template-columns: 1fr;
        }
    }
</style>
