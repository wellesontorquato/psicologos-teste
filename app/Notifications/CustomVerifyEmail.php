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
        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ],
            false // Gera sem usar o app.url
        );

        $customUrl = str_replace(config('app.url'), config('app.url_verification'), $signedUrl);

        return (new MailMessage)
            ->subject('Confirme seu e-mail para ativar sua conta no PsiGestor')
            ->markdown('emails.verify', [
                'verificationUrl' => $customUrl,
                'user' => $notifiable,
            ]);
    }
}

