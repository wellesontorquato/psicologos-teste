<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

class FeriadosController extends Controller
{
    public function index(Request $request)
    {
        $ano = (int) $request->query('ano', now()->year);
        $token = '21103|eW7JUTpzyVz2mH8MRiJoAFKn3sbHxXlB';

        $cacheKey = "feriados_invertexto:{$ano}";
        $ttl = now()->addDays(30);

        // Se já está no cache, retorna
        if (Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey));
        }

        try {
            $url = "https://api.invertexto.com/v1/holidays/{$ano}?token={$token}";

            $response = Http::timeout(5)->get($url);

            if ($response->failed()) {
                return response()->json(['error' => 'Falha ao obter feriados'], 500);
            }

            $dados = $response->json();

            // Salva em cache
            Cache::put($cacheKey, $dados, $ttl);

            return response()->json($dados);

        } catch (\Throwable $e) {
            return response()->json(['error' => 'Erro interno'], 500);
        }
    }
}
