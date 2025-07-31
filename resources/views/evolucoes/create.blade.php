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

        <div class="mb-3">
            <label class="form-label fw-semibold">Paciente</label>
            <select name="paciente_id" class="form-select shadow-sm" required>
                @foreach($pacientes as $paciente)
                    <option value="{{ $paciente->id }}"
                        {{ old('paciente_id', $pacienteSelecionado ?? '') == $paciente->id ? 'selected' : '' }}>
                        {{ $paciente->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Data</label>
            <input type="date" name="data" class="form-control shadow-sm"
                   value="{{ old('data', $dataSelecionada ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Anotação Clínica</label>
            <textarea name="texto" class="form-control shadow-sm" rows="5" required>{{ old('texto') }}</textarea>
        </div>

        {{-- Botões responsivos --}}
        <div class="d-flex flex-column flex-md-row gap-2">
            <button class="btn btn-success w-100 w-md-auto">Salvar</button>
            <a href="{{ route('evolucoes.index') }}" class="btn btn-secondary w-100 w-md-auto">Cancelar</a>
        </div>
    </form>
</div>
@endsection
