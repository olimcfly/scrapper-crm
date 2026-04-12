<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class SourceModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM sources ORDER BY name ASC')->fetchAll();
    }
}
