<?php

declare(strict_types=1);

namespace App\Services\Prospecting\Connectors;

use App\Services\Apify\ApifyDatasetService;
use App\Services\Apify\ApifyRunService;
use RuntimeException;

final class InstagramConnector extends OfficialApiPlaceholderConnector
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
        $hashtag = ltrim(trim((string) ($filters['hashtag'] ?? '')), '#');
        if ($hashtag === '') {
            throw new RuntimeException('Le champ hashtag est requis pour Instagram Hashtag Scraper.');
        }

        $resultsLimit = max(1, (int) ($filters['results_limit'] ?? 20));
        $runInput = [
            'hashtags' => [$hashtag],
            'resultsType' => 'posts',
            'resultsLimit' => $resultsLimit,
        ];

        $run = $this->runs->runActor('instagram_hashtag', $runInput, 120);
        $datasetId = (string) ($run['data']['defaultDatasetId'] ?? '');

        if ($datasetId === '') {
            throw new RuntimeException('Run Apify terminé sans dataset exploitable.');
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
        return ['status' => 'connected', 'message' => 'Token Apify disponible et connecteur Instagram hashtag prêt.'];
    }

    public function normalize(array $item): array
    {
        $hashtag = ltrim((string) ($item['queryTag'] ?? ''), '#');
        $caption = trim((string) ($item['caption'] ?? ''));

        $username = trim((string) ($item['ownerUsername'] ?? ''));

        return [
            'source' => static::sourceKey(),
            'business_name' => '',
            'full_name' => (string) ($item['ownerFullName'] ?? ''),
            'job_title' => '',
            'city' => '',
            'website' => '',
            'phone' => '',
            'email' => '',
            'profile_url' => $username !== '' ? 'https://www.instagram.com/' . ltrim($username, '@') . '/' : '',
            'post_url' => (string) ($item['url'] ?? ''),
            'hashtag' => $hashtag,
            'caption' => $caption,
            'engagement_score' => (int) ($item['likesCount'] ?? 0) + (int) ($item['commentsCount'] ?? 0),
            'rating' => null,
            'reviews_count' => null,
        ];
    }

    protected static function sourceKey(): string
    {
        return 'instagram';
    }
}
