<?php

declare(strict_types=1);

return [
    'base_url' => rtrim((string) (getenv('APIFY_BASE_URL') ?: 'https://api.apify.com'), '/'),
    'token' => trim((string) (getenv('APIFY_API_TOKEN') ?: getenv('APIFY_TOKEN') ?: '')),
    'user_id' => trim((string) getenv('APIFY_USER_ID')),
    'actors' => [
        'google_maps' => trim((string) (getenv('APIFY_ACTOR_GOOGLE_MAPS') ?: 'compass~crawler-google-places')),
        'google_search' => trim((string) (getenv('APIFY_ACTOR_GOOGLE_SEARCH') ?: 'apify~google-search-scraper')),
        'instagram_profile' => trim((string) (getenv('APIFY_ACTOR_INSTAGRAM_PROFILE') ?: 'apify~instagram-profile-scraper')),
        'instagram_hashtag' => trim((string) (getenv('APIFY_ACTOR_INSTAGRAM_HASHTAG') ?: 'apify~instagram-hashtag-scraper')),
        'linkedin_profile' => trim((string) (getenv('APIFY_ACTOR_LINKEDIN_PROFILE') ?: 'curious_coder~linkedin-profile-scraper')),
        'tiktok' => trim((string) (getenv('APIFY_ACTOR_TIKTOK') ?: 'clockworks~tiktok-scraper')),
    ],
];
