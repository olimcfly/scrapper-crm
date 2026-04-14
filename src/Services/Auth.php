<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Session;
use App\Models\UserModel;
use PDO;

final class Auth
{
    private const SESSION_KEY = 'auth_user';
    private const LAST_ACTIVITY_KEY = 'auth_last_activity';
    private const SHOW_WELCOME_POPUP_KEY = 'show_welcome_popup';

    private UserModel $users;
    private int $idleTimeoutSeconds;

    public function __construct(
        private readonly PDO $pdo,
        private readonly string $usersTable = 'users'
    ) {
        $this->users = new UserModel($this->pdo, $this->usersTable);
        $this->idleTimeoutSeconds = (int) (getenv('AUTH_IDLE_TIMEOUT') ?: 1800);
    }

    public function attempt(string $email, string $password): bool
    {
        Session::start();

        $email = mb_strtolower(trim($email));
        if ($email === '' || $password === '') {
            return false;
        }

        $user = $this->users->findByEmail($email);
        if ($user === null) {
            return false;
        }

        if ((int) ($user['is_active'] ?? 0) !== 1) {
            return false;
        }

        if (!isset($user['password']) || !password_verify($password, (string) $user['password'])) {
            return false;
        }

        Session::regenerate();

        $userId = (int) $user['id'];
        $_SESSION[self::SESSION_KEY] = [
            'id' => $userId,
            'name' => $this->buildDisplayName($user),
            'email' => (string) $user['email'],
        ];
        $_SESSION[self::LAST_ACTIVITY_KEY] = time();
        $_SESSION[self::SHOW_WELCOME_POPUP_KEY] = true;

        $this->users->updateLastLogin($userId);

        return true;
    }

    public function check(): bool
    {
        Session::start();

        $sessionUser = $_SESSION[self::SESSION_KEY] ?? null;
        if (!is_array($sessionUser) || !isset($sessionUser['id'])) {
            return false;
        }

        $lastActivity = (int) ($_SESSION[self::LAST_ACTIVITY_KEY] ?? 0);
        if ($lastActivity > 0 && (time() - $lastActivity) > $this->idleTimeoutSeconds) {
            $this->logout();
            return false;
        }

        $user = $this->users->findById((int) $sessionUser['id']);
        if ($user === null || (int) ($user['is_active'] ?? 0) !== 1) {
            $this->logout();
            return false;
        }

        $_SESSION[self::SESSION_KEY]['name'] = $this->buildDisplayName($user);
        $_SESSION[self::SESSION_KEY]['email'] = (string) ($user['email'] ?? '');
        $_SESSION[self::LAST_ACTIVITY_KEY] = time();

        return true;
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

    /**
     * Creates a session for a user identified by email (used after OTP verification).
     */
    public function loginByEmail(string $email): bool
    {
        $user = $this->users->findByEmail($email);
        if ($user === null || (int) ($user['is_active'] ?? 0) !== 1) {
            return false;
        }

        Session::start();
        Session::regenerate();

        $userId = (int) $user['id'];
        $_SESSION[self::SESSION_KEY] = [
            'id'    => $userId,
            'name'  => $this->buildDisplayName($user),
            'email' => (string) $user['email'],
        ];
        $_SESSION[self::LAST_ACTIVITY_KEY] = time();
        $_SESSION[self::SHOW_WELCOME_POPUP_KEY] = true;

        $this->users->updateLastLogin($userId);

        return true;
    }

    /**
     * Returns the user array if the email exists and is active, null otherwise.
     * Used to check authorised users before generating an OTP (without revealing existence).
     */
    public function findUserByEmail(string $email): ?array
    {
        $user = $this->users->findByEmail($email);
        if ($user === null || (int) ($user['is_active'] ?? 0) !== 1) {
            return null;
        }

        return $user;
    }

    public function logout(): void
    {
        Session::start();

        unset($_SESSION[self::SESSION_KEY], $_SESSION[self::LAST_ACTIVITY_KEY]);
        Session::destroy();
    }

    private function buildDisplayName(array $user): string
    {
        $firstName = trim((string) ($user['first_name'] ?? ''));
        $lastName = trim((string) ($user['last_name'] ?? ''));
        $fullName = trim($firstName . ' ' . $lastName);

        if ($fullName !== '') {
            return $fullName;
        }

        $email = (string) ($user['email'] ?? '');
        $localPart = explode('@', $email)[0] ?? '';
        $normalized = trim((string) preg_replace('/[._-]+/', ' ', $localPart));

        if ($normalized === '') {
            return 'Utilisateur';
        }

        return mb_convert_case($normalized, MB_CASE_TITLE, 'UTF-8');
    }
}
