<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class ProspectNoteModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function byProspect(int $prospectId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM prospect_notes WHERE prospect_id = :prospect_id ORDER BY created_at DESC');
        $stmt->execute(['prospect_id' => $prospectId]);

        return $stmt->fetchAll();
    }

    public function create(int $prospectId, string $content): int
    {
        $stmt = $this->db->prepare('INSERT INTO prospect_notes (prospect_id, content, created_at) VALUES (:prospect_id, :content, NOW())');
        $stmt->execute(['prospect_id' => $prospectId, 'content' => $content]);

        return (int) $this->db->lastInsertId();
    }
}
