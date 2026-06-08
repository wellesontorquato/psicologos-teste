<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReengagementEmailNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $whatsappUrl = 'https://wa.me/5582991128022?text=' . urlencode(
            'Olá! Recebi o e-mail do PsiGestor e quero reivindicar meus 10 dias extras de teste.'
        );

        return (new MailMessage)
            ->subject('Seu acesso ao PsiGestor está te esperando')
            ->view('emails.reengagement', [
                'user' => $notifiable,
                'loginUrl' => url('/login'),
                'whatsappUrl' => $whatsappUrl,
                'logoUrl' => asset('images/logo-psigestor.png'),
            ]);
    }
}