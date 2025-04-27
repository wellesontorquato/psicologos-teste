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
            <a href="mailto:contato@psigestor.com.br" style="color: #00aaff; text-decoration: none;">
                contato@psigestor.com.br
            </a>
        </p>

        <p style="font-size: 1.1rem; color: #333; margin-top: 40px;">
            Juntos, podemos construir um PsiGestor ainda mais completo, eficiente e acolhedor. Conte conosco sempre!
        </p>
    </div>
</section>

@endsection
