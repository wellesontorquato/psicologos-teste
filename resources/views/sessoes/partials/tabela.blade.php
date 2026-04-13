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

{{-- Tabela em telas médias para cima --}}
<div class="d-none d-md-block">
    <table class="table table-bordered table-hover shadow-sm bg-white align-middle">
        <thead class="table-light">
            <tr>
                <th>Paciente</th>
                <th>Data</th>
                <th>Duração</th>
                <th>Valor</th>
                <th>Pago?</th>
                <th>Status</th>
                <th>Lembrete</th>
                <th style="min-width: 290px;">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sessoes as $sessao)
                @php
                    $status = $sessao->status_confirmacao ?? 'PENDENTE';

                    $icone = match($status) {
                        'CONFIRMADA' => ['✅', 'text-success', 'Confirmada'],
                        'CANCELADA'  => ['❌', 'text-danger', 'Cancelada'],
                        'REMARCAR'   => ['🔄', 'text-warning', 'Remarcar'],
                        default      => ['⏳', 'text-secondary', 'Pendente'],
                    };

                    if ($status === 'REMARCAR' && !is_null($sessao->data_hora)) {
                        $icone = ['📅', 'text-info', 'Remarcado'];
                    }

                    $moeda = $sessao->moeda ?? 'BRL';
                    $simbolo = $simbolosMoeda[$moeda] ?? $moeda;

                    $telefoneNormalizado = preg_replace('/\D+/', '', $sessao->paciente->telefone ?? '');
                    $temTelefone = !empty($telefoneNormalizado);
                    $dataBase = $sessao->data_hora_original ?: $sessao->data_hora;
                @endphp

                <tr>
                    <td>
                        <div class="fw-semibold">{{ $sessao->paciente->nome }}</div>

                        @if(!empty($sessao->paciente->telefone))
                            <div class="small text-muted">{{ $sessao->paciente->telefone }}</div>
                        @endif
                    </td>

                    <td>
                        @if(is_null($sessao->data_hora))
                            @if($sessao->status_confirmacao === 'REMARCAR')
                                📝 <span class="text-warning fw-bold">Reagendar Consulta</span>
                            @elseif($sessao->status_confirmacao === 'CANCELADA')
                                ❌ <span class="text-danger fw-bold">Consulta Cancelada</span>
                            @else
                                —
                            @endif
                        @else
                            {{ \Carbon\Carbon::parse($sessao->data_hora)->format('d/m/Y H:i') }}
                        @endif
                    </td>

                    <td>{{ $sessao->duracao }} min</td>

                    <td>
                        <span class="fw-bold">{{ $simbolo }} {{ number_format((float) $sessao->valor, 2, ',', '.') }}</span>
                        <span class="badge bg-light text-dark border ms-1" style="font-size: 0.7rem;">
                            {{ $moeda }}
                        </span>
                    </td>

                    <td>
                        <span class="badge {{ $sessao->foi_pago ? 'bg-success' : 'bg-secondary' }}">
                            {{ $sessao->foi_pago ? 'Sim' : 'Não' }}
                        </span>
                    </td>

                    <td>
                        <div class="{{ $icone[1] }} fw-semibold mb-2">{{ $icone[0] }} {{ $icone[2] }}</div>

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
                        <div class="d-flex flex-column gap-2">
                            <span class="badge {{ (int) $sessao->lembrete_enviado === 1 ? 'bg-success' : 'bg-secondary' }}">
                                {{ (int) $sessao->lembrete_enviado === 1 ? 'Lembrete enviado' : 'Lembrete pendente' }}
                            </span>

                            @if((int) $sessao->lembrete_enviado === 1 && $dataBase)
                                <small class="text-muted">
                                    Base do lembrete:
                                    {{ \Carbon\Carbon::parse($dataBase)->format('d/m/Y H:i') }}
                                </small>
                            @endif
                        </div>
                    </td>

                    <td>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('sessoes.edit', $sessao) }}" class="btn btn-warning btn-sm">
                                Editar
                            </a>

                            @if($temTelefone && $dataBase)
                                <a href="{{ route('sessoes.whatsapp', $sessao->id) }}"
                                   target="_blank"
                                   class="btn btn-success btn-sm btn-whatsapp-lembrete">
                                    WhatsApp
                                </a>
                            @else
                                <button type="button"
                                        class="btn btn-success btn-sm"
                                        disabled
                                        title="Paciente sem telefone válido ou sessão sem data">
                                    WhatsApp
                                </button>
                            @endif

                            <button type="button"
                                    class="btn btn-outline-primary btn-sm"
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
                               class="btn btn-outline-success btn-sm">
                                Evolução
                            </a>

                            <form action="{{ route('sessoes.destroy', $sessao) }}"
                                  method="POST"
                                  class="form-excluir d-inline no-spinner">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="query_string" value="">
                                <input type="hidden" name="aba" value="">
                                <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">Nenhuma sessão encontrada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Cards em telas pequenas --}}
<div class="d-md-none">
    @forelse($sessoes as $sessao)
        @php
            $status = $sessao->status_confirmacao ?? 'PENDENTE';

            $icone = match($status) {
                'CONFIRMADA' => ['✅', 'text-success', 'Confirmada'],
                'CANCELADA'  => ['❌', 'text-danger', 'Cancelada'],
                'REMARCAR'   => ['🔄', 'text-warning', 'Remarcar'],
                default      => ['⏳', 'text-secondary', 'Pendente'],
            };

            if ($status === 'REMARCAR' && !is_null($sessao->data_hora)) {
                $icone = ['📅', 'text-info', 'Remarcado'];
            }

            $moeda = $sessao->moeda ?? 'BRL';
            $simbolo = $simbolosMoeda[$moeda] ?? $moeda;

            $telefoneNormalizado = preg_replace('/\D+/', '', $sessao->paciente->telefone ?? '');
            $temTelefone = !empty($telefoneNormalizado);
            $dataBase = $sessao->data_hora_original ?: $sessao->data_hora;
        @endphp

        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h5 class="card-title mb-1">{{ $sessao->paciente->nome }}</h5>

                @if(!empty($sessao->paciente->telefone))
                    <p class="text-muted small mb-2">{{ $sessao->paciente->telefone }}</p>
                @endif

                <p class="mb-1">
                    <i class="bi bi-calendar-event"></i>
                    @if(is_null($sessao->data_hora))
                        @if($sessao->status_confirmacao === 'REMARCAR')
                            <span class="text-warning fw-bold">Reagendar Consulta</span>
                        @elseif($sessao->status_confirmacao === 'CANCELADA')
                            <span class="text-danger fw-bold">Consulta Cancelada</span>
                        @else
                            —
                        @endif
                    @else
                        {{ \Carbon\Carbon::parse($sessao->data_hora)->format('d/m/Y H:i') }}
                    @endif
                </p>

                <p class="mb-1"><i class="bi bi-clock"></i> {{ $sessao->duracao }} min</p>

                <p class="mb-1">
                    <i class="bi bi-cash-coin"></i>
                    <span class="fw-bold">{{ $simbolo }} {{ number_format((float) $sessao->valor, 2, ',', '.') }}</span>
                    <span class="badge bg-light text-dark border ms-1" style="font-size: 0.7rem;">
                        {{ $moeda }}
                    </span>
                </p>

                <p class="mb-1">
                    <i class="bi bi-credit-card"></i>
                    <span class="badge {{ $sessao->foi_pago ? 'bg-success' : 'bg-secondary' }}">
                        {{ $sessao->foi_pago ? 'Sim' : 'Não' }}
                    </span>
                </p>

                <p class="mb-2 {{ $icone[1] }} fw-semibold">{{ $icone[0] }} {{ $icone[2] }}</p>

                <div class="mb-2">
                    <span class="badge {{ (int) $sessao->lembrete_enviado === 1 ? 'bg-success' : 'bg-secondary' }}">
                        {{ (int) $sessao->lembrete_enviado === 1 ? 'Lembrete enviado' : 'Lembrete pendente' }}
                    </span>
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

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('sessoes.edit', $sessao) }}" class="btn btn-warning btn-sm">Editar</a>

                    @if($temTelefone && $dataBase)
                        <a href="{{ route('sessoes.whatsapp', $sessao->id) }}"
                           target="_blank"
                           class="btn btn-success btn-sm btn-whatsapp-lembrete">
                            WhatsApp
                        </a>
                    @else
                        <button type="button"
                                class="btn btn-success btn-sm"
                                disabled
                                title="Paciente sem telefone válido ou sessão sem data">
                            WhatsApp
                        </button>
                    @endif

                    <button type="button"
                            class="btn btn-outline-primary btn-sm"
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
                       class="btn btn-outline-success btn-sm">
                        Evolução
                    </a>

                    <form action="{{ route('sessoes.destroy', $sessao) }}"
                          method="POST"
                          class="form-excluir d-inline no-spinner">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="query_string" value="">
                        <input type="hidden" name="aba" value="">
                        <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info text-center">Nenhuma sessão encontrada.</div>
    @endforelse
</div>
