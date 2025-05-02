@extends('layouts.landing')

@section('content')

<section class="hero" id="inicio" style="background: linear-gradient(to right, #00aaff, #00c4ff); color: white; padding: 80px 20px;">
    <div class="hero-container" style="max-width: 1200px; margin: auto; display: flex; flex-wrap: wrap; align-items: center; justify-content: center;">
        <div class="hero-text" style="flex: 1 1 400px; text-align: center; padding: 20px;">
            <h1 style="font-size: 2.5rem; margin-bottom: 20px;">Bem-vindo ao <strong>PsiGestor</strong></h1>
            <p style="font-size: 1.1rem; margin-bottom: 20px;">
                Organize sua clínica com eficiência, empatia e tecnologia feita sob medida para psicólogos.<br>
                Controle sua agenda, evoluções, finanças e documentos em um só lugar — com leveza e precisão.
            </p>
            <p><strong>PsiGestor é mais que uma plataforma.</strong><br>
            É seu novo aliado para transformar o caos em clareza, e o cuidado em performance.<br>
            Mais tempo pra você. Mais resultado pra sua clínica.</p>
            <a href="{{ route('register') }}" class="btn-cta" style="margin-top: 20px; display: inline-block;">Comece agora...</a>
            <div class="trial-box" style=" margin-top: 20px; display: inline-block; padding: 15px 20px; background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.4); border-radius: 12px; max-width: 320px; text-align: center; color: #ffffff; font-size: 0.95rem; backdrop-filter: blur(5px); box-shadow: 0 0 10px rgba(0,0,0,0.1);">
            <i class="bi bi-hourglass-split" style="font-size: 1.5rem; display: block; margin-bottom: 8px;"></i>
            Teste <strong>100% grátis</strong> por 10 dias<br>
            <span style="font-size: 0.85rem; display: block; margin-top: 5px;">
                ✅ Sem necessidade de cartão de crédito.<br>
                ✅ Comece em segundos.
            </span>
        </div>

        </div>
        <div class="hero-img" style="flex: 1 1 400px; text-align: center;">
            <img src="/images/ilustracao-psicologo-vetor.png" alt="PsiGestor - Psicólogo com paciente" style="max-width: 100%; height: auto;">
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

@endsection
