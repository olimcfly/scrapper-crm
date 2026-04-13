<?php

declare(strict_types=1);

namespace App\Services;

final class ProspectContentGenerator
{
    /**
     * @param array{
     *  awareness_level:string,
     *  summary:string,
     *  pain_points:array<int,string>,
     *  desires:array<int,string>,
     *  content_angles:array<int,string>,
     *  recommended_hooks:array<int,string>
     * } $analysis
     * @param array{
     *  content_type:string,
     *  channel:string,
     *  objective:string,
     *  tone:string,
     *  length:string
     * } $options
     * @return array{content:string,meta:array{source:string,warning?:string}}
     */
    public function generate(array $analysis, array $options): array
    {
        $content = $this->buildFallbackContent($analysis, $options);

        return [
            'content' => $content,
            'meta' => [
                'source' => 'fallback',
                'warning' => 'Mode dégradé actif: contenu généré à partir de votre analyse prospect.',
            ],
        ];
    }

    /**
     * @param array{
     *  awareness_level:string,
     *  summary:string,
     *  pain_points:array<int,string>,
     *  desires:array<int,string>,
     *  content_angles:array<int,string>,
     *  recommended_hooks:array<int,string>
     * } $analysis
     * @param array{
     *  content_type:string,
     *  channel:string,
     *  objective:string,
     *  tone:string,
     *  length:string
     * } $options
     */
    private function buildFallbackContent(array $analysis, array $options): string
    {
        $hook = $analysis['recommended_hooks'][0] ?? 'Vous voulez des actions simples qui donnent des résultats ?';
        $painPoint = $analysis['pain_points'][0] ?? 'vous perdez du temps avec des actions peu efficaces';
        $desire = $analysis['desires'][0] ?? 'obtenir des résultats réguliers';
        $angle = $analysis['content_angles'][0] ?? 'une méthode simple en 3 étapes';

        $lines = [
            '🎯 Canal: ' . ucfirst($options['channel']) . ' | Format: ' . $options['content_type'],
            'Niveau de conscience: ' . $analysis['awareness_level'],
            '',
            $hook,
            '',
            'Si aujourd’hui ' . $painPoint . ', vous n’êtes pas seul.',
            'La bonne nouvelle: il est possible de ' . mb_strtolower($desire) . '.',
            'Angle recommandé: ' . $angle . '.',
            '',
            '👉 Objectif du message: ' . $options['objective'] . '.',
            '👉 Tonalité: ' . $options['tone'] . '.',
        ];

        if ($options['length'] !== 'courte') {
            $lines[] = '';
            $lines[] = 'Résumé prospect: ' . $analysis['summary'];
            $lines[] = 'Prochaine action: proposez un échange rapide avec une promesse concrète liée à son besoin principal.';
        }

        if ($options['length'] === 'longue') {
            $lines[] = 'Preuve sociale suggérée: partagez un mini cas client avant/après.';
            $lines[] = 'CTA conseillé: "Répondez INFO et je vous envoie la trame complète."';
        }

        return implode("\n", $lines);
    }
}
