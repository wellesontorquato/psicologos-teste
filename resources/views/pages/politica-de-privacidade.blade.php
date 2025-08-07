@extends('layouts.landing')

@section('title', 'Política de Privacidade | PsiGestor')

@section('content')

{{-- Título com fundo gradiente --}}
<section style="background: linear-gradient(to right, #00aaff, #00c4ff); padding: 60px 20px;">
    <h1 style="text-align: center; color: white; font-size: 2rem; margin: 0;">
        Política de Privacidade
    </h1>
</section>

{{-- Conteúdo da política --}}
<section style="padding: 60px 20px;">
    <div style="max-width: 900px; margin: auto; font-size: 1.05rem; color: #333;">

        <p><strong>Última atualização:</strong> {{ now()->format('d/m/Y') }}</p>

        <p>O <strong>PsiGestor</strong> se compromete a proteger a sua privacidade e garantir a segurança dos seus dados pessoais. Esta Política de Privacidade explica como coletamos, usamos, armazenamos, compartilhamos e protegemos seus dados, em conformidade com a <strong>Lei Geral de Proteção de Dados (LGPD - Lei nº 13.709/2018)</strong> e o <strong>Regulamento Geral sobre a Proteção de Dados da União Europeia (GDPR)</strong>.</p>

        <hr>

        <h3>1. Dados que coletamos</h3>
        <ul>
            <li>Dados cadastrais (nome, e-mail, telefone, CPF, CNPJ, etc);</li>
            <li>Informações de login (usuário, senha criptografada);</li>
            <li>Dados clínicos inseridos por profissionais (ex: prontuários, sessões, evoluções);</li>
            <li>Informações financeiras e de pagamento;</li>
            <li>Dados de uso e navegação no site e sistema (cookies, IP, localização, tipo de navegador);</li>
            <li>Outros dados fornecidos voluntariamente pelo usuário.</li>
        </ul>

        <h3>2. Finalidades do uso dos dados</h3>
        <ul>
            <li>Permitir o uso da plataforma PsiGestor;</li>
            <li>Gerenciar sessões, prontuários e histórico clínico de pacientes;</li>
            <li>Emitir cobranças e relatórios financeiros;</li>
            <li>Enviar comunicações operacionais, de suporte e marketing (com consentimento);</li>
            <li>Cumprir obrigações legais e regulatórias;</li>
            <li>Melhorar a experiência do usuário com base no comportamento de uso.</li>
        </ul>

        <h3>3. Compartilhamento de dados</h3>
        <p>Os dados pessoais não são vendidos a terceiros. Podemos compartilhar informações com:</p>
        <ul>
            <li>Prestadores de serviços essenciais à operação do PsiGestor (ex: servidores, gateway de pagamento);</li>
            <li>Autoridades legais ou regulatórias, mediante obrigação legal;</li>
            <li>Em caso de fusão, aquisição ou venda da empresa, com garantia de continuidade desta política.</li>
        </ul>

        <h3>4. Base legal para tratamento de dados</h3>
        <p>Coletamos e tratamos dados com base em:</p>
        <ul>
            <li>Consentimento do usuário;</li>
            <li>Execução de contrato (ex: prestação de serviço da plataforma);</li>
            <li>Obrigação legal;</li>
            <li>Interesse legítimo (com análise de impacto e minimização de risco).</li>
        </ul>

        <h3>5. Direitos dos titulares</h3>
        <p>Você pode, a qualquer momento:</p>
        <ul>
            <li>Acessar seus dados pessoais;</li>
            <li>Corrigir dados incompletos ou desatualizados;</li>
            <li>Solicitar anonimização ou exclusão;</li>
            <li>Solicitar portabilidade para outro serviço;</li>
            <li>Revogar o consentimento dado anteriormente;</li>
            <li>Solicitar informações sobre o compartilhamento de dados.</li>
        </ul>
        <p>Para exercer seus direitos, entre em contato pelo e-mail <strong>psigestor@devtorquato.com.br</strong>.</p>

        <h3>6. Segurança dos dados</h3>
        <p>Adotamos medidas técnicas e organizacionais rigorosas para proteger seus dados, como:</p>
        <ul>
            <li>Criptografia e armazenamento seguro;</li>
            <li>Controle de acesso restrito a dados sensíveis;</li>
            <li>Monitoramento e prevenção de invasões;</li>
            <li>Backups periódicos e ambiente protegido em nuvem.</li>
        </ul>

        <h3>7. Uso de cookies</h3>
        <p>Utilizamos cookies para melhorar sua navegação e personalizar conteúdos. Você pode aceitar ou rejeitar cookies não essenciais no banner exibido ao acessar o site. Para mais detalhes, acesse nossa <a href="{{ route('cookies') }}">Política de Cookies</a>.</p>

        <h3>8. Transferência internacional de dados</h3>
        <p>Seus dados podem ser processados fora do Brasil ou da UE em servidores em nuvem com garantia de proteção equivalente, conforme cláusulas contratuais aprovadas pela LGPD/GDPR.</p>

        <h3>9. Alterações nesta política</h3>
        <p>Esta política pode ser atualizada periodicamente. As alterações serão publicadas nesta página com a nova data de revisão. Recomendamos a leitura regular.</p>

        <h3>10. Encarregado de dados (DPO)</h3>
        <p>O PsiGestor nomeou um Encarregado pelo Tratamento de Dados (DPO), que pode ser contatado em:</p>
        <p><strong>E-mail:</strong> psigestor@devtorquato.com.br<br>
        <strong>Responsável:</strong> Equipe de Privacidade - PsiGestor</p>

        <p>Ao continuar utilizando nosso site e plataforma, você declara estar ciente e de acordo com esta Política de Privacidade.</p>
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
