<?php

declare(strict_types=1);

namespace App\Services;

final class AiMessageGenerator
{
    public function generate(array $analysis, array $options): string
    {
        $hook = $analysis['recommended_hooks'][0] ?? 'Je vous contacte suite à votre contexte.';
        $pain = $analysis['pain_points'][0] ?? 'vous perdez du temps avec des actions peu lisibles';
        $desire = $analysis['desires'][0] ?? 'obtenir un système simple et régulier';

        return implode("\n", [
            $hook,
            '',
            'Je me permets un message rapide sur ' . ($options['channel'] ?? 'votre canal préféré') . '.',
            'Souvent, le frein principal est: ' . $pain . '.',
            'Mon objectif: vous aider à ' . mb_strtolower((string) $desire) . ' avec une action très simple.',
            'Si vous voulez, je vous envoie une trame personnalisée en 3 points.',
        ]);
    }
}
