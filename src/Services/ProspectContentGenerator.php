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
     *  length:string,
     *  framework:string,
     *  focus_input:string,
     *  guided_mode:string
     * } $options
     * @return array{content:string,meta:array{source:string,warning?:string}}
     */
    public function generate(array $analysis, array $options, array $foundation = []): array
    {
        $content = $this->buildFallbackContent($analysis, $options, $foundation);

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
     *  length:string,
     *  framework:string,
     *  focus_input:string,
     *  guided_mode:string
     * } $options
     */
    private function buildFallbackContent(array $analysis, array $options, array $foundation): string
    {
        $hook = $analysis['recommended_hooks'][0] ?? 'Vous voulez des actions simples qui donnent des résultats ?';
        $painPoint = $analysis['pain_points'][0] ?? 'vous perdez du temps avec des actions peu efficaces';
        $desire = $analysis['desires'][0] ?? 'obtenir des résultats réguliers';
        $angle = $analysis['content_angles'][0] ?? 'une méthode simple en 3 étapes';

        $lines = [];
        if (trim((string) ($foundation['business_name'] ?? '')) !== '') {
            $lines[] = 'Marque: ' . $foundation['business_name'];
        }
        if (trim((string) ($foundation['offer_name'] ?? '')) !== '') {
            $lines[] = 'Offre: ' . $foundation['offer_name'];
        }
        if (trim((string) ($foundation['promise'] ?? '')) !== '') {
            $lines[] = 'Promesse: ' . $foundation['promise'];
        }

        $lines = array_merge($lines, [
            '🎯 Canal: ' . ucfirst($options['channel']) . ' | Format: ' . $options['content_type'],
            'Niveau de conscience: ' . $analysis['awareness_level'],
        ]);

        if (trim((string) ($options['framework'] ?? '')) !== '') {
            $lines[] = 'Framework activé: ' . $options['framework'];
        }

        if (trim((string) ($options['focus_input'] ?? '')) !== '') {
            $lines[] = 'Demande atelier: ' . $options['focus_input'];
        }

        $lines[] = '';
        $lines[] = $hook;
        $lines[] = '';
        $lines[] = 'Si aujourd’hui ' . $painPoint . ', vous n’êtes pas seul.';
        $lines[] = 'La bonne nouvelle: il est possible de ' . mb_strtolower($desire) . '.';
        $lines[] = 'Angle recommandé: ' . $angle . '.';
        $lines[] = '';
        $lines[] = '👉 Objectif du message: ' . $options['objective'] . '.';
        $lines[] = '👉 Tonalité: ' . $options['tone'] . '.';

        if ($options['length'] !== 'courte') {
            $lines[] = '';
            $lines[] = 'Résumé prospect: ' . $analysis['summary'];
            $lines[] = 'Prochaine action: proposez un échange rapide avec une promesse concrète liée à son besoin principal.';
        }

        if (($options['guided_mode'] ?? '0') === '1') {
            $lines[] = '';
            $lines[] = 'Mode apprentissage guidé:';
            $lines[] = '1) Reformulez le problème du prospect en une phrase simple.';
            $lines[] = '2) Ajoutez une preuve terrain ou un mini cas.';
            $lines[] = '3) Terminez par un CTA unique et mesurable.';
        }

        if ($options['length'] === 'longue') {
            $lines[] = 'Preuve sociale suggérée: partagez un mini cas client avant/après.';
            $lines[] = 'CTA conseillé: "Répondez INFO et je vous envoie la trame complète."';
        }

        return implode("\n", $lines);
    }
}
