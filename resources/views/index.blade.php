@extends('layouts.landing')

@section('content')

<section class="hero" id="inicio" style="
    background: linear-gradient(to right, #00aaff, #00c4ff);
    color: white;
    padding: 40px 20px;
">
    <div class="hero-container" style="
        max-width: 1200px;
        margin: auto;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 30px;
    ">
        {{-- TEXTO PRINCIPAL --}}
        <div class="hero-text" style="
            flex: 1 1 400px;
            text-align: left;
            padding: 20px;
        ">
            <h1 style="font-size: clamp(1.8rem, 5vw, 2.5rem); margin-bottom: 15px; line-height: 1.2;">
                Bem-vindo ao <strong>PsiGestor</strong>
            </h1>
            <p style="font-size: 1.05rem; margin-bottom: 15px; line-height: 1.5;">
                Organize sua clínica com eficiência, empatia e tecnologia feita sob medida para psicólogos, psicanalistas e psiquiatras.
                Controle sua agenda, evoluções, finanças e documentos em um só lugar — com leveza e precisão.
            </p>
            <p style="font-size: 1rem; margin-bottom: 20px;">
                <strong>PsiGestor é mais que uma plataforma.</strong><br>
                É seu aliado para transformar o caos em clareza e o cuidado em performance.
            </p>

            <div style="
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 15px;
                margin-top: 10px;
            ">
                <a href="{{ route('register') }}" class="btn-cta" style="
                    padding: 12px 25px;
                    background: white;
                    color: #00aaff;
                    border-radius: 30px;
                    font-weight: bold;
                    text-decoration: none;
                    transition: 0.3s ease;
                    box-shadow: 0 3px 10px rgba(0,0,0,0.15);
                " onmouseover="this.style.background='#e6f7ff';" onmouseout="this.style.background='white';">
                    Comece agora...
                </a>

                <div class="trial-box" style="
                    padding: 12px 18px;
                    background: rgba(255, 255, 255, 0.2);
                    border: 1px solid rgba(255, 255, 255, 0.4);
                    border-radius: 10px;
                    color: #ffffff;
                    font-size: 0.9rem;
                    backdrop-filter: blur(5px);
                    max-width: 260px;
                    box-shadow: 0 0 10px rgba(0,0,0,0.1);
                ">
                    <strong>🎁 10 dias grátis</strong><br>
                    <small>
                        ✅ Sem cartão de crédito<br>
                        ✅ Acesso imediato
                    </small>
                </div>
            </div>
        </div>

        {{-- IMAGEM COM CARROSSEL --}}
        <div class="hero-img" style="flex: 1 1 400px; padding: 10px; text-align: center; margin-top: 30px;">
            <p style="font-size: 1rem; font-weight: 500; color: #ffffff; margin-bottom: 10px;">
                🖥️ Veja como é o painel do <strong>PsiGestor</strong>
            </p>

            <div class="carousel-tilt" id="carouselTilt" style="
                width: 100%;
                max-width: 580px;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 12px 25px rgba(0,0,0,0.2);
                transform-style: preserve-3d;
                transition: transform 0.4s ease;
                margin: auto;
            ">
                <img id="carouselImage" src="/images/demo1.png" alt="Mockup PsiGestor" style="
                    width: 100%;
                    display: block;
                    transition: transform 0.4s ease;
                ">
            </div>

            <div id="carouselDots" style="margin-top: 10px; text-align: center;">
                <span class="dot active" data-index="0"></span>
                <span class="dot" data-index="1"></span>
                <span class="dot" data-index="2"></span>
            </div>
        </div>
    </div>
</section>

{{-- Outras Seções --}}
@include('components.funcionalidades')
<hr style="border: none; border-top: 2px solid #e0e0e0; margin: 60px auto; max-width: 80%;">
@include('components.depoimentos')
<hr style="border: none; border-top: 2px solid #e0e0e0; margin: 60px auto; max-width: 80%;">
@include('components.noticias')
<hr style="border: none; border-top: 2px solid #e0e0e0; margin: 60px auto; max-width: 80%;">
{{-- Contato --}}
@include('components.contato')

{{-- Botão WhatsApp --}}
<a href="https://wa.me/5582991128022?text=Olá,%20tenho%20interesse%20no%20PsiGestor!" target="_blank" style="
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 999;
    display: flex;
    align-items: center;
    gap: 10px;
    background: #25d366;
    color: white;
    padding: 10px 15px;
    border-radius: 50px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s ease;
" onmouseover="this.style.background='#1ebd5a';" onmouseout="this.style.background='#25d366';">
    <img src="https://psigestor.com/images/whatsapp.png" alt="WhatsApp" style="width: 24px; height: 24px;">
    (82) 99112-8022
</a>

@push('styles')
<style>

    .form-group {
        margin-bottom: 18px;
    }

    .input-field {
        width: 100%;
        padding: 14px 16px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }

    .input-field:focus {
        border-color: #00aaff;
        box-shadow: 0 0 0 3px rgba(0, 170, 255, 0.15);
        outline: none;
    }

    select.input-field {
        background: #fff;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }

    .submit-btn {
        background: #00aaff;
        color: #fff;
        border: none;
        padding: 14px 30px;
        border-radius: 8px;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .submit-btn:hover {
        background: #0095d8;
    }

    /* 👇 CENTRALIZAÇÃO do reCAPTCHA */
    .g-recaptcha {
        display: flex !important;
        justify-content: center !important;
        width: 100% !important;
        margin: 0 auto !important;
    }

    .dot {
        height: 10px;
        width: 10px;
        margin: 0 6px;
        background-color: #ccc;
        border-radius: 50%;
        display: inline-block;
        transition: background-color 0.3s ease;
        cursor: pointer;
    }

    .dot.active {
        background-color: #00aaff;
    }
    .dot {
        height: 10px;
        width: 10px;
        margin: 0 6px;
        background-color: #ccc;
        border-radius: 50%;
        display: inline-block;
        transition: background-color 0.3s ease;
        cursor: pointer;
    }

    .dot.active {
        background-color: #00aaff;
    }

    @media (max-width: 768px) {
        .hero-container {
            flex-direction: column;
            padding: 20px 10px;
        }

        .hero-text, .hero-img {
            text-align: center !important;
        }
    }
</style>
@endpush

@push('scripts')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('scripts')
<script>
    const images = [
        '/images/demo1.png',
        '/images/demo2.png',
        '/images/demo3.png'
    ];

    let currentIndex = 0;
    const imageEl = document.getElementById('carouselImage');
    const dots = document.querySelectorAll('#carouselDots .dot');
    const tiltEl = document.getElementById('carouselTilt');

    function showImage(index) {
        currentIndex = index;
        imageEl.src = images[index];
        dots.forEach(dot => dot.classList.remove('active'));
        dots[index].classList.add('active');
    }

    dots.forEach(dot => {
        dot.addEventListener('click', () => {
            showImage(Number(dot.dataset.index));
        });
    });

    setInterval(() => {
        currentIndex = (currentIndex + 1) % images.length;
        showImage(currentIndex);
    }, 5000);

    if (tiltEl) {
        tiltEl.style.transition = 'transform 0.4s ease';
        tiltEl.style.transformStyle = 'preserve-3d';
        tiltEl.style.willChange = 'transform';
        tiltEl.style.backfaceVisibility = 'hidden';

        tiltEl.addEventListener('mouseenter', () => {
            tiltEl.style.transform = 'rotateX(4deg) rotateY(-16deg)';
        });

        tiltEl.addEventListener('mouseleave', () => {
            tiltEl.style.transform = 'rotateX(0deg) rotateY(0deg)';
        });
    }
</script>
@endpush

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const cta = document.querySelector('.btn-cta');
    if (cta) {
      cta.addEventListener('click', function () {
        fbq('track', 'Lead');
      });
    }
  });
</script>
@endpush

@endsection



