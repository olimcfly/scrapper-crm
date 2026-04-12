<?php

declare(strict_types=1);

return [
    'api_token' => getenv('APIFY_API_TOKEN') ?: '',
    'user_id' => getenv('APIFY_USER_ID') ?: '',
    'base_url' => rtrim(getenv('APIFY_BASE_URL') ?: 'https://api.apify.com/v2', '/'),
];
