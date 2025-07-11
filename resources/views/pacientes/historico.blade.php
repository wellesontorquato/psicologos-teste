@extends('layouts.app')

@section('title', 'Histórico do Paciente | PsiGestor')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center py-3 mb-4 border-bottom">
        <h2 class="mb-0">
            <i class="bi bi-journal-text me-2 text-primary"></i>
            Histórico de {{ $paciente->nome }}
        </h2>

        <a href="{{ route('pacientes.historico.pdf', $paciente->id) }}" target="_blank" class="btn btn-danger shadow-sm">
            <i class="bi bi-file-earmark-pdf me-1"></i> Exportar PDF
        </a>
    </div>

    @if ($eventos->count() === 0)
        <div class="alert alert-warning">
            Nenhuma sessão ou evolução registrada para este paciente.
        </div>
    @else
        @php
            $eventosAgrupados = $eventos->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item['data'])->format('d/m/Y');
            });
        @endphp

        <ul class="timeline">
            @foreach ($eventosAgrupados as $dataFormatada => $eventosDoDia)
                @php
                    // Verifica se há pelo menos uma sessão confirmada neste grupo
                    $exibirGrupo = $eventosDoDia->contains(function ($evento) {
                        $status = \Illuminate\Support\Str::upper(trim($evento['status_confirmacao'] ?? ''));
                        return $evento['tipo'] !== 'Sessão' || $status === 'CONFIRMADA';
                    });
                @endphp

                @if (!$exibirGrupo)
                    @continue
                @endif

                <li class="timeline-item position-relative mb-5 ps-4 border-start border-info">
                    <span class="position-absolute top-0 start-0 translate-middle p-2 rounded-circle border border-light bg-info"></span>

                    <div class="p-3 rounded bg-light border">
                        <h5 class="fw-bold text-info mb-3">📅 Eventos do dia {{ $dataFormatada }}</h5>

                        @foreach ($eventosDoDia as $evento)
                            @php
                                $isMedicacao = str_starts_with($evento['descricao'], 'Medicação registrada:') || str_starts_with($evento['descricao'], 'Medicação Inicial:');
                                $isInicial = str_starts_with($evento['descricao'], 'Medicação Inicial:');
                                $status = \Illuminate\Support\Str::upper(trim($evento['status_confirmacao'] ?? ''));
                                $isSessaoConfirmada = $evento['tipo'] === 'Sessão' && $status === 'CONFIRMADA';
                            @endphp

                            @if ($evento['tipo'] === 'Sessão' && !$isSessaoConfirmada)
                                @continue
                            @endif

                            <div class="mb-4">
                                <h6 class="fw-bold">
                                    @if ($evento['tipo'] === 'Sessão')
                                        🧘 Sessão
                                    @elseif ($isMedicacao)
                                        💊 {{ $isInicial ? 'Medicação Inicial' : 'Nova Medicação' }}
                                    @else
                                        📄 Evolução
                                    @endif
                                </h6>

                                @if ($evento['hora'])
                                    <p class="text-muted mb-1">{{ $evento['hora'] }}</p>
                                @endif

                                <div>
                                    @if (!$isMedicacao && $evento['tipo'] !== 'Sessão')
                                        <p class="fw-semibold text-primary mb-2">Lembrete para a próxima sessão:</p>
                                    @endif
                                    {!! $evento['descricao'] !!}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="mt-4 d-flex justify-content-center">
            {{ $eventos->links() }}
        </div>
    @endif

    <a href="{{ route('pacientes.index') }}" class="btn btn-secondary mt-4">← Voltar</a>
</div>
@endsection

@section('styles')
<style>
.timeline {
    list-style: none;
    padding-left: 0;
    position: relative;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #0d6efd;
}
.timeline-item {
    position: relative;
    margin-left: 20px;
}
</style>
@endsection
