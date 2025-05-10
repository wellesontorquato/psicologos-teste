<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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
            'g-recaptcha-response' => 'required', // nova regra
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
        Mail::raw("
            Nome: {$dados['nome']}
            E-mail: {$dados['email']}
            Telefone: {$dados['telefone']}
            Assunto: {$dados['assunto']}
            Mensagem:
            {$dados['mensagem']}
        ", function ($message) use ($dados) {
            $message->to('psigestor@devtorquato.com.br')
                    ->subject('Novo contato via site: ' . $dados['assunto']);
        });

        return back()->with('success', 'Mensagem enviada com sucesso! Em breve retornaremos.');
    }
}
