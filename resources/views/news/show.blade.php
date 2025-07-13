@php
    $title = $news->title . ' | PsiGestor';
@endphp

@extends('layouts.landing')

@section('content')
<section style="padding: 40px 20px; background: #fff;">
    <div style="max-width: 800px; margin: auto;">
        {{-- Categoria --}}
        @if ($news->category)
            <p style="
                font-size: 0.8rem;
                font-weight: 600;
                text-transform: uppercase;
                color: #00aaff;
                letter-spacing: 1px;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                border-left: 4px solid #00aaff;
                padding-left: 10px;
                margin-bottom: 12px;
            ">
                <i class="bi bi-tag-fill" style="font-size: 0.85rem;"></i> {{ $news->category }}
            </p>
        @endif

        {{-- Título --}}
        <h1 style="font-size: 2rem; font-weight: bold; margin-bottom: 10px; color: #111;">
            {{ $news->title }}
        </h1>

        {{-- Subtítulo --}}
        @if ($news->subtitle)
            <p style="font-size: 1.15rem; color: #444; margin-top: -5px; margin-bottom: 20px;">
                {{ $news->subtitle }}
            </p>
        @endif

        {{-- Metadados --}}
        <p style="color: #666; font-size: 0.9rem; margin-bottom: 20px;">
            <i class="bi bi-person"></i> Por <strong>{{ $news->author_name ?? 'Equipe PsiGestor' }}</strong><br>
            <i class="bi bi-calendar"></i> Publicado em {{ $news->created_at->format('d/m/Y \à\s H:i') }}
            @if ($news->updated_at && $news->updated_at->gt($news->created_at))
                <br><i class="bi bi-clock-history"></i> Atualizado {{ $news->updated_at->diffForHumans() }}
            @endif
        </p>

        {{-- Imagem de capa --}}
        @if ($news->image)
            <img src="{{ $news->image_url }}"
                 alt="{{ $news->title }}"
                 style="width: 50%; border-radius: 10px; margin-bottom: 30px;">
        @endif

        {{-- Conteúdo --}}
        <div class="noticia-conteudo">
            {!! $news->content !!}
        </div>

        {{-- Compartilhamento --}}
        <div style="margin-top: 40px; margin-bottom: 30px;">
            <p style="font-weight: bold; margin-bottom: 10px; color: #111;">Compartilhe:</p>
            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                @php
                    $url = urlencode(route('blog.show', $news->slug));
                    $title = urlencode($news->title);
                @endphp

                {{-- WhatsApp --}}
                <a href="https://wa.me/?text={{ $title }}%0A{{ $url }}" target="_blank" style="
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    background-color: #25D366;
                    color: #fff;
                    padding: 10px 18px;
                    border-radius: 6px;
                    text-decoration: none;
                    font-weight: 500;
                ">
                    <img src="https://img.icons8.com/?size=100&id=16713&format=png&color=000000" alt="WhatsApp" width="20" height="20">
                </a>

                {{-- Facebook --}}
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ $url }}" target="_blank" style="
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    background-color: #1877F2;
                    color: #fff;
                    padding: 10px 18px;
                    border-radius: 6px;
                    text-decoration: none;
                    font-weight: 500;
                ">
                    <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/facebook/facebook-original.svg" alt="Facebook" width="20" height="20">
                </a>

                {{-- X (Twitter) --}}
                <a href="https://twitter.com/intent/tweet?text={{ $title }}&url={{ $url }}" target="_blank" style="
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    background-color: #000;
                    color: #fff;
                    padding: 10px 18px;
                    border-radius: 6px;
                    text-decoration: none;
                    font-weight: 500;
                ">
                    <img src="https://img.icons8.com/?size=100&id=6Fsj3rv2DCmG&format=png&color=000000" alt="X" width="20" height="20">
                </a>

                {{-- Instagram --}}
                <button onclick="copiarLinkInstagram('{{ route('blog.show', $news->slug) }}')" style="
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    background: linear-gradient(45deg, #f58529, #dd2a7b, #8134af);
                    color: #fff;
                    padding: 10px 18px;
                    border: none;
                    border-radius: 6px;
                    font-weight: 500;
                    cursor: pointer;
                ">
                    <img src="https://img.icons8.com/?size=100&id=32323&format=png&color=000000" alt="Instagram" width="20" height="20">
                </button>
            </div>
        </div>

        {{-- Notícias Relacionadas --}}
        @if ($related->count())
            <div style="margin-top: 60px;">
                <h3 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 20px; color: #111;">📰 Leia também...</h3>

                <div style="display: flex; flex-direction: column; gap: 20px;">
                    @foreach ($related as $item)
                        <a href="{{ route('blog.show', $item->slug) }}" style="
                            display: flex;
                            gap: 16px;
                            align-items: flex-start;
                            background: #f9f9f9;
                            padding: 12px;
                            border-radius: 10px;
                            text-decoration: none;
                            color: inherit;
                            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
                            transition: box-shadow 0.3s ease;
                        " onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'" onmouseout="this.style.boxShadow='0 2px 6px rgba(0,0,0,0.05)'">
                            @if ($item->image)
                                <img src="{{ $item->image_url }}" alt="{{ $item->title }}" style="width: 100px; height: 80px; object-fit: cover; border-radius: 6px;">
                            @endif
                            <div style="flex: 1;">
                                <h4 style="font-size: 1rem; font-weight: 600; color: #222; margin-bottom: 6px;">
                                    {{ $item->title }}
                                </h4>
                                <p style="font-size: 0.9rem; color: #555; line-height: 1.4; max-height: 2.8em; overflow: hidden;">
                                    {!! $item->excerpt !!}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Voltar --}}
        <div style="margin-top: 40px;">
            <a href="{{ route('blog.index') }}" style="
                display: inline-block;
                background: #00aaff;
                color: #fff;
                padding: 10px 20px;
                border-radius: 30px;
                font-weight: 500;
                text-decoration: none;
                transition: background 0.3s ease;
            " onmouseover="this.style.background='#008ecc'" onmouseout="this.style.background='#00aaff'">
                ← Voltar ao blog
            </a>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .noticia-conteudo {
        line-height: 1.7;
        font-size: 1.05rem;
        color: #333;
        overflow: hidden;
    }

    .noticia-conteudo a {
        color: #00aaff;
        font-weight: 500;
        text-decoration: underline;
        transition: color 0.3s ease;
    }

    .noticia-conteudo a:hover {
        color: #008ecc;
    }

    .noticia-conteudo img {
        max-width: 280px;
        border-radius: 10px;
        object-fit: cover;
        height: auto;
    }

    /* Responsivo: todas imagens centralizadas em mobile */
    @media (max-width: 768px) {
        .noticia-conteudo img {
            float: none !important;
            display: block;
            margin: 0 auto 20px auto !important;
            width: 100%;
            max-width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Garante que todos os links no conteúdo abram em nova aba
    document.addEventListener('DOMContentLoaded', function () {
        const content = document.querySelector('.noticia-conteudo');
        if (content) {
            content.querySelectorAll('a').forEach(link => {
                link.setAttribute('target', '_blank');
                link.setAttribute('rel', 'noopener noreferrer');
            });
        }
    });
</script>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const imgs = document.querySelectorAll('.noticia-conteudo img');

        imgs.forEach((img, index) => {
            img.style.maxWidth = '280px';
            img.style.borderRadius = '10px';
            img.style.objectFit = 'cover';
            img.style.margin = '0 20px 20px 0';
            img.style.float = (index % 2 === 0) ? 'left' : 'right';
        });
    });
</script>
@endpush

@push('scripts')
<script>
    function copiarLinkInstagram(url) {
        navigator.clipboard.writeText(url).then(() => {
            alert('Link copiado! Agora é só colar no seu story ou feed do Instagram. 😉');
        });
    }
</script>
@endpush

