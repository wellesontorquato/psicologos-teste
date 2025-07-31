@extends('layouts.profissional')

@section('title', $user->name . ' | Perfil Profissional')

@section('content')
<div class="profile-grid">
    
    {{-- COLUNA DA ESQUERDA (SIDEBAR) --}}
    <aside class="profile-sidebar">
        
        {{-- CARD DO PERFIL --}}
        <div class="card profile-header-card">
            <img src="{{ $user->profile_photo_url ?? asset('images/default-profile.png') }}" 
                 class="profile-photo" 
                 alt="Foto de {{ $user->name }}">

            <h2>{{ $user->name }}</h2>
            <p class="specialty">{{ $user->especialidade ?? 'Psicólogo(a)' }}</p>

            @php
                use Illuminate\Support\Str;
                $link = $user->link_principal;
                $btnClass = 'btn-default';
                $btnText = 'Agende sua consulta';
                $btnIcon = 'bi-calendar-check';

                if ($link) {
                    if (Str::contains($link, ['wa.me', 'whatsapp.com'])) {
                        $btnClass = 'btn-whatsapp'; $btnText = 'WhatsApp'; $btnIcon = 'bi-whatsapp';
                    } elseif (Str::contains($link, 'instagram.com')) {
                        $btnClass = 'btn-instagram'; $btnText = 'Instagram'; $btnIcon = 'bi-instagram';
                    } elseif (Str::contains($link, 'facebook.com')) {
                        $btnClass = 'btn-facebook'; $btnText = 'Facebook'; $btnIcon = 'bi-facebook';
                    } elseif (Str::contains($link, 'linkedin.com')) {
                        $btnClass = 'btn-linkedin'; $btnText = 'LinkedIn'; $btnIcon = 'bi-linkedin';
                    } elseif (Str::contains($link, 'calendly.com')) {
                        $btnClass = 'btn-calendly'; $btnText = 'Agende via Calendly'; $btnIcon = 'bi-calendar-event';
                    } elseif (Str::contains($link, ['t.me', 'telegram.me'])) {
                        $btnClass = 'btn-telegram'; $btnText = 'Telegram'; $btnIcon = 'bi-telegram';
                    }
                }
            @endphp

            @if(!empty($link))
                <a href="{{ $link }}" class="btn-link {{ $btnClass }}" target="_blank" rel="noopener noreferrer">
                    <i class="bi {{ $btnIcon }}"></i>
                    {{ $btnText }}
                </a>
            @else
                <p class="text-muted mt-3">
                    <em>Link de contato não configurado.</em>
                </p>
            @endif
        </div>

        {{-- CARD DE CONTATO --}}
        <div class="card contact-info">
            <h4><i class="bi bi-person-rolodex"></i> Contato</h4>
            @if(!empty($user->whatsapp))
                <p>
                    <i class="bi bi-whatsapp" style="color:#25D366"></i>
                    <a href="https://wa.me/55{{ preg_replace('/\D/','',$user->whatsapp) }}" target="_blank">
                        {{ $user->whatsapp }}
                    </a>
                </p>
            @endif
            <p>
                <i class="bi bi-envelope-at-fill" style="color:#6c757d"></i>
                <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
            </p>
        </div>
    </aside>

    {{-- COLUNA DA DIREITA (CONTEÚDO PRINCIPAL) --}}
    <main class="profile-main-content">
        
        {{-- SOBRE MIM --}}
        <div class="card">
            <h4><i class="bi bi-person-vcard-fill"></i> Sobre mim</h4>
            <p style="white-space: pre-wrap;">{{ $user->bio ?? 'Este profissional ainda não adicionou uma descrição.' }}</p>
        </div>

        {{-- ÁREAS DE ATUAÇÃO --}}
        @php
            // Decodifica as áreas apenas uma vez
            $areas = !empty($user->areas) ? json_decode($user->areas, true) : [];
        @endphp
        
        @if(is_array($areas) && count($areas) > 0)
            <div class="card">
                <h4><i class="bi bi-journals"></i> Áreas de Atuação</h4>
                <ul class="areas-list">
                    @foreach($areas as $area)
                        <li>{{ $area }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </main>
</div>
@endsection