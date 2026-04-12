<?php

declare(strict_types=1);

namespace App\Services\Apify;

use RuntimeException;

final class ApifyRunService
{
    private ApifyClient $client;

    /** @var array<string, string> */
    private array $actors;

    public function __construct(?ApifyClient $client = null)
    {
        $config = require dirname(__DIR__, 3) . '/config/apify.php';
        $this->actors = is_array($config['actors'] ?? null) ? $config['actors'] : [];
        $this->client = $client ?? new ApifyClient();
    }

    public function runActor(string $source, array $input = [], ?int $waitForFinish = null): array
    {
        $actorId = trim((string) ($this->actors[$source] ?? ''));
        if ($actorId === '') {
            throw new RuntimeException('Source Apify non configurée: ' . $source);
        }

        $query = [];
        if ($waitForFinish !== null && $waitForFinish > 0) {
            $query['waitForFinish'] = $waitForFinish;
        }

        return $this->client->post('/v2/acts/' . rawurlencode($actorId) . '/runs', $input, $query);
    }

    public function getRun(string $runId): array
    {
        return $this->client->get('/v2/actor-runs/' . rawurlencode($runId));
    }
}
