<?php

declare(strict_types=1);

return [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'port' => (int) (getenv('DB_PORT') ?: 3306),
    'database' => getenv('DB_NAME') ?: 'efcv1044_db_crm',
    'username' => getenv('DB_USER') ?: 'efcv1044_db_crm',
    'password' => getenv('DB_PASSWORD') ?: '',
    'charset' => 'utf8mb4',
];
