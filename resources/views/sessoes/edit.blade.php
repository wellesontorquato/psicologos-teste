@extends('layouts.app')

@section('title', 'Editar Sessão | PsiGestor')

@section('content')
<div class="container">
    <h2 class="mb-4">Editar Sessão</h2>

    <form method="POST" action="{{ route('sessoes.update', $sessao->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="paciente_id" class="form-label">Paciente</label>
            <select name="paciente_id" id="paciente_id" class="form-control" required>
                @foreach($pacientes as $paciente)
                    <option value="{{ $paciente->id }}" {{ $sessao->paciente_id == $paciente->id ? 'selected' : '' }}>
                        {{ $paciente->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="data_hora" class="form-label">Data e Hora</label>
            <input type="datetime-local" name="data_hora" id="data_hora" class="form-control"
                   value="{{ \Carbon\Carbon::parse($sessao->data_hora)->format('Y-m-d\TH:i') }}" required>
        </div>

        <div class="mb-3">
            <label for="duracao" class="form-label">Duração (minutos)</label>
            <input type="number" name="duracao" id="duracao" class="form-control"
                   value="{{ $sessao->duracao }}" required>
        </div>

        <div class="mb-3">
            <label for="valor" class="form-label">Valor (R$)</label>
            <input type="number" step="0.01" name="valor" id="valor" class="form-control"
                   value="{{ $sessao->valor }}">
        </div>

        <!-- Campo oculto para garantir envio de "0" se o checkbox estiver desmarcado -->
        <input type="hidden" name="foi_pago" value="0">

        <div class="mb-3 form-check">
            <input type="checkbox" name="foi_pago" id="foiPago" class="form-check-input" value="1"
                   {{ $sessao->foi_pago ? 'checked' : '' }}>
            <label class="form-check-label" for="foiPago">Foi Pago?</label>
        </div>

        <button type="submit" class="btn btn-primary">Atualizar</button>
        <a href="{{ route('sessoes.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Conflito de Horário',
                text: '{{ session('error') }}',
                confirmButtonColor: '#3085d6',
            });
        </script>
    @endif
@endsection

