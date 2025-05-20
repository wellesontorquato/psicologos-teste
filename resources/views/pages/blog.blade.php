@extends('layouts.landing')

@section('content')
<section style="padding: 60px 20px; max-width: 1200px; margin: auto;">
    <h1 style="font-size: 2rem; font-weight: bold; margin-bottom: 30px; display: flex; align-items: center; gap: 10px;">
        <i class="bi bi-newspaper" style="font-size: 1.8rem; color: #00aaff;"></i> Blog do PsiGestor
    </h1>

    @if($news->count())
        <div style="display: flex; flex-direction: column; gap: 30px;">
            @foreach($news as $article)
                <a href="{{ route('blog.show', $article->slug) }}" style="
                    display: flex;
                    align-items: flex-start;
                    gap: 20px;
                    background: #fff;
                    padding: 16px;
                    border: 1px solid #eee;
                    border-radius: 10px;
                    text-decoration: none;
                    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
                    transition: box-shadow 0.3s ease;
                " onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='0 2px 6px rgba(0,0,0,0.05)'">
                    @if ($article->image)
                        <img src="{{ Storage::disk('s3')->url($article->image) }}"
                             alt="{{ $article->title }}"
                             style="width: 160px; height: 100px; object-fit: cover; border-radius: 8px;">
                    @endif
                    <div style="flex: 1;">
                        <h2 style="font-size: 1.1rem; font-weight: 600; color: #222; margin-bottom: 8px;">
                            {{ $article->title }}
                        </h2>
                        <p style="font-size: 0.95rem; color: #555; line-height: 1.4; max-height: 3.6em; overflow: hidden;">
                            {!! $article->excerpt !!}
                        </p>
                        <span style="font-size: 0.9rem; color: #00aaff; margin-top: 8px; display: inline-block;">Ler mais →</span>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Paginação --}}
        <div style="margin-top: 40px;">
            {{ $news->links() }}
        </div>
    @else
        <p style="text-align: center; color: #888;">Nenhuma notícia encontrada.</p>
    @endif
</section>
@endsection
