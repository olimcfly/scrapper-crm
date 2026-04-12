<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

final class PipelineStageModel
{
    private PDO $db;
    private ?bool $isTableAvailable = null;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function all(): array
    {
        if (!$this->isTableAvailable()) {
            return [];
        }

        $stmt = $this->db->query('SELECT id, name, position FROM pipeline_stages ORDER BY position ASC');
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        if (!$this->isTableAvailable()) {
            return null;
        }

        $stmt = $this->db->prepare('SELECT id, name, position FROM pipeline_stages WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $stage = $stmt->fetch();

        return $stage ?: null;
    }

    public function isTableAvailable(): bool
    {
        if ($this->isTableAvailable !== null) {
            return $this->isTableAvailable;
        }

        try {
            $stmt = $this->db->query('SHOW TABLES LIKE \'pipeline_stages\'');
            $this->isTableAvailable = (bool) $stmt->fetchColumn();
        } catch (PDOException) {
            $this->isTableAvailable = false;
        }

        return $this->isTableAvailable;
    }
}
