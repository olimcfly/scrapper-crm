<?php

declare(strict_types=1);

namespace App\Services;

final class Logger
{
    public static function error(string $message): void
    {
        $config = require dirname(__DIR__, 2) . '/config/app.php';
        $logFile = $config['log_file'];
        $date = date('Y-m-d H:i:s');

        @file_put_contents($logFile, sprintf("[%s] ERROR %s\n", $date, $message), FILE_APPEND);
    }
}
