<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

final class OpenAiContentGenerator
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = trim((string) getenv('OPENAI_API_KEY'));
        $this->model = trim((string) (getenv('OPENAI_MODEL') ?: 'gpt-4.1-mini'));
    }

    public function generate(string $type, array $context): array
    {
        if ($this->apiKey === '') {
            throw new RuntimeException('OPENAI_API_KEY manquant. Configurez la clé API.');
        }

        $prompt = $this->buildPrompt($type, $context);
        $responseText = $this->request($prompt);
        $parsed = json_decode($responseText, true);

        if (!is_array($parsed)) {
            throw new RuntimeException('Réponse IA invalide (JSON attendu).');
        }

        return [
            'hook' => trim((string) ($parsed['hook'] ?? '')),
            'content' => trim((string) ($parsed['content'] ?? $parsed['script'] ?? $parsed['message'] ?? '')),
            'angle' => trim((string) ($parsed['angle'] ?? '')),
        ];
    }

    private function request(string $prompt): string
    {
        $payload = [
            'model' => $this->model,
            'input' => $prompt,
            'text' => [
                'format' => [
                    'type' => 'json_object',
                ],
            ],
            'temperature' => 0.8,
            'max_output_tokens' => 500,
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
            CURLOPT_TIMEOUT => 8,
            CURLOPT_CONNECTTIMEOUT => 3,
        ]);

        $raw = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if (!is_string($raw) || $raw === '') {
            throw new RuntimeException('Aucune réponse IA reçue. ' . $curlError);
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded) || $httpCode >= 400) {
            $message = is_array($decoded) ? (string) ($decoded['error']['message'] ?? 'Erreur API OpenAI') : 'Erreur API OpenAI';
            throw new RuntimeException($message);
        }

        $outputText = trim((string) ($decoded['output_text'] ?? ''));
        if ($outputText !== '') {
            return $outputText;
        }

        throw new RuntimeException('Réponse IA vide.');
    }

    private function buildPrompt(string $type, array $context): string
    {
        $commonContext = "Prospect: {$context['full_name']}\n" .
            "Activité: {$context['activity']}\n" .
            "Objectif: {$context['objectif_contact']}\n" .
            "Blocages/frustrations: {$context['blocages']}\n" .
            "Désirs: {$context['desirs']}\n" .
            "Interaction précédente: {$context['interaction']}\n" .
            "Niveau de conscience: {$context['awareness_level']}\n";

        if ($type === 'video') {
            return "Crée un script vidéo 30-60 secondes.\n\nStructure :\n\nHook immédiat\nSituation réelle\nProblème\nDéclic\nMini solution\n\nStyle :\n\nnaturel\nparlé\nfluide\n\nRetour :\n{\n\"hook\":\"\",\n\"script\":\"\"\n}\n\n" . $commonContext;
        }

        if ($type === 'dm') {
            return "Tu es un expert en communication humaine.\n\nCrée un message DM basé sur :\n\ninteraction précédente (like, commentaire)\nniveau de conscience\n\nObjectif :\n→ démarrer une conversation naturelle\n\nINTERDIT :\n\nvendre\npitcher\nêtre agressif\n\nFormat :\n\ncourt\nhumain\npersonnalisé\n\nRetour :\n{\n\"message\":\"\"\n}\n\n" . $commonContext;
        }

        return "Tu es un expert en copywriting et marketing.\n\nCrée un contenu basé sur :\n\nniveau de conscience du prospect\nses frustrations\nses désirs\n\nObjectif :\n→ capter l’attention\n→ créer de l’identification\n→ déclencher interaction\n\nStructure :\n\nHook (court, impactant)\nDéveloppement (problème + prise de conscience)\nSolution subtile\nOuverture (interaction)\n\nTon :\n\nhumain\ndirect\njamais commercial\n\nRetour en JSON :\n{\n\"hook\":\"\",\n\"content\":\"\",\n\"angle\":\"\"\n}\n\n" . $commonContext;
    }
}
