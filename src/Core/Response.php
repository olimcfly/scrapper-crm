<?php

declare(strict_types=1);

namespace App\Core;

final class Response
{
    public static function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        echo $json !== false ? $json : '{"error":"Erreur de sérialisation JSON."}';
    }

    public static function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}
