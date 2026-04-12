<?php

declare(strict_types=1);

namespace App\Services;

final class Logger
{
    public static function info(string $message): void
    {
        self::write('INFO', $message);
    }

    public static function error(string $message): void
    {
        self::write('ERROR', $message);
    }

    private static function write(string $level, string $message): void
    {
        $config = require dirname(__DIR__, 2) . '/config/app.php';
        $logFile = $config['log_file'];
        $logDir = dirname($logFile);
        $date = date('Y-m-d H:i:s');
        $sanitizedMessage = str_replace(["\r", "\n"], ' ', $message);

        if (!is_dir($logDir)) {
            @mkdir($logDir, 0775, true);
        }

        @file_put_contents($logFile, sprintf("[%s] %s %s\n", $date, $level, $sanitizedMessage), FILE_APPEND);
    }
}
