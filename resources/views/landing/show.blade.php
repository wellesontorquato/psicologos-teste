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

                function montarBotao($link) {
                    if (!$link) return null;
                    $btn = ['class' => 'btn-default', 'text' => 'Acessar', 'icon' => 'bi-link'];

                    if (Str::contains($link, ['wa.me', 'whatsapp.com'])) {
                        $btn = ['class' => 'btn-whatsapp', 'text' => 'WhatsApp', 'icon' => 'bi-whatsapp'];
                    } elseif (Str::contains($link, 'instagram.com')) {
                        $btn = ['class' => 'btn-instagram', 'text' => 'Instagram', 'icon' => 'bi-instagram'];
                    } elseif (Str::contains($link, 'facebook.com')) {
                        $btn = ['class' => 'btn-facebook', 'text' => 'Facebook', 'icon' => 'bi-facebook'];
                    } elseif (Str::contains($link, 'linkedin.com')) {
                        $btn = ['class' => 'btn-linkedin', 'text' => 'LinkedIn', 'icon' => 'bi-linkedin'];
                    } elseif (Str::contains($link, 'calendly.com')) {
                        $btn = ['class' => 'btn-calendly', 'text' => 'Agende via Calendly', 'icon' => 'bi-calendar-event'];
                    } elseif (Str::contains($link, ['t.me', 'telegram.me'])) {
                        $btn = ['class' => 'btn-telegram', 'text' => 'Telegram', 'icon' => 'bi-telegram'];
                    }

                    return array_merge($btn, ['url' => $link]);
                }

                $botoes = [];
                if (!empty($user->link_principal)) $botoes[] = montarBotao($user->link_principal);
                if (!empty($user->link_extra1)) $botoes[] = montarBotao($user->link_extra1);
                if (!empty($user->link_extra2)) $botoes[] = montarBotao($user->link_extra2);
            @endphp

            @if(count($botoes) > 0)
                <div class="links-container">
                    @foreach($botoes as $btn)
                        <a href="{{ $btn['url'] }}" 
                           class="btn-link {{ $btn['class'] }}" 
                           target="_blank" rel="noopener noreferrer">
                            <i class="bi {{ $btn['icon'] }}"></i>
                            {{ $btn['text'] }}
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-muted mt-3">
                    <em>Nenhum link configurado.</em>
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
