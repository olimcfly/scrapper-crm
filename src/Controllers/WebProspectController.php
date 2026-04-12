<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\ProspectModel;
use App\Models\ProspectNoteModel;
use App\Models\ProspectStatusModel;
use App\Models\SourceModel;
use App\Models\TagModel;
use App\Services\ProspectValidator;

final class WebProspectController
{
    private ProspectModel $prospects;
    private ProspectNoteModel $notes;
    private ProspectStatusModel $statuses;
    private SourceModel $sources;
    private TagModel $tags;
    private ProspectValidator $validator;

    public function __construct()
    {
        $this->prospects = new ProspectModel();
        $this->notes = new ProspectNoteModel();
        $this->statuses = new ProspectStatusModel();
        $this->sources = new SourceModel();
        $this->tags = new TagModel();
        $this->validator = new ProspectValidator();
    }

    public function index(Request $request): void
    {
        $input = $request->input();

        $search = trim((string) ($input['q'] ?? ''));
        $sort = (string) ($input['sort'] ?? 'date');
        $allowedSorts = ['date', 'name', 'score', 'city'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'date';
        }

        $page = filter_var($input['page'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $page = $page !== false ? (int) $page : 1;

        $limit = filter_var($input['limit'] ?? 20, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $limit = $limit !== false ? (int) $limit : 20;
        $allowedLimits = [10, 20, 50, 100];
        if (!in_array($limit, $allowedLimits, true)) {
            $limit = 20;
        }

        $offset = ($page - 1) * $limit;
        $result = $this->prospects->listPaginated($search, $sort, $limit, $offset);
        $total = (int) $result['total'];
        $totalPages = max(1, (int) ceil($total / $limit));
        if ($page > $totalPages) {
            $page = $totalPages;
            $offset = ($page - 1) * $limit;
            $result = $this->prospects->listPaginated($search, $sort, $limit, $offset);
        }

        View::render('prospects/list', [
            'title' => 'Prospects',
            'prospects' => $result['items'],
            'filters' => [
                'q' => $search,
                'sort' => $sort,
                'page' => $page,
                'limit' => $limit,
            ],
            'pagination' => [
                'total' => (int) $result['total'],
                'page' => $page,
                'limit' => $limit,
                'total_pages' => $totalPages,
                'has_prev' => $page > 1,
                'has_next' => $page < $totalPages,
            ],
        ]);
    }

    public function create(Request $request): void
    {
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

        $payload = $this->validator->normalize($input);
        $this->prospects->update($id, $payload);
        Response::redirect('/prospects/' . $id);
    }

    public function destroy(Request $request, int $id): void
    {
        unset($request);
        $this->prospects->delete($id);
        Response::redirect('/prospects');
    }

    public function addNote(Request $request, int $id): void
    {
        $content = trim((string) ($request->input()['content'] ?? ''));
        if ($content !== '') {
            $this->notes->create($id, $content);
        }

        Response::redirect('/prospects/' . $id);
    }

    public function changeStatus(Request $request, int $id): void
    {
        $statusId = (int) ($request->input()['status_id'] ?? 0);
        if ($statusId > 0) {
            $this->prospects->updateStatus($id, $statusId);
        }

        Response::redirect('/prospects/' . $id);
    }
}
