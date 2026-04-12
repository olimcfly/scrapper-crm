<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\LoginTokenModel;

final class OtpService
{
    private LoginTokenModel $tokens;

    public function __construct()
    {
        $this->tokens = new LoginTokenModel();
    }

    /**
     * Generates a 6-digit OTP, stores its hash in DB, returns the plaintext code.
     */
    public function generate(string $email): string
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $hash = hash('sha256', $code);
        $this->tokens->create($email, $hash, 900); // 15 minutes

        return $code;
    }

    /**
     * Verifies a submitted code against the stored hash.
     * Returns true and marks the token as used on success.
     * Increments attempt counter on failure.
     */
    public function verify(string $email, string $code): bool
    {
        $token = $this->tokens->findValid($email);
        if ($token === null) {
            return false;
        }

        $hash = hash('sha256', $code);
        if (!hash_equals((string) $token['token_hash'], $hash)) {
            $this->tokens->incrementAttempts((int) $token['id']);
            return false;
        }

        $this->tokens->markUsed((int) $token['id']);
        return true;
    }
}
