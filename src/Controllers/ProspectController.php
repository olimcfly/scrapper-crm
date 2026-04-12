<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\ProspectModel;
use App\Models\ProspectNoteModel;
use App\Models\TagModel;
use App\Services\Logger;
use App\Services\ProspectValidator;

final class ProspectController
{
    private ProspectModel $prospects;
    private ProspectNoteModel $notes;
    private TagModel $tags;
    private ProspectValidator $validator;

    public function __construct()
    {
        $this->prospects = new ProspectModel();
        $this->notes = new ProspectNoteModel();
        $this->tags = new TagModel();
        $this->validator = new ProspectValidator();
    }

    public function index(Request $request): void
    {
        unset($request);
        Response::json(['data' => $this->prospects->all()]);
    }

    public function show(Request $request, int $id): void
    {
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
        if ($this->prospects->find($id) === null) {
            Response::json(['error' => 'Prospect introuvable.'], 404);
            return;
        }

        $this->prospects->delete($id);
        Response::json(['message' => 'Prospect supprimé.']);
    }

    public function addNote(Request $request, int $id): void
    {
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

    public function changeStatus(Request $request, int $id): void
    {
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
}
