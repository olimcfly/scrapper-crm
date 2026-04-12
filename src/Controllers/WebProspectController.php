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
        unset($request);
        View::render('prospects/list', [
            'title' => 'Prospects',
            'prospects' => $this->prospects->all(),
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
