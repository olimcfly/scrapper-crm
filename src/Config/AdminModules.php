<?php

declare(strict_types=1);

namespace App\Config;

final class AdminModules
{
    public static function all(): array
    {
        return [
            ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => '🏠', 'description' => 'Vue globale', 'path' => '/dashboard', 'status' => 'active', 'core' => true, 'ready' => true, 'nav' => 'main'],
            ['key' => 'prospection', 'label' => 'Prospection', 'icon' => '🔎', 'description' => 'Collecte multi-source', 'path' => '/prospects/sources', 'status' => 'active', 'core' => true, 'ready' => true, 'nav' => 'main'],
            ['key' => 'analyse', 'label' => 'Analyse', 'icon' => '🎯', 'description' => 'Analyse stratégique', 'path' => '/strategie', 'status' => 'active', 'core' => true, 'ready' => true, 'nav' => 'main'],
            ['key' => 'studio', 'label' => 'Studio de contenu', 'icon' => '✍️', 'description' => 'Création orientée conversion', 'path' => '/contenu', 'status' => 'active', 'core' => true, 'ready' => true, 'nav' => 'main'],
            ['key' => 'messages', 'label' => 'Messages IA', 'icon' => '💬', 'description' => 'Messages contextuels', 'path' => '/messages-ia', 'status' => 'active', 'core' => true, 'ready' => true, 'nav' => 'main'],
            ['key' => 'contacts', 'label' => 'Contacts', 'icon' => '👥', 'description' => 'Fiches contacts', 'path' => '/prospects', 'status' => 'active', 'core' => true, 'ready' => true, 'nav' => 'main'],
            ['key' => 'pipeline', 'label' => 'Pipeline', 'icon' => '📈', 'description' => 'Suivi de conversion', 'path' => '/pipeline', 'status' => 'active', 'core' => true, 'ready' => true, 'nav' => 'main'],
            ['key' => 'foundation', 'label' => 'Fondation stratégique', 'icon' => '🧠', 'description' => 'Socle métier central', 'path' => '/fondation-strategique', 'status' => 'active', 'core' => true, 'ready' => true, 'nav' => 'main'],
            ['key' => 'public-pages', 'label' => 'Pages publiques', 'icon' => '🌐', 'description' => 'Pages partageables', 'path' => '/pages-publiques', 'status' => 'active', 'core' => true, 'ready' => true, 'nav' => 'main'],
            ['key' => 'resources', 'label' => 'Ressources', 'icon' => '📚', 'description' => 'Bibliothèque actionnable', 'path' => '/ressources', 'status' => 'active', 'core' => false, 'ready' => true, 'nav' => 'secondary'],
            ['key' => 'training', 'label' => 'Formation', 'icon' => '🎓', 'description' => 'Parcours guidé', 'path' => '/formation', 'status' => 'active', 'core' => false, 'ready' => true, 'nav' => 'secondary'],
            ['key' => 'settings', 'label' => 'Paramètres', 'icon' => '⚙️', 'description' => 'Préférences', 'path' => '/settings', 'status' => 'active', 'core' => false, 'ready' => true, 'nav' => 'secondary'],
        ];
    }

    public static function statusCounters(): array
    {
        $counters = ['active' => 0, 'mvp' => 0, 'placeholder' => 0];
        foreach (self::all() as $module) {
            if (isset($counters[$module['status']])) {
                $counters[$module['status']]++;
            }
        }

        return $counters;
    }

    public static function coreModules(): array
    {
        return array_values(array_filter(self::all(), static fn (array $module): bool => $module['core']));
    }

    public static function statusLabels(): array
    {
        return ['active' => 'Actif', 'mvp' => 'MVP', 'placeholder' => 'Placeholder'];
    }

    public static function statusClassMap(): array
    {
        return ['active' => 'status-active', 'mvp' => 'status-mvp', 'placeholder' => 'status-placeholder'];
    }

    public static function findByKey(string $key): ?array
    {
        foreach (self::all() as $module) {
            if ($module['key'] === $key) {
                return $module;
            }
        }

        return null;
    }
}
