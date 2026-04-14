<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class SearchRunModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function create(int $userId, string $source, string $searchType, array $filters): int
    {
        $stmt = $this->db->prepare('INSERT INTO search_runs (user_id, source, search_type, status, filters_json, started_at, created_at, updated_at)
            VALUES (:user_id, :source, :search_type, :status, :filters_json, NOW(), NOW(), NOW())');
        $stmt->execute([
            'user_id' => $userId,
            'source' => $source,
            'search_type' => $searchType,
            'status' => 'running',
            'filters_json' => json_encode($filters, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function finish(int $runId, string $status, int $resultsCount, ?string $errorMessage = null): void
    {
        $stmt = $this->db->prepare('UPDATE search_runs
            SET status = :status, results_count = :results_count, error_message = :error_message, ended_at = NOW(), updated_at = NOW()
            WHERE id = :id');
        $stmt->execute([
            'id' => $runId,
            'status' => $status,
            'results_count' => $resultsCount,
            'error_message' => $errorMessage,
        ]);
    }

    public function latestByUser(int $userId, int $limit = 10): array
    {
        $stmt = $this->db->prepare('SELECT * FROM search_runs WHERE user_id = :user_id ORDER BY id DESC LIMIT :limit');
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findByUser(int $userId, int $runId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM search_runs WHERE user_id = :user_id AND id = :id LIMIT 1');
        $stmt->execute([
            'user_id' => $userId,
            'id' => $runId,
        ]);

        $run = $stmt->fetch();
        return is_array($run) ? $run : null;
    }
}
