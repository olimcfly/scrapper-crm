<?php

declare(strict_types=1);

require __DIR__ . '/../src/Services/Apify/ApifyClient.php';

use App\Services\Apify\ApifyClient;

$cases = [
    [
        'base' => 'https://api.apify.com/v2',
        'path' => '/acts/apify~instagram-hashtag-scraper/runs',
        'query' => ['waitForFinish' => 120],
        'expected' => 'https://api.apify.com/v2/acts/apify~instagram-hashtag-scraper/runs?waitForFinish=120',
        'label' => 'instagram_hashtag (base with /v2)',
    ],
    [
        'base' => 'https://api.apify.com',
        'path' => '/acts/apify~instagram-hashtag-scraper/runs',
        'query' => ['waitForFinish' => 120],
        'expected' => 'https://api.apify.com/v2/acts/apify~instagram-hashtag-scraper/runs?waitForFinish=120',
        'label' => 'instagram_hashtag (base without /v2)',
    ],
    [
        'base' => 'https://api.apify.com/v2/',
        'path' => '/acts/compass~crawler-google-places/runs',
        'query' => ['waitForFinish' => 60],
        'expected' => 'https://api.apify.com/v2/acts/compass~crawler-google-places/runs?waitForFinish=60',
        'label' => 'google_maps actor',
    ],
    [
        'base' => 'https://api.apify.com/v2',
        'path' => '/v2/actor-runs/abc123',
        'query' => [],
        'expected' => 'https://api.apify.com/v2/actor-runs/abc123',
        'label' => 'legacy path with /v2 prefix',
    ],
];

foreach ($cases as $case) {
    $actual = ApifyClient::composeUrl($case['base'], $case['path'], $case['query']);
    if ($actual !== $case['expected']) {
        fwrite(STDERR, sprintf("[FAIL] %s\nExpected: %s\nActual:   %s\n", $case['label'], $case['expected'], $actual));
        exit(1);
    }

    echo sprintf("[OK] %s -> %s\n", $case['label'], $actual);
}

echo "All Apify URL composition checks passed.\n";
