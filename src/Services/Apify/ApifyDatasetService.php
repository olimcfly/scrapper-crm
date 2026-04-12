<?php

declare(strict_types=1);

namespace App\Services\Apify;

final class ApifyDatasetService
{
    private ApifyClient $client;

    public function __construct(?ApifyClient $client = null)
    {
        $this->client = $client ?? new ApifyClient();
    }

    public function getDatasetItems(string $datasetId, int $limit = 50, int $offset = 0): array
    {
        return $this->client->get('/v2/datasets/' . rawurlencode($datasetId) . '/items', [
            'clean' => 'true',
            'format' => 'json',
            'limit' => max(1, min(500, $limit)),
            'offset' => max(0, $offset),
        ]);
    }
}
