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
            ->greeting('Olá!')
            ->line('Vimos que você iniciou seu cadastro no PsiGestor e queremos te convidar a conhecer melhor a plataforma.')
            ->line('O PsiGestor foi desenvolvido para facilitar a rotina de profissionais da saúde mental, ajudando na organização de agenda, pacientes, financeiro e gestão clínica em um só lugar.')
            ->line('Você pode acessar sua conta e explorar os recursos disponíveis com tranquilidade, sem necessidade de cadastrar cartão de crédito neste período inicial.')
            ->line('Além disso, se você recebeu este e-mail, foi selecionado para ganhar mais 10 dias gratuitos de teste, além do período que já havia sido disponibilizado no momento do cadastro.')
            ->action('Reivindicar meus 10 dias extras', $whatsappUrl)
            ->line('Para acessar sua conta, entre pelo link: ' . url('/login'))
            ->line('Esperamos que o PsiGestor ajude a tornar sua rotina mais simples, organizada e leve.')
            ->salutation('Atenciosamente, equipe PsiGestor');
    }
}