<?php

declare(strict_types=1);

namespace App\Services\Prospecting\Connectors;

use App\Services\Prospecting\ProspectSourceConnectorInterface;

abstract class OfficialApiPlaceholderConnector implements ProspectSourceConnectorInterface
{
    public function search(array $filters): array
    {
        return [
            'status' => 'not_supported',
            'message' => 'La recherche prospects n\'est pas exposée par ce connecteur officiel.',
            'filters' => $filters,
            'results' => [],
        ];
    }

    public function connect(array $credentials): array
    {
        $accountId = trim((string) ($credentials['account_id'] ?? ''));

        return [
            'status' => $accountId === '' ? 'error' : 'connected',
            'message' => $accountId === ''
                ? 'account_id requis pour tester la connexion.'
                : 'Connexion validée (placeholder API officielle).',
            'account_id' => $accountId,
        ];
    }

    public function sync(array $payload = []): array
    {
        return [
            'status' => 'queued',
            'message' => 'Synchronisation officielle à implémenter.',
            'payload' => $payload,
        ];
    }

    public function normalize(array $item): array
    {
        return [
            'source' => static::sourceKey(),
            'business_name' => (string) ($item['business_name'] ?? ''),
            'full_name' => (string) ($item['full_name'] ?? ''),
            'job_title' => (string) ($item['job_title'] ?? ''),
            'city' => (string) ($item['city'] ?? ''),
            'website' => (string) ($item['website'] ?? ''),
            'phone' => (string) ($item['phone'] ?? ''),
            'email' => (string) ($item['email'] ?? ''),
            'profile_url' => (string) ($item['profile_url'] ?? ''),
            'rating' => isset($item['rating']) ? (float) $item['rating'] : null,
            'reviews_count' => isset($item['reviews_count']) ? (int) $item['reviews_count'] : null,
        ];
    }

    abstract protected static function sourceKey(): string;
}
