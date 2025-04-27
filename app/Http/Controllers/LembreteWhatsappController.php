<?php

namespace App\Http\Controllers;

use App\Models\Sessao;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class LembreteWhatsappController extends Controller
{
    public function enviarLembretesManualmente()
    {
        $hoje = now()->startOfDay();
        $sextaFeira = $hoje->copy()->next(Carbon::FRIDAY);
        $sabado = $hoje->copy()->next(Carbon::SATURDAY);

        $sessoes = Sessao::with('paciente')
            ->whereDate('data', $hoje)
            ->orWhere(function ($query) use ($sextaFeira, $hoje) {
                if ($hoje->isFriday()) {
                    $query->whereDate('data', $hoje->copy()->addDays(3)); // sexta → segunda
                }
            })
            ->get();

        foreach ($sessoes as $sessao) {
            if (!$sessao->paciente || !$sessao->paciente->telefone) {
                continue;
            }

            $numero = '55' . preg_replace('/[^0-9]/', '', $sessao->paciente->telefone); // limpa e adiciona DDI
            $mensagem = "Olá {$sessao->paciente->nome}, tudo bem? Lembrando que você tem uma sessão marcada para {$sessao->data->format('d/m')} às {$sessao->hora}. Confirma sua presença?";

            Http::withHeaders([
                'Authorization' => 'Bearer psicologo:$2b$10$PKtItzgnFZpuYW1K9kVpdO_MNaKDApM5SmRQmH4O5Rz9F5n88WeHi',
            ])->post('http://localhost:21465/api/psicologo/send-message', [
                'phone' => $numero,
                'message' => $mensagem,
            ]);
        }

        return response()->json(['status' => 'Lembretes enviados com sucesso.']);
    }
}
