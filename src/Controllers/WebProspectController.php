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
use App\Services\Csrf;
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

        $filters = [
            'q' => trim((string) ($request->input()['q'] ?? '')),
            'status_id' => (int) ($request->input()['status_id'] ?? 0),
            'source_id' => (int) ($request->input()['source_id'] ?? 0),
            'page' => max(1, (int) ($request->input()['page'] ?? 1)),
            'per_page' => 20,
        ];
        $result = $this->prospects->search($filters);

        View::render('prospects/list', [
            'title' => 'Prospects',
            'prospects' => $result['data'],
            'pagination' => $result['pagination'],
            'filters' => $filters,
            'statuses' => $this->statuses->all(),
            'sources' => $this->sources->all(),
        ]);
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
        if (!$this->validateCsrf($input)) {
            Response::redirect('/prospects/create');
        }

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

        $payload = $this->validator->normalize($input);
        $id = $this->prospects->create($payload);

        $tagIds = array_filter(array_map('intval', explode(',', (string) ($input['tag_ids'] ?? ''))));
        if ($tagIds !== []) {
            $this->tags->syncProspectTags($id, $tagIds);
        }

        Response::redirect('/prospects/' . $id);
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
        if (!$this->validateCsrf($input)) {
            Response::redirect('/prospects/' . $id . '/edit');
        }

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

        $payload = $this->validator->normalize($input);
        $this->prospects->update($id, $payload);
        Response::redirect('/prospects/' . $id);
    }

    public function destroy(Request $request, int $id): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        if (!$this->validateCsrf($request->input())) {
            Response::redirect('/prospects/' . $id);
        }

        $this->prospects->delete($id);
        Response::redirect('/prospects');
    }

    public function addNote(Request $request, int $id): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        if (!$this->validateCsrf($request->input())) {
            Response::redirect('/prospects/' . $id);
        }

        $content = trim((string) ($request->input()['content'] ?? ''));
        if ($content !== '') {
            $this->notes->create($id, $content);
        }

        Response::redirect('/prospects/' . $id);
    }

    public function changeStatus(Request $request, int $id): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        if (!$this->validateCsrf($request->input())) {
            Response::redirect('/prospects/' . $id);
        }

        $statusId = (int) ($request->input()['status_id'] ?? 0);
        if ($statusId > 0) {
            $this->prospects->updateStatus($id, $statusId);
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

    private function validateCsrf(array $input): bool
    {
        return Csrf::isValid((string) ($input['_csrf_token'] ?? ''));
    }

}
