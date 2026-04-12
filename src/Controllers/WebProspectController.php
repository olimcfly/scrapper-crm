<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\ProspectModel;
use App\Models\ProspectNoteModel;
use App\Models\ProspectStatusModel;
use App\Models\SourceModel;
use App\Models\TagModel;
use App\Services\Auth;
use App\Services\Logger;
use App\Services\ProspectValidator;

final class WebProspectController
{
    private ProspectModel $prospects;
    private ProspectNoteModel $notes;
    private ProspectStatusModel $statuses;
    private SourceModel $sources;
    private TagModel $tags;
    private ProspectValidator $validator;
    private Auth $auth;

    public function __construct()
    {
        $this->prospects = new ProspectModel();
        $this->notes = new ProspectNoteModel();
        $this->statuses = new ProspectStatusModel();
        $this->sources = new SourceModel();
        $this->tags = new TagModel();
        $this->validator = new ProspectValidator();
        $this->auth = new Auth(Database::connection());
    }

    public function index(Request $request): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        try {
            unset($request);
            View::render('prospects/list', [
                'title' => 'Prospects',
                'prospects' => $this->prospects->all(),
            ]);
        } catch (\Throwable $e) {
            Logger::error($e->getMessage());
            View::render('errors/not-found', ['title' => 'Erreur serveur']);
        }
    }

    public function create(Request $request): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        unset($request);
        View::render('prospects/form', [
            'title' => 'Nouveau prospect',
            'action' => '/prospects/create',
            'prospect' => [],
            'statuses' => $this->statuses->all(),
            'sources' => $this->sources->all(),
            'errors' => [],
        ]);
    }

    public function store(Request $request): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        $input = $request->input();
        $errors = $this->validator->validate($input);

        if ($errors !== []) {
            View::render('prospects/form', [
                'title' => 'Nouveau prospect',
                'action' => '/prospects/create',
                'prospect' => $input,
                'statuses' => $this->statuses->all(),
                'sources' => $this->sources->all(),
                'errors' => $errors,
            ]);
            return;
        }

        try {
            $payload = $this->validator->normalize($input);
            $id = $this->prospects->create($payload);

            $tagIds = array_values(array_filter(array_map('intval', explode(',', (string) ($input['tag_ids'] ?? ''))), static fn (int $tagId): bool => $tagId > 0));
            if ($tagIds !== []) {
                $this->tags->syncProspectTags($id, $tagIds);
            }

            Response::redirect('/prospects/' . $id);
        } catch (\Throwable $e) {
            Logger::error($e->getMessage());
            View::render('prospects/form', [
                'title' => 'Nouveau prospect',
                'action' => '/prospects/create',
                'prospect' => $input,
                'statuses' => $this->statuses->all(),
                'sources' => $this->sources->all(),
                'errors' => ['Une erreur serveur est survenue.'],
            ]);
        }
    }

    public function show(Request $request, int $id): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        unset($request);
        $prospect = $this->prospects->find($id);
        if ($prospect === null) {
            View::render('errors/not-found', ['title' => 'Introuvable']);
            return;
        }

        View::render('prospects/detail', [
            'title' => 'Fiche prospect',
            'prospect' => $prospect,
            'notes' => $this->notes->byProspect($id),
            'statuses' => $this->statuses->all(),
        ]);
    }

    public function edit(Request $request, int $id): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        unset($request);
        $prospect = $this->prospects->find($id);
        if ($prospect === null) {
            View::render('errors/not-found', ['title' => 'Introuvable']);
            return;
        }

        View::render('prospects/form', [
            'title' => 'Modifier prospect',
            'action' => '/prospects/' . $id . '/edit',
            'prospect' => $prospect,
            'statuses' => $this->statuses->all(),
            'sources' => $this->sources->all(),
            'errors' => [],
        ]);
    }

    public function update(Request $request, int $id): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        if ($this->prospects->find($id) === null) {
            View::render('errors/not-found', ['title' => 'Introuvable']);
            return;
        }

        $input = $request->input();
        $errors = $this->validator->validate($input);
        if ($errors !== []) {
            View::render('prospects/form', [
                'title' => 'Modifier prospect',
                'action' => '/prospects/' . $id . '/edit',
                'prospect' => $input,
                'statuses' => $this->statuses->all(),
                'sources' => $this->sources->all(),
                'errors' => $errors,
            ]);
            return;
        }

        try {
            $payload = $this->validator->normalize($input);
            $this->prospects->update($id, $payload);
            Response::redirect('/prospects/' . $id);
        } catch (\Throwable $e) {
            Logger::error($e->getMessage());
            View::render('prospects/form', [
                'title' => 'Modifier prospect',
                'action' => '/prospects/' . $id . '/edit',
                'prospect' => $input,
                'statuses' => $this->statuses->all(),
                'sources' => $this->sources->all(),
                'errors' => ['Une erreur serveur est survenue.'],
            ]);
        }
    }

    public function destroy(Request $request, int $id): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        try {
            unset($request);
            $this->prospects->delete($id);
            Response::redirect('/prospects');
        } catch (\Throwable $e) {
            Logger::error($e->getMessage());
            View::render('errors/not-found', ['title' => 'Erreur serveur']);
        }
    }

    public function addNote(Request $request, int $id): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        $content = trim((string) ($request->input()['content'] ?? ''));
        if ($content !== '') {
            try {
                $this->notes->create($id, $content);
            } catch (\Throwable $e) {
                Logger::error($e->getMessage());
            }
        }

        Response::redirect('/prospects/' . $id);
    }

    public function changeStatus(Request $request, int $id): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        $statusId = (int) ($request->input()['status_id'] ?? 0);
        if ($statusId > 0) {
            try {
                $this->prospects->updateStatus($id, $statusId);
            } catch (\Throwable $e) {
                Logger::error($e->getMessage());
            }
        }

        Response::redirect('/prospects/' . $id);
    }

    private function requireAuth(): bool
    {
        if ($this->auth->check()) {
            return true;
        }

        Response::redirect('/login');
        return false;
    }

}
