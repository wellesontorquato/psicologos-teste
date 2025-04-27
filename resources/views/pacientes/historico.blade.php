@extends('layouts.app')

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

    @if (empty($eventos))
        <div class="alert alert-warning">
            Nenhuma sessão ou evolução registrada para este paciente.
        </div>
    @else
        <ul class="timeline">
            @foreach ($eventos as $evento)
                @php
                    $isMedicacao = str_starts_with($evento['descricao'], 'Medicação registrada:') || str_starts_with($evento['descricao'], 'Medicação Inicial:');
                    $isInicial = str_starts_with($evento['descricao'], 'Medicação Inicial:');
                @endphp

                <li class="timeline-item position-relative mb-5 ps-4 border-start 
                    {{ $evento['tipo'] === 'Sessão' ? 'border-success' : ($isMedicacao ? 'border-danger' : 'border-primary') }}">
                    <span class="position-absolute top-0 start-0 translate-middle p-2 rounded-circle border border-light
                        {{ $evento['tipo'] === 'Sessão' ? 'bg-success' : ($isMedicacao ? 'bg-danger' : 'bg-primary') }}">
                    </span>

                    <h5 class="fw-bold">
                        @if ($evento['tipo'] === 'Sessão')
                            🧘 Sessão — {{ $evento['data'] }}
                        @elseif ($isMedicacao)
                            💊 {{ $isInicial ? 'Medicação Inicial' : 'Nova Medicação' }} — {{ $evento['data'] }}
                        @else
                            📄 Evolução — {{ $evento['data'] }}
                        @endif
                    </h5>

                    @if($evento['hora'])
                        <p class="text-muted mb-1">{{ $evento['hora'] }}</p>
                    @endif

                    <div>{!! $evento['descricao'] !!}</div>
                </li>
            @endforeach
        </ul>
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
