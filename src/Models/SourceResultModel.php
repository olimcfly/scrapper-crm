<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class SourceResultModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function bulkInsert(int $runId, string $source, array $results): void
    {
        if ($results === []) {
            return;
        }

        $stmt = $this->db->prepare('INSERT INTO source_results (search_run_id, source, normalized_payload_json, created_at)
            VALUES (:search_run_id, :source, :payload, NOW())');

        foreach ($results as $result) {
            $stmt->execute([
                'search_run_id' => $runId,
                'source' => $source,
                'payload' => json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ]);
        }
    }

    public function countByRun(int $runId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) AS c FROM source_results WHERE search_run_id = :id');
        $stmt->execute(['id' => $runId]);
        $row = $stmt->fetch();

        return (int) ($row['c'] ?? 0);
    }

    public function byRun(int $runId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM source_results WHERE search_run_id = :id ORDER BY id DESC');
        $stmt->execute(['id' => $runId]);

        return $stmt->fetchAll();
    }
}
