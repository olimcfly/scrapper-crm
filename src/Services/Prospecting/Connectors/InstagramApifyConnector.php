<?php

declare(strict_types=1);

namespace App\Services\Prospecting\Connectors;

use App\Services\Apify\ApifyDatasetService;
use App\Services\Apify\ApifyRunService;
use App\Services\Prospecting\ProspectSourceConnectorInterface;
use RuntimeException;

final class InstagramApifyConnector implements ProspectSourceConnectorInterface
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
        $directUrl = trim((string) ($filters['direct_url'] ?? ''));
        if ($directUrl === '') {
            throw new RuntimeException('Le champ direct_url est requis pour Instagram (Apify).');
        }

        $resultsLimit = max(1, (int) ($filters['results_limit'] ?? 50));
        $searchType = trim((string) ($filters['search_type'] ?? 'hashtag'));
        $searchLimit = max(1, (int) ($filters['search_limit'] ?? 1));

        $runInput = [
            'directUrls' => [$directUrl],
            'resultsType' => 'posts',
            'resultsLimit' => $resultsLimit,
            'searchType' => $searchType,
            'searchLimit' => $searchLimit,
        ];

        $run = $this->runs->runActor('instagram', $runInput, 120);
        $datasetId = (string) ($run['data']['defaultDatasetId'] ?? '');

        if ($datasetId === '') {
            throw new RuntimeException('Run Apify Instagram terminé sans dataset exploitable.');
        }

        $items = $this->datasets->getDatasetItems($datasetId, $resultsLimit, 0);
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
        return ['status' => 'connected', 'message' => 'Token Apify disponible et connecteur Instagram prêt.'];
    }

    public function sync(array $payload = []): array
    {
        return $this->search($payload);
    }

    public function normalize(array $item): array
    {
        return [
            'source' => 'instagram',
            'business_name' => '',
            'full_name' => (string) ($item['ownerFullName'] ?? $item['ownerUsername'] ?? ''),
            'job_title' => '',
            'city' => '',
            'website' => '',
            'phone' => '',
            'email' => '',
            'profile_url' => (string) ($item['ownerProfilePicUrl'] ?? ''),
            'instagram_url' => (string) ($item['url'] ?? ''),
            'username' => (string) ($item['ownerUsername'] ?? ''),
            'likes_count' => isset($item['likesCount']) ? (int) $item['likesCount'] : null,
            'comments_count' => isset($item['commentsCount']) ? (int) $item['commentsCount'] : null,
        ];
    }
}
