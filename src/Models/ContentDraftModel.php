<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class ContentDraftModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO content_generation_drafts
            (user_id, analysis_id, content_type, channel, objective, tone, generated_content, variant_label, created_at)
            VALUES
            (:user_id, :analysis_id, :content_type, :channel, :objective, :tone, :generated_content, :variant_label, NOW())');

        $stmt->execute([
            'user_id' => $data['user_id'],
            'analysis_id' => $data['analysis_id'],
            'content_type' => $data['content_type'],
            'channel' => $data['channel'],
            'objective' => $data['objective'],
            'tone' => $data['tone'],
            'generated_content' => $data['generated_content'],
            'variant_label' => $data['variant_label'] ?? 'Variante 1',
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function latestByUser(int $userId, int $limit = 20): array
    {
        $stmt = $this->db->prepare('SELECT d.*, a.summary AS analysis_summary, a.awareness_level
            FROM content_generation_drafts d
            INNER JOIN strategy_profile_analyses a ON a.id = d.analysis_id
            WHERE d.user_id = :user_id
            ORDER BY d.created_at DESC, d.id DESC
            LIMIT :limit');
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findByIdForUser(int $id, int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM content_generation_drafts WHERE id = :id AND user_id = :user_id LIMIT 1');
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }
}
