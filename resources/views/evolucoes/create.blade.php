@extends('layouts.app')
@section('title', 'Criar Evolução | PsiGestor')
@section('content')
<div class="container">
    <h2 class="mb-3">Nova Evolução</h2>

    {{-- Botão Voltar --}}
    <a href="{{ route('evolucoes.index') }}" class="btn btn-outline-secondary mb-3 btn-voltar-sessoes">
        <i class="bi bi-arrow-left"></i> Voltar para lista de evoluções
    </a>

    {{-- Exibe erro via SweetAlert --}}
    @if(session('error'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Erro ao salvar',
                text: '{{ session('error') }}',
                confirmButtonColor: '#00aaff'
            });
        </script>
    @endif

    {{-- Formulário --}}
    <form action="{{ route('evolucoes.store') }}" method="POST" class="card p-3 shadow-sm">
        @csrf

        {{-- Paciente --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Paciente</label>
            <select id="pacienteSelect" name="paciente_id" class="form-select shadow-sm" 
                    required {{ isset($sessao) && $sessao ? 'disabled' : '' }}>
                <option value="">-- Selecione --</option>
                @foreach($pacientes as $paciente)
                    <option value="{{ $paciente->id }}"
                        {{ old('paciente_id', $pacienteSelecionado ?? ($sessao?->paciente_id ?? '')) == $paciente->id ? 'selected' : '' }}>
                        {{ $paciente->nome }}
                    </option>
                @endforeach
            </select>
            @if(isset($sessao) && $sessao)
                <input type="hidden" name="paciente_id" value="{{ $sessao->paciente_id }}">
            @endif
        </div>

        {{-- Data --}}
        @if(isset($sessao) && $sessao)
            <input type="hidden" name="data" value="{{ $dataSelecionada }}">
            <p class="text-muted">
                <strong>Data da Sessão:</strong> {{ \Carbon\Carbon::parse($dataSelecionada)->format('d/m/Y') }}
            </p>
        @else
            <div class="mb-3">
                <label class="form-label fw-semibold">Data</label>
                <input type="date" name="data" class="form-control shadow-sm"
                    value="{{ old('data', $dataSelecionada ?? '') }}" required>
            </div>
        @endif

        {{-- Sessão vinculada --}}
        @if(isset($sessao) && $sessao)
            <input type="hidden" name="sessao_id" value="{{ $sessao->id }}">
            <p class="text-muted">
                Evolução vinculada automaticamente à sessão de 
                <span class="badge bg-success">{{ $sessao->data_hora->format('d/m/Y H:i') }}</span>
            </p>
        @else
            <div class="mb-3">
                <label class="form-label fw-semibold">Sessão (opcional)</label>
                <select id="sessaoSelect" name="sessao_id" class="form-select shadow-sm">
                    <option value="">-- Selecione o paciente primeiro --</option>
                </select>
            </div>
        @endif

        {{-- Texto da Evolução --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Anotação Clínica</label>
            <textarea name="texto" class="form-control shadow-sm" rows="5" required>{{ old('texto') }}</textarea>
        </div>

        {{-- Botões --}}
        <div class="d-flex flex-column flex-md-row gap-2">
            <button class="btn btn-success w-100 w-md-auto">Salvar</button>
            <a href="{{ route('evolucoes.index') }}" class="btn btn-secondary w-100 w-md-auto">Cancelar</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
@if(!isset($sessao) || !$sessao)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pacienteSelect = document.getElementById('pacienteSelect');
    const sessaoSelect = document.getElementById('sessaoSelect');

    if (pacienteSelect && sessaoSelect) {
        pacienteSelect.addEventListener('change', function() {
            const pacienteId = this.value;
            sessaoSelect.innerHTML = '<option value="">Carregando sessões...</option>';

            if (pacienteId) {
                fetch(`/pacientes/${pacienteId}/sessoes`)
                    .then(res => res.json())
                    .then(data => {
                        sessaoSelect.innerHTML = '<option value="">-- Sem vínculo --</option>';
                        if (data.length === 0) {
                            sessaoSelect.innerHTML = '<option value="">Nenhuma sessão encontrada</option>';
                        } else {
                            data.forEach(sessao => {
                                const opt = document.createElement('option');
                                opt.value = sessao.id;
                                opt.textContent = sessao.data_hora;
                                sessaoSelect.appendChild(opt);
                            });
                        }
                    })
                    .catch(() => {
                        sessaoSelect.innerHTML = '<option value="">Erro ao carregar sessões</option>';
                    });
            } else {
                sessaoSelect.innerHTML = '<option value="">-- Selecione o paciente primeiro --</option>';
            }
        });

        // dispara carregamento automático se paciente já vier selecionado
        if (pacienteSelect.value) {
            pacienteSelect.dispatchEvent(new Event('change'));
        }
    }
});
</script>
@endif
@endsection
