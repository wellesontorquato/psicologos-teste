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

        <div class="mb-3">
            <label class="form-label fw-semibold">Paciente</label>
            <select name="paciente_id" class="form-select shadow-sm" required>
                @foreach($pacientes as $paciente)
                    <option value="{{ $paciente->id }}" {{ $evolucao->paciente_id == $paciente->id ? 'selected' : '' }}>
                        {{ $paciente->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Data</label>
            <input type="date" name="data" class="form-control shadow-sm" 
                   value="{{ $evolucao->data }}" required>
        </div>

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
