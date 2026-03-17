<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\AI\ClinicalWritingCopilotService;

class ClinicalCopilotController extends Controller
{
    public function gerar(Request $request, ClinicalWritingCopilotService $copilotService): JsonResponse
    {
        $validated = $request->validate([
            'topicos' => ['required', 'string', 'min:5', 'max:10000'],
        ]);

        try {
            $evolucao = $copilotService->gerarEvolucao($validated['topicos']);

            if (!is_string($evolucao) || trim($evolucao) === '') {
                return response()->json([
                    'message' => 'A IA não retornou uma evolução válida.',
                ], 422);
            }

            return response()->json([
                'evolucao' => trim($evolucao),
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Não foi possível gerar a evolução com IA no momento.',
            ], 500);
        }
    }
}