<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ClinicalWritingCopilotService
{
    public function gerarEvolucao(string $topicos): string
    {
        $topicos = trim($topicos);

        if ($topicos === '') {
            return '';
        }

        $model = config('services.gemini.model', 'gemini-2.5-flash');
        $apiKey = config('services.gemini.key');

        if (!$apiKey) {
            throw new \RuntimeException('GEMINI_API_KEY não configurada.');
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $this->buildPrompt($topicos),
                        ],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.3,
                'maxOutputTokens' => 700,
            ],
        ];

        $response = Http::timeout(60)
            ->acceptJson()
            ->post($url . '?key=' . $apiKey, $payload);

        if (!$response->successful()) {
            throw new \RuntimeException(
                'Falha na comunicação com Gemini. Status: ' . $response->status() . '. Resposta: ' . $response->body()
            );
        }

        $json = $response->json();

        $content = data_get($json, 'candidates.0.content.parts.0.text');

        if (!is_string($content) || trim($content) === '') {
            $finishReason = data_get($json, 'candidates.0.finishReason');
            $blockReason = data_get($json, 'promptFeedback.blockReason');

            throw new \RuntimeException(
                'O Gemini não retornou conteúdo válido.'
                . ($finishReason ? ' finishReason: ' . $finishReason . '.' : '')
                . ($blockReason ? ' blockReason: ' . $blockReason . '.' : '')
            );
        }

        return $this->sanitizeOutput($content);
    }

    private function buildPrompt(string $topicos): string
    {
        $systemPrompt = <<<PROMPT
Você é um assistente de escrita clínica para psicólogos.

Sua função é transformar tópicos curtos e anotações objetivas da sessão em um texto único de evolução de prontuário, em português do Brasil, com linguagem profissional, clara, ética e objetiva.

REGRAS OBRIGATÓRIAS:
- Retorne apenas o texto final da evolução, sem títulos, sem listas, sem explicações extras.
- Não invente fatos, sintomas, falas, diagnósticos, riscos, medicações ou contextos não informados.
- Não use linguagem exagerada ou conclusões clínicas que não estejam sustentadas pelos tópicos.
- Não faça julgamento moral.
- Não use estrutura em bullet points.
- Não inclua cabeçalhos como "Evolução:", "Resumo:", "Prontuário:".
- Não mencione que o texto foi gerado por IA.
- Escreva em parágrafo corrido, com boa coesão.
- Mantenha tom clínico, mas natural e legível.
- Se houver intervenções ou encaminhamentos nos tópicos, incorpore-os ao texto.
- Se os tópicos forem breves, ainda assim gere uma evolução enxuta e coerente, sem inventar informações.
PROMPT;

        $userPrompt = <<<PROMPT
Com base exclusivamente nos tópicos abaixo, escreva uma evolução de prontuário psicológico em texto corrido.

TÓPICOS DA SESSÃO:
{$topicos}

INSTRUÇÕES:
- Produza um único texto final.
- Seja fiel apenas ao que foi informado.
- Não invente conteúdo ausente.
- Use linguagem profissional e objetiva.
PROMPT;

        return $systemPrompt . "\n\n" . $userPrompt;
    }

    private function sanitizeOutput(string $content): string
    {
        $content = trim($content);

        $content = preg_replace('/^```[a-zA-Z0-9]*\s*/', '', $content);
        $content = preg_replace('/\s*```$/', '', $content);

        $prefixes = [
            'Evolução:',
            'Evolucao:',
            'Resumo:',
            'Prontuário:',
            'Prontuario:',
            'Texto final:',
        ];

        foreach ($prefixes as $prefix) {
            if (Str::startsWith($content, $prefix)) {
                $content = trim(Str::after($content, $prefix));
            }
        }

        return trim($content);
    }
}