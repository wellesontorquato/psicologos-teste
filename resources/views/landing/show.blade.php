@extends('layouts.profissional')

@section('content')
<div class="container py-5">
    <div class="text-center mb-4">
        <img src="{{ $user->profile_photo_url ?? asset('images/default-profile.png') }}" 
             class="rounded-circle shadow-sm mb-3" width="120" height="120" 
             alt="Foto de {{ $user->name }}">

        <h2 class="fw-bold">{{ $user->name }}</h2>
        <p class="text-muted">{{ $user->especialidade ?? 'Psicólogo(a)' }}</p>

        @php
            use Illuminate\Support\Str;

            $link = $user->link_principal;
            $textoBotao = 'Agende sua consulta';
            $iconeBotao = 'bi-calendar-event';
            $corBotao = '#00aaff'; // cor padrão PsiGestor

            if ($link) {
                if (Str::contains($link, ['wa.me', 'whatsapp.com'])) {
                    $textoBotao = 'Fale comigo no WhatsApp';
                    $iconeBotao = 'bi-whatsapp';
                    $corBotao = '#25D366';
                } elseif (Str::contains($link, 'instagram.com')) {
                    $textoBotao = 'Fale comigo no Instagram';
                    $iconeBotao = 'bi-instagram';
                    $corBotao = '#C13584';
                } elseif (Str::contains($link, 'facebook.com')) {
                    $textoBotao = 'Fale comigo no Facebook';
                    $iconeBotao = 'bi-facebook';
                    $corBotao = '#1877F2';
                } elseif (Str::contains($link, 'linkedin.com')) {
                    $textoBotao = 'Fale comigo no LinkedIn';
                    $iconeBotao = 'bi-linkedin';
                    $corBotao = '#0A66C2';
                } elseif (Str::contains($link, 'calendly.com')) {
                    $textoBotao = 'Agende via Calendly';
                    $iconeBotao = 'bi-calendar-check';
                    $corBotao = '#0069A5';
                } elseif (Str::contains($link, ['t.me', 'telegram.me'])) {
                    $textoBotao = 'Fale comigo no Telegram';
                    $iconeBotao = 'bi-telegram';
                    $corBotao = '#0088CC';
                }
            }
        @endphp

        @if(!empty($link))
            <a href="{{ $link }}" 
               class="btn px-4 d-inline-flex align-items-center gap-2 text-white"
               style="background-color: {{ $corBotao }}; border:none;"
               target="_blank" rel="noopener noreferrer">
                <i class="bi {{ $iconeBotao }}"></i> {{ $textoBotao }}
            </a>
        @else
            <p class="text-muted mt-3">
                <em>Este profissional ainda não configurou um link de contato.</em>
            </p>
        @endif
    </div>

    {{-- SOBRE MIM --}}
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body">
            <h4 class="mb-3 text-primary">Sobre mim</h4>
            <p>
                {{ $user->bio 
                    ? nl2br(e($user->bio)) 
                    : 'Este profissional ainda não adicionou uma descrição.' 
                }}
            </p>
        </div>
    </div>

    {{-- ÁREAS DE ATUAÇÃO --}}
    @if(!empty($user->areas) && count(json_decode($user->areas, true)) > 0)
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body">
                <h4 class="mb-3 text-primary">Áreas de Atuação</h4>
                <ul class="list-unstyled">
                    @foreach(json_decode($user->areas, true) as $area)
                        <li>• {{ $area }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- CONTATO --}}
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body">
            <h4 class="mb-3 text-primary">Contato</h4>
            
            @if(!empty($user->whatsapp))
                <p>
                    <strong>WhatsApp:</strong> 
                    <a href="https://wa.me/55{{ preg_replace('/\D/','',$user->whatsapp) }}" 
                       target="_blank" class="text-decoration-none">
                       {{ $user->whatsapp }}
                    </a>
                </p>
            @endif

            <p>
                <strong>Email:</strong> 
                <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                    {{ $user->email }}
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
