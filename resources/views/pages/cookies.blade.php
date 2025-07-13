@extends('layouts.landing')

@section('title', 'Política de Cookies | PsiGestor')

@section('content')

{{-- Título com fundo gradiente --}}
<section style="background: linear-gradient(to right, #00aaff, #00c4ff); padding: 60px 20px;">
    <h1 style="text-align: center; color: white; font-size: 2rem; margin: 0;">
        Política de Cookies
    </h1>
</section>

{{-- Conteúdo --}}
<section style="padding: 60px 20px;">
    <div style="max-width: 900px; margin: auto; font-size: 1.05rem; color: #333;">

        <p><strong>Última atualização:</strong> {{ now()->format('d/m/Y') }}</p>

        <p>Esta Política de Cookies explica como o <strong>PsiGestor</strong> utiliza cookies e tecnologias semelhantes para reconhecer você quando visita nosso site <a href="https://psigestor.com">psigestor.com</a>. Ela descreve o que são essas tecnologias, por que as usamos e quais são seus direitos de controle sobre o uso delas, em conformidade com a <strong>LGPD</strong> e o <strong>GDPR</strong>.</p>

        <hr>

        <h3>1. O que são cookies?</h3>
        <p>Cookies são pequenos arquivos de texto que são armazenados no seu navegador ou dispositivo quando você visita um site. Eles ajudam a lembrar suas preferências e aprimorar sua experiência de navegação.</p>

        <h3>2. Por que usamos cookies?</h3>
        <p>Utilizamos cookies por diversos motivos, como:</p>
        <ul>
            <li>Assegurar o funcionamento adequado do site e da plataforma;</li>
            <li>Lembrar preferências de navegação (ex: idioma);</li>
            <li>Analisar o desempenho e uso do site (Google Analytics);</li>
            <li>Oferecer conteúdo e anúncios mais relevantes (com consentimento prévio).</li>
        </ul>

        <h3>3. Tipos de cookies que usamos</h3>
        <ul>
            <li><strong>Cookies estritamente necessários:</strong> essenciais para o funcionamento básico do site;</li>
            <li><strong>Cookies de desempenho:</strong> ajudam a entender como os usuários interagem com o site;</li>
            <li><strong>Cookies de funcionalidade:</strong> lembram escolhas feitas pelo usuário;</li>
            <li><strong>Cookies de marketing:</strong> usados para rastrear visitantes e exibir anúncios relevantes (apenas com consentimento).</li>
        </ul>

        <h3>4. Consentimento para uso de cookies</h3>
        <p>Ao acessar nosso site pela primeira vez, você verá um banner solicitando seu consentimento para uso de cookies. Você pode:</p>
        <ul>
            <li>Aceitar todos os cookies;</li>
            <li>Rejeitar os cookies não essenciais;</li>
            <li>Personalizar suas preferências de cookies.</li>
        </ul>

        <p>Você pode alterar ou revogar seu consentimento a qualquer momento clicando no botão “Preferências de Cookies”, normalmente disponível no rodapé do site.</p>

        <h3>5. Como gerenciar cookies</h3>
        <p>Você pode configurar seu navegador para bloquear ou excluir cookies. No entanto, isso pode afetar a funcionalidade de algumas partes do site. Links úteis:</p>
        <ul>
            <li><a href="https://support.google.com/chrome/answer/95647" target="_blank">Gerenciar cookies no Chrome</a></li>
            <li><a href="https://support.mozilla.org/pt-BR/kb/gerencie-configuracoes-de-cookies" target="_blank">Gerenciar cookies no Firefox</a></li>
            <li><a href="https://support.apple.com/pt-br/guide/safari/sfri11471/mac" target="_blank">Gerenciar cookies no Safari</a></li>
        </ul>

        <h3>6. Cookies de terceiros</h3>
        <p>Podemos utilizar cookies de terceiros, como o Google Analytics, para entender melhor o comportamento dos usuários. Esses cookies seguem as políticas dos respectivos provedores.</p>

        <h3>7. Atualizações nesta política</h3>
        <p>Esta Política de Cookies pode ser atualizada a qualquer momento. Recomendamos a leitura periódica desta página para se manter informado.</p>

        <h3>8. Contato</h3>
        <p>Em caso de dúvidas sobre esta política ou sobre como usamos cookies, entre em contato pelo e-mail:</p>
        <p><strong>psigestor@devtorquato.com.br</strong></p>

        <p>Ao continuar usando nosso site, você concorda com o uso de cookies conforme descrito nesta política.</p>
    </div>
</section>

{{-- BOTÃO WHATSAPP FLOTANTE --}}
<a href="https://wa.me/5582991128022?text=Ol%C3%A1%2C%20tenho%20interesse%20no%20PsiGestor%20e%20gostaria%20de%20saber%20mais%20sobre%20os%20planos!" target="_blank" style="
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
