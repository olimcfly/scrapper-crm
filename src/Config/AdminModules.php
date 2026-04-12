<?php

declare(strict_types=1);

namespace App\Config;

final class AdminModules
{
    /**
     * @return array<int, array{key:string,label:string,description:string,path:string,status:string,core:bool,ready:bool}>
     */
    public static function all(): array
    {
        return [
            ['key' => 'dashboard', 'label' => 'Dashboard', 'description' => 'Pilotage global et priorités produit.', 'path' => '/admin/dashboard', 'status' => 'active', 'core' => false, 'ready' => true],
            ['key' => 'collecte-profils', 'label' => 'Collecte profils', 'description' => 'Import, enrichissement et qualification des prospects.', 'path' => '/prospects', 'status' => 'active', 'core' => true, 'ready' => true],
            ['key' => 'strategie-prospect', 'label' => 'Stratégie par prospect', 'description' => 'Plans d’approche personnalisés par cible.', 'path' => '/admin/modules/strategie-prospect', 'status' => 'beta', 'core' => true, 'ready' => false],
            ['key' => 'generation-contenu', 'label' => 'Génération contenu', 'description' => 'Création assistée de messages et supports.', 'path' => '/admin/modules/generation-contenu', 'status' => 'beta', 'core' => true, 'ready' => false],
            ['key' => 'messages-ia', 'label' => 'Messages IA', 'description' => 'Automatisation des séquences conversationnelles.', 'path' => '/admin/modules/messages-ia', 'status' => 'in_progress', 'core' => true, 'ready' => false],
            ['key' => 'contacts', 'label' => 'Contacts', 'description' => 'Gestion centralisée des interlocuteurs et historiques.', 'path' => '/admin/modules/contacts', 'status' => 'active', 'core' => true, 'ready' => false],
            ['key' => 'campagnes', 'label' => 'Campagnes', 'description' => 'Orchestration multi-canal des actions commerciales.', 'path' => '/admin/modules/campagnes', 'status' => 'in_progress', 'core' => false, 'ready' => false],
            ['key' => 'pipeline', 'label' => 'Pipeline', 'description' => 'Vue d’avancement des opportunités.', 'path' => '/admin/modules/pipeline', 'status' => 'beta', 'core' => false, 'ready' => false],
            ['key' => 'automatisations', 'label' => 'Automatisations', 'description' => 'Règles métier et actions déclenchées.', 'path' => '/admin/modules/automatisations', 'status' => 'in_progress', 'core' => false, 'ready' => false],
            ['key' => 'analytics', 'label' => 'Analytics', 'description' => 'Indicateurs de performance et tableaux avancés.', 'path' => '/admin/modules/analytics', 'status' => 'in_progress', 'core' => false, 'ready' => false],
            ['key' => 'templates', 'label' => 'Templates', 'description' => 'Bibliothèque de modèles et contenus réutilisables.', 'path' => '/admin/modules/templates', 'status' => 'beta', 'core' => false, 'ready' => false],
            ['key' => 'integrations', 'label' => 'Intégrations', 'description' => 'Connexions API et synchronisations externes.', 'path' => '/admin/modules/integrations', 'status' => 'in_progress', 'core' => false, 'ready' => false],
            ['key' => 'notifications', 'label' => 'Notifications', 'description' => 'Alertes produit, tâches et suivis d’équipe.', 'path' => '/admin/modules/notifications', 'status' => 'beta', 'core' => false, 'ready' => false],
            ['key' => 'settings', 'label' => 'Paramètres', 'description' => 'Configuration générale de l’espace admin.', 'path' => '/settings', 'status' => 'active', 'core' => false, 'ready' => true],
        ];
    }

    /** @return array{active:int,beta:int,in_progress:int} */
    public static function statusCounters(): array
    {
        $counters = ['active' => 0, 'beta' => 0, 'in_progress' => 0];
        foreach (self::all() as $module) {
            if (isset($counters[$module['status']])) {
                $counters[$module['status']]++;
            }
        }

        return $counters;
    }

    /** @return array<int, array{key:string,label:string,description:string,path:string,status:string,core:bool,ready:bool}> */
    public static function coreModules(): array
    {
        return array_values(array_filter(self::all(), static fn (array $module): bool => $module['core']));
    }

    /** @return array<string,string> */
    public static function statusLabels(): array
    {
        return ['active' => 'Actif', 'beta' => 'Bêta', 'in_progress' => 'En cours de développement'];
    }

    /** @return array<string,string> */
    public static function statusClassMap(): array
    {
        return ['active' => 'status-active', 'beta' => 'status-beta', 'in_progress' => 'status-dev'];
    }

    /** @return array{key:string,label:string,description:string,path:string,status:string,core:bool,ready:bool}|null */
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
