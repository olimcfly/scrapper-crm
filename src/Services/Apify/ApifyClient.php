<?php

declare(strict_types=1);

namespace App\Services\Apify;

use RuntimeException;

final class ApifyClient
{
    private ApifyUrlBuilder $urlBuilder;
    private string $token;

    public function __construct()
    {
        $config = require dirname(__DIR__, 3) . '/config/apify.php';
        $this->baseUrl = rtrim((string) ($config['base_url'] ?? 'https://api.apify.com'), '/');
        $this->token = trim((string) ($config['token'] ?? ''));

        if ($this->token === '') {
            throw new RuntimeException('Token Apify manquant. Définir APIFY_API_TOKEN (ou APIFY_TOKEN) dans .env.');
        }
    }

    public function get(string $path, array $query = []): array
    {
        return $this->request('GET', $path, null, $query);
    }

    public function post(string $path, array $payload = [], array $query = []): array
    {
        return $this->request('POST', $path, $payload, $query);
    }

    /**
     * @param array<string, scalar|null> $query
     */
    public static function composeUrl(string $baseUrl, string $path, array $query = []): string
    {
        $normalizedBase = rtrim($baseUrl, '/');
        $normalizedPath = self::normalizePath($normalizedBase, $path);

        $url = $normalizedBase . $normalizedPath;
        if ($query !== []) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }

    private function request(string $method, string $path, ?array $payload = null, array $query = []): array
    {
        $url = self::composeUrl($this->baseUrl, $path, $query);

        $ch = curl_init($url);
        if ($ch === false) {
            throw new RuntimeException('Impossible d’initialiser la requête Apify.');
        }

        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $this->token,
        ];

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 45,
        ];

        if ($payload !== null) {
            $headers[] = 'Content-Type: application/json';
            $options[CURLOPT_HTTPHEADER] = $headers;
            $options[CURLOPT_POSTFIELDS] = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        curl_setopt_array($ch, $options);

        $raw = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($raw === false || $curlError !== '') {
            throw new RuntimeException('Échec appel Apify: ' . $curlError);
        }

        $json = json_decode($raw, true);
        if (!is_array($json)) {
            throw new RuntimeException('Réponse Apify invalide (JSON).');
        }

        if ($httpCode >= 400) {
            $message = (string) ($json['error']['message'] ?? ('Erreur Apify HTTP ' . $httpCode));
            $responseExcerpt = mb_substr(trim($raw), 0, 500);
            throw new RuntimeException(sprintf(
                'Apify %s %s -> HTTP %d. Message: %s. Body: %s',
                strtoupper($method),
                $url,
                $httpCode,
                $message,
                $responseExcerpt
            ));
        }

        return $json;
    }

    private static function normalizePath(string $baseUrl, string $path): string
    {
        $normalizedPath = '/' . ltrim($path, '/');
        $baseHasV2 = preg_match('#/v2$#', $baseUrl) === 1;

        if ($baseHasV2) {
            if (preg_match('#^/v2(?:/|$)#', $normalizedPath) === 1) {
                return substr($normalizedPath, 3) ?: '/';
            }

            return $normalizedPath;
        }

        if (preg_match('#^/v2(?:/|$)#', $normalizedPath) === 1) {
            return $normalizedPath;
        }

        return '/v2' . $normalizedPath;
    }
}
