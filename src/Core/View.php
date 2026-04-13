<?php

declare(strict_types=1);

namespace App\Core;

use App\Services\Auth;
use App\Config\AdminModules;
final class View
{
    private static ?Auth $auth = null;

    public static function render(string $template, array $data = []): void
    {
        $basePath = dirname(__DIR__, 2) . '/templates/';
        $templatePath = $basePath . $template . '.php';

        if (!is_file($templatePath)) {
            http_response_code(500);
            echo 'Template not found: ' . htmlspecialchars($template);
            return;
        }

        $data['authUser'] = $data['authUser'] ?? self::auth()->user();
        $data['csrfToken'] = $data['csrfToken'] ?? Csrf::token();
        $data['adminModules'] = $data['adminModules'] ?? AdminModules::all();
        $data['currentPath'] = $data['currentPath'] ?? parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);

        extract($data, EXTR_SKIP);
        require $basePath . 'layout/header.php';
        require $templatePath;
        require $basePath . 'layout/footer.php';
    }

    private static function auth(): Auth
    {
        if (self::$auth instanceof Auth) {
            return self::$auth;
        }

        self::$auth = new Auth(Database::connection());

        return self::$auth;
    }
}
