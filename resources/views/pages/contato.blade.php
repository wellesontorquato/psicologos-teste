@extends('layouts.landing')

@section('title', 'Fale Conosco | PsiGestor')

@section('content')

{{-- Título com fundo gradiente --}}
<section style="background: linear-gradient(to right, #00aaff, #00c4ff); padding: 60px 20px;">
    <h1 style="text-align: center; color: white; font-size: 2rem; margin: 0;">
        Fale Conosco
    </h1>
</section>

{{-- Conteúdo expandido --}}
<section style="padding: 60px 20px;">
    <div style="max-width: 900px; margin: auto; text-align: center;">
        <p style="font-size: 1.1rem; color: #333; margin-bottom: 25px;">
            Estamos aqui para ouvir você! Seja para tirar dúvidas, enviar sugestões, relatar alguma dificuldade ou compartilhar elogios — nossa equipe terá o maior prazer em responder.
        </p>

        <p style="font-size: 1.1rem; color: #333; margin-bottom: 25px;">
            O <strong>PsiGestor</strong> foi criado para facilitar o dia a dia de psicólogos e clínicas, e cada contato que recebemos é uma oportunidade de evoluir junto com quem confia em nosso trabalho.
        </p>

        <p style="font-size: 1.1rem; color: #333; margin-bottom: 25px;">
            Nosso suporte funciona em horário comercial, mas você pode escrever a qualquer momento. Respondemos sempre o mais rápido possível.
        </p>

        <p style="font-size: 1.1rem; color: #333;">
            Envie um e-mail para:
        </p>

        <p style="font-size: 1.2rem; font-weight: bold; color: #00aaff;">
            <a href="mailto:psigestor@devtorquato.com.br" style="color: #00aaff; text-decoration: none;">
                psigestor@devtorquato.com.br
            </a>
        </p>

        <p style="font-size: 1.1rem; color: #333; margin-top: 40px;">
            Juntos, podemos construir um PsiGestor ainda mais completo, eficiente e acolhedor. Conte conosco sempre!
        </p>
    </div>
</section>
{{-- BOTÃO WHATSAPP FLOTANTE --}}
<a href="https://wa.me/5582991128022?text=Ol%C3%A1%2C%20tenho%20interesse%20no%20PsiGestor%20e%20gostaria%20de%20saber%20mais%20sobre%20os%20planos." target="_blank" style="
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
    <img src="https://psicologos-teste-production.up.railway.app/images/whatsapp.png" alt="WhatsApp" style="width: 24px; height: 24px;">
    (82) 99112-8022
</a>
@endsection
