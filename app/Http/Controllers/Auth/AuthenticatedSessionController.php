<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Services\AuditLogger;

class AuthenticatedSessionController extends Controller
{
    /**
     * Exibe o formulário de login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Processa a tentativa de login.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // ✅ Loga o evento de login
        AuditLogger::log('login', 'Usuário realizou login com sucesso');

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Realiza o logout.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // ✅ Loga o evento de logout antes de sair
        AuditLogger::log('logout', 'Usuário fez logout');

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
