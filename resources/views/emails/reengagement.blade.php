<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Seu acesso ao PsiGestor está te esperando</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f8fb; font-family: Arial, Helvetica, sans-serif; color:#1f2937;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f8fb; padding:32px 16px;">
        <tr>
            <td align="center">

                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:620px; background:#ffffff; border-radius:18px; overflow:hidden; box-shadow:0 10px 30px rgba(15, 23, 42, 0.08);">

                    <tr>
                        <td align="center" style="padding:34px 32px 18px; background:#ffffff;">
                            <img src="{{ $logoUrl }}" alt="PsiGestor" style="max-width:190px; height:auto; display:block;">
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:10px 36px 0; text-align:center;">
                            <h1 style="margin:0; font-size:26px; line-height:1.3; color:#0f172a; font-weight:700;">
                                Seu acesso ao PsiGestor está te esperando
                            </h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:22px 36px 0;">
                            <p style="margin:0 0 16px; font-size:16px; line-height:1.7; color:#475569;">
                                Olá{{ !empty($user->name) ? ', ' . explode(' ', trim($user->name))[0] : '' }}!
                            </p>

                            <p style="margin:0 0 16px; font-size:16px; line-height:1.7; color:#475569;">
                                Vimos que você iniciou seu cadastro no <strong>PsiGestor</strong> e queremos te convidar a conhecer melhor a plataforma.
                            </p>

                            <p style="margin:0 0 16px; font-size:16px; line-height:1.7; color:#475569;">
                                O PsiGestor foi desenvolvido para facilitar a rotina de profissionais da saúde mental, ajudando na organização de agenda, pacientes, financeiro e gestão clínica em um só lugar.
                            </p>

                            <p style="margin:0 0 20px; font-size:16px; line-height:1.7; color:#475569;">
                                Você pode acessar sua conta e explorar os recursos disponíveis com tranquilidade, sem necessidade de cadastrar cartão de crédito neste período inicial.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 36px 24px;">
                            <div style="background:#eef9ff; border:1px solid #bae6fd; border-radius:14px; padding:20px;">
                                <p style="margin:0 0 8px; font-size:15px; line-height:1.6; color:#0369a1; font-weight:700;">
                                    Benefício exclusivo
                                </p>

                                <p style="margin:0; font-size:15px; line-height:1.7; color:#334155;">
                                    Se você recebeu este e-mail, foi selecionado para reivindicar <strong>mais 10 dias gratuitos de teste</strong>, além do período já disponibilizado no momento do cadastro.
                                </p>

                                <p style="margin:12px 0 0; font-size:13px; line-height:1.6; color:#64748b;">
                                    O bônus é válido apenas para novos usuários que ainda não tiveram assinatura ativa no PsiGestor. A liberação está sujeita à validação do cadastro pela nossa equipe.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding:4px 36px 12px;">
                            <a href="{{ $whatsappUrl }}" style="display:inline-block; background:#0ea5e9; color:#ffffff; text-decoration:none; padding:15px 26px; border-radius:10px; font-size:15px; font-weight:700;">
                                Reivindicar meus 10 dias extras
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding:8px 36px 28px;">
                            <p style="margin:0; font-size:14px; line-height:1.6; color:#64748b;">
                                Ou acesse sua conta diretamente pelo link:
                                <br>
                                <a href="{{ $loginUrl }}" style="color:#0ea5e9; text-decoration:none; font-weight:700;">
                                    Entrar no PsiGestor
                                </a>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 36px 32px;">
                            <p style="margin:0; font-size:15px; line-height:1.7; color:#475569;">
                                Esperamos que o PsiGestor ajude a tornar sua rotina mais simples, organizada e leve.
                            </p>

                            <p style="margin:20px 0 0; font-size:15px; line-height:1.7; color:#475569;">
                                Atenciosamente,<br>
                                <strong>Equipe PsiGestor</strong>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="background:#f8fafc; padding:20px 36px; text-align:center; border-top:1px solid #e2e8f0;">
                            <p style="margin:0; font-size:12px; line-height:1.6; color:#94a3b8;">
                                Este e-mail foi enviado porque houve um cadastro iniciado no PsiGestor.
                                <br>
                                Se você não reconhece esse cadastro, desconsidere esta mensagem.
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>