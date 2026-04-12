<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
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
        if (!$this->requireAuth()) {
            return;
        }

        unset($request);
        Response::json(['data' => $this->prospects->all()]);
    }

    public function show(Request $request, int $id): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        unset($request);
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
        if (!$this->requireAuth()) {
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
                $tagIds = array_values(array_filter(array_map('intval', $input['tag_ids']), static fn (int $tagId): bool => $tagId > 0));
                $this->tags->syncProspectTags($id, $tagIds);
            }

            Response::json(['data' => $prospect], 201);
        } catch (\Throwable $e) {
            Logger::error($e->getMessage());
            Response::json(['error' => 'Impossible de créer le prospect.'], 500);
        }
    }

    public function update(Request $request, int $id): void
    {
        if (!$this->requireAuth()) {
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
                $tagIds = array_values(array_filter(array_map('intval', $input['tag_ids']), static fn (int $tagId): bool => $tagId > 0));
                $this->tags->syncProspectTags($id, $tagIds);
            }

            Response::json(['data' => $this->prospects->find($id)]);
        } catch (\Throwable $e) {
            Logger::error($e->getMessage());
            Response::json(['error' => 'Impossible de modifier le prospect.'], 500);
        }
    }

    public function delete(Request $request, int $id): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        unset($request);
        if ($this->prospects->find($id) === null) {
            Response::json(['error' => 'Prospect introuvable.'], 404);
            return;
        }

        try {
            $this->prospects->delete($id);
            Response::json(['message' => 'Prospect supprimé.']);
        } catch (\Throwable $e) {
            Logger::error($e->getMessage());
            Response::json(['error' => 'Impossible de supprimer le prospect.'], 500);
        }
    }

    public function addNote(Request $request, int $id): void
    {
        if (!$this->requireAuth()) {
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

        try {
            $noteId = $this->notes->create($id, $content);
            Response::json(['data' => ['id' => $noteId, 'prospect_id' => $id, 'content' => $content]], 201);
        } catch (\Throwable $e) {
            Logger::error($e->getMessage());
            Response::json(['error' => 'Impossible d\'ajouter la note.'], 500);
        }
    }

    public function notes(Request $request, int $id): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        unset($request);
        if ($this->prospects->find($id) === null) {
            Response::json(['error' => 'Prospect introuvable.'], 404);
            return;
        }

        Response::json(['data' => $this->notes->byProspect($id)]);
    }

    public function changeStatus(Request $request, int $id): void
    {
        if (!$this->requireAuth()) {
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

        try {
            $this->prospects->updateStatus($id, $statusId);
            Response::json(['message' => 'Statut mis à jour.']);
        } catch (\Throwable $e) {
            Logger::error($e->getMessage());
            Response::json(['error' => 'Impossible de mettre à jour le statut.'], 500);
        }
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
