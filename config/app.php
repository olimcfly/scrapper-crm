<?php

declare(strict_types=1);

return [
    'env' => getenv('APP_ENV') ?: 'production',
    'debug' => filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOL),
    'base_path' => dirname(__DIR__),
    'timezone' => getenv('APP_TIMEZONE') ?: 'Europe/Paris',
    'log_file' => dirname(__DIR__) . '/storage/logs/app.log',
];
