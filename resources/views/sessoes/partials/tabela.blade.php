{{-- Tabela em telas médias para cima --}}
<div class="d-none d-md-block">
    <table class="table table-bordered table-hover shadow-sm bg-white">
        <thead class="table-light">
            <tr>
                <th>Paciente</th>
                <th>Data</th>
                <th>Duração</th>
                <th>Valor</th>
                <th>Pago?</th>
                <th>Status</th>
                <th>Ações</th>
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
                @endphp
                <tr>
                    <td>{{ $sessao->paciente->nome }}</td>
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
                        @php
                            $moeda = $sessao->moeda ?? 'BRL';

                            $simbolos = [
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

                            $simbolo = $simbolos[$moeda] ?? $moeda;
                        @endphp

                        <span class="fw-bold">{{ $simbolo }} {{ number_format($sessao->valor, 2, ',', '.') }}</span>

                        <span class="badge bg-light text-dark border ms-1" style="font-size: 0.7rem;">
                            {{ $moeda }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $sessao->foi_pago ? 'bg-success' : 'bg-secondary' }}">
                            {{ $sessao->foi_pago ? 'Sim' : 'Não' }}
                        </span>
                    </td>
                    <td class="{{ $icone[1] }}">{{ $icone[0] }} {{ $icone[2] }}</td>
                    <td>
                        <a href="{{ route('sessoes.edit', $sessao) }}" class="btn btn-warning btn-sm">Editar</a>

                        <form action="{{ route('sessoes.destroy', $sessao) }}" method="POST" class="form-excluir d-inline no-spinner">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="query_string" value="">
                            <input type="hidden" name="aba" value="">
                            <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                        </form>

                        <button type="button"
                            class="btn btn-outline-primary btn-sm mt-1"
                            data-bs-toggle="modal"
                            data-bs-target="#modalRecorrencia"
                            data-sessao-id="{{ $sessao->id }}">
                            Recorrências
                        </button>

                        <a href="{{ route('evolucoes.create', [
                                'paciente' => $sessao->paciente_id,
                                'sessao_id' => $sessao->id,
                                'data' => now()->format('Y-m-d') // força data de hoje
                            ]) }}"
                        class="btn btn-outline-success btn-sm mt-1">
                            Evolução
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Nenhuma sessão encontrada.</td>
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
        @endphp
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h5 class="card-title mb-2">{{ $sessao->paciente->nome }}</h5>
                <p class="mb-1"><i class="bi bi-calendar-event"></i> 
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
                @php
                    $moeda = $sessao->moeda ?? 'BRL';

                    $simbolos = [
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

                    $simbolo = $simbolos[$moeda] ?? $moeda;
                @endphp

                <p class="mb-1">
                    <i class="bi bi-cash-coin"></i> 
                    <span class="fw-bold">{{ $simbolo }} {{ number_format($sessao->valor, 2, ',', '.') }}</span>

                    <span class="badge bg-light text-dark border ms-1" style="font-size: 0.7rem;">
                        {{ $moeda }}
                    </span>
                </p>
                <p class="mb-1"><i class="bi bi-credit-card"></i> 
                    <span class="badge {{ $sessao->foi_pago ? 'bg-success' : 'bg-secondary' }}">
                        {{ $sessao->foi_pago ? 'Sim' : 'Não' }}
                    </span>
                </p>
                <p class="mb-2 {{ $icone[1] }}">{{ $icone[0] }} {{ $icone[2] }}</p>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('sessoes.edit', $sessao) }}" class="btn btn-warning btn-sm">Editar</a>

                    <form action="{{ route('sessoes.destroy', $sessao) }}" method="POST" class="form-excluir d-inline no-spinner">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="query_string" value="">
                        <input type="hidden" name="aba" value="">
                        <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                    </form>

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
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info text-center">Nenhuma sessão encontrada.</div>
    @endforelse
</div>
