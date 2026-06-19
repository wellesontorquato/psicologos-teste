@extends('layouts.app')

@section('title', 'Editar Sessão | PsiGestor')

@section('content')
<style>
    .sess-page {
        width: 100%;
    }

    .sess-content {
        width: 100%;
        max-width: 800px;
        margin: 0 auto;
    }

    .sess-header {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-bottom: 22px;
    }

    .sess-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .sess-subtitle {
        color: #64748b;
        margin: 4px 0 0;
        font-size: .95rem;
    }

    .sess-btn {
        min-height: 44px;
        border-radius: 14px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        white-space: nowrap;
        padding: 0 20px;
        transition: all 0.2s ease;
    }

    .sess-card {
        background: rgba(255,255,255,.98);
        border: 1px solid rgba(226,232,240,.95);
        border-radius: 22px;
        box-shadow: 0 14px 38px rgba(15,23,42,.06);
        padding: 24px;
        margin-bottom: 18px;
    }

    .sess-form-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 18px;
    }

    .sess-field label {
        font-size: .86rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 8px;
        display: block;
    }

    .sess-field .form-control,
    .sess-field .form-select {
        min-height: 48px;
        border-radius: 14px;
        border-color: #dbe3ef;
        font-size: .95rem;
        color: #0f172a;
        background-color: #f8fafc;
        padding: 10px 16px;
        transition: all 0.2s ease;
    }

    .sess-field .form-control:focus,
    .sess-field .form-select:focus {
        border-color: #2563eb;
        background-color: #fff;
        box-shadow: 0 0 0 .25rem rgba(37,99,235,.12);
    }

    .sess-checkbox-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 8px;
        padding: 12px 16px;
        background: #f8fafc;
        border-radius: 14px;
        border: 1px solid #dbe3ef;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .sess-checkbox-wrapper:hover {
        border-color: #cbd5e1;
    }

    .sess-checkbox-wrapper input[type="checkbox"] {
        width: 22px;
        height: 22px;
        border-radius: 6px;
        cursor: pointer;
        accent-color: #2563eb;
    }

    .sess-checkbox-wrapper label {
        margin: 0;
        font-size: .95rem;
        font-weight: 700;
        color: #0f172a;
        cursor: pointer;
    }

    .sess-form-actions {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid #e2e8f0;
    }

    @media (min-width: 768px) {
        .sess-header {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }

        .sess-card {
            padding: 32px;
        }

        .sess-form-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .sess-col-full {
            grid-column: 1 / -1;
        }

        .sess-form-actions {
            grid-template-columns: auto auto;
            justify-content: flex-end;
        }
    }
</style>

<div class="container-fluid py-4 sess-page">
    <div class="sess-content">
        
        <div class="sess-header">
            <div>
                <h1 class="sess-title">Editar Sessão</h1>
                <p class="sess-subtitle">Atualize os detalhes do atendimento agendado.</p>
            </div>

            <a href="{{ route('sessoes.index') }}" class="btn btn-outline-secondary sess-btn">
                <i class="bi bi-arrow-left"></i>
                Voltar para lista
            </a>
        </div>

        <form method="POST" action="{{ route('sessoes.update', $sessao->id) }}" class="sess-card">
            @csrf
            @method('PUT')

            <div class="sess-form-grid">
                
                {{-- Paciente (Ocupa linha toda no desktop) --}}
                <div class="sess-field sess-col-full">
                    <label for="paciente_id">Paciente</label>
                    <select name="paciente_id" id="paciente_id" class="form-select" required>
                        @foreach($pacientes as $paciente)
                            <option value="{{ $paciente->id }}" {{ $sessao->paciente_id == $paciente->id ? 'selected' : '' }}>
                                {{ $paciente->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Data e Hora --}}
                <div class="sess-field">
                    <label for="data_hora">Data e Hora</label>
                    <input type="datetime-local" name="data_hora" id="data_hora" 
                           class="form-control"
                           value="{{ \Carbon\Carbon::parse($sessao->data_hora)->format('Y-m-d\TH:i') }}" 
                           required>
                </div>

                {{-- Duração --}}
                <div class="sess-field">
                    <label for="duracao">Duração (minutos)</label>
                    <input type="number" name="duracao" id="duracao" 
                           class="form-control"
                           value="{{ $sessao->duracao }}" 
                           required>
                </div>

                {{-- Moeda --}}
                <div class="sess-field">
                    <label for="moeda">Moeda</label>
                    <select name="moeda" id="moeda" class="form-select" required>
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
                </div>

                {{-- Valor --}}
                <div class="sess-field">
                    <label for="valor">Valor do Atendimento</label>
                    <input type="number" step="0.01" name="valor" id="valor" 
                           class="form-control"
                           value="{{ $sessao->valor }}"
                           placeholder="0,00">
                </div>

                {{-- Foi Pago (Ocupa linha toda no desktop) --}}
                <div class="sess-col-full">
                    <!-- Fallback oculto se o checkbox for desmarcado -->
                    <input type="hidden" name="foi_pago" value="0">
                    
                    <label class="sess-checkbox-wrapper" for="foi_pago">
                        <input type="checkbox" name="foi_pago" id="foi_pago" 
                               value="1" {{ $sessao->foi_pago ? 'checked' : '' }}>
                        <label for="foi_pago">Este atendimento já foi pago pelo paciente</label>
                    </label>
                </div>

            </div>

            <div class="sess-form-actions">
                <a href="{{ route('sessoes.index') }}" class="btn btn-outline-secondary sess-btn">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary sess-btn">
                    <i class="bi bi-arrow-repeat"></i>
                    Atualizar Sessão
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Conflito de Horário',
            text: '{{ session('error') }}',
            confirmButtonColor: '#2563eb',
            background: '#fff',
            color: '#0f172a',
            customClass: {
                popup: 'sess-swal-popup'
            }
        });
    @endif
</script>
@endsection