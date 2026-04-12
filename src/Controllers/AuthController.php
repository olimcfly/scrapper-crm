<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Database;
use App\Core\LoginRateLimiter;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Services\Auth;

final class AuthController
{
    private const MAX_ATTEMPTS = 5;
    private const DECAY_SECONDS = 300;

    private Auth $auth;
    private LoginRateLimiter $rateLimiter;

    public function __construct()
    {
        $this->auth = new Auth(Database::connection());
        $this->rateLimiter = new LoginRateLimiter();
    }

    public function showLogin(Request $request): void
    {
        unset($request);

        if ($this->auth->check()) {
            Response::redirect('/prospects');
        }

        View::render('auth/login', [
            'title' => 'Connexion',
            'errors' => [],
            'old' => ['email' => ''],
            'csrfToken' => Csrf::refresh(),
        ]);
    }

    public function login(Request $request): void
    {
        $input = $request->input();
        $email = trim((string) ($input['email'] ?? ''));
        $password = (string) ($input['password'] ?? '');
        $submittedToken = (string) ($input['_csrf'] ?? '');

        $throttleKey = $this->throttleKey($email);
        if ($this->rateLimiter->tooManyAttempts($throttleKey, self::MAX_ATTEMPTS, self::DECAY_SECONDS)) {
            $seconds = $this->rateLimiter->availableIn($throttleKey, self::DECAY_SECONDS);
            View::render('auth/login', [
                'title' => 'Connexion',
                'errors' => ['Trop de tentatives. Réessayez dans ' . $seconds . ' secondes.'],
                'old' => ['email' => $email],
                'csrfToken' => Csrf::refresh(),
            ]);
            return;
        }

        $errors = [];

        if (!Csrf::verify($submittedToken)) {
            $errors[] = 'Session expirée. Veuillez réessayer.';
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false || mb_strlen($password) < 8) {
            $errors[] = 'Email ou mot de passe invalide.';
        }

        if ($errors === [] && !$this->auth->attempt($email, $password)) {
            $errors[] = 'Email ou mot de passe invalide.';
        }

        if ($errors !== []) {
            $this->rateLimiter->hit($throttleKey, self::DECAY_SECONDS);

            View::render('auth/login', [
                'title' => 'Connexion',
                'errors' => array_values(array_unique($errors)),
                'old' => ['email' => $email],
                'csrfToken' => Csrf::refresh(),
            ]);
            return;
        }

        $this->rateLimiter->clear($throttleKey);
        Response::redirect('/prospects');
    }

    public function logout(Request $request): void
    {
        $input = $request->input();

        if (!Csrf::verify((string) ($input['_csrf'] ?? ''))) {
            Response::redirect('/login');
        }

        $this->auth->logout();
        Response::redirect('/login');
    }

    private function throttleKey(string $email): string
    {
        $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');

        return hash('sha256', mb_strtolower(trim($email)) . '|' . $ip);
    }
}
