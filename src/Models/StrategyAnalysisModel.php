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

    public function create(int $userId, string $profile, array $analysis, array $selection = []): int
    {
        $stmt = $this->db->prepare('INSERT INTO strategy_profile_analyses
            (user_id, profile_text, objective, persona_group, persona_subtype, offer_type, maturity_level, contact_intention, awareness_level, summary, pain_points_json, desires_json, content_angles_json, hooks_json, created_at)
            VALUES
            (:user_id, :profile_text, :objective, :persona_group, :persona_subtype, :offer_type, :maturity_level, :contact_intention, :awareness_level, :summary, :pain_points_json, :desires_json, :content_angles_json, :hooks_json, NOW())');

        $stmt->execute([
            'user_id' => $userId,
            'profile_text' => $profile,
            'objective' => (string) ($selection['objective'] ?? ''),
            'persona_group' => (string) ($selection['persona_group'] ?? ''),
            'persona_subtype' => (string) ($selection['persona_subtype'] ?? ''),
            'offer_type' => (string) ($selection['offer_type'] ?? ''),
            'maturity_level' => (string) ($selection['maturity_level'] ?? ''),
            'contact_intention' => (string) ($selection['contact_intention'] ?? ''),
            'awareness_level' => (string) ($analysis['awareness_level'] ?? ''),
            'summary' => (string) ($analysis['summary'] ?? ''),
            'pain_points_json' => json_encode($analysis['pain_points'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'desires_json' => json_encode($analysis['desires'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'content_angles_json' => json_encode($analysis['content_angles'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'hooks_json' => json_encode($analysis['recommended_hooks'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function latestByUser(int $userId, int $limit = 20): array
    {
        $stmt = $this->db->prepare('SELECT * FROM strategy_profile_analyses WHERE user_id = :user_id ORDER BY created_at DESC, id DESC LIMIT :limit');
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findByIdForUser(int $id, int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM strategy_profile_analyses WHERE id = :id AND user_id = :user_id LIMIT 1');
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }
}
