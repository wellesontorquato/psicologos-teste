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
                <td>R$ {{ number_format($sessao->valor, 2, ',', '.') }}</td>
                <td>
                    <span class="badge {{ $sessao->foi_pago ? 'bg-success' : 'bg-secondary' }}">
                        {{ $sessao->foi_pago ? 'Sim' : 'Não' }}
                    </span>
                </td>
                <td class="{{ $icone[1] }}">{{ $icone[0] }} {{ $icone[2] }}</td>
                <td>
                    <a href="{{ route('sessoes.edit', $sessao) }}" class="btn btn-warning btn-sm">Editar</a>
                    
                    {{-- Formulário com suporte a query e aba ativa --}}
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

                    <a href="{{ route('evolucoes.create', ['paciente' => $sessao->paciente_id, 'data' => optional($sessao->data_hora)->format('Y-m-d')]) }}"
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
