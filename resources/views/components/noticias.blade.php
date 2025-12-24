<section class="section-news" style="background-color: #ffffff; padding: 60px 20px;">
    <div style="max-width: 1200px; margin: auto;">
        <h2 style="text-align: center; font-size: 1.8rem; margin-bottom: 20px; font-weight: bold;">
            📰 Últimas Notícias
        </h2>
        <p style="text-align: center; color: #666; max-width: 700px; margin: 0 auto 40px;">
            Saúde mental, bem-estar e inovações do universo PsiGestor, em um só lugar.
        </p>

        {{-- Container que será preenchido via JS --}}
        <div id="home-news-root">
            {{-- Skeleton (carregando) --}}
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
                @for ($i = 0; $i < 3; $i++)
                    <div style="
                        display: flex;
                        gap: 15px;
                        align-items: flex-start;
                        padding: 10px;
                        border-radius: 10px;
                        background: #f9f9f9;
                        border: 1px solid #eee;
                    ">
                        <div style="width: 80px; height: 80px; border-radius: 8px; background: #eaeaea;"></div>
                        <div style="flex: 1;">
                            <div style="height: 14px; width: 80%; background: #eaeaea; border-radius: 6px; margin-bottom: 10px;"></div>
                            <div style="height: 12px; width: 90%; background: #efefef; border-radius: 6px; margin-bottom: 6px;"></div>
                            <div style="height: 12px; width: 70%; background: #efefef; border-radius: 6px;"></div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>
</section>

<script>
(function () {
    const root = document.getElementById('home-news-root');
    if (!root) return;

    const endpoint = @json(route('home.news'));

    // Pequena util: escapa HTML pra evitar XSS em título (boa prática)
    function escapeHtml(str) {
        return String(str ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    // Excerpt pode vir com HTML (no seu caso é texto), então vamos manter seguro removendo tags
    function stripTags(html) {
        const div = document.createElement('div');
        div.innerHTML = html ?? '';
        return (div.textContent || div.innerText || '').trim();
    }

    function renderNews(items) {
        if (!Array.isArray(items) || items.length === 0) {
            root.innerHTML = `<p style="text-align: center; color: #999;">Nenhuma notícia publicada ainda.</p>`;
            return;
        }

        const cards = items.slice(0, 6).map(article => {
            const title = escapeHtml(article.title);
            const excerpt = escapeHtml(stripTags(article.excerpt)).slice(0, 180); // corte leve p/ não estourar

            const url = @json(url('/blog')) + '/' + encodeURIComponent(article.slug);

            let imgHtml = '';
            if (article.image_url) {
                const imageUrl = escapeHtml(article.image_url);
                const webpUrl = article.image_webp_url ? escapeHtml(article.image_webp_url) : '';

                imgHtml = `
                    <picture>
                        ${webpUrl && webpUrl !== imageUrl ? `<source srcset="${webpUrl}" type="image/webp">` : ``}
                        <img
                            src="${imageUrl}"
                            alt="${title}"
                            loading="lazy"
                            width="80"
                            height="80"
                            style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;"
                        >
                    </picture>
                `;
            }

            return `
                <a href="${url}" style="
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
                    ${imgHtml}
                    <div style="flex: 1">
                        <h3 style="font-size: 1rem; font-weight: 600; margin: 0 0 6px; line-height: 1.3; color: #333;">
                            ${title}
                        </h3>
                        <p style="font-size: 0.85rem; color: #666; line-height: 1.4; max-height: 2.8em; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                            ${excerpt}
                        </p>
                    </div>
                </a>
            `;
        }).join('');

        root.innerHTML = `
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
                ${cards}
            </div>
        `;
    }

    fetch(endpoint, {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        cache: 'no-store' // força o browser a não reaproveitar
    })
    .then(async (res) => {
        if (!res.ok) throw new Error('Falha ao buscar notícias');
        return await res.json();
    })
    .then(renderNews)
    .catch(() => {
        root.innerHTML = `<p style="text-align: center; color: #999;">Não foi possível carregar as notícias agora.</p>`;
    });
})();
</script>
