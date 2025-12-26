<section class="section-news" style="background-color:#ffffff; padding:60px 20px;">
    <style>
        /* ====== NEWS (sutil upgrade) ====== */
        .news-wrap {
            max-width: 1200px;
            margin: 0 auto;
        }

        .news-title {
            text-align: center;
            font-size: 1.85rem;
            margin-bottom: 14px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #0f172a;
        }

        .news-subtitle {
            text-align: center;
            color: #64748b;
            max-width: 720px;
            margin: 0 auto 36px;
            font-size: 1rem;
            line-height: 1.6;
        }

        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 18px;
        }

        .news-card {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            text-decoration: none;
            color: inherit;
            padding: 14px;
            border-radius: 14px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.05);
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
            position: relative;
            overflow: hidden;
        }

        .news-card:hover {
            transform: translateY(-2px);
            border-color: rgba(0,170,255,0.35);
            box-shadow: 0 10px 24px rgba(0, 170, 255, 0.12);
        }

        .news-thumb {
            width: 82px;
            height: 82px;
            border-radius: 12px;
            object-fit: cover;
            flex: 0 0 auto;
            background: #f1f5f9;
            border: 1px solid #eef2f7;
        }

        .news-content {
            flex: 1;
            min-width: 0; /* evita overflow */
        }

        .news-card h3 {
            font-size: 1.02rem;
            font-weight: 800;
            margin: 0 0 6px;
            line-height: 1.25;
            color: #0f172a;
        }

        .news-card p {
            font-size: 0.92rem;
            color: #475569;
            line-height: 1.45;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .news-meta {
            margin-top: 8px;
            font-size: 0.78rem;
            color: #94a3b8;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .news-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(0, 170, 255, 0.08);
            color: #0369a1;
            border: 1px solid rgba(0, 170, 255, 0.18);
            font-weight: 700;
            font-size: 0.72rem;
            white-space: nowrap;
        }

        /* ====== Skeleton ====== */
        .sk-card {
            display: flex;
            gap: 14px;
            padding: 14px;
            border-radius: 14px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.05);
        }

        .sk-block {
            background: linear-gradient(90deg, #eef2f7 25%, #f6f7fb 37%, #eef2f7 63%);
            background-size: 400% 100%;
            animation: sk 1.2s ease infinite;
            border-radius: 10px;
        }

        .sk-thumb { width: 82px; height: 82px; border-radius: 12px; }
        .sk-line-1 { height: 14px; width: 85%; margin: 2px 0 10px; }
        .sk-line-2 { height: 12px; width: 95%; margin-bottom: 8px; border-radius: 8px; }
        .sk-line-3 { height: 12px; width: 70%; border-radius: 8px; }

        @keyframes sk {
            0% { background-position: 100% 0; }
            100% { background-position: 0 0; }
        }

        /* Responsivo: deixa os cards um pouco mais “respirados” no mobile */
        @media (max-width: 600px) {
            .news-card, .sk-card { padding: 12px; border-radius: 12px; }
            .news-thumb, .sk-thumb { width: 74px; height: 74px; border-radius: 10px; }
            .news-title { font-size: 1.6rem; }
        }
    </style>

    <div class="news-wrap">
        <h2 class="news-title">📰 Últimas Notícias</h2>
        <p class="news-subtitle">
            Saúde mental, bem-estar e inovações do universo PsiGestor, em um só lugar.
        </p>

        {{-- Container preenchido via JS --}}
        <div id="home-news-root">
            {{-- Skeleton (carregando) --}}
            <div class="news-grid">
                @for ($i = 0; $i < 3; $i++)
                    <div class="sk-card">
                        <div class="sk-block sk-thumb"></div>
                        <div style="flex:1;">
                            <div class="sk-block sk-line-1"></div>
                            <div class="sk-block sk-line-2"></div>
                            <div class="sk-block sk-line-3"></div>
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

    function escapeHtml(str) {
        return String(str ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function stripTags(html) {
        const div = document.createElement('div');
        div.innerHTML = html ?? '';
        return (div.textContent || div.innerText || '').trim();
    }

    function renderNews(items) {
        if (!Array.isArray(items) || items.length === 0) {
            root.innerHTML = `<p style="text-align:center; color:#94a3b8;">Nenhuma notícia publicada ainda.</p>`;
            return;
        }

        const baseBlogUrl = @json(url('/blog'));

        const cards = items.slice(0, 6).map(article => {
            const title = escapeHtml(article.title);
            const excerpt = escapeHtml(stripTags(article.excerpt)).slice(0, 220);
            const url = baseBlogUrl + '/' + encodeURIComponent(article.slug);

            let imgHtml = `
                <div class="news-thumb" aria-hidden="true"></div>
            `;

            if (article.image_url) {
                const imageUrl = escapeHtml(article.image_url);
                const webpUrl = article.image_webp_url ? escapeHtml(article.image_webp_url) : '';

                imgHtml = `
                    <picture>
                        ${webpUrl && webpUrl !== imageUrl ? `<source srcset="${webpUrl}" type="image/webp">` : ``}
                        <img
                            class="news-thumb"
                            src="${imageUrl}"
                            alt="${title}"
                            loading="lazy"
                            width="82"
                            height="82"
                        >
                    </picture>
                `;
            }

            return `
                <a href="${url}" class="news-card">
                    ${imgHtml}
                    <div class="news-content">
                        <h3>${title}</h3>
                        <p>${excerpt}</p>
                        <div class="news-meta">
                            <span class="news-pill">Ler notícia</span>
                        </div>
                    </div>
                </a>
            `;
        }).join('');

        root.innerHTML = `<div class="news-grid">${cards}</div>`;
    }

    fetch(endpoint, {
        method: 'GET',
        headers: { 'Accept': 'application/json' },
        cache: 'no-store' // força o browser a não reaproveitar
    })
    .then(async (res) => {
        if (!res.ok) throw new Error('Falha ao buscar notícias');
        return await res.json();
    })
    .then(renderNews)
    .catch(() => {
        root.innerHTML = `<p style="text-align:center; color:#94a3b8;">Não foi possível carregar as notícias agora.</p>`;
    });
})();
</script>
