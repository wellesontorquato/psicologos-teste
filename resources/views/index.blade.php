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

            {{-- ✅ NOVO: Chips (pill buttons) --}}
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

            {{-- ✅ NOVO: Barra de destaque --}}
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
            <div class="carousel-tilt" id="carouselTilt">
                <div id="carouselPicture">
                    {{-- O conteúdo será preenchido pelo JavaScript --}}
                </div>
                <div class="carousel-dots" id="carouselDots">
                    {{-- Dots serão gerados pelo JavaScript --}}
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
    /* Prefixo para garantir compatibilidade do Flexbox */
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    /* Prefixo para garantir compatibilidade do Flexbox */
    -webkit-align-items: center;
    -ms-flex-align: center;
    align-items: center;
    overflow: hidden;
}

/* LAYOUT ATUALIZADO PARA DESKTOP */
.hero-container {
    max-width: 1200px;
    margin: auto;
    /* Prefixo para garantir compatibilidade do Flexbox */
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    /* Prefixo para garantir compatibilidade do Flexbox */
    -webkit-align-items: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-justify-content: space-between;
    -ms-flex-pack: justify;
    justify-content: space-between;
    gap: 40px; /* A maioria dos navegadores modernos já suporta `gap` */
}

.hero-text {
    /* Prefixo para garantir compatibilidade do Flexbox */
    -webkit-flex: 1 1 55%;
    -ms-flex: 1 1 55%;
    flex: 1 1 55%;
    max-width: 600px;
}

.hero-image-wrapper {
    /* Prefixo para garantir compatibilidade do Flexbox */
    -webkit-flex: 1 1 45%;
    -ms-flex: 1 1 45%;
    flex: 1 1 45%;
    max-width: 500px;
    /* Prefixo para garantir compatibilidade do Flexbox */
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

/* NOVO TÍTULO DO CARROSSEL */
.carousel-title {
    font-size: 1.1rem;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.95);
    margin-bottom: 20px;
    text-align: center;
}

.hero-text h1 {
    /* Fallback para navegadores que não suportam clamp() */
    font-size: 2.8rem;
    /* Valor moderno com clamp() */
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
    /* Fallback para navegadores que não suportam clamp() */
    font-size: 1.1rem;
    /* Valor moderno com clamp() */
    font-size: clamp(1rem, 2.5vw, 1.15rem);
    margin-bottom: 18px; /* era 30px */
    line-height: 1.6;
    color: rgba(255, 255, 255, 0.9);
}

/* ✅ NOVO: Chips */
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

.hero-chip i {
    font-size: 1.1rem;
    opacity: 0.95;
}

/* ✅ NOVO: Barra destaque */
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
    background: rgba(255,215,0,0.95); /* douradinho */
    box-shadow: 0 0 0 4px rgba(255,215,0,0.18);
    flex: 0 0 auto;
}

.hero-highlight span:last-child {
    font-weight: 600;
}

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
    /* Prefixo para o efeito de vidro no Safari */
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

.whatsapp-fab i {
    font-size: 1.5rem;
}

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

/* LAYOUT ATUALIZADO PARA MOBILE */
@media (max-width: 992px) {
    .hero-container {
        -webkit-flex-direction: column;
        -ms-flex-direction: column;
        flex-direction: column;
        text-align: center;
    }
    .hero-cta-group {
        -webkit-justify-content: center;
        -ms-flex-pack: center;
        justify-content: center;
    }
    .hero-image-wrapper {
        margin-top: 40px;
    }

    /* Centraliza chips e highlight no mobile/tablet */
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

@media (max-width: 768px) {
    .whatsapp-fab span { display: none; }
    .whatsapp-fab {
        width: 55px;
        height: 55px;
        padding: 0;
        -webkit-justify-content: center;
        -ms-flex-pack: center;
        justify-content: center;
    }

    /* Chips ficam 100% mais “encaixados” */
    .hero-chip {
        padding: 11px 14px;
        border-radius: 12px;
        font-size: 0.95rem;
    }
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
    let images = getCurrentImages();
    let currentIndex = 0;
    let carouselInterval;

    const pictureEl = document.getElementById('carouselPicture');
    const dotsContainer = document.getElementById('carouselDots');

    if (!pictureEl || !dotsContainer) {
        console.error("Elementos do carrossel não encontrados!");
        return;
    }

    function getCurrentImages() {
        const raw = mediaQuery.matches ? mobileImages : desktopImages;
        return raw.map(img => ({
            webp: `{{ asset('images/') }}/${img.webp}`,
            fallback: `{{ asset('images/') }}/${img.fallback}`
        }));
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
                oldPicture.querySelector('img').style.opacity = '0';
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

    // Inicializa tudo
    setupDots();
    showImage(0);
    startCarousel();

    // Atualiza imagens e dots ao redimensionar para outro breakpoint
    mediaQuery.addEventListener('change', () => {
        images = getCurrentImages();
        currentIndex = 0;
        setupDots();
        showImage(0);
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
