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
            ['key' => 'dashboard', 'label' => 'Dashboard', 'description' => 'Vue de démarrage avec prochaines actions prioritaires.', 'path' => '/dashboard', 'status' => 'active', 'core' => true, 'ready' => true, 'nav' => 'dashboard'],
            ['key' => 'collecte-profils', 'label' => 'Prospects', 'description' => 'Collecte multi-sources, connexions et recherche par connecteur.', 'path' => '/prospects/sources', 'status' => 'active', 'core' => true, 'ready' => true, 'nav' => 'prospects'],
            ['key' => 'strategie-prospect', 'label' => 'Stratégie', 'description' => 'Analyse psychologique du prospect et recommandations marketing actionnables.', 'path' => '/strategie', 'status' => 'active', 'core' => true, 'ready' => true, 'nav' => 'strategy'],
            ['key' => 'generation-contenu', 'label' => 'Contenu', 'description' => 'Génération de contenu orientée objectif commercial.', 'path' => '/admin/modules/generation-contenu', 'status' => 'mvp', 'core' => true, 'ready' => false, 'nav' => 'secondary'],
            ['key' => 'messages-ia', 'label' => 'Messages IA', 'description' => 'Flow guidé générer → éditer → envoyer.', 'path' => '/messages-ia', 'status' => 'mvp', 'core' => true, 'ready' => false, 'nav' => 'messages'],
            ['key' => 'contacts', 'label' => 'Contacts', 'description' => 'Contacts actionnables avec prochaine action visible.', 'path' => '/admin/modules/contacts', 'status' => 'mvp', 'core' => true, 'ready' => false, 'nav' => 'secondary'],
            ['key' => 'pipeline', 'label' => 'Pipeline', 'description' => 'Kanban mobile pour guider chaque prochaine action de conversion.', 'path' => '/pipeline', 'status' => 'active', 'core' => true, 'ready' => true, 'nav' => 'pipeline'],
            ['key' => 'settings', 'label' => 'Paramètres', 'description' => 'Configuration de l’espace et préférences.', 'path' => '/settings', 'status' => 'placeholder', 'core' => false, 'ready' => true, 'nav' => 'secondary'],
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
