<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class ProspectModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function all(): array
    {
        $sql = 'SELECT p.*, s.name AS status_name, so.name AS source_name
                FROM prospects p
                LEFT JOIN prospect_statuses s ON s.id = p.status_id
                LEFT JOIN sources so ON so.id = p.source_id
                ORDER BY p.updated_at DESC';

        return $this->db->query($sql)->fetchAll();
    }

    public function listPaginated(string $search, string $sort, int $limit, int $offset): array
    {
        $sortMap = [
            'name' => "COALESCE(NULLIF(p.full_name, ''), CONCAT_WS(' ', p.first_name, p.last_name)) ASC",
            'score' => 'p.score DESC',
            'city' => 'p.city ASC',
            'date' => 'p.created_at DESC',
        ];
        $orderBy = $sortMap[$sort] ?? $sortMap['date'];

        $where = '';
        $params = [];
        if ($search !== '') {
            $where = 'WHERE (
                p.first_name LIKE :search OR
                p.last_name LIKE :search OR
                p.full_name LIKE :search OR
                p.activity LIKE :search OR
                p.city LIKE :search OR
                p.professional_email LIKE :search
            )';
            $params['search'] = '%' . $search . '%';
        }

        $countSql = "SELECT COUNT(*) FROM prospects p {$where}";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "SELECT p.*, s.name AS status_name, so.name AS source_name
                FROM prospects p
                LEFT JOIN prospect_statuses s ON s.id = p.status_id
                LEFT JOIN sources so ON so.id = p.source_id
                {$where}
                ORDER BY {$orderBy}
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        if (isset($params['search'])) {
            $stmt->bindValue(':search', $params['search']);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items' => $stmt->fetchAll(),
            'total' => $total,
        ];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM prospects WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $prospect = $stmt->fetch();

        return $prospect ?: null;
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO prospects (
                    first_name, last_name, full_name, business_name, activity, city, country, website,
                    professional_email, professional_phone, instagram_url, facebook_url, linkedin_url, tiktok_url,
                    source_id, status_id, score, notes_summary, created_at, updated_at
                ) VALUES (
                    :first_name, :last_name, :full_name, :business_name, :activity, :city, :country, :website,
                    :professional_email, :professional_phone, :instagram_url, :facebook_url, :linkedin_url, :tiktok_url,
                    :source_id, :status_id, :score, :notes_summary, NOW(), NOW()
                )';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE prospects SET
                    first_name = :first_name,
                    last_name = :last_name,
                    full_name = :full_name,
                    business_name = :business_name,
                    activity = :activity,
                    city = :city,
                    country = :country,
                    website = :website,
                    professional_email = :professional_email,
                    professional_phone = :professional_phone,
                    instagram_url = :instagram_url,
                    facebook_url = :facebook_url,
                    linkedin_url = :linkedin_url,
                    tiktok_url = :tiktok_url,
                    source_id = :source_id,
                    status_id = :status_id,
                    score = :score,
                    notes_summary = :notes_summary,
                    updated_at = NOW()
                WHERE id = :id';

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data + ['id' => $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM prospects WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function updateStatus(int $id, int $statusId): bool
    {
        $stmt = $this->db->prepare('UPDATE prospects SET status_id = :status_id, updated_at = NOW() WHERE id = :id');
        return $stmt->execute(['id' => $id, 'status_id' => $statusId]);
    }
}
