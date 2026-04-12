<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Services\Auth;
use App\Services\Csrf;

final class AuthController
{
    private Auth $auth;

    public function __construct()
    {
        $this->auth = new Auth(Database::connection());
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
        ]);
    }

    public function login(Request $request): void
    {
        $input = $request->input();
        if (!Csrf::isValid((string) ($input['_csrf_token'] ?? ''))) {
            View::render('auth/login', [
                'title' => 'Connexion',
                'errors' => ['Session expirée, veuillez réessayer.'],
                'old' => ['email' => ''],
            ]);
            return;
        }

        $email = trim((string) ($input['email'] ?? ''));
        $password = (string) ($input['password'] ?? '');

        $errors = [];

        if ($email === '') {
            $errors[] = 'L\'email est obligatoire.';
        } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = 'Le format de l\'email est invalide.';
        }

        if ($password === '') {
            $errors[] = 'Le mot de passe est obligatoire.';
        }

        if ($errors === [] && !$this->auth->attempt($email, $password)) {
            $errors[] = 'Identifiants invalides. Vérifiez votre email et votre mot de passe.';
        }

        if ($errors !== []) {
            View::render('auth/login', [
                'title' => 'Connexion',
                'errors' => $errors,
                'old' => ['email' => $email],
            ]);
            return;
        }

        Response::redirect('/prospects');
    }

    public function logout(Request $request): void
    {
        $input = $request->input();
        if (!Csrf::isValid((string) ($input['_csrf_token'] ?? ''))) {
            Response::redirect('/prospects');
        }

        $this->auth->logout();
        Response::redirect('/login');
    }
}
