<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class StrategyAnalysisModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function create(int $userId, string $profile, array $analysis): int
    {
        $stmt = $this->db->prepare('INSERT INTO strategy_profile_analyses
            (user_id, profile_text, awareness_level, summary, pain_points_json, desires_json, content_angles_json, hooks_json, created_at)
            VALUES
            (:user_id, :profile_text, :awareness_level, :summary, :pain_points_json, :desires_json, :content_angles_json, :hooks_json, NOW())');

        $stmt->execute([
            'user_id' => $userId,
            'profile_text' => $profile,
            'awareness_level' => (string) ($analysis['awareness_level'] ?? ''),
            'summary' => (string) ($analysis['summary'] ?? ''),
            'pain_points_json' => json_encode($analysis['pain_points'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'desires_json' => json_encode($analysis['desires'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'content_angles_json' => json_encode($analysis['content_angles'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'hooks_json' => json_encode($analysis['recommended_hooks'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);

        return (int) $this->db->lastInsertId();
    }
}
