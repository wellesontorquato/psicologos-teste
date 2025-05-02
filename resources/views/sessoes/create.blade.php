@extends('layouts.app')

@section('title', 'Criar Sessão | PsiGestor')

@section('content')
<div class="container">
    <h2 class="mb-3">Nova Sessão</h2>

    <a href="{{ route('agenda') }}" class="btn btn-outline-secondary mb-4">
        ← Voltar para Agenda
    </a>

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

    <form action="{{ route('sessoes.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="paciente_id" class="form-label">Paciente</label>
            <select name="paciente_id" id="paciente_id" class="form-control" required>
                @foreach($pacientes as $paciente)
                    <option value="{{ $paciente->id }}" {{ old('paciente_id') == $paciente->id ? 'selected' : '' }}>
                        {{ $paciente->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="data_hora" class="form-label">Data e Hora</label>
            <input type="datetime-local" name="data_hora" id="data_hora" class="form-control"
                   value="{{ old('data_hora', request('data') ? \Carbon\Carbon::parse(request('data'))->format('Y-m-d\TH:i') : '') }}"
                   required>
        </div>

        <div class="mb-3">
            <label for="duracao" class="form-label">Duração (minutos)</label>
            <input type="number" name="duracao" id="duracao" class="form-control"
                   value="{{ old('duracao', 50) }}" required>
        </div>

        <div class="mb-3">
            <label for="valor" class="form-label">Valor (R$)</label>
            <input type="number" step="0.01" name="valor" id="valor" class="form-control"
                   value="{{ old('valor') }}">
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="foi_pago" id="foi_pago" class="form-check-input"
                   value="1" {{ old('foi_pago') ? 'checked' : '' }}>
            <label for="foi_pago" class="form-check-label">Foi Pago?</label>
        </div>

        <button class="btn btn-success">Salvar</button>
        <a href="{{ route('sessoes.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
