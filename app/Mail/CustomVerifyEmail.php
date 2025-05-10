<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomVerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $verificationUrl;

    public function __construct($verificationUrl)
    {
        $this->verificationUrl = $verificationUrl;
    }

    public function build()
    {
        return $this->subject('Confirme seu e-mail')
                    ->markdown('emails.verify-email', [
                        'verificationUrl' => $this->verificationUrl,
                    ]);
    }
}
