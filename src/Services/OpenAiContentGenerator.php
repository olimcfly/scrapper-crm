<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;
use Throwable;

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
        try {
            if ($this->apiKey === '') {
                throw new RuntimeException('OPENAI_API_KEY manquant');
            }

            $prompt = $this->buildPrompt($type, $context);
            $responseText = $this->request($prompt);
            $parsed = json_decode($responseText, true);

            if (!is_array($parsed)) {
                throw new RuntimeException('Réponse IA invalide (JSON attendu).');
            }

            return $this->normalizePayload($type, $parsed, $context, 'openai');
        } catch (Throwable $e) {
            return $this->buildTemplateFallback($type, $context, $e->getMessage());
        }
    }

    private function request(string $prompt): string
    {
        $payload = [
            'model' => $this->model,
            'input' => $prompt,
            'text' => ['format' => ['type' => 'json_object']],
            'temperature' => 0.8,
            'max_output_tokens' => 1400,
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
            CURLOPT_TIMEOUT => 12,
            CURLOPT_CONNECTTIMEOUT => 4,
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
        if ($outputText === '') {
            throw new RuntimeException('Réponse IA vide.');
        }

        return $outputText;
    }

    private function buildPrompt(string $type, array $context): string
    {
        $commonContext = "Prospect: {$context['full_name']}\n" .
            "Activité: {$context['activity']}\n" .
            "Objectif: {$context['objectif_contact']}\n" .
            "Pain points: {$context['pain_points_text']}\n" .
            "Désirs: {$context['desires_text']}\n" .
            "Niveau de conscience: {$context['awareness_level']}\n" .
            "Angle choisi: {$context['angle']}\n" .
            "Hook choisi: {$context['hook']}\n" .
            "Canal visé: {$context['channel']}\n";

        return "Tu es un ContentGeneratorService B2C local (bien-être / praticiens).\n" .
            "Retourne STRICTEMENT un JSON avec la forme:\n" .
            "{\n" .
            "  \"context_used\": { ... },\n" .
            "  \"variants\": [\n" .
            "    {\n" .
            "      \"label\": \"Variante 1\",\n" .
            "      \"title\": \"\",\n" .
            "      \"subject\": \"\",\n" .
            "      \"opening\": \"\",\n" .
            "      \"body\": \"\",\n" .
            "      \"closing\": \"\",\n" .
            "      \"cta\": \"\",\n" .
            "      \"format\": \"simple|carousel|dm|whatsapp|sms\"\n" .
            "    }\n" .
            "  ]\n" .
            "}\n" .
            "Règles :\n" .
            "- Génère 3 variantes minimum.\n" .
            "- Type demandé: {$type}.\n" .
            "- Si type=post: hook/titre + corps structuré + fin douce (CTA léger optionnel) + option simple/carrousel.\n" .
            "- Si type=email: objet + ouverture + corps utile + clôture naturelle.\n" .
            "- Si type=message_court: format DM/WhatsApp/SMS, naturel, court, non agressif.\n" .
            "- Adapte explicitement au niveau de conscience, pain points, désirs, angle et hook.\n" .
            "- Français, ton humain, pas de promesses agressives.\n\n" .
            $commonContext;
    }

    private function normalizePayload(string $type, array $payload, array $context, string $source): array
    {
        $variants = [];
        foreach (($payload['variants'] ?? []) as $index => $variant) {
            if (!is_array($variant)) {
                continue;
            }

            $variants[] = $this->normalizeVariant($variant, $type, $index + 1);
        }

        if (count($variants) < 3) {
            return $this->buildTemplateFallback($type, $context, 'Réponse IA incomplète (<3 variantes).');
        }

        return [
            'source' => $source,
            'warning' => '',
            'context_used' => $this->buildContextUsed($context, $payload['context_used'] ?? []),
            'variants' => $variants,
            'primary' => $variants[0],
        ];
    }

    private function normalizeVariant(array $variant, string $type, int $index): array
    {
        $title = trim((string) ($variant['title'] ?? ''));
        $subject = trim((string) ($variant['subject'] ?? ''));
        $opening = trim((string) ($variant['opening'] ?? ''));
        $body = trim((string) ($variant['body'] ?? ''));
        $closing = trim((string) ($variant['closing'] ?? ''));
        $cta = trim((string) ($variant['cta'] ?? ''));
        $format = trim((string) ($variant['format'] ?? ''));

        if ($body === '' && isset($variant['content'])) {
            $body = trim((string) $variant['content']);
        }

        if ($type === 'message_court' && $format === '') {
            $format = 'dm';
        }

        return [
            'label' => trim((string) ($variant['label'] ?? 'Variante ' . $index)),
            'title' => $title,
            'subject' => $subject,
            'opening' => $opening,
            'body' => $body,
            'closing' => $closing,
            'cta' => $cta,
            'format' => $format,
        ];
    }

    private function buildTemplateFallback(string $type, array $context, string $error): array
    {
        $pain = $context['pain_points'][0] ?? 'manque de visibilité';
        $desire = $context['desires'][0] ?? 'gagner en sérénité';
        $angle = $context['angle'] !== '' ? $context['angle'] : 'éducatif';
        $hook = $context['hook'] !== '' ? $context['hook'] : 'Et si vous simplifiiez votre quotidien ?';

        $base = [
            'label' => 'Variante %d',
            'title' => $hook,
            'subject' => 'Une idée simple pour ' . $context['full_name'],
            'opening' => 'Bonjour ' . $context['full_name'] . ',',
            'body' => "Vous m’avez semblé concerné(e) par {$pain}.\nJe vous partage une approche {$angle}, simple à tester dès cette semaine, pour avancer vers {$desire}.",
            'closing' => 'Si vous voulez, je peux vous envoyer un exemple adapté à votre activité.',
            'cta' => 'Dites-moi si cela vous parle.',
            'format' => $type === 'post' ? 'simple' : ($type === 'message_court' ? 'dm' : ''),
        ];

        $variants = [];
        for ($i = 1; $i <= 3; $i++) {
            $variant = $base;
            $variant['label'] = sprintf($base['label'], $i);
            if ($type === 'post') {
                $variant['format'] = $i === 2 ? 'carousel' : 'simple';
                $variant['body'] = "{$hook}\n\n{$pain} revient souvent chez les praticiens locaux.\nVoici une piste {$angle} en 3 points:\n1) clarifier l’objectif client\n2) proposer un micro-pas\n3) mesurer le retour terrain\n\nObjectif: {$desire}.";
                $variant['opening'] = '';
                $variant['subject'] = '';
            } elseif ($type === 'email') {
                $variant['subject'] = "{$context['activity']}: une piste concrète pour {$desire}";
                $variant['body'] = "Je vous écris car {$pain} revient souvent dans votre secteur.\nJe vous propose un mini plan actionnable dès aujourd’hui, sans complexifier votre journée.";
                $variant['format'] = 'email';
            } else {
                $variant['body'] = "Bonjour {$context['full_name']}, je pense à vous suite à {$context['channel']}.\nSi utile, je peux partager une idée rapide pour avancer sur {$pain}, sans pression.";
                $variant['subject'] = '';
                $variant['opening'] = '';
                $variant['closing'] = '';
                $variant['cta'] = '';
                $variant['format'] = $i === 2 ? 'whatsapp' : ($i === 3 ? 'sms' : 'dm');
            }
            $variants[] = $variant;
        }

        return [
            'source' => 'fallback',
            'warning' => 'Mode dégradé actif (IA indisponible): templates serveur utilisés. Détail: ' . $error,
            'context_used' => $this->buildContextUsed($context, []),
            'variants' => $variants,
            'primary' => $variants[0],
        ];
    }

    private function buildContextUsed(array $context, array $contextFromModel): array
    {
        $base = [
            'awareness_level' => (string) ($context['awareness_level'] ?? ''),
            'pain_points' => $context['pain_points'] ?? [],
            'desires' => $context['desires'] ?? [],
            'angle' => (string) ($context['angle'] ?? ''),
            'hook' => (string) ($context['hook'] ?? ''),
            'channel' => (string) ($context['channel'] ?? ''),
            'objectif_contact' => (string) ($context['objectif_contact'] ?? ''),
            'activity' => (string) ($context['activity'] ?? ''),
        ];

        if (!is_array($contextFromModel)) {
            return $base;
        }

        return array_merge($base, $contextFromModel);
    }
}
