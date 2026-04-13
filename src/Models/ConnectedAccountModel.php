<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class ConnectedAccountModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function upsert(int $userId, string $source, string $status, ?string $externalAccountId, ?string $errorMessage = null): void
    {
        $sql = 'INSERT INTO connected_accounts (user_id, source, status, external_account_id, error_message, connected_at, updated_at)
                VALUES (:user_id, :source, :status, :external_account_id, :error_message, NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                    status = VALUES(status),
                    external_account_id = VALUES(external_account_id),
                    error_message = VALUES(error_message),
                    connected_at = IF(VALUES(status) = "connected", NOW(), connected_at),
                    updated_at = NOW()';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'source' => $source,
            'status' => $status,
            'external_account_id' => $externalAccountId,
            'error_message' => $errorMessage,
        ]);
    }

    public function byUser(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM connected_accounts WHERE user_id = :user_id ORDER BY updated_at DESC');
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }
}
