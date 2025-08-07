@extends('layouts.app')

@section('title', 'Importar sessões em Massa | PsiGestor')

@section('content')
<div class="container mt-5">
    <h3>Importar Sessões por Planilha</h3>

    @if (session('sucesso'))
        <script>
            Swal.fire('Sucesso!', '{{ session('sucesso') }}', 'success');
        </script>
    @endif

    <a href="{{ route('sessoes.modelo') }}" class="btn btn-outline-secondary mb-3 no-spinner">
        <i class="bi bi-download no-spinner"></i> Baixar planilha modelo
    </a>

    <form action="{{ route('sessoes.importar') }}" method="POST" enctype="multipart/form-data" class="mt-4">
        @csrf
        <div class="mb-3">
            <label for="arquivo" class="form-label">Selecione o arquivo Excel (.xlsx)</label>
            <input type="file" name="arquivo" id="arquivo" class="form-control" required accept=".xlsx,.xls">
        </div>
        <button type="submit" class="btn btn-primary">Importar Sessões</button>
    </form>

    <hr class="my-4">
    <p class="text-muted">Formato esperado da planilha:</p>
    <ul>
        <li><strong>Paciente</strong> – Nome idêntico ao cadastro</li>
        <li><strong>Data</strong> – Ex: 31/07/2025 10:00</li>
        <li><strong>Duração (Minutos)</strong> – Ex: 50</li>
        <li><strong>Valor</strong> – Ex: 150,00</li>
        <li><strong>Pago</strong> – Sim / Não</li>
        <li><strong>Status</strong> – Confirmada / Pendente</li>
        <li><strong>Evolução</strong> – Texto livre (opcional, mas obrigatório se for sessão passada)</li>
    </ul>
</div>
@endsection
