<?php

declare(strict_types=1);

namespace App\Core;

final class LoginRateLimiter
{
    private const SESSION_KEY = '_login_attempts';

    public function tooManyAttempts(string $key, int $maxAttempts, int $decaySeconds): bool
    {
        Session::start();
        $this->cleanup($key, $decaySeconds);

        $attempts = $_SESSION[self::SESSION_KEY][$key]['attempts'] ?? 0;

        return (int) $attempts >= $maxAttempts;
    }

    public function hit(string $key, int $decaySeconds): void
    {
        Session::start();
        $this->cleanup($key, $decaySeconds);

        if (!isset($_SESSION[self::SESSION_KEY][$key])) {
            $_SESSION[self::SESSION_KEY][$key] = [
                'attempts' => 0,
                'first_attempt_at' => time(),
            ];
        }

        $_SESSION[self::SESSION_KEY][$key]['attempts']++;
    }

    public function clear(string $key): void
    {
        Session::start();
        unset($_SESSION[self::SESSION_KEY][$key]);
    }

    public function availableIn(string $key, int $decaySeconds): int
    {
        Session::start();

        $firstAttemptAt = (int) ($_SESSION[self::SESSION_KEY][$key]['first_attempt_at'] ?? 0);
        if ($firstAttemptAt <= 0) {
            return 0;
        }

        $remaining = ($firstAttemptAt + $decaySeconds) - time();

        return max(0, $remaining);
    }

    private function cleanup(string $key, int $decaySeconds): void
    {
        Session::start();

        $firstAttemptAt = (int) ($_SESSION[self::SESSION_KEY][$key]['first_attempt_at'] ?? 0);
        if ($firstAttemptAt <= 0) {
            return;
        }

        if ((time() - $firstAttemptAt) >= $decaySeconds) {
            unset($_SESSION[self::SESSION_KEY][$key]);
        }
    }
}
