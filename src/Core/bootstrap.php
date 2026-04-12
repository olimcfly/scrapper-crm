<?php

declare(strict_types=1);

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (str_starts_with($class, $prefix) === false) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = dirname(__DIR__) . '/' . str_replace('\\', '/', $relative) . '.php';

    if (is_file($file)) {
        require $file;
    }
});

$appConfig = require dirname(__DIR__, 2) . '/config/app.php';
date_default_timezone_set($appConfig['timezone']);

App\Core\Session::start($appConfig['session'] ?? []);
