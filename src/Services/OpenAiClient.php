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

        $json = $this->request($payload);
        $outputText = trim((string) ($json['output_text'] ?? ''));
        if ($outputText === '') {
            throw new RuntimeException('Réponse OpenAI vide.');
        }

        return ['output_text' => $outputText];
    }

    /**
     * @param array{summary:string,awareness_level:string,pain_points:array<int,string>,main_desire:string,recommended_tone:string,hook_angle:string} $context
     * @return array<int,string>
     */
    public function generateMessageVariants(string $messageType, string $channel, array $context): array
    {
        if ($this->apiKey === '') {
            throw new RuntimeException('OPENAI_API_KEY manquant dans .env');
        }

        $instructions = "Tu es un expert en prospection relationnelle. Génère 3 variantes de message.\n"
            . "Contraintes: naturel, concret, pas de hard-sell, canal respecté.\n"
            . "Réponds strictement en JSON: {\"variants\":[\"\",\"\",\"\"]}.";

        $userPayload = sprintf(
            "Type message: %s\nCanal: %s\nRésumé prospect: %s\nNiveau conscience: %s\nPain points: %s\nDésir principal: %s\nTon recommandé: %s\nHook/angle: %s",
            $messageType,
            $channel,
            $context['summary'],
            $context['awareness_level'],
            implode(' | ', $context['pain_points']),
            $context['main_desire'],
            $context['recommended_tone'],
            $context['hook_angle']
        );

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
                        ['type' => 'input_text', 'text' => $userPayload],
                    ],
                ],
            ],
        ];

        $json = $this->request($payload);
        $outputText = trim((string) ($json['output_text'] ?? ''));
        if ($outputText === '') {
            throw new RuntimeException('Réponse OpenAI vide.');
        }

        $decoded = json_decode($outputText, true);
        if (!is_array($decoded) || !is_array($decoded['variants'] ?? null)) {
            throw new RuntimeException('Réponse OpenAI invalide (variants manquantes).');
        }

        $variants = [];
        foreach ($decoded['variants'] as $item) {
            $text = trim((string) $item);
            if ($text !== '') {
                $variants[] = $text;
            }
        }

        if ($variants === []) {
            throw new RuntimeException('Aucune variante retournée.');
        }

        return array_slice($variants, 0, 3);
    }

    /** @param array<string,mixed> $payload */
    private function request(array $payload): array
    {
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

        return $json;
    }
}
