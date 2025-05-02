@extends('layouts.app')
@section('title', 'Evoluções | PsiGestor')
@section('content')
<div class="container">
    <h2>Evoluções</h2>

    <a href="{{ route('evolucoes.create') }}" class="btn btn-primary mb-3">Nova Evolução</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Paciente</th>
                <th>Data</th>
                <th>Anotação</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($evolucoes as $evolucao)
                <tr>
                    <td>{{ $evolucao->paciente->nome }}</td>
                    <td>{{ \Carbon\Carbon::parse($evolucao->data)->format('d/m/Y') }}</td>
                    <td>{{ Str::limit($evolucao->texto, 50) }}</td>
                    <td>
                        <a href="{{ route('evolucoes.edit', $evolucao) }}" class="btn btn-warning btn-sm">Editar</a>
                        <form action="{{ route('evolucoes.destroy', $evolucao) }}" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Deseja excluir?')">Excluir</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
