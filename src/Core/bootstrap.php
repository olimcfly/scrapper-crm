<?php

declare(strict_types=1);

/**
 * Charge un fichier .env local sans dépendance externe.
 *
 * Les variables déjà définies dans l'environnement système ne sont pas écrasées.
 */
function loadEnvFile(string $path): void
{
    if (!is_file($path) || !is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!is_array($lines)) {
        return;
    }

    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '#')) {
            continue;
        }

        $separatorPosition = strpos($line, '=');
        if ($separatorPosition === false || $separatorPosition === 0) {
            continue;
        }

        $name = trim(substr($line, 0, $separatorPosition));
        if ($name === '') {
            continue;
        }

        if (getenv($name) !== false || array_key_exists($name, $_ENV) || array_key_exists($name, $_SERVER)) {
            continue;
        }

        $value = trim(substr($line, $separatorPosition + 1));
        if (
            strlen($value) >= 2
            && (($value[0] === '"' && $value[strlen($value) - 1] === '"')
            || ($value[0] === '\'' && $value[strlen($value) - 1] === '\''))
        ) {
            $value = substr($value, 1, -1);
        }

        putenv($name . '=' . $value);
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

loadEnvFile(dirname(__DIR__, 2) . '/.env');

// Composer autoloader (PHPMailer, etc.)
$vendorAutoload = dirname(__DIR__, 2) . '/vendor/autoload.php';
if (is_file($vendorAutoload)) {
    require $vendorAutoload;
}

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
