<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;

class CustomVerifyEmail extends BaseVerifyEmail
{
    public function toMail($notifiable)
    {
        // Cria uma nova instância temporária do URL Generator só para este e-mail
        $customUrlGenerator = App::make('url');
        $customUrlGenerator->forceRootUrl(config('app.url_verification'));
        $customUrlGenerator->forceScheme('http'); // força http SOMENTE no botão

        // Gera o link de verificação usando a nova instância (não afeta o resto do app)
        $verificationUrl = $customUrlGenerator->temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        return (new MailMessage)
            ->subject('Confirme seu e-mail para ativar sua conta no PsiGestor')
            ->markdown('emails.verify-email', [
                'verificationUrl' => $verificationUrl,
                'user' => $notifiable,
            ]);
    }
}
