@extends('layouts.profissional')

@section('title', $user->name . ' | Perfil Profissional')

@section('content')
<div class="profile-grid">
    
    {{-- COLUNA DA ESQUERDA (SIDEBAR) --}}
    <aside class="profile-sidebar">
        
        {{-- CARD DO PERFIL --}}
        <div class="card profile-header-card">
            <img src="{{ $user->profile_photo_url ?? versao('images/default-profile.png') }}" 
                 class="profile-photo" 
                 alt="Foto de {{ $user->name }}">

            <h2>{{ $user->name }}</h2>
            <p class="specialty">{{ $user->tipo_profissional ?? 'Psicólogo(a)' }}</p>

            @php
                use Illuminate\Support\Str;
                $links = array_filter([$user->link_principal, $user->link_extra1, $user->link_extra2]);
                $botoes = [];

                foreach ($links as $link) {
                    $btn = ['class' => 'btn-default', 'text' => 'Acessar Link', 'icon' => 'bi-link-45deg', 'url' => $link];

                    if (Str::contains($link, ['wa.me', 'whatsapp.com'])) {
                        $btn = ['class' => 'btn-whatsapp', 'text' => 'WhatsApp', 'icon' => 'bi-whatsapp', 'url' => $link];
                    } elseif (Str::contains($link, 'instagram.com')) {
                        $btn = ['class' => 'btn-instagram', 'text' => 'Instagram', 'icon' => 'bi-instagram', 'url' => $link];
                    } elseif (Str::contains($link, 'facebook.com')) {
                        $btn = ['class' => 'btn-facebook', 'text' => 'Facebook', 'icon' => 'bi-facebook', 'url' => $link];
                    } elseif (Str::contains($link, 'linkedin.com')) {
                        $btn = ['class' => 'btn-linkedin', 'text' => 'LinkedIn', 'icon' => 'bi-linkedin', 'url' => $link];
                    } elseif (Str::contains($link, 'calendly.com')) {
                        $btn = ['class' => 'btn-calendly', 'text' => 'Agendar Consulta', 'icon' => 'bi-calendar-event', 'url' => $link];
                    } elseif (Str::contains($link, ['t.me', 'telegram.me'])) {
                        $btn = ['class' => 'btn-telegram', 'text' => 'Telegram', 'icon' => 'bi-telegram', 'url' => $link];
                    }
                    $botoes[] = $btn;
                }
            @endphp

            @if(count($botoes) > 0)
                <div class="links-container">
                    @foreach($botoes as $btn)
                        <a href="{{ e($btn['url']) }}" 
                           class="btn-link {{ $btn['class'] }}" 
                           target="_blank" rel="noopener noreferrer">
                            <i class="bi {{ $btn['icon'] }}"></i>
                            {{ $btn['text'] }}
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-muted mt-3" style="font-size: 0.9rem;">
                    <em>Nenhum link de contato configurado.</em>
                </p>
            @endif
        </div>

        {{-- CARD DE CONTATO --}}
        <div class="card contact-info">
            <h4><i class="bi bi-person-rolodex"></i> Contato</h4>
            {{-- Mostra o WhatsApp aqui apenas se não houver um botão principal para ele --}}
            @if(!empty($user->whatsapp) && !collect($botoes)->contains('text', 'WhatsApp'))
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
            <blockquote class="bio-text">{{ $user->bio ?? 'Este profissional ainda não adicionou uma descrição.' }}</blockquote>
        </div>

        {{-- ÁREAS DE ATUAÇÃO --}}
        @php
            $areas = json_decode($user->areas ?? '[]', true) ?? [];
        @endphp
        
        @if(count($areas) > 0)
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