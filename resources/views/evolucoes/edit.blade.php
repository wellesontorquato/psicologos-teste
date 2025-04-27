@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Editar Evolução</h2>

    <form action="{{ route('evolucoes.update', $evolucao) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Paciente</label>
            <select name="paciente_id" class="form-control" required>
                @foreach($pacientes as $paciente)
                    <option value="{{ $paciente->id }}" {{ $evolucao->paciente_id == $paciente->id ? 'selected' : '' }}>
                        {{ $paciente->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Data</label>
            <input type="date" name="data" class="form-control" value="{{ $evolucao->data }}" required>
        </div>

        <div class="mb-3">
            <label>Anotação Clínica</label>
            <textarea name="texto" class="form-control" rows="5" required>{{ $evolucao->texto }}</textarea>
        </div>

        <button class="btn btn-primary">Atualizar</button>
        <a href="{{ route('evolucoes.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
