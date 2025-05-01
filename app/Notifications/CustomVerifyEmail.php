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
        // Backup da URL e scheme atuais
        $originalUrl = config('app.url');
        $originalScheme = URL::getScheme();

        // Força temporariamente a URL personalizada (http)
        URL::forceRootUrl(config('app.url_verification'));
        URL::forceScheme('http');

        // Gera o link com assinatura válida (baseado no app.url_verification)
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        // Restaura as configurações originais
        URL::forceRootUrl($originalUrl);
        URL::forceScheme($originalScheme);

        return (new MailMessage)
            ->subject('Confirme seu e-mail para ativar sua conta no PsiGestor')
            ->markdown('emails.verify', [
                'verificationUrl' => $verificationUrl,
                'user' => $notifiable,
            ]);
    }
}
