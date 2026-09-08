@extends('layouts.landing')

@section('content')

{{-- HERO SECTION — React + Motion --}}
<div
    id="psigestor-home-hero"
    data-register-url="{{ route('register') }}"
    data-login-url="{{ route('login') }}"
></div>

<noscript>
    <section style="
        padding: 80px 20px;
        text-align: center;
        background: #f7fbff;
    ">
        <div style="
            max-width: 760px;
            margin: 0 auto;
        ">
            <h1 style="
                color: #15283f;
                font-size: clamp(2.2rem, 6vw, 4rem);
                font-weight: 700;
            ">
                Mais tempo para cuidar.
                Menos tempo organizando.
            </h1>

            <p style="
                color: #62748a;
                line-height: 1.7;
                margin: 24px auto;
            ">
                Agenda, pacientes, evoluções e financeiro
                em um só lugar.
            </p>

            <a
                href="{{ route('register') }}"
                style="
                    display: inline-block;
                    padding: 14px 24px;
                    border-radius: 14px;
                    background: #00aaff;
                    color: white;
                    font-weight: 700;
                    text-decoration: none;
                "
            >
                Começar 10 dias grátis
            </a>
        </div>
    </section>
</noscript>

{{-- OUTRAS SEÇÕES --}}
<div
    id="psigestor-home-features"
    data-features-url="{{ route('funcionalidades') }}"
></div>
<div class="section-divider"></div>
<div
    id="psigestor-home-social"
></div>
<div class="section-divider"></div>
<div
    id="psigestor-home-news"
    data-endpoint="{{ route('home.news') }}"
    data-blog-url="{{ route('blog.index') }}"
></div>
<div
    id="psigestor-home-cta"
    data-register-url="{{ route('register') }}"
    data-login-url="{{ route('login') }}"
></div>

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
@viteReactRefresh
@vite('resources/js/homepage/index.jsx')

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

