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
        $actorId = $this->normalizeActorId((string) ($this->actors[$source] ?? ''));
        if ($actorId === '') {
            throw new RuntimeException('Source Apify non configurée: ' . $source);
        }

        $input = $this->normalizeInputForSource($source, $input);

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

    /** @param array<string, mixed> $input */
    private function normalizeInputForSource(string $source, array $input): array
    {
        if ($source !== 'tiktok') {
            return $input;
        }

        $hashtags = [];
        if (is_array($input['hashtags'] ?? null)) {
            foreach ($input['hashtags'] as $tag) {
                $normalized = trim((string) $tag);
                if ($normalized !== '') {
                    $hashtags[] = ltrim($normalized, '#');
                }
            }
        }

        if ($hashtags === []) {
            $fallbackTag = trim((string) ($input['hashtag'] ?? 'fyp'));
            $hashtags = [ltrim($fallbackTag !== '' ? $fallbackTag : 'fyp', '#')];
        }

        return array_merge([
            'hashtags' => $hashtags,
            'resultsPerPage' => 100,
            'maxFollowersPerProfile' => 0,
            'maxFollowingPerProfile' => 0,
            'commentsPerPost' => 0,
            'topLevelCommentsPerPost' => 0,
            'maxRepliesPerComment' => 0,
            'proxyCountryCode' => 'None',
        ], $input, [
            'hashtags' => $hashtags,
        ]);
    }

    private function normalizeActorId(string $rawActorId): string
    {
        $actorId = trim($rawActorId);
        if ($actorId === '') {
            return '';
        }

        if (str_contains($actorId, '://')) {
            throw new RuntimeException(sprintf(
                'Actor ID invalide pour Apify: "%s". Attendu: "username~actor-name" ou un ID acteur.',
                $actorId
            ));
        }

        if (str_contains($actorId, '/')) {
            $actorId = preg_replace('/\//', '~', $actorId, 1) ?? $actorId;
        }

        return trim($actorId);
    }
}
