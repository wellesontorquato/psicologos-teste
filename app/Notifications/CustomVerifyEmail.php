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
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Confirme seu e-mail para ativar sua conta no PsiGestor')
            ->markdown('emails.verify', [
                'verificationUrl' => $verificationUrl,
                'user' => $notifiable,
            ]);
    }

    protected function verificationUrl($notifiable)
    {
        $baseUrl = config('app.url_verification') ?? config('app.url');

        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ],
            false // esse false evita que ele adicione APP_URL automaticamente
        );

        // Agora substitui manualmente a base
        return str_replace(config('app.url'), $baseUrl, $signedUrl);
    }
}
