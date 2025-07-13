@extends('layouts.landing')

@section('title', 'Termos de Uso | PsiGestor')

@section('content')

{{-- Título com fundo gradiente --}}
<section style="background: linear-gradient(to right, #00aaff, #00c4ff); padding: 60px 20px;">
    <h1 style="text-align: center; color: white; font-size: 2rem; margin: 0;">
        Termos de Uso
    </h1>
</section>

{{-- Conteúdo dos termos --}}
<section style="padding: 60px 20px;">
    <div style="max-width: 900px; margin: auto; font-size: 1.05rem; color: #333;">
        <p><strong>Última atualização:</strong> {{ now()->format('d/m/Y') }}</p>

        <p>Bem-vindo ao <strong>PsiGestor</strong>. Ao acessar e utilizar nossa plataforma, você concorda com os termos e condições a seguir. Leia atentamente antes de utilizar nossos serviços.</p>

        <hr>

        <h3>1. Aceitação dos Termos</h3>
        <p>O uso do site <a href="https://psigestor.com">psigestor.com</a> e da plataforma PsiGestor implica na aceitação integral e irrestrita destes Termos de Uso. Caso não concorde com alguma das condições, recomendamos que não utilize nossos serviços.</p>

        <h3>2. Serviços Oferecidos</h3>
        <p>O PsiGestor oferece uma plataforma digital voltada para psicólogos, psicanalistas e psiquiatras, com funcionalidades como: agendamento de sessões, prontuários eletrônicos, controle financeiro, lembretes e outros recursos relacionados à gestão clínica.</p>

        <h3>3. Cadastro de Usuário</h3>
        <ul>
            <li>Para acessar a plataforma, é necessário se cadastrar fornecendo informações verdadeiras e atualizadas.</li>
            <li>O usuário é responsável por manter a confidencialidade de sua senha e conta.</li>
            <li>O uso da conta é pessoal e intransferível.</li>
        </ul>

        <h3>4. Responsabilidades do Usuário</h3>
        <ul>
            <li>Usar a plataforma de forma ética, legal e de acordo com os princípios da profissão;</li>
            <li>Não inserir ou compartilhar conteúdos ofensivos, ilegais ou protegidos por direitos autorais sem autorização;</li>
            <li>Proteger os dados sensíveis de seus pacientes, conforme previsto na LGPD e no Código de Ética Profissional.</li>
        </ul>

        <h3>5. Propriedade Intelectual</h3>
        <p>Todo o conteúdo da plataforma (marcas, textos, layouts, imagens, código-fonte) é de propriedade exclusiva do PsiGestor e protegido por leis de direitos autorais e propriedade intelectual.</p>

        <h3>6. Privacidade e Proteção de Dados</h3>
        <p>O uso da plataforma está sujeito à nossa <a href="{{ route('politica-de-privacidade') }}">Política de Privacidade</a>. O PsiGestor adota medidas de segurança para proteger os dados dos usuários e está em conformidade com a LGPD.</p>

        <h3>7. Cancelamento e Rescisão</h3>
        <p>O usuário pode cancelar sua conta a qualquer momento. O PsiGestor se reserva o direito de suspender ou encerrar contas em caso de uso indevido ou violação destes termos.</p>

        <h3>8. Limitação de Responsabilidade</h3>
        <p>O PsiGestor não se responsabiliza por:</p>
        <ul>
            <li>Falhas de conexão ou disponibilidade do sistema por fatores externos;</li>
            <li>Danos indiretos causados por uso inadequado da plataforma;</li>
            <li>Erros causados por terceiros integrados (ex: gateways de pagamento, servidores).</li>
        </ul>

        <h3>9. Alterações nos Termos</h3>
        <p>Reservamo-nos o direito de modificar estes termos a qualquer momento. As alterações entrarão em vigor após a publicação nesta página. É responsabilidade do usuário revisar periodicamente.</p>

        <h3>10. Contato</h3>
        <p>Em caso de dúvidas sobre estes termos, entre em contato:</p>
        <p><strong>E-mail:</strong> contato@psigestor.com<br>
        <strong>WhatsApp:</strong> (82) 99112-8022</p>

        <p>Ao continuar utilizando a plataforma, você concorda com os termos aqui descritos.</p>
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
