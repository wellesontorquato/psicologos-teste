<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client as GoogleClient;

class GoogleAuthController extends Controller
{
    protected function makeClient(): GoogleClient
    {
        $client = new GoogleClient();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));
        $client->setAccessType('offline');
        $client->setIncludeGrantedScopes(true);
        $client->setPrompt('consent');
        $client->setScopes([
            'https://www.googleapis.com/auth/calendar',
            'https://www.googleapis.com/auth/calendar.events',
        ]);
        return $client;
    }

    public function redirect(Request $r)
    {
        $client = $this->makeClient();

        // CSRF state
        $state = bin2hex(random_bytes(16));
        $r->session()->put('google_oauth_state', $state);
        $client->setState($state);

        return redirect()->away($client->createAuthUrl());
    }

    public function callback(Request $r)
    {
        // Usuário cancelou/negou no Google?
        if ($r->query('error') === 'access_denied') {
            return redirect()->route('agenda')->with('error', 'Conexão ao Google cancelada pelo usuário.');
        }

        // Valida CSRF state
        $state = $r->query('state');
        if (!$state || $state !== $r->session()->pull('google_oauth_state')) {
            abort(419, 'Invalid OAuth state');
        }

        $code = $r->query('code');
        if (!$code) {
            return redirect()->route('agenda')->with('error', 'Code ausente na resposta do Google.');
        }

        $client = $this->makeClient();

        try {
            $token = $client->fetchAccessTokenWithAuthCode($code);
        } catch (\Throwable $e) {
            return redirect()->route('agenda')->with('error', 'Falha ao trocar o code por token.');
        }

        if (isset($token['error'])) {
            return redirect()->route('agenda')->with('error', 'Erro no OAuth: ' . $token['error']);
        }

        // $token: access_token, expires_in, (refresh_token na 1ª vez), scope, token_type, created
        $user = $r->user();
        $user->update([
            'google_access_token'     => $token['access_token'] ?? null,
            // Em reconexões o refresh_token pode não vir; preserva o existente
            'google_refresh_token'    => $token['refresh_token'] ?? $user->google_refresh_token,
            'google_token_expires_at' => now()->addSeconds($token['expires_in'] ?? 3500),
            'google_calendar_id'      => 'primary',
            'google_connected'        => true,
        ]);

        return redirect()->route('agenda')->with('success', 'Google Agenda conectado!');
    }

    public function disconnect(Request $r)
    {
        $user = $r->user();

        // Revoga no Google (tenta com refresh_token; se não tiver, usa access_token)
        try {
            $client = $this->makeClient();
            $tokenToRevoke = $user->google_refresh_token ?: $user->google_access_token;
            if ($tokenToRevoke) {
                // google/apiclient faz POST para https://oauth2.googleapis.com/revoke
                $client->revokeToken($tokenToRevoke);
            }
        } catch (\Throwable $e) {
            // opcional: logar $e->getMessage()
        }

        // Limpa credenciais locais
        $user->update([
            'google_access_token'     => null,
            'google_refresh_token'    => null,
            'google_token_expires_at' => null,
            'google_calendar_id'      => null,
            'google_connected'        => false,
        ]);

        return redirect()->route('agenda')->with('success', 'Integração com Google Agenda removida.');
    }
}
