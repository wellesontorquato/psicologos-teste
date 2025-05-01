@component('mail::message')

# Bem-vindo ao PsiGestor, {{ $user->email }}!

Olá! Obrigado por se cadastrar em nossa plataforma.

Por favor, clique no botão abaixo para verificar seu endereço de e-mail e ativar sua conta:

<p style="text-align: center; margin: 20px 0;">
    <a href="{{ $verificationUrl }}"
       style="background-color: #00aaff; color: #ffffff; padding: 12px 20px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
        Verificar E-mail
    </a>
</p>

Se você não criou esta conta, nenhuma ação é necessária.

---

### Siga-nos nas redes sociais:

<table role="presentation" align="center" style="margin-top: 10px;">
    <tr>
        <td>
            <a href="https://www.instagram.com/psigestor/" target="_blank">
                <img src="https://png.pngtree.com/element_our/png/20181011/instagram-social-media-icon-design-template-vector-png_126996.png" alt="Instagram" width="32" height="32" style="margin-right:10px;">
            </a>
        </td>
        <td>
            <a href="https://www.facebook.com/psigestor" target="_blank">
                <img src="https://upload.wikimedia.org/wikipedia/commons/6/6c/Facebook_Logo_2023.png" alt="Facebook" width="32" height="32" style="margin-right:10px;">
            </a>
        </td>
        <td>
            <a href="https://twitter.com/psigestor" target="_blank">
                <img src="https://static.vecteezy.com/system/resources/previews/042/148/611/non_2x/new-twitter-x-logo-twitter-icon-x-social-media-icon-free-png.png" alt="Twitter" width="32" height="32">
            </a>
        </td>
    </tr>
</table>
@endcomponent
