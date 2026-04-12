<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class TagModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function all(): array
    {
        $stmt = $this->db->prepare('SELECT * FROM tags ORDER BY name ASC');
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function syncProspectTags(int $prospectId, array $tagIds): void
    {
        $this->db->beginTransaction();

        try {
            $delete = $this->db->prepare('DELETE FROM prospect_tag WHERE prospect_id = :prospect_id');
            $delete->execute(['prospect_id' => $prospectId]);

            if ($tagIds !== []) {
                $insert = $this->db->prepare('INSERT INTO prospect_tag (prospect_id, tag_id) VALUES (:prospect_id, :tag_id)');
                foreach ($tagIds as $tagId) {
                    $insert->execute(['prospect_id' => $prospectId, 'tag_id' => (int) $tagId]);
                }
            }

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
