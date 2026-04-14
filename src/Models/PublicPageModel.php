<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class PublicPageModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO public_pages (user_id, type, title, subtitle, slug, status, body_html, source_snapshot_json, created_at, updated_at)
            VALUES (:user_id, :type, :title, :subtitle, :slug, :status, :body_html, :source_snapshot_json, NOW(), NOW())');
        $stmt->execute([
            'user_id' => $data['user_id'],
            'type' => $data['type'],
            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?? '',
            'slug' => $data['slug'],
            'status' => $data['status'] ?? 'draft',
            'body_html' => $data['body_html'] ?? '',
            'source_snapshot_json' => json_encode($data['snapshot'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function updateForUser(int $id, int $userId, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE public_pages SET type = :type, title = :title, subtitle = :subtitle, slug = :slug, status = :status, body_html = :body_html, source_snapshot_json = :source_snapshot_json, updated_at = NOW(), published_at = CASE WHEN :status = "published" THEN IFNULL(published_at, NOW()) ELSE NULL END WHERE id = :id AND user_id = :user_id');
        $stmt->execute([
            'id' => $id,
            'user_id' => $userId,
            'type' => $data['type'],
            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?? '',
            'slug' => $data['slug'],
            'status' => $data['status'],
            'body_html' => $data['body_html'] ?? '',
            'source_snapshot_json' => json_encode($data['snapshot'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);
    }

    public function latestByUser(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM public_pages WHERE user_id = :user_id ORDER BY updated_at DESC, id DESC');
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }

    public function findByIdForUser(int $id, int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM public_pages WHERE id = :id AND user_id = :user_id LIMIT 1');
        $stmt->execute(['id' => $id, 'user_id' => $userId]);

        return $stmt->fetch() ?: null;
    }

    public function findPublishedBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM public_pages WHERE slug = :slug AND status = "published" LIMIT 1');
        $stmt->execute(['slug' => $slug]);

        return $stmt->fetch() ?: null;
    }

    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql = 'SELECT id FROM public_pages WHERE slug = :slug';
        $params = ['slug' => $slug];
        if ($excludeId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $excludeId;
        }
        $sql .= ' LIMIT 1';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (bool) $stmt->fetch();
    }
}
