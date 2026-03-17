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

        $payload = [
            'model' => config('services.openai.model', 'gpt-4o-mini'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $this->getSystemPrompt(),
                ],
                [
                    'role' => 'user',
                    'content' => $this->buildUserPrompt($topicos),
                ],
            ],
            'temperature' => 0.3,
            'max_tokens' => 700,
        ];

        $response = Http::timeout(60)
            ->acceptJson()
            ->withToken(config('services.openai.key'))
            ->post('https://api.openai.com/v1/chat/completions', $payload);

        if (!$response->successful()) {
            throw new \RuntimeException(
                'Falha na comunicação com a OpenAI. Status: ' . $response->status()
            );
        }

        $content = data_get($response->json(), 'choices.0.message.content');

        if (!is_string($content) || trim($content) === '') {
            throw new \RuntimeException('A OpenAI não retornou conteúdo válido.');
        }

        return $this->sanitizeOutput($content);
    }

    private function getSystemPrompt(): string
    {
        return <<<PROMPT
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

OBJETIVO DO TEXTO:
- Registrar o que foi trazido na sessão
- Apontar aspectos emocionais/comportamentais mencionados ou observados nos tópicos
- Descrever intervenções realizadas, se houver
- Registrar combinados/encaminhamentos, se houver
PROMPT;
    }

    private function buildUserPrompt(string $topicos): string
    {
        return <<<PROMPT
Com base exclusivamente nos tópicos abaixo, escreva uma evolução de prontuário psicológico em texto corrido.

TÓPICOS DA SESSÃO:
{$topicos}

INSTRUÇÕES:
- Produza um único texto final.
- Seja fiel apenas ao que foi informado.
- Não invente conteúdo ausente.
- Use linguagem profissional e objetiva.
PROMPT;
    }

    private function sanitizeOutput(string $content): string
    {
        $content = trim($content);

        // Remove cercas de markdown, caso venham
        $content = preg_replace('/^```[a-zA-Z0-9]*\s*/', '', $content);
        $content = preg_replace('/\s*```$/', '', $content);

        // Remove rótulos iniciais comuns
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