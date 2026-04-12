<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Session;
use PDO;

final class Auth
{
    private const SESSION_KEY = 'auth_user';

    public function __construct(
        private readonly PDO $pdo,
        private readonly string $usersTable = 'users'
    ) {
    }

    public function attempt(string $email, string $password): bool
    {
        Session::start();

        $email = mb_strtolower(trim($email));
        if ($email === '' || $password === '') {
            return false;
        }

        $user = $this->findUserByEmail($email);
        if ($user === null) {
            return false;
        }

        if (!isset($user['password']) || !password_verify($password, (string) $user['password'])) {
            return false;
        }

        Session::regenerate();

        $_SESSION[self::SESSION_KEY] = [
            'id' => (int) $user['id'],
            'email' => (string) $user['email'],
        ];

        return true;
    }

    public function check(): bool
    {
        Session::start();

        return isset($_SESSION[self::SESSION_KEY]['id']);
    }

    public function user(): ?array
    {
        Session::start();

        if (!$this->check()) {
            return null;
        }

        return $_SESSION[self::SESSION_KEY];
    }

    public function id(): ?int
    {
        Session::start();

        if (!$this->check()) {
            return null;
        }

        return (int) $_SESSION[self::SESSION_KEY]['id'];
    }

    public function logout(): void
    {
        Session::start();

        unset($_SESSION[self::SESSION_KEY]);
        Session::regenerate();
    }

    private function findUserByEmail(string $email): ?array
    {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $this->usersTable)) {
            return null;
        }

        $sql = sprintf(
            'SELECT id, email, password FROM `%s` WHERE email = :email LIMIT 1',
            $this->usersTable
        );

        $statement = $this->pdo->prepare($sql);
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();

        return $user !== false ? $user : null;
    }
}
