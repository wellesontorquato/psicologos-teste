@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Nova Evolução</h2>

    <form action="{{ route('evolucoes.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Paciente</label>
            <select name="paciente_id" class="form-control" required>
                @foreach($pacientes as $paciente)
                    <option value="{{ $paciente->id }}">{{ $paciente->nome }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Data</label>
            <input type="date" name="data" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Anotação Clínica</label>
            <textarea name="texto" class="form-control" rows="5" required></textarea>
        </div>

        <button class="btn btn-success">Salvar</button>
        <a href="{{ route('evolucoes.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
