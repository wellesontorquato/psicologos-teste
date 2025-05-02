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
        justify-content: space-between;
        gap: 30px;
    ">
        {{-- TEXTO PRINCIPAL --}}
        <div class="hero-text" style="
            flex: 1 1 500px;
            text-align: left;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        ">
            <h1 style="font-size: 2.3rem; margin-bottom: 15px; line-height: 1.2;">
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

            {{-- BOTÃO + BOX JUNTOS --}}
            <div style="
                display: flex;
                flex-direction: row;
                align-items: center;
                gap: 15px;
                flex-wrap: wrap;
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
                    display: inline-block;
                    box-shadow: 0 3px 10px rgba(0,0,0,0.15);
                " onmouseover="this.style.background='#e6f7ff';" onmouseout="this.style.background='white';">
                    Comece agora...
                </a>

                <div class="trial-box" style="
                    padding: 12px 18px;
                    background: rgba(255, 255, 255, 0.2);
                    border: 1px solid rgba(255, 255, 255, 0.4);
                    border-radius: 10px;
                    text-align: left;
                    color: #ffffff;
                    font-size: 0.9rem;
                    backdrop-filter: blur(5px);
                    max-width: 260px;
                    box-shadow: 0 0 10px rgba(0,0,0,0.1);
                ">
                    <strong>🎁 10 dias grátis</strong><br>
                    <small>
                        ✅ Sem cartão de crédito para o cadastro<br>
                        ✅ Acesso imediato
                    </small>
                </div>
            </div>
        </div>

        {{-- IMAGEM --}}
        <div class="hero-img" style="
            flex: 1 1 400px;
            text-align: center;
            padding: 10px;
        ">
            <img src="/images/ilustracao-psicologo-vetor.png" alt="PsiGestor - Psicólogo com paciente" style="
                max-width: 100%;
                height: auto;
                max-height: 350px;
            ">
        </div>
    </div>
</section>


{{-- FUNCIONALIDADES --}}
@include('components.funcionalidades')

{{-- DEPOIMENTOS --}}
@include('components.depoimentos')

{{-- SOBRE --}}
<div class="section" id="sobre" style="padding: 60px 20px;" data-aos="fade-up">
    <h2 style="text-align: center; margin-bottom: 30px;">Sobre o PsiGestor</h2>
    <p style="max-width: 900px; margin: auto; text-align: center;">
        O <strong>PsiGestor</strong> nasceu para simplificar a gestão clínica, unindo eficiência com empatia.
        Criado por profissionais da saúde, o sistema acompanha o ritmo do seu consultório com segurança e humanidade.
    </p>
</div>

{{-- CONTATO --}}
<div class="section contact" id="contato" style="background-color: #f0f8ff; padding: 60px 20px;" data-aos="fade-up">
    <h2 style="text-align: center;">Fale Conosco</h2>
    <p style="text-align: center;">
        Tem dúvidas ou sugestões? Envie um e-mail para
        <a href="mailto:contato@psigestor.com.br" style="color: #00aaff; font-weight: bold;">contato@psigestor.com.br</a>
    </p>
</div>

{{-- BOTÃO WHATSAPP FLOTANTE --}}
<a href="https://wa.me/5582991128022" target="_blank" style="
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
">
    <img src="https://psicologos-teste-production.up.railway.app/images/whatsapp-icon.png" alt="WhatsApp" style="width: 24px; height: 24px;">
    (11) 99999-9999
</a>

@endsection
