@extends('layouts.landing')

@section('content')

{{-- HERO SECTION --}}
<section class="hero" id="inicio">
    <div class="hero-container">
        
        {{-- COLUNA DE TEXTO --}}
        <div class="hero-text" data-aos="fade-right">
            <h1>A sua prática clínica, <br><strong>organizada e humanizada.</strong></h1>
            <p class="subtitle">
                PsiGestor é a plataforma completa para psicólogos, psicanalistas e psiquiatras.
                Concentre-se no que realmente importa: o cuidado com seus pacientes.
            </p>

            {{-- Chips --}}
            <div class="hero-chips" aria-label="Destaques do PsiGestor">
                <div class="hero-chip">
                    <i class="bi bi-calendar-check"></i>
                    <span>Agenda e lembretes</span>
                </div>

                <div class="hero-chip">
                    <i class="bi bi-shield-check"></i>
                    <span>Prontuário seguro</span>
                </div>

                <div class="hero-chip">
                    <i class="bi bi-graph-up"></i>
                    <span>Financeiro organizado</span>
                </div>
            </div>

            {{-- Barra destaque --}}
            <div class="hero-highlight" role="note" aria-label="Mensagem sazonal de ano novo">
                <span class="hero-highlight-dot" aria-hidden="true"></span>
                <span>Comece o ano com a rotina clínica em dia — sem complicação.</span>
            </div>

            <div class="hero-cta-group">
                <a href="{{ route('register') }}" class="btn-hero-main">
                    Comece seu teste grátis
                </a>
                <div class="trial-box">
                    <strong>🎁 10 dias grátis</strong>
                    <strong><small>✅ Sem cartão de crédito</small></strong>
                    <strong><small>✅ Acesso imediato</small></strong>
                </div>
            </div>
        </div>

        {{-- COLUNA DA IMAGEM/CARROSSEL --}}
        <div class="hero-image-wrapper" data-aos="fade-left" data-aos-delay="200">
            <h3 class="carousel-title">Veja como é a interface do PsiGestor</h3>

            {{-- dica de clique --}}
            <div class="carousel-hint">
                <i class="bi bi-arrows-fullscreen"></i>
                <span>Clique para ampliar</span>
            </div>

            <div class="carousel-tilt" id="carouselTilt">
                <div id="carouselPicture" class="carousel-clickable" role="button" aria-label="Abrir galeria de imagens" tabindex="0">
                    {{-- O conteúdo será preenchido pelo JavaScript --}}
                </div>
                <div class="carousel-dots" id="carouselDots">
                    {{-- Dots serão gerados pelo JavaScript --}}
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ Modal Galeria (Bootstrap) --}}
    <div class="modal fade" id="heroGalleryModal" tabindex="-1" aria-labelledby="heroGalleryTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content hero-gallery-modal">
                <div class="modal-header hero-gallery-header">
                    <div class="d-flex flex-column">
                        <h5 class="modal-title" id="heroGalleryTitle" style="margin:0; font-weight:900;">Galeria PsiGestor</h5>
                        <small class="hero-gallery-subtitle">Use ← → para navegar</small>
                    </div>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <div class="modal-body hero-gallery-body">
                    <button type="button" class="hero-gallery-nav hero-gallery-prev" id="heroGalleryPrev" aria-label="Imagem anterior">
                        <i class="bi bi-chevron-left"></i>
                    </button>

                    <figure class="hero-gallery-figure">
                        <img id="heroGalleryImg" src="" alt="Imagem ampliada do PsiGestor" loading="lazy">
                    </figure>

                    <button type="button" class="hero-gallery-nav hero-gallery-next" id="heroGalleryNext" aria-label="Próxima imagem">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>

                <div class="modal-footer hero-gallery-footer">
                    <span id="heroGalleryCounter" class="hero-gallery-counter">1 / 3</span>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 12px; font-weight: 900;">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- OUTRAS SEÇÕES --}}
@include('components.funcionalidades')
<div class="section-divider"></div>
@include('components.depoimentos')
<div class="section-divider"></div>
@include('components.noticias')

{{-- BOTÃO WHATSAPP FLUTUANTE --}}
<a href="https://wa.me/5582991128022?text=Olá,%20tenho%20interesse%20no%20PsiGestor!"
   aria-label="Abrir conversa no WhatsApp com PsiGestor"
   target="_blank" 
   class="whatsapp-fab">
   <i class="bi bi-whatsapp"></i>
   <span>Fale Conosco</span>
</a>

@endsection

@push('styles')
<style>
/* HERO SECTION */
.hero {
    background: linear-gradient(135deg, #00aaff 0%, #0077ff 100%);
    color: white;
    padding: 60px 20px;
    min-height: 90vh;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-align-items: center;
    -ms-flex-align: center;
    align-items: center;
    overflow: hidden;
}

/* LAYOUT DESKTOP */
.hero-container {
    max-width: 1200px;
    margin: auto;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-align-items: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-justify-content: space-between;
    -ms-flex-pack: justify;
    justify-content: space-between;
    gap: 40px;
}

.hero-text {
    -webkit-flex: 1 1 55%;
    -ms-flex: 1 1 55%;
    flex: 1 1 55%;
    max-width: 600px;
}

.hero-image-wrapper {
    -webkit-flex: 1 1 45%;
    -ms-flex: 1 1 45%;
    flex: 1 1 45%;
    max-width: 500px;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
    -webkit-align-items: center;
    -ms-flex-align: center;
    align-items: center;
}

/* TÍTULO */
.carousel-title {
    font-size: 1.1rem;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.95);
    margin-bottom: 10px;
    text-align: center;
}

/* dica */
.carousel-hint{
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: .9rem;
    color: rgba(255,255,255,.88);
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.18);
    padding: 8px 12px;
    border-radius: 999px;
    margin-bottom: 14px;
    -webkit-backdrop-filter: blur(10px);
    backdrop-filter: blur(10px);
}

.hero-text h1 {
    font-size: 2.8rem;
    font-size: clamp(2rem, 5vw, 3.2rem);
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 20px;
}

.hero-text h1 strong {
    font-weight: 700;
    color: #e0f7ff;
}

.hero-text .subtitle {
    font-size: 1.1rem;
    font-size: clamp(1rem, 2.5vw, 1.15rem);
    margin-bottom: 18px;
    line-height: 1.6;
    color: rgba(255, 255, 255, 0.9);
}

/* Chips */
.hero-chips {
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-flex-wrap: wrap;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 16px;
}

.hero-chip {
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-align-items: center;
    -ms-flex-align: center;
    align-items: center;
    gap: 10px;

    padding: 12px 16px;
    border-radius: 14px;

    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.22);

    -webkit-backdrop-filter: blur(10px);
    backdrop-filter: blur(10px);

    box-shadow: 0 10px 22px rgba(0,0,0,0.12);
    color: rgba(255,255,255,0.92);
    font-weight: 700;
    line-height: 1.1;
}

.hero-chip i { font-size: 1.1rem; opacity: 0.95; }

/* Barra destaque */
.hero-highlight {
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-align-items: center;
    -ms-flex-align: center;
    align-items: center;
    gap: 10px;

    width: 100%;
    max-width: 560px;

    padding: 12px 14px;
    border-radius: 14px;

    background: rgba(0,0,0,0.18);
    border: 1px solid rgba(255,255,255,0.18);

    -webkit-backdrop-filter: blur(10px);
    backdrop-filter: blur(10px);

    box-shadow: 0 12px 28px rgba(0,0,0,0.14);
    color: rgba(255,255,255,0.92);

    margin-bottom: 22px;
}

.hero-highlight-dot {
    width: 10px;
    height: 10px;
    border-radius: 999px;
    background: rgba(255,215,0,0.95);
    box-shadow: 0 0 0 4px rgba(255,215,0,0.18);
    flex: 0 0 auto;
}

.hero-highlight span:last-child { font-weight: 600; }

/* CTA */
.hero-cta-group {
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-flex-wrap: wrap;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
    -webkit-align-items: center;
    -ms-flex-align: center;
    align-items: center;
    gap: 20px;
}

.btn-hero-main {
    background: white;
    color: #0077ff;
    padding: 15px 35px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 1rem;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
}

.btn-hero-main:hover {
    background: #f0f8ff;
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
}

.trial-box {
    padding: 10px 15px;
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 12px;
    -webkit-backdrop-filter: blur(8px);
    backdrop-filter: blur(8px);
    color: #ffffff;
    font-size: 0.9rem;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
    gap: 5px;
}

#carouselPicture {
    position: relative;
    width: 100%;
    padding-top: 56.25%;
    border-radius: 20px;
    overflow: hidden;
    background: rgba(0,0,0,0.2);
}

/* torna clicável para abrir galeria */
.carousel-clickable{
    cursor: pointer;
    outline: none;
}
.carousel-clickable:focus{
    box-shadow: 0 0 0 4px rgba(255,255,255,0.25);
    border-radius: 20px;
}

.carousel-tilt {
    width: 100%;
    position: relative;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
    transform-style: preserve-3d;
    transition: transform 0.4s ease;
    will-change: transform;
    border-radius: 20px;
}

.carousel-tilt img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    border-radius: 20px;
    transition: opacity 0.6s ease-in-out;
}

.carousel-dots {
    position: absolute;
    bottom: -35px;
    left: 50%;
    transform: translateX(-50%);
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    gap: 8px;
}

.dot {
    height: 10px;
    width: 10px;
    background-color: rgba(255, 255, 255, 0.4);
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s ease;
}

.dot.active {
    background-color: white;
    transform: scale(1.2);
}

/* WhatsApp */
.whatsapp-fab {
    position: fixed;
    bottom: 25px;
    right: 25px;
    z-index: 999;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-align-items: center;
    -ms-flex-align: center;
    align-items: center;
    gap: 8px;
    background: #25d366;
    color: white;
    padding: 12px 20px;
    border-radius: 50px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    font-size: 1rem;
}
.whatsapp-fab i { font-size: 1.5rem; }
.whatsapp-fab:hover {
    background: #1ebd5a;
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 6px 16px rgba(0,0,0,0.3);
}

.section-divider {
    border: 0;
    height: 1px;
    background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0));
    margin: 20px auto 0 auto;
    max-width: 80%;
}

/* =========================
   ✅ MODAL GALERIA (paleta do site + desafogo + pan em 100%)
   ========================= */

/* vars de paleta (puxa pro azul do hero, mas bem mais claro) */
:root{
    --pg-primary: #0077ff;
    --pg-primary-2: #00aaff;
    --pg-ink: #0f172a;
    --pg-muted: #64748b;
    --pg-surface: #ffffff;
    --pg-border: rgba(15, 23, 42, 0.10);
    --pg-shadow: 0 28px 90px rgba(2, 6, 23, 0.28);
}

.hero-gallery-modal{
    border-radius: 18px;
    overflow: hidden;
    border: 0;
    background: var(--pg-surface);
    box-shadow: var(--pg-shadow);
}

/* header mais “cara de site”: branco + faixa gradiente sutil */
.hero-gallery-header{
    border: 0;
    color: var(--pg-ink);
    padding: 14px 16px;
    background:
        radial-gradient(1000px 220px at 20% 0%, rgba(0,170,255,0.20), transparent 55%),
        radial-gradient(900px 240px at 85% 0%, rgba(0,119,255,0.18), transparent 55%),
        #fff;
    border-bottom: 1px solid var(--pg-border);
}

.hero-gallery-header .btn-close{
    filter: none;
    opacity: .8;
}
.hero-gallery-header .btn-close:hover{ opacity: 1; }

.hero-gallery-subtitle{
    margin-top: 4px;
    color: var(--pg-muted);
    font-size: .9rem;
}

.hero-gallery-body{
    position: relative;
    padding: 14px 14px 10px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* Toolbar */
.hero-gallery-toolbar{
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap;
}

.hero-gallery-counter{
    color: var(--pg-ink);
    font-weight: 900;
}

/* ações com “pill” igual ao resto do site */
.hero-gallery-actions{
    display: inline-flex;
    gap: 8px;
    flex-wrap: wrap;
}

.hero-gallery-btn{
    border: 1px solid rgba(0,119,255,0.18);
    background: rgba(0,119,255,0.06);
    color: #0454c8;
    border-radius: 999px;
    padding: 8px 12px;
    font-weight: 900;
    cursor: pointer;
    transition: transform .16s ease, background .16s ease, border-color .16s ease;
}
.hero-gallery-btn:hover{
    background: rgba(0,119,255,0.10);
    border-color: rgba(0,119,255,0.28);
    transform: translateY(-1px);
}

/* Stage (zoom/pan) */
.hero-gallery-stage{
    position: relative;
    width: 100%;
    height: min(74vh, 720px);
    border-radius: 16px;
    background:
        linear-gradient(180deg, rgba(0,119,255,0.05), rgba(0,170,255,0.02)),
        #fff;
    border: 1px solid var(--pg-border);
    overflow: hidden;
    /* importante: permite pan mesmo em 100% (sem “capturar” scroll do browser) */
    touch-action: none;
}

/* ✅ imagem agora pode ser “arrastável” mesmo em 100%:
   - ao invés de travar no centro, a gente deixa ela com width/height 100% e object-fit: contain
   - e o pan é aplicado via translate/scale no JS
*/
.hero-gallery-stage img{
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    transform-origin: center center;
    will-change: transform;
    user-select: none;
    -webkit-user-drag: none;
    max-width: none;
    max-height: none;
    /* garante que em 100% ela “encaixa” melhor e diminui corte */
    width: 100%;
    height: 100%;
    object-fit: contain;
}

/* Prev/Next flutuantes (mais leves) */
.hero-gallery-nav{
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 44px;
    height: 44px;
    border-radius: 999px;
    border: 1px solid rgba(0,119,255,0.18);
    background: rgba(255,255,255,0.75);
    color: #0454c8;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: transform .16s ease, background .16s ease, opacity .16s ease;
    z-index: 3;
    -webkit-backdrop-filter: blur(10px);
    backdrop-filter: blur(10px);
}
.hero-gallery-nav:hover{
    transform: translateY(-50%) scale(1.06);
    background: rgba(255,255,255,0.92);
}
.hero-gallery-prev{ left: 10px; }
.hero-gallery-next{ right: 10px; }
.hero-gallery-nav:disabled{
    opacity: .45;
    cursor: default;
}

/* ✅ Desafogo embaixo: thumbs “respiram” e não encostam no footer */
.hero-gallery-thumbs{
    display: flex;
    gap: 10px;
    overflow-x: auto;
    padding: 6px 2px 14px; /* mais “respiro” embaixo */
    scrollbar-width: thin;
}

/* Thumbs */
.hero-thumb{
    flex: 0 0 auto;
    width: 96px;
    height: 58px;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid rgba(0, 119, 255, 0.14);
    background: rgba(0, 119, 255, 0.04);
    cursor: pointer;
    opacity: .84;
    transition: transform .16s ease, opacity .16s ease, border-color .16s ease, box-shadow .16s ease;
}
.hero-thumb img{
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.hero-thumb:hover{
    opacity: 1;
    transform: translateY(-1px);
}
.hero-thumb.is-active{
    opacity: 1;
    border-color: rgba(0,119,255,0.45);
    box-shadow: 0 10px 22px rgba(0,119,255,0.14);
}

/* Footer (mais claro) */
.hero-gallery-footer{
    border-top: 1px solid var(--pg-border);
    background: #fff;
    padding: 12px 16px 14px; /* ✅ “desafogo” extra */
    display:flex;
    justify-content: space-between;
    align-items:center;
    gap: 10px;
    flex-wrap: wrap;
}

/* ====== MOBILE-FIRST: melhora geral ====== */
@media (max-width: 992px) {
    .hero-container {
        -webkit-flex-direction: column;
        -ms-flex-direction: column;
        flex-direction: column;
        text-align: center;
        gap: 28px;
    }

    .hero-cta-group {
        -webkit-justify-content: center;
        -ms-flex-pack: center;
        justify-content: center;
    }

    .hero-image-wrapper { margin-top: 10px; }

    .hero-chips {
        -webkit-justify-content: center;
        -ms-flex-pack: center;
        justify-content: center;
    }

    .hero-highlight {
        margin-left: auto;
        margin-right: auto;
    }
}

/* ✅ Mobile melhor: chips em grid + CTA full */
@media (max-width: 768px) {
    .hero { padding: 46px 16px; min-height: auto; }

    .hero-text h1 { margin-bottom: 14px; }
    .hero-text .subtitle { margin-bottom: 14px; }

    .hero-chips{
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 14px;
    }

    .hero-chip{
        width: 100%;
        justify-content: center;
        padding: 11px 12px;
        border-radius: 12px;
        font-size: 0.92rem;
        text-align: center;
    }

    .hero-chip i{ font-size: 1.05rem; }

    .hero-highlight{
        max-width: 100%;
        padding: 12px 12px;
        border-radius: 12px;
        font-size: 0.95rem;
        margin-bottom: 16px;
        text-align: left;
    }

    .hero-cta-group{
        width: 100%;
        gap: 12px;
    }

    .btn-hero-main{
        width: 100%;
        padding: 14px 18px;
        text-align: center;
    }

    .trial-box{
        width: 100%;
        align-items: center;
    }

    .carousel-hint{
        font-size: .85rem;
        padding: 7px 10px;
    }

    .whatsapp-fab span { display: none; }
    .whatsapp-fab {
        width: 55px;
        height: 55px;
        padding: 0;
        -webkit-justify-content: center;
        -ms-flex-pack: center;
        justify-content: center;
    }

    /* Modal mais compacto + mais “respiro” embaixo */
    .hero-gallery-body{ padding: 12px 12px 12px; gap: 10px; }
    .hero-gallery-stage{ height: min(66vh, 520px); border-radius: 14px; }
    .hero-thumb{ width: 86px; height: 54px; border-radius: 12px; }
    .hero-gallery-nav{ width: 40px; height: 40px; }
    .hero-gallery-btn{ padding: 7px 10px; }
    .hero-gallery-footer{ padding-bottom: 16px; }
}

/* Telas bem pequenas: chips 1 coluna */
@media (max-width: 420px) {
    .hero-chips{ grid-template-columns: 1fr; }
}
</style>
@endpush


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const mobileImages = [
        { webp: 'demo1.webp', fallback: 'demo1.png' },
        { webp: 'demo2.webp', fallback: 'demo2.png' },
        { webp: 'demo3.webp', fallback: 'demo3.png' }
    ];

    const desktopImages = [
        { webp: 'demo1_resized.webp', fallback: 'demo1.png' },
        { webp: 'demo2_resized.webp', fallback: 'demo2.png' },
        { webp: 'demo3_resized.webp', fallback: 'demo3.png' }
    ];

    const mediaQuery = window.matchMedia('(max-width: 768px)');

    // ✅ listas resolvidas (URL completa) para usar no modal conforme contexto
    const mobileImagesResolved = mobileImages.map(img => ({
        webp: `{{ asset('images/') }}/${img.webp}`,
        fallback: `{{ asset('images/') }}/${img.fallback}`
    }));

    const desktopImagesResolved = desktopImages.map(img => ({
        webp: `{{ asset('images/') }}/${img.webp}`,
        fallback: `{{ asset('images/') }}/${img.fallback}`
    }));

    let images = getCurrentImages(); // carrossel usa a lista “do breakpoint”
    let currentIndex = 0;
    let carouselInterval;

    const pictureEl = document.getElementById('carouselPicture');
    const dotsContainer = document.getElementById('carouselDots');

    if (!pictureEl || !dotsContainer) {
        console.error("Elementos do carrossel não encontrados!");
        return;
    }

    // deixa o carrossel focável/clicável (pra abrir modal)
    pictureEl.classList.add('carousel-clickable');
    pictureEl.setAttribute('tabindex', '0');
    pictureEl.setAttribute('role', 'button');
    pictureEl.setAttribute('aria-label', 'Abrir galeria de imagens do PsiGestor');

    function getCurrentImages() {
        return mediaQuery.matches ? mobileImagesResolved : desktopImagesResolved;
    }

    // ✅ modal abre “mobile no mobile” e “desktop no desktop”
    function bestModalSrc(idx) {
        const list = mediaQuery.matches ? mobileImagesResolved : desktopImagesResolved;
        return list[idx]?.fallback || '';
    }

    function showImage(index) {
        if (pictureEl.querySelector(`[data-index="${index}"]`)) return;

        const oldPicture = pictureEl.querySelector('picture');
        currentIndex = index;

        const newPicture = document.createElement('picture');
        newPicture.dataset.index = index;
        newPicture.style.position = 'absolute';
        newPicture.style.top = '0';
        newPicture.style.left = '0';
        newPicture.style.width = '100%';
        newPicture.style.height = '100%';

        const source = document.createElement('source');
        const img = document.createElement('img');

        source.srcset = images[index].webp;
        source.type = 'image/webp';

        img.src = images[index].fallback;
        img.alt = `Mockup PsiGestor ${index + 1}`;
        img.style.opacity = '0';
        img.style.transition = 'opacity 0.6s ease-in-out';

        newPicture.appendChild(source);
        newPicture.appendChild(img);
        pictureEl.appendChild(newPicture);

        img.onload = () => {
            requestAnimationFrame(() => img.style.opacity = '1');
            if (oldPicture) {
                const oldImg = oldPicture.querySelector('img');
                if (oldImg) oldImg.style.opacity = '0';
                setTimeout(() => oldPicture.remove(), 600);
            }
        };

        img.onerror = () => {
            console.error(`Erro ao carregar: ${img.src}`);
            newPicture.remove();
        };

        document.querySelectorAll('.dot').forEach(d => d.classList.remove('active'));
        const activeDot = document.querySelector(`.dot[data-index="${index}"]`);
        if (activeDot) activeDot.classList.add('active');
    }

    function startCarousel() {
        stopCarousel();
        carouselInterval = setInterval(() => {
            showImage((currentIndex + 1) % images.length);
        }, 5000);
    }

    function stopCarousel() {
        clearInterval(carouselInterval);
    }

    function setupDots() {
        dotsContainer.innerHTML = '';
        images.forEach((_, i) => {
            const dot = document.createElement('span');
            dot.classList.add('dot');
            dot.dataset.index = i;
            dot.addEventListener('click', () => {
                stopCarousel();
                showImage(i);
                startCarousel();
            });
            dotsContainer.appendChild(dot);
        });
    }

    // ====== Galeria (Modal) - requer HTML do modal no blade ======
    const modalEl = document.getElementById('heroGalleryModal');
    let modalInstance = null;

    // estado do pan/zoom (pan funciona mesmo em 100% e com folga)
    let stageEl, imgEl, prevBtn, nextBtn, counterEl, thumbsEl;
    let zoomInBtn, zoomOutBtn, zoomResetBtn;

    let scale = 1;
    const minScale = 1;
    const maxScale = 4;

    let tx = 0;
    let ty = 0;

    let isPanning = false;
    let panStartX = 0;
    let panStartY = 0;
    let stageRect = null;

    // touch pinch
    let touchMode = null; // 'pan' | 'pinch'
    let pinchStartDist = 0;
    let pinchStartScale = 1;
    let pinchMid = { x: 0, y: 0 };

    // swipe
    let swipeStartX = 0;
    let swipeStartY = 0;
    let swipeActive = false;

    function clamp(v, min, max){ return Math.max(min, Math.min(max, v)); }

    function applyTransform(){
        if (!imgEl) return;
        imgEl.style.transform = `translate(calc(-50% + ${tx}px), calc(-50% + ${ty}px)) scale(${scale})`;
    }

    // ✅ pan com “slack” em 1x e limites maiores com zoom
    function limitPan(){
        if (!stageRect) return;

        const stageW = stageRect.width;
        const stageH = stageRect.height;

        const baseSlack = Math.min(90, stageW * 0.12); // folga maior para não “cortar”
        const maxX = ((stageW * (scale - 1)) / 2) + baseSlack;
        const maxY = ((stageH * (scale - 1)) / 2) + baseSlack;

        tx = clamp(tx, -maxX, maxX);
        ty = clamp(ty, -maxY, maxY);
    }

    function updateResetLabel(){
        if (!zoomResetBtn) return;
        zoomResetBtn.textContent = `${Math.round(scale * 100)}%`;
    }

    function resetZoom(){
        scale = 1;
        tx = 0;
        ty = 0;
        applyTransform();
        updateResetLabel();
    }

    function setScale(nextScale, anchorX = null, anchorY = null){
        const prevScale = scale;
        scale = clamp(nextScale, minScale, maxScale);

        if (anchorX != null && anchorY != null && stageRect) {
            const ax = anchorX - stageRect.left - stageRect.width / 2;
            const ay = anchorY - stageRect.top  - stageRect.height / 2;
            const ratio = scale / prevScale;

            tx = (tx + ax) * ratio - ax;
            ty = (ty + ay) * ratio - ay;
        }

        limitPan();
        applyTransform();
        updateResetLabel();
    }

    function dist(t1, t2){
        const dx = t2.clientX - t1.clientX;
        const dy = t2.clientY - t1.clientY;
        return Math.hypot(dx, dy);
    }

    function midpoint(t1, t2){
        return { x: (t1.clientX + t2.clientX)/2, y: (t1.clientY + t2.clientY)/2 };
    }

    function ensureGalleryMarkup(){
        if (!modalEl) return;

        const body = modalEl.querySelector('.modal-body');
        const footer = modalEl.querySelector('.modal-footer');
        const title = modalEl.querySelector('#heroGalleryTitle');

        if (!body || body.querySelector('.hero-gallery-stage')) return;

        body.classList.add('hero-gallery-body');
        body.innerHTML = `
            <div class="hero-gallery-toolbar">
                <span id="heroGalleryCounter" class="hero-gallery-counter">1 / ${images.length}</span>
                <div class="hero-gallery-actions">
                    <button type="button" class="hero-gallery-btn" id="heroZoomOut" aria-label="Diminuir zoom">−</button>
                    <button type="button" class="hero-gallery-btn" id="heroZoomReset" aria-label="Resetar zoom">100%</button>
                    <button type="button" class="hero-gallery-btn" id="heroZoomIn" aria-label="Aumentar zoom">+</button>
                </div>
            </div>

            <div class="hero-gallery-stage" id="heroGalleryStage" aria-label="Área de zoom e navegação">
                <button type="button" class="hero-gallery-nav hero-gallery-prev" id="heroGalleryPrev" aria-label="Imagem anterior">
                    <i class="bi bi-chevron-left"></i>
                </button>

                <img id="heroGalleryImg" src="" alt="Imagem ampliada do PsiGestor" loading="lazy"/>

                <button type="button" class="hero-gallery-nav hero-gallery-next" id="heroGalleryNext" aria-label="Próxima imagem">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>

            <div class="hero-gallery-thumbs" id="heroGalleryThumbs" aria-label="Miniaturas"></div>
        `;

        if (footer) footer.classList.add('hero-gallery-footer');

        if (title) {
            const small = modalEl.querySelector('.hero-gallery-subtitle');
            if (!small) {
                const wrap = title.parentElement;
                if (wrap) {
                    const s = document.createElement('div');
                    s.className = 'hero-gallery-subtitle';
                    s.textContent = 'Pinch para zoom • Arraste para mover • Swipe para trocar';
                    wrap.appendChild(s);
                }
            }
        }
    }

    function getGalleryEls(){
        stageEl = modalEl.querySelector('#heroGalleryStage');
        imgEl   = modalEl.querySelector('#heroGalleryImg');
        prevBtn = modalEl.querySelector('#heroGalleryPrev');
        nextBtn = modalEl.querySelector('#heroGalleryNext');
        counterEl = modalEl.querySelector('#heroGalleryCounter');
        thumbsEl  = modalEl.querySelector('#heroGalleryThumbs');

        zoomInBtn = modalEl.querySelector('#heroZoomIn');
        zoomOutBtn = modalEl.querySelector('#heroZoomOut');
        zoomResetBtn = modalEl.querySelector('#heroZoomReset');

        stageRect = stageEl ? stageEl.getBoundingClientRect() : null;
    }

    function syncThumbs(){
        if (!thumbsEl) return;
        const children = Array.from(thumbsEl.children);
        children.forEach((el, i) => el.classList.toggle('is-active', i === currentIndex));
        const active = children[currentIndex];
        if (active && active.scrollIntoView) {
            active.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
        }
    }

    // ✅ thumbs agora seguem “mobile no mobile / desktop no desktop”
    function buildThumbs(){
        if (!thumbsEl) return;
        thumbsEl.innerHTML = '';

        const list = mediaQuery.matches ? mobileImagesResolved : desktopImagesResolved;

        list.forEach((img, i) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'hero-thumb';
            btn.setAttribute('aria-label', `Abrir imagem ${i+1}`);

            const im = document.createElement('img');
            im.src = img.fallback;
            im.alt = `Miniatura ${i+1}`;

            btn.appendChild(im);
            btn.addEventListener('click', () => setGalleryIndex(i));
            thumbsEl.appendChild(btn);
        });

        syncThumbs();
    }

    function setGalleryIndex(idx){
        currentIndex = (idx + images.length) % images.length;
        getGalleryEls();

        if (imgEl) {
            imgEl.src = bestModalSrc(currentIndex);
            imgEl.alt = `Mockup PsiGestor ${currentIndex + 1}`;
        }
        if (counterEl) counterEl.textContent = `${currentIndex + 1} / ${images.length}`;

        syncThumbs();
        resetZoom();
    }

    function goPrev(){ setGalleryIndex(currentIndex - 1); }
    function goNext(){ setGalleryIndex(currentIndex + 1); }

    function openGallery(idx){
        if (!modalEl) return;
        ensureGalleryMarkup();
        if (!modalInstance) modalInstance = new bootstrap.Modal(modalEl, { keyboard: true });
        setGalleryIndex(idx);
        modalInstance.show();
    }

    // clique no carrossel abre
    pictureEl.addEventListener('click', () => openGallery(currentIndex));
    pictureEl.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            openGallery(currentIndex);
        }
    });

    function bindGalleryEvents(){
        if (!modalEl) return;
        getGalleryEls();
        if (!stageEl || !imgEl) return;

        buildThumbs();

        // nav
        prevBtn && prevBtn.addEventListener('click', goPrev);
        nextBtn && nextBtn.addEventListener('click', goNext);

        // zoom buttons
        zoomInBtn && zoomInBtn.addEventListener('click', () => {
            stageRect = stageEl.getBoundingClientRect();
            setScale(scale * 1.2, stageRect.left + stageRect.width/2, stageRect.top + stageRect.height/2);
        });

        zoomOutBtn && zoomOutBtn.addEventListener('click', () => {
            stageRect = stageEl.getBoundingClientRect();
            setScale(scale * 0.85, stageRect.left + stageRect.width/2, stageRect.top + stageRect.height/2);
        });

        zoomResetBtn && zoomResetBtn.addEventListener('click', resetZoom);

        // wheel zoom desktop
        stageEl.addEventListener('wheel', (e) => {
            stageRect = stageEl.getBoundingClientRect();
            const delta = -e.deltaY;
            const factor = delta > 0 ? 1.12 : 0.90;
            setScale(scale * factor, e.clientX, e.clientY);
            e.preventDefault();
        }, { passive: false });

        // mouse pan (sempre)
        stageEl.addEventListener('mousedown', (e) => {
            isPanning = true;
            stageRect = stageEl.getBoundingClientRect();
            panStartX = e.clientX - tx;
            panStartY = e.clientY - ty;
            e.preventDefault();
        });

        window.addEventListener('mousemove', (e) => {
            if (!isPanning) return;
            tx = e.clientX - panStartX;
            ty = e.clientY - panStartY;
            limitPan();
            applyTransform();
        });

        window.addEventListener('mouseup', () => { isPanning = false; });

        // touch
        stageEl.addEventListener('touchstart', (e) => {
            stageRect = stageEl.getBoundingClientRect();
            const touches = e.touches;

            swipeActive = true;
            swipeStartX = touches[0].clientX;
            swipeStartY = touches[0].clientY;

            if (touches.length === 1) {
                touchMode = 'pan';
                panStartX = touches[0].clientX - tx;
                panStartY = touches[0].clientY - ty;
            } else if (touches.length === 2) {
                touchMode = 'pinch';
                pinchStartDist = dist(touches[0], touches[1]);
                pinchStartScale = scale;
                pinchMid = midpoint(touches[0], touches[1]);
            }

            e.preventDefault();
        }, { passive: false });

        stageEl.addEventListener('touchmove', (e) => {
            stageRect = stageEl.getBoundingClientRect();
            const touches = e.touches;

            if (touches.length === 1 && touchMode === 'pan') {
                const x = touches[0].clientX;
                const y = touches[0].clientY;

                tx = x - panStartX;
                ty = y - panStartY;
                limitPan();
                applyTransform();
            } else if (touches.length === 2 && touchMode === 'pinch') {
                const d = dist(touches[0], touches[1]);
                const ratio = d / pinchStartDist;
                const next = pinchStartScale * ratio;
                setScale(next, pinchMid.x, pinchMid.y);
            }

            e.preventDefault();
        }, { passive: false });

        stageEl.addEventListener('touchend', (e) => {
            // swipe só quando estiver em 100% (ou quase)
            if (swipeActive && scale <= 1.02) {
                const endTouch = (e.changedTouches && e.changedTouches[0]) ? e.changedTouches[0] : null;
                if (endTouch) {
                    const dx = endTouch.clientX - swipeStartX;
                    const dy = endTouch.clientY - swipeStartY;

                    if (Math.abs(dx) > 55 && Math.abs(dy) < 45) {
                        if (dx < 0) goNext();
                        else goPrev();
                    }
                }
            }

            swipeActive = false;
            touchMode = null;
        }, { passive: false });

        // double click/tap: alterna zoom
        let lastTap = 0;
        stageEl.addEventListener('click', (e) => {
            const now = Date.now();
            if (now - lastTap < 280) {
                stageRect = stageEl.getBoundingClientRect();
                if (scale <= 1.05) setScale(2.0, e.clientX, e.clientY);
                else resetZoom();
            }
            lastTap = now;
        });

        // teclado no modal
        modalEl.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft')  { e.preventDefault(); goPrev(); }
            if (e.key === 'ArrowRight') { e.preventDefault(); goNext(); }
        });

        window.addEventListener('resize', () => {
            if (!stageEl) return;
            stageRect = stageEl.getBoundingClientRect();
            limitPan();
            applyTransform();
        });
    }

    // pausa/retoma carrossel ao abrir/fechar modal
    if (modalEl) {
        modalEl.addEventListener('show.bs.modal', () => stopCarousel());
        modalEl.addEventListener('shown.bs.modal', () => {
            if (!modalEl.dataset.bound) {
                bindGalleryEvents();
                modalEl.dataset.bound = '1';
            }
            setGalleryIndex(currentIndex);
        });
        modalEl.addEventListener('hidden.bs.modal', () => {
            startCarousel();
            resetZoom();
        });
    }

    // ====== Inicializa tudo ======
    setupDots();
    showImage(0);
    startCarousel();

    // ✅ breakpoint: atualiza carrossel + (se modal aberto) atualiza thumbs e imagem do modal
    mediaQuery.addEventListener('change', () => {
        images = getCurrentImages();

        currentIndex = 0;
        setupDots();
        showImage(0);

        if (modalEl && modalEl.classList.contains('show')) {
            buildThumbs();
            setGalleryIndex(currentIndex);
        }
    });

    // Efeito tilt 3D
    const tiltElement = document.getElementById('carouselTilt');
    if (tiltElement) {
        tiltElement.addEventListener('mousemove', (e) => {
            const { left, top, width, height } = tiltElement.getBoundingClientRect();
            const x = (e.clientX - left) / width - 0.5;
            const y = (e.clientY - top) / height - 0.5;
            const rotateX = y * -12;
            const rotateY = x * 12;
            tiltElement.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.05,1.05,1.05)`;
        });

        tiltElement.addEventListener('mouseleave', () => {
            tiltElement.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale3d(1,1,1)';
        });
    }

    // Facebook Pixel (opcional)
    const cta = document.querySelector('.btn-hero-main');
    if (cta) {
        cta.addEventListener('click', () => {
            if (typeof fbq === 'function') fbq('track', 'Lead');
        });
    }
});
</script>
@endpush



