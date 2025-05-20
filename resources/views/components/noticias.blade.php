<section class="section-news" style="background-color: #ffffff; padding: 5px 20px 5px 20px;">
    <div style="max-width: 1200px; margin: auto;">
        <h2 style="text-align: center; font-size: 1.8rem; margin-bottom: 12px; font-weight: bold;">
            📰 Últimas Notícias
        </h2>
        <p style="text-align: center; color: #666; max-width: 700px; margin: 0 auto 20px;">
            Fique por dentro das novidades e atualizações do PsiGestor.
        </p>

        @if($news->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
                @foreach($news->take(6) as $article)
                    <a href="{{ route('blog.show', $article->slug) }}" style="
                        display: flex;
                        gap: 15px;
                        align-items: flex-start;
                        text-decoration: none;
                        color: inherit;
                        padding: 10px;
                        border-radius: 10px;
                        background: #f9f9f9;
                        transition: background 0.3s ease;
                    " onmouseover="this.style.background='#f1faff'" onmouseout="this.style.background='#f9f9f9'">
                        @if ($article->image)
                            <img src="{{ Storage::disk('s3')->url($article->image) }}"
                                 alt="{{ $article->title }}"
                                 style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                        @endif
                        <div style="flex: 1">
                            <h3 style="font-size: 1rem; font-weight: 600; margin: 0 0 6px; line-height: 1.3; color: #333;">
                                {{ $article->title }}
                            </h3>
                            <p style="font-size: 0.85rem; color: #666; line-height: 1.4; max-height: 2.8em; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                {!! $article->excerpt !!}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <p style="text-align: center; color: #999;">Nenhuma notícia publicada ainda.</p>
        @endif
    </div>
</section>
