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

    public function search(array $filters, int $page = 1, int $perPage = 15): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        $offset = ($page - 1) * $perPage;

        $where = [];
        $params = [];

        $query = trim((string) ($filters['q'] ?? ''));
        if ($query !== '') {
            $where[] = '(p.full_name LIKE :q OR p.business_name LIKE :q OR p.activity LIKE :q OR p.city LIKE :q OR p.professional_email LIKE :q)';
            $params['q'] = '%' . $query . '%';
        }

        $statusId = (int) ($filters['status_id'] ?? 0);
        if ($statusId > 0) {
            $where[] = 'p.status_id = :status_id';
            $params['status_id'] = $statusId;
        }

        $sourceId = (int) ($filters['source_id'] ?? 0);
        if ($sourceId > 0) {
            $where[] = 'p.source_id = :source_id';
            $params['source_id'] = $sourceId;
        }

        $whereClause = $where === [] ? '' : ' WHERE ' . implode(' AND ', $where);

        $countStmt = $this->db->prepare('SELECT COUNT(*) FROM prospects p' . $whereClause);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = 'SELECT p.*, s.name AS status_name, so.name AS source_name
                FROM prospects p
                LEFT JOIN prospect_statuses s ON s.id = p.status_id
                LEFT JOIN sources so ON so.id = p.source_id' . $whereClause . '
                ORDER BY p.updated_at DESC
                LIMIT :limit OFFSET :offset';
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items' => $stmt->fetchAll(),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => max(1, (int) ceil($total / $perPage)),
        ];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT p.*, s.name AS status_name, so.name AS source_name
                                    FROM prospects p
                                    LEFT JOIN prospect_statuses s ON s.id = p.status_id
                                    LEFT JOIN sources so ON so.id = p.source_id
                                    WHERE p.id = :id
                                    LIMIT 1');
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
