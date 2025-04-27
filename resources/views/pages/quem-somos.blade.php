@extends('layouts.landing')

@section('title', 'Quem Somos | PsiGestor')

@section('content')

{{-- Título com fundo gradiente --}}
<section style="background: linear-gradient(to right, #00aaff, #00c4ff); padding: 60px 20px;">
    <h1 style="text-align: center; color: white; font-size: 2rem; margin: 0;">
        Quem Somos
    </h1>
</section>

{{-- Conteúdo institucional --}}
<section style="padding: 60px 20px;">
    <div style="max-width: 900px; margin: auto; text-align: center;">
        <p style="font-size: 1.1rem; color: #333; margin-bottom: 25px;">
            O <strong>PsiGestor</strong> é uma solução digital pensada especialmente para quem dedica sua vida ao cuidado emocional e psicológico das pessoas. Nascemos da união entre profissionais da saúde e da tecnologia com um propósito claro: facilitar a gestão de sessões com leveza, organização e eficiência.
        </p>

        <p style="font-size: 1.1rem; color: #333; margin-bottom: 25px;">
            Sabemos que o tempo do psicólogo é precioso. Por isso, criamos uma plataforma que automatiza processos, simplifica o agendamento de sessões, mantém registros clínicos organizados e permite o acompanhamento financeiro completo — tudo isso com segurança, sigilo e foco na experiência do usuário.
        </p>

        <p style="font-size: 1.1rem; color: #333; margin-bottom: 25px;">
            Mais do que um sistema, o PsiGestor é um parceiro estratégico para clínicas e profissionais autônomos que desejam crescer, melhorar sua rotina e dedicar mais tempo ao que realmente importa: o cuidado com o ser humano.
        </p>

        <p style="font-size: 1.1rem; color: #333; margin-bottom: 25px;">
            Estamos em constante evolução, ouvindo cada feedback e adaptando o sistema às reais necessidades do dia a dia clínico. Acreditamos na tecnologia como aliada da empatia, e é isso que entregamos: uma plataforma moderna, intuitiva e feita com carinho para você.
        </p>

        <p style="font-size: 1.1rem; color: #333;">
            Seja bem-vindo ao PsiGestor. Estamos aqui para simplificar sua jornada profissional — com cuidado, confiança e inovação.
        </p>
    </div>
</section>

@endsection
