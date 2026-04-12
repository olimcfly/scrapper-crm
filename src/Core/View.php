<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $template, array $data = []): void
    {
        $basePath = dirname(__DIR__, 2) . '/templates/';
        $templatePath = $basePath . $template . '.php';

        if (!is_file($templatePath)) {
            http_response_code(500);
            echo 'Template not found: ' . htmlspecialchars($template);
            return;
        }

        extract($data, EXTR_SKIP);
        require $basePath . 'layout/header.php';
        require $templatePath;
        require $basePath . 'layout/footer.php';
    }
}
