@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Sessões</h2>

    <a href="{{ route('sessoes.create') }}" class="btn btn-primary mb-3">Nova Sessão</a>

    {{-- Filtros --}}
    <form method="GET" class="row g-3 mb-4 align-items-end">
        <div class="col-md-3">
            <label class="form-label fw-bold">Pago?</label>
            <select name="foi_pago" class="form-select shadow-sm">
                <option value="">Todos</option>
                <option value="Sim" {{ request('foi_pago') == 'Sim' ? 'selected' : '' }}>Sim</option>
                <option value="Não" {{ request('foi_pago') == 'Não' ? 'selected' : '' }}>Não</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-bold">Status</label>
            <select name="status" class="form-select shadow-sm">
                <option value="Todos">Todos</option>
                <option value="CONFIRMADA" {{ request('status') == 'CONFIRMADA' ? 'selected' : '' }}>Confirmada</option>
                <option value="REMARCAR" {{ request('status') == 'REMARCAR' ? 'selected' : '' }}>Remarcar</option>
                <option value="REMARCADO" {{ request('status') == 'REMARCADO' ? 'selected' : '' }}>Remarcado</option>
                <option value="CANCELADA" {{ request('status') == 'CANCELADA' ? 'selected' : '' }}>Cancelada</option>
                <option value="PENDENTE" {{ request('status') == 'PENDENTE' ? 'selected' : '' }}>Pendente</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-bold">Período</label>
            <select name="periodo" class="form-select shadow-sm">
                <option value="">Todos</option>
                <option value="hoje" {{ request('periodo') == 'hoje' ? 'selected' : '' }}>Hoje</option>
                <option value="semana" {{ request('periodo') == 'semana' ? 'selected' : '' }}>Esta semana</option>
                <option value="proxima" {{ request('periodo') == 'proxima' ? 'selected' : '' }}>Próxima semana</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-bold">Buscar</label>
            <input type="text" name="busca" class="form-control shadow-sm"
                placeholder="Nome, telefone ou e-mail"
                value="{{ request('busca') }}">
        </div>

        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-outline-secondary shadow-sm w-100">🔍 Filtrar</button>
            <a href="{{ route('sessoes.index') }}" class="btn btn-outline-dark shadow-sm w-100">❌ Limpar</a>
        </div>
    </form>

    {{-- Botões de Exportação --}}
    <div class="mb-4">
        <a href="{{ route('sessoes.export', array_merge(request()->all(), ['format' => 'pdf'])) }}"
           class="btn btn-danger me-2 shadow-sm">
            <i class="bi bi-file-earmark-pdf"></i> Exportar PDF
        </a>
        <a href="{{ route('sessoes.export', array_merge(request()->all(), ['format' => 'excel'])) }}"
           class="btn btn-success shadow-sm">
            <i class="bi bi-file-earmark-excel"></i> Exportar Excel
        </a>
    </div>

    {{-- Badges de filtros ativos --}}
    @if(request()->filled('foi_pago') || request()->filled('status') || request()->filled('periodo') || request()->filled('busca'))
        <div class="mb-3">
            <span class="me-2">🔎 <strong>Filtros ativos:</strong></span>
            @if(request()->filled('foi_pago'))
                <span class="badge bg-info text-dark me-1">
                    💰 Pago: {{ request('foi_pago') }}
                </span>
            @endif
            @if(request()->filled('status') && request('status') !== 'Todos')
                <span class="badge bg-warning text-dark me-1">
                    📋 Status: {{ ucfirst(strtolower(request('status'))) }}
                </span>
            @endif
            @if(request()->filled('periodo'))
                <span class="badge bg-primary text-light me-1">
                    📅 Período: {{ ucfirst(request('periodo')) }}
                </span>
            @endif
            @if(request()->filled('busca'))
                <span class="badge bg-secondary me-1">
                    🔍 Busca: {{ request('busca') }}
                </span>
            @endif
        </div>
    @endif

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
            @foreach($sessoes as $sessao)
            @php
                $status = $sessao->status_confirmacao ?? 'PENDENTE';

                if ($status === 'REMARCAR' && !is_null($sessao->data_hora)) {
                    $icone = ['📅', 'text-info', 'Remarcado'];
                } else {
                    $icone = match($status) {
                        'CONFIRMADA' => ['✅', 'text-success', 'Confirmada'],
                        'CANCELADA'  => ['❌', 'text-danger', 'Cancelada'],
                        'REMARCAR'   => ['🔄', 'text-warning', 'Remarcar'],
                        default      => ['⏳', 'text-secondary', 'Pendente'],
                    };
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
                    <td class="{{ $icone[1] }}">
                        {{ $icone[0] }} {{ $icone[2] }}
                    </td>
                    <td>
                        <a href="{{ route('sessoes.edit', $sessao) }}" class="btn btn-warning btn-sm">Editar</a>
                        <form action="{{ route('sessoes.destroy', $sessao) }}" method="POST" class="form-excluir d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Paginação --}}
    <div class="mt-4">
        {{ $sessoes->withQueryString()->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    document.querySelectorAll('.form-excluir').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Tem certeza?',
                text: "Essa ação não poderá ser desfeita.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
