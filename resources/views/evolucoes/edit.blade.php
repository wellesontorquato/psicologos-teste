@extends('layouts.app')
@section('title', 'Editar Evolução | PsiGestor')
@section('content')
<div class="container">
    <h2 class="mb-3">Editar Evolução</h2>

    {{-- Botão Voltar --}}
    <a href="{{ route('evolucoes.index') }}" class="btn btn-outline-secondary mb-3 btn-voltar-sessoes">
        <i class="bi bi-arrow-left"></i> Voltar para lista de evoluções
    </a>

    {{-- Formulário --}}
    <form action="{{ route('evolucoes.update', $evolucao) }}" method="POST" class="card p-3 shadow-sm">
        @csrf
        @method('PUT')

        {{-- Paciente --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Paciente</label>
            <select id="pacienteSelect" name="paciente_id" class="form-select shadow-sm" required {{ $evolucao->sessao ? 'readonly disabled' : '' }}>
                <option value="">-- Selecione --</option>
                @foreach($pacientes as $paciente)
                    <option value="{{ $paciente->id }}" {{ $evolucao->paciente_id == $paciente->id ? 'selected' : '' }}>
                        {{ $paciente->nome }}
                    </option>
                @endforeach
            </select>
            @if($evolucao->sessao)
                <input type="hidden" name="paciente_id" value="{{ $evolucao->paciente_id }}">
            @endif
        </div>

        {{-- Data --}}
        @if($evolucao->sessao)
            <input type="hidden" name="data" value="{{ \Carbon\Carbon::parse($evolucao->data)->format('Y-m-d') }}">
            <p class="text-muted">
                <strong>Data da Sessão:</strong> {{ \Carbon\Carbon::parse($evolucao->data)->format('d/m/Y') }}
            </p>
        @else
            <div class="mb-3">
                <label class="form-label fw-semibold">Data</label>
                <input type="date" name="data" class="form-control shadow-sm" 
                       value="{{ \Carbon\Carbon::parse($evolucao->data)->format('Y-m-d') }}" required>
            </div>
        @endif

        {{-- Sessão vinculada --}}
        @if($evolucao->sessao)
            <input type="hidden" name="sessao_id" value="{{ $evolucao->sessao->id }}">
            <p class="text-muted">
                Evolução vinculada à sessão de
                <strong>{{ $evolucao->sessao->data_hora->format('d/m/Y H:i') }}</strong>.
            </p>
        @else
            <div class="mb-3">
                <label class="form-label fw-semibold">Sessão (opcional)</label>
                <select id="sessaoSelect" name="sessao_id" class="form-select shadow-sm">
                    <option value="">-- Selecionar sessão --</option>
                    @foreach($sessoesPaciente as $s)
                        @php
                            // Se seu model Sessao tem cast: 'data_hora' => 'datetime', dá pra usar optional()
                            $labelData = optional($s->data_hora)?->format('d/m/Y H:i');
                            if (!$labelData && $s->data_hora) {
                                // caso não tenha cast, garante formatação
                                $labelData = \Illuminate\Support\Carbon::parse($s->data_hora)->format('d/m/Y H:i');
                            }
                            $label = $labelData ? $labelData . ' (' . (int)($s->duracao ?? 0) . 'min)' : 'Sem data / remarcar';
                        @endphp
                        <option value="{{ $s->id }}" {{ (int)$evolucao->sessao_id === (int)$s->id ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        {{-- Texto da Evolução --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Anotação Clínica</label>
            <textarea name="texto" class="form-control shadow-sm" rows="5" required>{{ $evolucao->texto }}</textarea>
        </div>

        {{-- Botões responsivos --}}
        <div class="d-flex flex-column flex-md-row gap-2">
            <button type="submit" class="btn btn-primary w-100 w-md-auto">Atualizar</button>
            <a href="{{ route('evolucoes.index') }}" class="btn btn-secondary w-100 w-md-auto">Cancelar</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pacienteSelect = document.getElementById('pacienteSelect');
    const sessaoSelect = document.getElementById('sessaoSelect');

    // só ativa AJAX se a evolução NÃO estiver vinculada a uma sessão
    @if(!$evolucao->sessao)
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
                                    if ("{{ $evolucao->sessao_id }}" == sessao.id) {
                                        opt.selected = true;
                                    }
                                    sessaoSelect.appendChild(opt);
                                });
                            }
                        })
                        .catch(() => {
                            sessaoSelect.innerHTML = '<option value="">Erro ao carregar sessões</option>';
                        });
                } else {
                    sessaoSelect.innerHTML = '<option value="">-- Selecione o paciente --</option>';
                }
            });

            // carrega automaticamente se já houver paciente selecionado
            if (pacienteSelect.value) {
                pacienteSelect.dispatchEvent(new Event('change'));
            }
        }
    @endif
});
</script>
@endsection
