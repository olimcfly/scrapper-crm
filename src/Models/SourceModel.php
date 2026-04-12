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
        $stmt = $this->db->prepare('SELECT * FROM sources ORDER BY name ASC');
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
