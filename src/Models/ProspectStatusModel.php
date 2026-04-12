<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class ProspectStatusModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function all(): array
    {
        $stmt = $this->db->prepare('SELECT * FROM prospect_statuses ORDER BY sort_order ASC, id ASC');
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
