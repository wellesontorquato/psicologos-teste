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
                text: @json(session('error')),
                confirmButtonColor: '#00aaff'
            });
        </script>
    @endif

    @php
        // Normaliza a data da sessão (se vier por parâmetro)
        $sessaoDt = null;
        if (isset($sessao) && $sessao && $sessao->data_hora) {
            $sessaoDt = $sessao->data_hora instanceof \Carbon\Carbon
                ? $sessao->data_hora
                : \Carbon\Carbon::parse($sessao->data_hora);
        }

        // Valor padrão da data do formulário
        $valorDataForm = old(
            'data',
            $dataSelecionada
                ?? ($sessaoDt ? $sessaoDt->format('Y-m-d') : '')
        );
    @endphp

    {{-- Formulário --}}
    <form action="{{ route('evolucoes.store') }}" method="POST" class="card p-3 shadow-sm">
        @csrf

        {{-- Paciente --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Paciente</label>
            <select id="pacienteSelect"
                    name="paciente_id"
                    class="form-select shadow-sm"
                    required
                    {{ (isset($sessao) && $sessao) ? 'disabled' : '' }}>
                <option value="">-- Selecione --</option>
                @foreach($pacientes as $paciente)
                    <option value="{{ $paciente->id }}"
                        {{ (string)old('paciente_id', $pacienteSelecionado ?? ($sessao->paciente_id ?? '')) === (string)$paciente->id ? 'selected' : '' }}>
                        {{ $paciente->nome }}
                    </option>
                @endforeach
            </select>
            @if(isset($sessao) && $sessao)
                {{-- Se o select estiver desabilitado, enviamos um hidden para manter o valor --}}
                <input type="hidden" name="paciente_id" value="{{ $sessao->paciente_id }}">
            @endif
        </div>

        {{-- Data --}}
        @if(isset($sessao) && $sessao)
            <input type="hidden" name="data" value="{{ $valorDataForm }}">
            <p class="text-muted">
                <strong>Data da Sessão:</strong>
                {{ $valorDataForm ? \Carbon\Carbon::parse($valorDataForm)->format('d/m/Y') : '—' }}
            </p>
        @else
            <div class="mb-3">
                <label class="form-label fw-semibold">Data</label>
                <input type="date"
                       name="data"
                       class="form-control shadow-sm"
                       value="{{ $valorDataForm }}"
                       required>
            </div>
        @endif

        {{-- Sessão vinculada --}}
        @if(isset($sessao) && $sessao)
            <input type="hidden" name="sessao_id" value="{{ $sessao->id }}">
            <p class="text-muted">
                Evolução vinculada automaticamente à sessão de
                <span class="badge bg-success">
                    {{ $sessaoDt ? $sessaoDt->format('d/m/Y H:i') : 'Sem data definida' }}
                </span>
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
document.addEventListener('DOMContentLoaded', function () {
  const pacienteSelect = document.getElementById('pacienteSelect');
  const sessaoSelect   = document.getElementById('sessaoSelect');
  const selectedOld    = String(@json(old('sessao_id', '')) || '');

  if (!pacienteSelect || !sessaoSelect) return;

  function preencherSessoes(pacienteId) {
    sessaoSelect.innerHTML = '<option value="">Carregando sessões...</option>';
    sessaoSelect.disabled = true;

    if (!pacienteId) {
      sessaoSelect.innerHTML = '<option value="">-- Selecione o paciente primeiro --</option>';
      sessaoSelect.disabled = false;
      return;
    }

    fetch(`/pacientes/${encodeURIComponent(pacienteId)}/sessoes`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept':'application/json' }
    })
    .then(async (res) => {
      if (!res.ok) throw new Error('HTTP ' + res.status);
      return res.json();
    })
    .then((lista) => {
      sessaoSelect.innerHTML = '<option value="">-- Sem vínculo --</option>';

      if (!Array.isArray(lista) || lista.length === 0) {
        sessaoSelect.insertAdjacentHTML('beforeend', '<option value="">Nenhuma sessão encontrada</option>');
        return;
      }

      lista.forEach((s) => {
        const opt = document.createElement('option');
        opt.value = s.id;
        // O endpoint retorna "label" (ex.: "15/08/2025 09:00 (50min)")
        opt.textContent = s.label || s.data_hora || 'Sem data / remarcar';
        if (String(s.id) === selectedOld) opt.selected = true;
        sessaoSelect.appendChild(opt);
      });
    })
    .catch(() => {
      sessaoSelect.innerHTML = '<option value="">Erro ao carregar sessões</option>';
    })
    .finally(() => {
      sessaoSelect.disabled = false;
    });
  }

  // Carrega quando o usuário troca o paciente
  pacienteSelect.addEventListener('change', function () {
    preencherSessoes(this.value);
  });

  // Dispara carregamento automático se já houver paciente selecionado (ex.: vindo da agenda)
  if (pacienteSelect.value) {
    preencherSessoes(pacienteSelect.value);
  }
});
</script>
@endif
@endsection
