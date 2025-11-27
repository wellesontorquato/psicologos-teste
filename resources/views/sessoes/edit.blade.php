@extends('layouts.app')

@section('title', 'Editar Sessão | PsiGestor')

@section('content')
<div class="container">
    <h2 class="mb-4 text-center text-md-start">Editar Sessão</h2>

    <a href="{{ route('sessoes.index') }}" class="btn btn-outline-secondary mb-3 btn-voltar-sessoes">
        <i class="bi bi-arrow-left"></i> Voltar para lista de sessões
    </a>

    <form method="POST" action="{{ route('sessoes.update', $sessao->id) }}" class="card shadow-sm p-4">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="paciente_id" class="form-label fw-semibold">Paciente</label>
            <select name="paciente_id" id="paciente_id" class="form-select" required>
                @foreach($pacientes as $paciente)
                    <option value="{{ $paciente->id }}" {{ $sessao->paciente_id == $paciente->id ? 'selected' : '' }}>
                        {{ $paciente->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label for="data_hora" class="form-label fw-semibold">Data e Hora</label>
                <input type="datetime-local" name="data_hora" id="data_hora" 
                       class="form-control"
                       value="{{ \Carbon\Carbon::parse($sessao->data_hora)->format('Y-m-d\TH:i') }}" required>
            </div>

            <div class="col-6 col-md-3">
                <label for="duracao" class="form-label fw-semibold">Duração (min)</label>
                <input type="number" name="duracao" id="duracao" class="form-control"
                       value="{{ $sessao->duracao }}" required>
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold">Valor</label>
                
                <div class="input-group">
                    <select name="moeda" id="moeda" class="form-select" style="max-width: 110px;">
                        @php
                            $moedas = ['BRL', 'USD', 'EUR', 'GBP', 'ARS', 'CLP', 'MXN', 'CAD', 'AUD'];
                            $moedaSessao = $sessao->moeda ?? 'BRL';
                        @endphp

                        @foreach($moedas as $m)
                            <option value="{{ $m }}" {{ $moedaSessao === $m ? 'selected' : '' }}>
                                {{ $m }}
                            </option>
                        @endforeach
                    </select>

                    <input 
                        type="number" 
                        step="0.01" 
                        name="valor" 
                        id="valor" 
                        class="form-control"
                        value="{{ $sessao->valor }}"
                        placeholder="0,00">
                </div>
                <small class="text-muted">
                    Valor cobrado na moeda selecionada.
                </small>
            </div>
        </div>

        <!-- Campo oculto para garantir envio de "0" se o checkbox estiver desmarcado -->
        <input type="hidden" name="foi_pago" value="0">

        <div class="form-check mt-3">
            <input type="checkbox" name="foi_pago" id="foiPago" class="form-check-input" value="1"
                   {{ $sessao->foi_pago ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="foiPago">Foi Pago?</label>
        </div>

        <div class="d-flex flex-column flex-md-row gap-2 mt-4">
            <button type="submit" class="btn btn-primary w-100 w-md-auto">Atualizar</button>
            <a href="{{ route('sessoes.index') }}" class="btn btn-secondary w-100 w-md-auto">Cancelar</a>
        </div>
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
