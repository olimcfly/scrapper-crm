<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Models\ProspectStatusModel;
use App\Models\SourceModel;
use App\Models\TagModel;
use App\Services\Auth;

final class LookupController
{
    private ProspectStatusModel $statuses;
    private SourceModel $sources;
    private TagModel $tags;
    private Auth $auth;

    public function __construct()
    {
        $this->statuses = new ProspectStatusModel();
        $this->sources = new SourceModel();
        $this->tags = new TagModel();
        $this->auth = new Auth(Database::connection());
    }

    public function statuses(Request $request): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        unset($request);
        Response::json(['data' => $this->statuses->all()]);
    }

    public function sources(Request $request): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        unset($request);
        Response::json(['data' => $this->sources->all()]);
    }

    public function tags(Request $request): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        unset($request);
        Response::json(['data' => $this->tags->all()]);
    }

    private function requireAuth(): bool
    {
        if ($this->auth->check()) {
            return true;
        }

        Response::json(['error' => 'Authentification requise.'], 401);
        return false;
    }
}
