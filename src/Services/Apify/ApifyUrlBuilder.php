<?php

declare(strict_types=1);

namespace App\Services\Apify;

final class ApifyUrlBuilder
{
    private string $baseUrl;
    private bool $baseIncludesApiVersion;

    public function __construct(string $baseUrl)
    {
        $normalizedBaseUrl = rtrim(trim($baseUrl), '/');
        $this->baseUrl = $normalizedBaseUrl !== '' ? $normalizedBaseUrl : 'https://api.apify.com';
        $this->baseIncludesApiVersion = $this->detectApiVersionInBaseUrl($this->baseUrl);
    }

    /** @param array<string, scalar> $query */
    public function build(string $path, array $query = []): string
    {
        $normalizedPath = '/' . ltrim(trim($path), '/');

        if ($this->baseIncludesApiVersion && preg_match('#^/v2(?:/|$)#', $normalizedPath) === 1) {
            $normalizedPath = preg_replace('#^/v2#', '', $normalizedPath, 1) ?: $normalizedPath;
            $normalizedPath = '/' . ltrim($normalizedPath, '/');
        }

        if (!$this->baseIncludesApiVersion && preg_match('#^/v2(?:/|$)#', $normalizedPath) !== 1) {
            $normalizedPath = '/v2' . $normalizedPath;
        }

        $url = $this->baseUrl . $normalizedPath;

        if ($query !== []) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }

    private function detectApiVersionInBaseUrl(string $baseUrl): bool
    {
        $path = trim((string) parse_url($baseUrl, PHP_URL_PATH), '/');
        if ($path === '') {
            return false;
        }

        $segments = explode('/', $path);

        return in_array('v2', $segments, true);
    }
}
