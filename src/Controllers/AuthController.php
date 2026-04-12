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
use App\Services\Mailer;
use App\Services\OtpService;

final class AuthController
{
    private const MAX_ATTEMPTS   = 5;
    private const DECAY_SECONDS  = 300;

    private Auth             $auth;
    private LoginRateLimiter $rateLimiter;
    private OtpService       $otp;

    public function __construct()
    {
        $this->auth        = new Auth(Database::connection());
        $this->rateLimiter = new LoginRateLimiter();
        $this->otp         = new OtpService();
    }

    // ── Step 1 : show e-mail form ────────────────────────────────────────────

    public function showLogin(Request $request): void
    {
        unset($request);

        if ($this->auth->check()) {
            Response::redirect('/prospects');
        }

        View::render('auth/login', [
            'title'     => 'Connexion',
            'errors'    => [],
            'old'       => ['email' => ''],
            'csrfToken' => Csrf::refresh(),
        ]);
    }

    // ── Step 1 : process e-mail, generate OTP, send mail ────────────────────

    public function login(Request $request): void
    {
        $input          = $request->input();
        $email          = mb_strtolower(trim((string) ($input['email'] ?? '')));
        $submittedToken = (string) ($input['_csrf'] ?? '');

        $throttleKey = $this->throttleKey($email);
        if ($this->rateLimiter->tooManyAttempts($throttleKey, self::MAX_ATTEMPTS, self::DECAY_SECONDS)) {
            $seconds = $this->rateLimiter->availableIn($throttleKey, self::DECAY_SECONDS);
            View::render('auth/login', [
                'title'     => 'Connexion',
                'errors'    => ['Trop de tentatives. Réessayez dans ' . $seconds . ' secondes.'],
                'old'       => ['email' => $email],
                'csrfToken' => Csrf::refresh(),
            ]);
            return;
        }

        $errors = [];

        if (!Csrf::verify($submittedToken)) {
            $errors[] = 'Session expirée. Veuillez réessayer.';
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = 'Adresse email invalide.';
        }

        if ($errors !== []) {
            View::render('auth/login', [
                'title'     => 'Connexion',
                'errors'    => $errors,
                'old'       => ['email' => $email],
                'csrfToken' => Csrf::refresh(),
            ]);
            return;
        }

        // Generate and send OTP only if the user exists (silently skip otherwise
        // to avoid enumerating valid e-mails).
        $user = $this->auth->findUserByEmail($email);
        if ($user !== null) {
            $code = $this->otp->generate($email);
            Mailer::sendOtp($email, $code);
        }

        // Always redirect to the verify page — do not reveal whether the e-mail
        // is registered.
        $_SESSION['otp_pending_email'] = $email;
        $this->rateLimiter->hit($throttleKey, self::DECAY_SECONDS);

        Response::redirect('/login/verify');
    }

    // ── Step 2 : show code entry form ───────────────────────────────────────

    public function showVerify(Request $request): void
    {
        unset($request);

        $email = (string) ($_SESSION['otp_pending_email'] ?? '');
        if ($email === '') {
            Response::redirect('/login');
            return;
        }

        View::render('auth/verify', [
            'title'     => 'Vérification',
            'email'     => $email,
            'errors'    => [],
            'csrfToken' => Csrf::refresh(),
        ]);
    }

    // ── Step 2 : verify code, open session ──────────────────────────────────

    public function verify(Request $request): void
    {
        $input          = $request->input();
        $submittedToken = (string) ($input['_csrf'] ?? '');
        $code           = trim((string) ($input['code'] ?? ''));

        $email = (string) ($_SESSION['otp_pending_email'] ?? '');
        if ($email === '') {
            Response::redirect('/login');
            return;
        }

        if (!Csrf::verify($submittedToken)) {
            View::render('auth/verify', [
                'title'     => 'Vérification',
                'email'     => $email,
                'errors'    => ['Session expirée. Veuillez réessayer.'],
                'csrfToken' => Csrf::refresh(),
            ]);
            return;
        }

        $throttleKey = 'otp|' . $this->throttleKey($email);
        if ($this->rateLimiter->tooManyAttempts($throttleKey, 5, 300)) {
            $seconds = $this->rateLimiter->availableIn($throttleKey, 300);
            View::render('auth/verify', [
                'title'     => 'Vérification',
                'email'     => $email,
                'errors'    => ['Trop de tentatives. Réessayez dans ' . $seconds . ' secondes.'],
                'csrfToken' => Csrf::refresh(),
            ]);
            return;
        }

        if (!preg_match('/^\d{6}$/', $code)) {
            $this->rateLimiter->hit($throttleKey, 300);
            View::render('auth/verify', [
                'title'     => 'Vérification',
                'email'     => $email,
                'errors'    => ['Le code doit être composé de 6 chiffres.'],
                'csrfToken' => Csrf::refresh(),
            ]);
            return;
        }

        if (!$this->otp->verify($email, $code)) {
            $this->rateLimiter->hit($throttleKey, 300);
            View::render('auth/verify', [
                'title'     => 'Vérification',
                'email'     => $email,
                'errors'    => ['Code incorrect ou expiré. Veuillez recommencer depuis la page de connexion.'],
                'csrfToken' => Csrf::refresh(),
            ]);
            return;
        }

        // Code valid — clear rate limiter, destroy OTP state, open session.
        $this->rateLimiter->clear($throttleKey);
        unset($_SESSION['otp_pending_email']);

        if (!$this->auth->loginByEmail($email)) {
            Response::redirect('/login');
            return;
        }

        Response::redirect('/prospects');
    }

    // ── Logout ───────────────────────────────────────────────────────────────

    public function logout(Request $request): void
    {
        $input = $request->input();

        if (!Csrf::verify((string) ($input['_csrf'] ?? ''))) {
            Response::redirect('/login');
        }

        $this->auth->logout();
        Response::redirect('/login');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function throttleKey(string $email): string
    {
        $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');

        return hash('sha256', mb_strtolower(trim($email)) . '|' . $ip);
    }
}
