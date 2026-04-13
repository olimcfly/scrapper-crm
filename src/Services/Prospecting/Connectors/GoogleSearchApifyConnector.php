<?php

declare(strict_types=1);

namespace App\Services\Prospecting\Connectors;

use App\Services\Apify\ApifyDatasetService;
use App\Services\Apify\ApifyRunService;
use App\Services\Prospecting\ProspectSourceConnectorInterface;
use RuntimeException;

final class GoogleSearchApifyConnector implements ProspectSourceConnectorInterface
{
    private ApifyRunService $runs;
    private ApifyDatasetService $datasets;

    public function __construct(?ApifyRunService $runs = null, ?ApifyDatasetService $datasets = null)
    {
        $this->runs = $runs ?? new ApifyRunService();
        $this->datasets = $datasets ?? new ApifyDatasetService();
    }

    public function search(array $filters): array
    {
        $query = trim((string) ($filters['query'] ?? ''));
        if ($query === '') {
            throw new RuntimeException('Le champ query est requis pour Google Search Scraper.');
        }

        $maxResults = max(1, (int) ($filters['max_results'] ?? 30));
        $maxPages = max(1, (int) ($filters['max_pages'] ?? 1));

        $runInput = [
            'queries' => $query,
            'maxPagesPerQuery' => $maxPages,
        ];

        $countryCode = strtoupper(trim((string) ($filters['country_code'] ?? '')));
        if ($countryCode !== '') {
            $runInput['countryCode'] = $countryCode;
        }

        $languageCode = trim((string) ($filters['language_code'] ?? ''));
        if ($languageCode !== '') {
            $runInput['languageCode'] = $languageCode;
        }

        $run = $this->runs->runActor('google_search', $runInput, 120);
        $datasetId = (string) ($run['data']['defaultDatasetId'] ?? '');

        if ($datasetId === '') {
            throw new RuntimeException('Run Apify terminé sans dataset exploitable.');
        }

        $items = $this->datasets->getDatasetItems($datasetId, 500, 0);

        $normalized = [];
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $organicResults = $item['organicResults'] ?? [];
            if (!is_array($organicResults)) {
                continue;
            }

            foreach ($organicResults as $organicResult) {
                if (!is_array($organicResult)) {
                    continue;
                }

                $normalized[] = $this->normalize($organicResult);

                if (count($normalized) >= $maxResults) {
                    break 2;
                }
            }
        }

        return [
            'status' => 'success',
            'run_id' => (string) ($run['data']['id'] ?? ''),
            'dataset_id' => $datasetId,
            'results_count' => count($normalized),
            'results' => $normalized,
        ];
    }

    public function connect(array $credentials): array
    {
        unset($credentials);
        return ['status' => 'connected', 'message' => 'Token Apify disponible et connecteur prêt.'];
    }

    public function sync(array $payload = []): array
    {
        return $this->search($payload);
    }

    public function normalize(array $item): array
    {
        return [
            'source' => 'google_search_scraper',
            'business_name' => '',
            'full_name' => (string) ($item['title'] ?? ''),
            'job_title' => '',
            'city' => '',
            'website' => (string) ($item['url'] ?? ''),
            'phone' => '',
            'email' => '',
            'profile_url' => (string) ($item['url'] ?? ''),
            'rating' => null,
            'reviews_count' => null,
        ];
    }
}
