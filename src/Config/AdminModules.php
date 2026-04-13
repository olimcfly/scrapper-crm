<?php

declare(strict_types=1);

namespace App\Config;

final class AdminModules
{
    /**
     * @return array<int, array{key:string,label:string,description:string,path:string,status:string,core:bool,ready:bool,nav:string}>
     */
   public static function all(): array
{
    return [
        ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => '🏠', 'description' => 'Vue globale', 'path' => '/dashboard', 'status' => 'active', 'core' => true, 'ready' => true, 'nav' => 'main'],

        ['key' => 'collecte-profils', 'label' => 'Prospects', 'icon' => '👥', 'description' => 'Collecte multi-source', 'path' => '/prospects/sources', 'status' => 'active', 'core' => true, 'ready' => true, 'nav' => 'main'],

        ['key' => 'strategie-prospect', 'label' => 'Stratégie', 'icon' => '🎯', 'description' => 'Analyse prospect', 'path' => '/strategie', 'status' => 'active', 'core' => true, 'ready' => true, 'nav' => 'main'],

        ['key' => 'generation-contenu', 'label' => 'Contenu', 'icon' => '✍️', 'description' => 'Création ciblée', 'path' => '/contenu', 'status' => 'active', 'core' => true, 'ready' => true, 'nav' => 'main'],

        ['key' => 'messages-ia', 'label' => 'Messages IA', 'icon' => '💬', 'description' => 'Générer et envoyer', 'path' => '/messages-ia', 'status' => 'active', 'core' => true, 'ready' => true, 'nav' => 'main'],

        ['key' => 'pipeline', 'label' => 'Pipeline', 'icon' => '📈', 'description' => 'Suivi conversion', 'path' => '/pipeline', 'status' => 'active', 'core' => true, 'ready' => true, 'nav' => 'main'],

        // SECONDARY
        ['key' => 'contacts', 'label' => 'Contacts', 'icon' => '📇', 'description' => 'Suivi actionnable', 'path' => '/admin/modules/contacts', 'status' => 'mvp', 'core' => true, 'ready' => false, 'nav' => 'secondary'],

        ['key' => 'settings', 'label' => 'Paramètres', 'icon' => '⚙️', 'description' => 'Préférences', 'path' => '/settings', 'status' => 'placeholder', 'core' => false, 'ready' => true, 'nav' => 'secondary'],
    ];
}

    /** @return array{active:int,mvp:int,placeholder:int} */
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

    /** @return array<int, array{key:string,label:string,description:string,path:string,status:string,core:bool,ready:bool,nav:string}> */
    public static function coreModules(): array
    {
        return array_values(array_filter(self::all(), static fn (array $module): bool => $module['core']));
    }

    /** @return array<string,string> */
    public static function statusLabels(): array
    {
        return ['active' => 'Actif', 'mvp' => 'MVP', 'placeholder' => 'Placeholder'];
    }

    /** @return array<string,string> */
    public static function statusClassMap(): array
    {
        return ['active' => 'status-active', 'mvp' => 'status-mvp', 'placeholder' => 'status-placeholder'];
    }

    /** @return array{key:string,label:string,description:string,path:string,status:string,core:bool,ready:bool,nav:string}|null */
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