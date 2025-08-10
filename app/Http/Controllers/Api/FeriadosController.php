<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FeriadosController extends Controller
{
    /**
     * Retorna feriados nacionais de um ano usando API do IBGE.
     * Exemplo: /api/feriados?ano=2025
     */
    public function index(Request $request)
    {
        $ano = $request->query('ano', now()->year);

        try {
            // Endpoint oficial IBGE - Calendário Nacional
            $url = "https://servicodados.ibge.gov.br/api/v2/calendario/nacional/{$ano}";

            $response = Http::timeout(10)->get($url);

            if ($response->failed()) {
                return response()->json([
                    'error' => 'Não foi possível obter os feriados do IBGE.'
                ], 500);
            }

            $feriados = $response->json();

            // Formata para retornar apenas info importante
            $dados = collect($feriados)->map(function ($feriado) {
                return [
                    'data' => $feriado['date'],
                    'nome' => $feriado['name'],
                    'tipo' => $feriado['type'] ?? 'Nacional'
                ];
            })->values();

            return response()->json([
                'ano' => $ano,
                'total' => count($dados),
                'feriados' => $dados
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao conectar à API do IBGE.',
                'detalhe' => $e->getMessage()
            ], 500);
        }
    }
}
