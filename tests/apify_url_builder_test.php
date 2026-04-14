<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/Services/Apify/ApifyUrlBuilder.php';

use App\Services\Apify\ApifyUrlBuilder;

/** @param array<string, scalar> $query */
function assertUrl(string $baseUrl, string $path, array $query, string $expected): void
{
    $builder = new ApifyUrlBuilder($baseUrl);
    $actual = $builder->build($path, $query);

    if ($actual !== $expected) {
        fwrite(STDERR, "[FAIL] URL mismatch\nExpected: {$expected}\nActual:   {$actual}\n");
        exit(1);
    }

    fwrite(STDOUT, "[OK] {$actual}\n");
}

assertUrl(
    'https://api.apify.com',
    '/acts/apify~instagram-hashtag-scraper/runs',
    ['waitForFinish' => 120],
    'https://api.apify.com/v2/acts/apify~instagram-hashtag-scraper/runs?waitForFinish=120'
);

assertUrl(
    'https://api.apify.com/v2',
    '/acts/apify~google-search-scraper/runs',
    ['waitForFinish' => 120],
    'https://api.apify.com/v2/acts/apify~google-search-scraper/runs?waitForFinish=120'
);

assertUrl(
    'https://api.apify.com/v2/',
    '/v2/acts/apify~instagram-hashtag-scraper/runs',
    ['waitForFinish' => 120],
    'https://api.apify.com/v2/acts/apify~instagram-hashtag-scraper/runs?waitForFinish=120'
);

fwrite(STDOUT, "All Apify URL builder assertions passed.\n");
