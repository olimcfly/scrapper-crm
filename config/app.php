<?php

declare(strict_types=1);

return [
    'env' => getenv('APP_ENV') ?: 'production',
    'debug' => filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOL),
    'base_path' => dirname(__DIR__),
    'timezone' => getenv('APP_TIMEZONE') ?: 'Europe/Paris',
    'log_file' => dirname(__DIR__) . '/storage/logs/app.log',
    'session' => [
        'name' => getenv('SESSION_NAME') ?: 'scrapper_crm_session',
        'lifetime' => (int) (getenv('SESSION_LIFETIME') ?: 0),
        'path' => '/',
        'domain' => getenv('SESSION_DOMAIN') ?: '',
        'secure' => filter_var(getenv('SESSION_SECURE') ?: true, FILTER_VALIDATE_BOOL),
        'httponly' => true,
        'samesite' => getenv('SESSION_SAMESITE') ?: 'Lax',
    ],
];
