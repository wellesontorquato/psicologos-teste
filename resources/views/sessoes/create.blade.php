@extends('layouts.app')

@section('title', 'Criar Sessão | PsiGestor')

@section('content')
<div class="container">
    <h2 class="mb-3">Nova Sessão</h2>

    {{-- Botão Voltar --}}
    <a href="{{ route('sessoes.index') }}" class="btn btn-outline-secondary mb-3 btn-voltar-sessoes">
        <i class="bi bi-arrow-left"></i> Voltar para lista de sessões
    </a>

    {{-- Erro via SweetAlert --}}
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
    <form action="{{ route('sessoes.store') }}" method="POST" class="card p-3 shadow-sm">
        @csrf

        <div class="mb-3">
            <label for="paciente_id" class="form-label fw-semibold">Paciente</label>
            <select name="paciente_id" id="paciente_id" class="form-select shadow-sm" required>
                @foreach($pacientes as $paciente)
                    <option value="{{ $paciente->id }}" {{ old('paciente_id') == $paciente->id ? 'selected' : '' }}>
                        {{ $paciente->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="data_hora" class="form-label fw-semibold">Data e Hora</label>
            <input type="datetime-local" name="data_hora" id="data_hora" 
                   class="form-control shadow-sm"
                   value="{{ old('data_hora', request('data') ? \Carbon\Carbon::parse(request('data'))->format('Y-m-d\TH:i') : '') }}"
                   required>
        </div>

        <div class="mb-3">
            <label for="duracao" class="form-label fw-semibold">Duração (minutos)</label>
            <input type="number" name="duracao" id="duracao" 
                   class="form-control shadow-sm"
                   value="{{ old('duracao', 50) }}" required>
        </div>

        <div class="mb-3">
            <label for="valor" class="form-label fw-semibold">Valor (R$)</label>
            <input type="number" step="0.01" name="valor" id="valor" 
                   class="form-control shadow-sm"
                   value="{{ old('valor') }}">
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="foi_pago" id="foi_pago" class="form-check-input"
                   value="1" {{ old('foi_pago') ? 'checked' : '' }}>
            <label for="foi_pago" class="form-check-label fw-semibold">Foi Pago?</label>
        </div>

        {{-- Botões Responsivos --}}
        <div class="d-flex flex-column flex-md-row gap-2">
            <button type="submit" class="btn btn-success w-100 w-md-auto">Salvar</button>
            <a href="{{ route('sessoes.index') }}" class="btn btn-secondary w-100 w-md-auto">Cancelar</a>
        </div>
    </form>
</div>
@endsection
