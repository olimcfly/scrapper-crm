<?php

declare(strict_types=1);

namespace App\Support;

final class AdminModules
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            [
                'slug' => 'collecte-profils',
                'label' => 'Collecte profils',
                'description' => 'Architecture multi-sources et connecteurs de prospection.',
                'status' => 'Actif',
                'route' => '/prospects/sources',
                'icon' => '🔎',
                'is_core' => true,
            ],
            [
                'slug' => 'strategie-prospect',
                'label' => 'Stratégie par prospect',
                'description' => 'Playbooks IA et priorisation commerciale.',
                'status' => 'Bêta',
                'route' => '/admin/modules/strategie-prospect',
                'icon' => '🧭',
                'is_core' => true,
            ],
            [
                'slug' => 'generation-contenu',
                'label' => 'Génération contenu',
                'description' => 'Production de contenus multicanaux assistée par IA.',
                'status' => 'En cours de développement',
                'route' => '/admin/modules/generation-contenu',
                'icon' => '📝',
                'is_core' => true,
            ],
            [
                'slug' => 'messages-ia',
                'label' => 'Messages IA',
                'description' => 'Rédaction assistée et adaptation au contexte prospect.',
                'status' => 'Bêta',
                'route' => '/admin/modules/messages-ia',
                'icon' => '💬',
                'is_core' => true,
            ],
            [
                'slug' => 'contacts',
                'label' => 'Contacts',
                'description' => 'Gestion unifiée des prospects et de leur cycle.',
                'status' => 'Actif',
                'route' => '/prospects',
                'icon' => '👥',
                'is_core' => true,
            ],
            [
                'slug' => 'segmentation',
                'label' => 'Segmentation',
                'description' => 'Segments dynamiques par persona et intention.',
                'status' => 'Bêta',
                'route' => '/admin/modules/segmentation',
                'icon' => '🧩',
                'is_core' => false,
            ],
            [
                'slug' => 'sequences',
                'label' => 'Séquences',
                'description' => 'Orchestration des séquences de prospection.',
                'status' => 'En cours de développement',
                'route' => '/admin/modules/sequences',
                'icon' => '⏱️',
                'is_core' => false,
            ],
            [
                'slug' => 'campagnes',
                'label' => 'Campagnes',
                'description' => 'Pilotage des campagnes par objectif.',
                'status' => 'En cours de développement',
                'route' => '/admin/modules/campagnes',
                'icon' => '📣',
                'is_core' => false,
            ],
            [
                'slug' => 'enrichissement',
                'label' => 'Enrichissement',
                'description' => 'Complétion intelligente des données prospects.',
                'status' => 'Bêta',
                'route' => '/admin/modules/enrichissement',
                'icon' => '✨',
                'is_core' => false,
            ],
            [
                'slug' => 'scoring',
                'label' => 'Scoring',
                'description' => 'Score de priorité commerciale et signaux d\'achat.',
                'status' => 'En cours de développement',
                'route' => '/admin/modules/scoring',
                'icon' => '📈',
                'is_core' => false,
            ],
            [
                'slug' => 'automatisations',
                'label' => 'Automatisations',
                'description' => 'Workflows et actions automatiques inter-modules.',
                'status' => 'En cours de développement',
                'route' => '/admin/modules/automatisations',
                'icon' => '⚙️',
                'is_core' => false,
            ],
            [
                'slug' => 'analytics',
                'label' => 'Analytics',
                'description' => 'KPI business et suivi des performances.',
                'status' => 'Bêta',
                'route' => '/admin/modules/analytics',
                'icon' => '📊',
                'is_core' => false,
            ],
            [
                'slug' => 'integrations',
                'label' => 'Intégrations',
                'description' => 'Connecteurs CRM, emailing et data providers.',
                'status' => 'En cours de développement',
                'route' => '/admin/modules/integrations',
                'icon' => '🔌',
                'is_core' => false,
            ],
            [
                'slug' => 'parametres',
                'label' => 'Paramètres',
                'description' => 'Configuration globale de la plateforme.',
                'status' => 'Actif',
                'route' => '/settings',
                'icon' => '🛠️',
                'is_core' => false,
            ],
        ];
    }

    /** @return array<string, int> */
    public static function statusCounts(): array
    {
        $counts = ['Actif' => 0, 'Bêta' => 0, 'En cours de développement' => 0];

        foreach (self::all() as $module) {
            $status = $module['status'];
            if (isset($counts[$status])) {
                $counts[$status]++;
            }
        }

        return $counts;
    }

    /** @return array<int, array<string, mixed>> */
    public static function coreModules(): array
    {
        return array_values(array_filter(self::all(), static fn (array $module): bool => (bool) ($module['is_core'] ?? false)));
    }

    public static function findBySlug(string $slug): ?array
    {
        foreach (self::all() as $module) {
            if ($module['slug'] === $slug) {
                return $module;
            }
        }

        return null;
    }
}
