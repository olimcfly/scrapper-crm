<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class LoginTokenModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function create(string $email, string $tokenHash, int $ttlSeconds = 900): void
    {
        $this->deleteByEmail($email);

        $stmt = $this->db->prepare(
            'INSERT INTO login_tokens (email, token_hash, expires_at)
             VALUES (:email, :hash, DATE_ADD(NOW(), INTERVAL :ttl SECOND))'
        );
        $stmt->execute(['email' => $email, 'hash' => $tokenHash, 'ttl' => $ttlSeconds]);
    }

    public function findValid(string $email): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, token_hash, attempts FROM login_tokens
             WHERE email = :email
               AND expires_at > NOW()
               AND used_at IS NULL
               AND attempts < 5
             ORDER BY created_at DESC
             LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();

        return $row !== false ? $row : null;
    }

    public function incrementAttempts(int $id): void
    {
        $stmt = $this->db->prepare(
            'UPDATE login_tokens SET attempts = attempts + 1 WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
    }

    public function markUsed(int $id): void
    {
        $stmt = $this->db->prepare(
            'UPDATE login_tokens SET used_at = NOW() WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
    }

    public function deleteByEmail(string $email): void
    {
        $stmt = $this->db->prepare('DELETE FROM login_tokens WHERE email = :email');
        $stmt->execute(['email' => $email]);
    }
}
