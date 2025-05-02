<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        try {
            Log::info('[DEBUG] Verificação de e-mail iniciada', [
                'auth_user' => auth()->user(),
                'request_user' => $request->user(),
                'id' => $request->route('id'),
                'hash' => $request->route('hash'),
            ]);

            if ($request->user()->hasVerifiedEmail()) {
                Log::info('[DEBUG] Já estava verificado.');
                return redirect()->route('dashboard')->with('status', 'email-already-verified');
            }

            if ($request->user()->markEmailAsVerified()) {
                Log::info('[DEBUG] Marcou como verificado com sucesso.');
                event(new Verified($request->user()));
            }

            Log::info('[DEBUG] Redirecionando para dashboard após verificação.');
            return redirect()->route('dashboard')->with('status', 'email-just-verified');

        } catch (\Throwable $e) {
            Log::error('[ERRO na verificação]', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            abort(500, 'Erro interno na verificação.');
        }
    }
}
