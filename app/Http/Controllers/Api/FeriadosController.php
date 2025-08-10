<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

class FeriadosController extends Controller
{
    /**
     * GET /api/feriados?ano=2025[&full=1]
     *
     * - Sem "full": retorna ["YYYY-MM-DD", ...]
     * - Com  "full": retorna [{ "data": "...", "nome": "...", "tipo": "..." }, ...]
     */
    public function index(Request $request)
    {
        $ano  = (int) $request->query('ano', now()->year);
        $full = $request->boolean('full', false);

        try {
            $url = "https://servicodados.ibge.gov.br/api/v2/calendario/nacional/{$ano}";
            $resp = Http::timeout(12)->get($url);

            if ($resp->failed()) {
                return response()->json(['error' => 'Falha ao obter feriados do IBGE.'], 502);
            }

            $dadosIbge = $resp->json();

            // Normaliza para nosso formato
            $colecao = collect($dadosIbge)->map(function ($f) {
                return [
                    'data' => $f['date'] ?? $f['data'] ?? null,
                    'nome' => $f['name'] ?? $f['nome'] ?? null,
                    'tipo' => $f['type'] ?? $f['tipo'] ?? 'Nacional',
                ];
            })->filter(fn ($f) => !empty($f['data']))->values();

            if ($full) {
                return response()->json($colecao);
            }

            // Retorna só as datas (string YYYY-MM-DD)
            return response()->json($colecao->pluck('data')->values());

        } catch (\Throwable $e) {
            return response()->json([
                'error'   => 'Erro ao conectar à API do IBGE.',
                'detalhe' => app()->hasDebugModeEnabled() ? $e->getMessage() : null,
            ], 500);
        }
    }
}
