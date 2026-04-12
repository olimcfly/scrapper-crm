<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

final class OpenAiClient
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = trim((string) getenv('OPENAI_API_KEY'));
        $this->model = trim((string) (getenv('OPENAI_MODEL') ?: 'gpt-4.1-mini'));
    }

    /**
     * @return array{output_text:string}
     */
    public function generateStructuredAnalysis(string $profile): array
    {
        if ($this->apiKey === '') {
            throw new RuntimeException('OPENAI_API_KEY manquant dans .env');
        }

        $instructions = <<<'TXT'
Tu es un expert en marketing et psychologie du prospect.

Analyse ce profil et détermine :
- Son niveau de conscience (Eugene Schwartz)
- Ses frustrations principales
- Ses désirs profonds
- Les angles de contenu adaptés
- 5 hooks marketing personnalisés

Réponds strictement en JSON valide, sans markdown, sans texte additionnel.
Format exact :
{
  "awareness_level": "",
  "summary": "",
  "pain_points": [],
  "desires": [],
  "content_angles": [],
  "recommended_hooks": []
}
TXT;

        $payload = [
            'model' => $this->model,
            'input' => [
                [
                    'role' => 'system',
                    'content' => [
                        ['type' => 'input_text', 'text' => $instructions],
                    ],
                ],
                [
                    'role' => 'user',
                    'content' => [
                        ['type' => 'input_text', 'text' => $profile],
                    ],
                ],
            ],
        ];

        $ch = curl_init('https://api.openai.com/v1/responses');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
            ],
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            CURLOPT_TIMEOUT => 35,
        ]);

        $raw = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($raw === false || $curlError !== '') {
            throw new RuntimeException('Échec appel OpenAI: ' . $curlError);
        }

        $json = json_decode($raw, true);
        if (!is_array($json)) {
            throw new RuntimeException('Réponse OpenAI invalide (JSON).');
        }

        if ($httpCode >= 400) {
            $message = (string) ($json['error']['message'] ?? 'Erreur OpenAI HTTP ' . $httpCode);
            throw new RuntimeException($message);
        }

        $outputText = trim((string) ($json['output_text'] ?? ''));
        if ($outputText === '') {
            throw new RuntimeException('Réponse OpenAI vide.');
        }

        return ['output_text' => $outputText];
    }
}
