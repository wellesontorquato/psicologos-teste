@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Teste de envio via WhatsApp</h1>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="/teste-whatsapp">
        @csrf
        <div class="mb-3">
            <label for="numero" class="form-label">Número (com DDI):</label>
            <input type="text" name="numero" id="numero" class="form-control" placeholder="Ex: 5599999999999" required>
        </div>
        <div class="mb-3">
            <label for="mensagem" class="form-label">Mensagem:</label>
            <textarea name="mensagem" id="mensagem" class="form-control" rows="4" required></textarea>
        </div>
        <button class="btn btn-primary">Enviar Mensagem</button>
    </form>
</div>
@endsection
