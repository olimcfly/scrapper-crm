<?php

return [
    'base_url' => getenv('APIFY_BASE_URL') ?: 'https://api.apify.com/v2',
    'token' => getenv('APIFY_API_TOKEN') ?: '',
    'user_id' => getenv('APIFY_USER_ID') ?: '',
    'actors' => [
        'instagram_profile' => 'ACTOR_ID_INSTAGRAM_PROFILE',
        'instagram_hashtag' => 'ACTOR_ID_INSTAGRAM_HASHTAG',
        'linkedin_profile' => 'ACTOR_ID_LINKEDIN_PROFILE',
        'tiktok' => 'ACTOR_ID_TIKTOK',
        'google_maps' => 'ACTOR_ID_GOOGLE_MAPS',
    ],
];
