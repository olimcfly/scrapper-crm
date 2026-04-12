<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class PipelineStageModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function all(): array
    {
        $stmt = $this->db->query('SELECT id, name, position FROM pipeline_stages ORDER BY position ASC');
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, name, position FROM pipeline_stages WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $stage = $stmt->fetch();

        return $stage ?: null;
    }
}
