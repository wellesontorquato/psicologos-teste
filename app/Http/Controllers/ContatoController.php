<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContatoMail;
use Illuminate\Support\Facades\Http;


class ContatoController extends Controller
{
    public function enviar(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email',
            'telefone' => 'nullable|string|max:20',
            'assunto' => 'required|string',
            'mensagem' => 'required|string',
            'g-recaptcha-response' => 'required',
        ]);

        // Validação do reCAPTCHA
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip(),
        ]);

        if (!($response->json()['success'] ?? false)) {
            return back()->with('error', 'Falha na verificação do reCAPTCHA. Por favor, tente novamente.');
        }

        // Enviar email
        Mail::to('psigestor@devtorquato.com.br')->send(new ContatoMail([
            'nome' => $request->nome,
            'email' => $request->email,
            'telefone' => $request->telefone,
            'assunto' => $request->assunto,
            'mensagem' => $request->mensagem,
        ]));

        return back()->with('success', 'Mensagem enviada com sucesso! Em breve retornaremos.');
    }
}
