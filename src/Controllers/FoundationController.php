<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Models\StrategicFoundationModel;
use App\Services\Auth;
use App\Services\StrategicFoundationContextService;

final class FoundationController
{
    private Auth $auth;
    private StrategicFoundationModel $model;
    private StrategicFoundationContextService $context;

    public function __construct()
    {
        $this->auth = new Auth(Database::connection());
        $this->model = new StrategicFoundationModel();
        $this->context = new StrategicFoundationContextService();
    }

    public function index(Request $request): void
    {
        unset($request);
        $user = $this->auth->user();
        if (!is_array($user) || !isset($user['id'])) {
            Response::redirect('/login');
            return;
        }

        $foundation = $this->model->findByUserId((int) $user['id']) ?? [];

        View::render('foundation/index', [
            'title' => 'Fondation stratégique',
            'foundation' => $foundation,
            'completion' => $this->context->completionStats($foundation),
            'successMessage' => Session::consumeFlash('success'),
            'warningMessage' => Session::consumeFlash('warning'),
        ]);
    }

    public function save(Request $request): void
    {
        $input = $request->input();
        if (!Csrf::verify((string) ($input['_csrf'] ?? ''))) {
            Session::flash('warning', 'Session expirée. Rechargez la page.');
            Response::redirect('/fondation-strategique');
            return;
        }

        $user = $this->auth->user();
        if (!is_array($user) || !isset($user['id'])) {
            Response::redirect('/login');
            return;
        }

        $this->model->upsertForUser((int) $user['id'], $input);
        Session::flash('success', 'Fondation stratégique enregistrée.');
        Response::redirect('/fondation-strategique');
    }
}
