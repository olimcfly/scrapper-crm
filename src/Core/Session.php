<?php

declare(strict_types=1);

namespace App\Core;

final class Session
{
    public static function start(array $config = []): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        if (headers_sent()) {
            return;
        }

        $sessionName = $config['name'] ?? null;
        if (is_string($sessionName) && $sessionName !== '') {
            session_name($sessionName);
        }

        session_set_cookie_params([
            'lifetime' => (int) ($config['lifetime'] ?? 0),
            'path' => $config['path'] ?? '/',
            'domain' => $config['domain'] ?? '',
            'secure' => (bool) ($config['secure'] ?? false),
            'httponly' => (bool) ($config['httponly'] ?? true),
            'samesite' => $config['samesite'] ?? 'Lax',
        ]);

        session_start();
    }

    public static function regenerate(bool $deleteOldSession = true): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }

        session_regenerate_id($deleteOldSession);
    }

    public static function destroy(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                (bool) $params['secure'],
                (bool) $params['httponly']
            );
        }

        session_destroy();
    }
}
