<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class GeneratedContentModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO generated_contents (
                    prospect_id, type, content, hook, angle, awareness_level, created_at
                ) VALUES (
                    :prospect_id, :type, :content, :hook, :angle, :awareness_level, NOW()
                )';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id, int $prospectId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM generated_contents WHERE id = :id AND prospect_id = :prospect_id LIMIT 1');
        $stmt->execute(['id' => $id, 'prospect_id' => $prospectId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function latestByProspectAndType(int $prospectId, string $type): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM generated_contents WHERE prospect_id = :prospect_id AND type = :type ORDER BY created_at DESC, id DESC LIMIT 1');
        $stmt->execute(['prospect_id' => $prospectId, 'type' => $type]);
        $row = $stmt->fetch();

        return $row ?: null;
    }
}
