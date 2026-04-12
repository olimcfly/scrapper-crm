<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Database;
use App\Models\ProspectModel;
use App\Models\ProspectNoteModel;
use App\Models\TagModel;
use App\Services\Auth;
use App\Services\Logger;
use App\Services\ProspectValidator;

final class ProspectController
{
    private ProspectModel $prospects;
    private ProspectNoteModel $notes;
    private TagModel $tags;
    private ProspectValidator $validator;
    private Auth $auth;

    public function __construct()
    {
        $this->prospects = new ProspectModel();
        $this->notes = new ProspectNoteModel();
        $this->tags = new TagModel();
        $this->validator = new ProspectValidator();
        $this->auth = new Auth(Database::connection());
    }

    public function index(Request $request): void
    {
        if (!$this->requireApiAuth()) {
            return;
        }

        $input = $request->input();
        $result = $this->prospects->search([
            'q' => trim((string) ($input['q'] ?? '')),
            'status_id' => (int) ($input['status_id'] ?? 0),
            'source_id' => (int) ($input['source_id'] ?? 0),
            'page' => max(1, (int) ($input['page'] ?? 1)),
            'per_page' => min(100, max(1, (int) ($input['per_page'] ?? 20))),
        ]);

        Response::json($result);
    }

    public function show(Request $request, int $id): void
    {
        unset($request);
        if (!$this->requireApiAuth()) {
            return;
        }

        $prospect = $this->prospects->find($id);

        if ($prospect === null) {
            Response::json(['error' => 'Prospect introuvable.'], 404);
            return;
        }

        $prospect['notes'] = $this->notes->byProspect($id);
        Response::json(['data' => $prospect]);
    }

    public function store(Request $request): void
    {
        if (!$this->requireApiAuth()) {
            return;
        }

        $input = $request->json();
        $errors = $this->validator->validate($input);

        if ($errors !== []) {
            Response::json(['error' => 'Validation échouée.', 'details' => $errors], 422);
            return;
        }

        try {
            $payload = $this->validator->normalize($input);
            $id = $this->prospects->create($payload);
            $prospect = $this->prospects->find($id);

            if (isset($input['tag_ids']) && is_array($input['tag_ids'])) {
                $this->tags->syncProspectTags($id, $input['tag_ids']);
            }

            Response::json(['data' => $prospect], 201);
        } catch (\Throwable $e) {
            Logger::error($e->getMessage());
            Response::json(['error' => 'Impossible de créer le prospect.'], 500);
        }
    }

    public function update(Request $request, int $id): void
    {
        if (!$this->requireApiAuth()) {
            return;
        }

        if ($this->prospects->find($id) === null) {
            Response::json(['error' => 'Prospect introuvable.'], 404);
            return;
        }

        $input = $request->json();
        $errors = $this->validator->validate($input);

        if ($errors !== []) {
            Response::json(['error' => 'Validation échouée.', 'details' => $errors], 422);
            return;
        }

        try {
            $payload = $this->validator->normalize($input);
            $this->prospects->update($id, $payload);

            if (isset($input['tag_ids']) && is_array($input['tag_ids'])) {
                $this->tags->syncProspectTags($id, $input['tag_ids']);
            }

            Response::json(['data' => $this->prospects->find($id)]);
        } catch (\Throwable $e) {
            Logger::error($e->getMessage());
            Response::json(['error' => 'Impossible de modifier le prospect.'], 500);
        }
    }

    public function delete(Request $request, int $id): void
    {
        unset($request);
        if (!$this->requireApiAuth()) {
            return;
        }

        if ($this->prospects->find($id) === null) {
            Response::json(['error' => 'Prospect introuvable.'], 404);
            return;
        }

        $this->prospects->delete($id);
        Response::json(['message' => 'Prospect supprimé.']);
    }

    public function addNote(Request $request, int $id): void
    {
        if (!$this->requireApiAuth()) {
            return;
        }

        if ($this->prospects->find($id) === null) {
            Response::json(['error' => 'Prospect introuvable.'], 404);
            return;
        }

        $content = trim((string) ($request->json()['content'] ?? ''));
        if ($content === '') {
            Response::json(['error' => 'Le contenu de la note est requis.'], 422);
            return;
        }

        $noteId = $this->notes->create($id, $content);
        Response::json(['data' => ['id' => $noteId, 'prospect_id' => $id, 'content' => $content]], 201);
    }

    public function notes(Request $request, int $id): void
    {
        unset($request);
        if (!$this->requireApiAuth()) {
            return;
        }

        if ($this->prospects->find($id) === null) {
            Response::json(['error' => 'Prospect introuvable.'], 404);
            return;
        }

        Response::json(['data' => $this->notes->byProspect($id)]);
    }

    public function changeStatus(Request $request, int $id): void
    {
        if (!$this->requireApiAuth()) {
            return;
        }

        if ($this->prospects->find($id) === null) {
            Response::json(['error' => 'Prospect introuvable.'], 404);
            return;
        }

        $statusId = (int) ($request->json()['status_id'] ?? 0);
        if ($statusId <= 0) {
            Response::json(['error' => 'status_id invalide.'], 422);
            return;
        }

        $this->prospects->updateStatus($id, $statusId);
        Response::json(['message' => 'Statut mis à jour.']);
    }

    private function requireApiAuth(): bool
    {
        if ($this->auth->check()) {
            return true;
        }

        Response::json(['error' => 'Authentification requise.'], 401);
        return false;
    }
}
