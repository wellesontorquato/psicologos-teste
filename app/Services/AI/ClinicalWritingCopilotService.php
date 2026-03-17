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

        $content = $this->gerarComTentativa($topicos, $model, $apiKey, 1500);

        return $this->sanitizeOutput($content);
    }

    private function gerarComTentativa(string $topicos, string $model, string $apiKey, int $maxOutputTokens): string
    {
        $json = $this->fazerRequisicaoGemini($topicos, $model, $apiKey, $maxOutputTokens);

        $content = data_get($json, 'candidates.0.content.parts.0.text');
        $finishReason = data_get($json, 'candidates.0.finishReason');
        $blockReason = data_get($json, 'promptFeedback.blockReason');

        if ($blockReason) {
            throw new \RuntimeException('Resposta bloqueada pelo Gemini. blockReason: ' . $blockReason . '.');
        }

        if (!is_string($content) || trim($content) === '') {
            throw new \RuntimeException(
                'O Gemini não retornou conteúdo válido.'
                . ($finishReason ? ' finishReason: ' . $finishReason . '.' : '')
            );
        }

        $content = trim($content);

        // Se vier truncado por limite, tenta novamente com mais tokens
        if (
            in_array($finishReason, ['MAX_TOKENS', 'RECITATION', 'OTHER'], true)
            || $this->pareceTextoCortado($content)
        ) {
            $jsonRetry = $this->fazerRequisicaoGemini($topicos, $model, $apiKey, 2500);

            $retryContent = data_get($jsonRetry, 'candidates.0.content.parts.0.text');
            $retryFinishReason = data_get($jsonRetry, 'candidates.0.finishReason');
            $retryBlockReason = data_get($jsonRetry, 'promptFeedback.blockReason');

            if ($retryBlockReason) {
                throw new \RuntimeException('Resposta bloqueada pelo Gemini. blockReason: ' . $retryBlockReason . '.');
            }

            if (is_string($retryContent) && trim($retryContent) !== '') {
                $content = trim($retryContent);

                if ($retryFinishReason === 'MAX_TOKENS' && $this->pareceTextoCortado($content)) {
                    throw new \RuntimeException('A resposta da IA foi interrompida por limite de tokens.');
                }
            }
        }

        return $content;
    }

    private function fazerRequisicaoGemini(string $topicos, string $model, string $apiKey, int $maxOutputTokens): array
    {
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
                'topP' => 0.9,
                'maxOutputTokens' => $maxOutputTokens,
            ],
        ];

        $response = Http::timeout(90)
            ->acceptJson()
            ->post($url . '?key=' . $apiKey, $payload);

        if (!$response->successful()) {
            throw new \RuntimeException(
                'Falha na comunicação com Gemini. Status: ' . $response->status() . '. Resposta: ' . $response->body()
            );
        }

        $json = $response->json();

        if (!is_array($json)) {
            throw new \RuntimeException('Resposta inválida da API do Gemini.');
        }

        return $json;
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
- Gere um texto completo, finalizado e bem encerrado, sem interromper frases pela metade.
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
- Entregue o texto completo, sem cortar palavras ou frases.
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

        $content = preg_replace("/\r\n|\r/", "\n", $content);
        $content = preg_replace("/[ \t]+/", ' ', $content);
        $content = preg_replace("/\n{2,}/", "\n\n", $content);
        $content = trim($content);

        // Se terminar com palavra claramente quebrada, tenta ao menos normalizar o fim
        if ($this->pareceTextoCortado($content)) {
            $content = rtrim($content, " \t\n\r\0\x0B,;:-");
        }

        // Garante fechamento mínimo do texto
        if (!preg_match('/[.!?…]$/u', $content)) {
            $content .= '.';
        }

        return trim($content);
    }

    private function pareceTextoCortado(string $content): bool
    {
        $content = trim($content);

        if ($content === '') {
            return false;
        }

        // termina com conectivos ou sinais que normalmente indicam corte
        $finaisSuspeitos = [
            ' e',
            ' de',
            ' da',
            ' do',
            ' em',
            ' com',
            ' para',
            ' por',
            ' que',
            ' ao',
            ' aos',
            ' na',
            ' no',
            ' das',
            ' dos',
            ',',
            ';',
            ':',
            '-',
            '(',
        ];

        foreach ($finaisSuspeitos as $final) {
            if (Str::endsWith(mb_strtolower($content), $final)) {
                return true;
            }
        }

        // palavra final muito curta/quebrada sem pontuação final
        if (!preg_match('/[.!?…]$/u', $content)) {
            $ultimaPalavra = preg_replace('/.*\s/u', '', $content);

            if ($ultimaPalavra !== '' && mb_strlen($ultimaPalavra) <= 5) {
                return true;
            }
        }

        return false;
    }
}