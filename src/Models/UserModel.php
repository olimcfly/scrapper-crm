<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

final class UserModel
{
    public function __construct(
        private readonly PDO $db,
        private readonly string $table = 'users'
    ) {
    }

    public function findByEmail(string $email): ?array
    {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $this->table)) {
            return null;
        }

        $sql = sprintf(
            'SELECT id, first_name, last_name, email, password, is_active FROM `%s` WHERE email = :email LIMIT 1',
            $this->table
        );

        $statement = $this->db->prepare($sql);
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();

        return $user !== false ? $user : null;
    }


    public function findById(int $id): ?array
    {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $this->table)) {
            return null;
        }

        $sql = sprintf(
            'SELECT id, first_name, last_name, email, is_active FROM `%s` WHERE id = :id LIMIT 1',
            $this->table
        );

        $statement = $this->db->prepare($sql);
        $statement->execute(['id' => $id]);
        $user = $statement->fetch();

        return $user !== false ? $user : null;
    }

    public function updateLastLogin(int $id): void
    {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $this->table)) {
            return;
        }

        $sql = sprintf('UPDATE `%s` SET last_login_at = NOW() WHERE id = :id', $this->table);
        $statement = $this->db->prepare($sql);
        $statement->execute(['id' => $id]);
    }
}
