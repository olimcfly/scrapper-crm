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
        'google_search_scraper' => [
            'label' => 'Google Search Scraper (Apify)',
            'kind' => 'prospecting_connector',
            'connector' => App\Services\Prospecting\Connectors\GoogleSearchApifyConnector::class,
            'search_types' => ['keyword', 'activity', 'city', 'name', 'job_title'],
            'fields' => [
                ['name' => 'query', 'label' => 'Requête Google', 'type' => 'text', 'required' => true],
                ['name' => 'country_code', 'label' => 'Code pays (ex: US, FR)', 'type' => 'text', 'required' => false],
                ['name' => 'language_code', 'label' => 'Code langue (ex: en, fr)', 'type' => 'text', 'required' => false],
                ['name' => 'max_pages', 'label' => 'Pages max par requête', 'type' => 'number', 'required' => false],
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
            'label' => 'Instagram Hashtag (Apify)',
            'kind' => 'prospecting_connector',
            'connector' => App\Services\Prospecting\Connectors\InstagramConnector::class,
            'search_types' => ['hashtag'],
            'fields' => [
                ['name' => 'hashtag', 'label' => 'Hashtag', 'type' => 'text', 'required' => true],
                ['name' => 'results_limit', 'label' => 'Résultats max', 'type' => 'number', 'required' => false],
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
