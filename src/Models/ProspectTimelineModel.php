<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class ProspectTimelineModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function byProspect(int $prospectId): array
    {
        $sql = 'SELECT created_at, event_type, details
                FROM (
                    SELECT pn.created_at, "note" AS event_type, pn.content AS details
                    FROM prospect_notes pn
                    WHERE pn.prospect_id = :prospect_id

                    UNION ALL

                    SELECT pe.created_at, pe.event_type, pe.details
                    FROM prospect_events pe
                    WHERE pe.prospect_id = :prospect_id
                ) timeline
                ORDER BY created_at DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['prospect_id' => $prospectId]);

        return $stmt->fetchAll();
    }

    public function create(int $prospectId, string $eventType, string $details): int
    {
        $stmt = $this->db->prepare('INSERT INTO prospect_events (prospect_id, event_type, details, created_at) VALUES (:prospect_id, :event_type, :details, NOW())');
        $stmt->execute([
            'prospect_id' => $prospectId,
            'event_type' => $eventType,
            'details' => $details,
        ]);

        return (int) $this->db->lastInsertId();
    }
}
