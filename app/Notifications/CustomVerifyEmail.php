<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends BaseVerifyEmail
{
    public function toMail($notifiable)
    {
        // Backup da URL atual
        $originalUrl = config('app.url');

        // Força temporariamente a URL personalizada para geração do link
        URL::forceRootUrl(config('app.url_verification'));

        // Gera o link com assinatura correta
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        // Restaura a URL original para não afetar outras partes do app
        URL::forceRootUrl($originalUrl);

        return (new MailMessage)
            ->subject('Confirme seu e-mail para ativar sua conta no PsiGestor')
            ->markdown('emails.verify', [
                'verificationUrl' => $verificationUrl,
                'user' => $notifiable,
            ]);
    }
}
