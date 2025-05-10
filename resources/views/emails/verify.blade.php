@component('mail::message')

# Bem-vindo ao PsiGestor, {{ $user->email }}!

Olá! Obrigado por se cadastrar em nossa plataforma.

Clique no botão abaixo para verificar seu e-mail e ativar sua conta:

@component('mail::button', ['url' => $verificationUrl, 'color' => 'primary'])
Verificar E-mail
@endcomponent

Se você não criou esta conta, nenhuma ação é necessária.

---

### Siga-nos nas redes sociais:

<table role="presentation" align="center" style="margin-top: 10px;">
    <tr>
        <td>
            <a href="https://www.instagram.com/psigestor/" target="_blank">
                <img src="https://www.psigestor.com/images/instagram.png" alt="Instagram" width="32" height="32" style="margin-right:10px;">
            </a>
        </td>
        <td>
            <a href="https://www.facebook.com/psigestor/" target="_blank">
                <img src="https://www.psigestor.com/images/facebook.png" alt="Facebook" width="32" height="32" style="margin-right:10px;">
            </a>
        </td>
        <td>
            <a href="https://x.com/psigestor" target="_blank">
                <img src="https://www.psigestor.com/images/twitter.png" alt="Twitter / X" width="32" height="32">
            </a>
        </td>
    </tr>
</table>

@endcomponent
