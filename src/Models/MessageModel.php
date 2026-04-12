<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

final class MessageModel
{
    private PDO $db;
    private ?bool $isTableAvailable = null;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function byProspect(int $prospectId): array
    {
        if (!$this->isTableAvailable()) {
            return [];
        }

        $stmt = $this->db->prepare('SELECT id, prospect_id, content, type, direction, created_at
                                    FROM messages
                                    WHERE prospect_id = :prospect_id
                                    ORDER BY created_at DESC');
        $stmt->execute(['prospect_id' => $prospectId]);

        return $stmt->fetchAll();
    }

    public function create(int $prospectId, string $content, string $type, string $direction): int
    {
        if (!$this->isTableAvailable()) {
            return 0;
        }

        $stmt = $this->db->prepare('INSERT INTO messages (prospect_id, content, type, direction, created_at)
                                    VALUES (:prospect_id, :content, :type, :direction, NOW())');
        $stmt->execute([
            'prospect_id' => $prospectId,
            'content' => $content,
            'type' => $type,
            'direction' => $direction,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function isTableAvailable(): bool
    {
        if ($this->isTableAvailable !== null) {
            return $this->isTableAvailable;
        }

        try {
            $stmt = $this->db->query('SHOW TABLES LIKE \'messages\'');
            $this->isTableAvailable = (bool) $stmt->fetchColumn();
        } catch (PDOException) {
            $this->isTableAvailable = false;
        }

        return $this->isTableAvailable;
    }
}
