<?php

declare(strict_types=1);

namespace App\Services\Prospecting\Connectors;

use App\Services\Apify\ApifyDatasetService;
use App\Services\Apify\ApifyRunService;
use App\Services\Prospecting\ProspectSourceConnectorInterface;
use RuntimeException;

final class GoogleMapsApifyConnector implements ProspectSourceConnectorInterface
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
            throw new RuntimeException('Le champ query est requis pour Google Maps Scraper.');
        }

        $maxResults = max(1, (int) ($filters['max_results'] ?? 30));
        $runInput = [
            'searchStringsArray' => [$query],
            'maxCrawledPlacesPerSearch' => $maxResults,
            'language' => 'fr',
        ];

        $location = trim((string) ($filters['location'] ?? ''));
        if ($location !== '') {
            $runInput['locationQuery'] = $location;
        }

        $run = $this->runs->runActor('google_maps', $runInput, 120);
        $datasetId = (string) ($run['data']['defaultDatasetId'] ?? '');

        if ($datasetId === '') {
            throw new RuntimeException('Run Apify terminé sans dataset exploitable.');
        }

        $items = $this->datasets->getDatasetItems($datasetId, $maxResults, 0);
        $normalized = array_map(fn (array $item): array => $this->normalize($item), $items);

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
            'source' => 'google_maps_scraper',
            'business_name' => (string) ($item['title'] ?? $item['name'] ?? ''),
            'full_name' => (string) ($item['title'] ?? $item['name'] ?? ''),
            'job_title' => '',
            'city' => (string) ($item['city'] ?? ''),
            'website' => (string) ($item['website'] ?? ''),
            'phone' => (string) ($item['phone'] ?? $item['phoneUnformatted'] ?? ''),
            'email' => '',
            'profile_url' => (string) ($item['url'] ?? ''),
            'rating' => isset($item['totalScore']) ? (float) $item['totalScore'] : null,
            'reviews_count' => isset($item['reviewsCount']) ? (int) $item['reviewsCount'] : null,
        ];
    }
}
