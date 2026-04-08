@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Simular resposta de paciente</h1>

    @if(session('success'))
        <div style="padding:12px; background:#d1fae5; color:#065f46; margin-bottom:16px;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="padding:12px; background:#fee2e2; color:#991b1b; margin-bottom:16px;">
            <ul style="margin:0; padding-left:18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.whatsapp.simular.enviar') }}">
        @csrf

        <div style="margin-bottom:12px;">
            <label>Número do paciente</label>
            <input type="text" name="telefone" value="{{ old('telefone') }}" placeholder="Ex: 5538999999999 ou 38999999999" style="width:100%; padding:10px;">
        </div>

        <div style="margin-bottom:12px;">
            <label>Resposta do paciente</label>
            <input type="text" name="resposta" value="{{ old('resposta') }}" placeholder="Ex: 1" style="width:100%; padding:10px;">
        </div>

        <div style="margin-bottom:12px;">
            <label>Evento</label>
            <input type="text" name="event" value="{{ old('event', 'onmessage') }}" style="width:100%; padding:10px;">
        </div>

        <div style="margin-bottom:12px;">
            <label>Tipo</label>
            <input type="text" name="type" value="{{ old('type', 'chat') }}" style="width:100%; padding:10px;">
        </div>

        <div style="margin-bottom:12px;">
            <label>Message ID opcional</label>
            <input type="text" name="message_id" value="{{ old('message_id') }}" placeholder="Se vazio, será gerado automaticamente" style="width:100%; padding:10px;">
        </div>

        <button type="submit" style="padding:12px 18px; cursor:pointer;">
            Simular resposta
        </button>
    </form>

    @if(session('resultado_webhook'))
        <div style="margin-top:24px;">
            <h3>Resultado</h3>
            <pre style="background:#111; color:#0f0; padding:16px; overflow:auto;">{{ json_encode(session('resultado_webhook'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    @endif
</div>
@endsection
