<?php

declare(strict_types=1);

return [
    'sources' => [
        'google_maps_scraper' => [
            'label' => 'Google Maps Scraper (Apify)',
            'kind' => 'prospecting_connector',
            'connector' => App\Services\Prospecting\Connectors\GoogleMapsApifyConnector::class,
            'search_types' => ['keyword', 'activity', 'city', 'name', 'job_title'],
            'fields' => [
                ['name' => 'query', 'label' => 'Mot-clé / activité / ville', 'type' => 'text', 'required' => true],
                ['name' => 'location', 'label' => 'Zone (optionnel)', 'type' => 'text', 'required' => false],
                ['name' => 'max_results', 'label' => 'Résultats max', 'type' => 'number', 'required' => false],
            ],
        ],
        'google_business_profile' => [
            'label' => 'Google Business Profile',
            'kind' => 'official_api',
            'connector' => App\Services\Prospecting\Connectors\GoogleBusinessProfileConnector::class,
            'search_types' => ['authorized_account_sync'],
            'fields' => [
                ['name' => 'account_id', 'label' => 'ID compte Google', 'type' => 'text', 'required' => true],
            ],
        ],
        'linkedin' => [
            'label' => 'LinkedIn',
            'kind' => 'official_api',
            'connector' => App\Services\Prospecting\Connectors\LinkedInConnector::class,
            'search_types' => ['authorized_account_sync'],
            'fields' => [
                ['name' => 'account_id', 'label' => 'ID compte LinkedIn', 'type' => 'text', 'required' => true],
            ],
        ],
        'instagram' => [
            'label' => 'Instagram (Apify)',
            'kind' => 'prospecting_connector',
            'connector' => App\Services\Prospecting\Connectors\InstagramApifyConnector::class,
            'search_types' => ['profile', 'hashtag'],
            'fields' => [
                ['name' => 'direct_url', 'label' => 'URL Instagram', 'type' => 'text', 'required' => true],
                ['name' => 'results_limit', 'label' => 'Résultats max', 'type' => 'number', 'required' => false],
                ['name' => 'search_type', 'label' => 'Type de recherche', 'type' => 'text', 'required' => false],
                ['name' => 'search_limit', 'label' => 'Search limit', 'type' => 'number', 'required' => false],
            ],
        ],
        'tiktok' => [
            'label' => 'TikTok',
            'kind' => 'official_api',
            'connector' => App\Services\Prospecting\Connectors\TikTokConnector::class,
            'search_types' => ['authorized_account_sync'],
            'fields' => [
                ['name' => 'account_id', 'label' => 'ID compte TikTok', 'type' => 'text', 'required' => true],
            ],
        ],
        'facebook' => [
            'label' => 'Facebook',
            'kind' => 'official_api',
            'connector' => App\Services\Prospecting\Connectors\FacebookConnector::class,
            'search_types' => ['authorized_account_sync'],
            'fields' => [
                ['name' => 'account_id', 'label' => 'ID compte Facebook', 'type' => 'text', 'required' => true],
            ],
        ],
    ],
];
