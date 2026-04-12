<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Models\MessageModel;
use App\Models\PipelineStageModel;
use App\Models\ProspectModel;
use App\Models\ProspectPipelineModel;
use App\Models\ProspectTimelineModel;
use App\Services\Auth;
use App\Services\ConversionSuggestionService;

final class PipelineController
{
    private Auth $auth;
    private ProspectPipelineModel $pipeline;
    private PipelineStageModel $stages;
    private ProspectModel $prospects;
    private MessageModel $messages;
    private ProspectTimelineModel $timeline;
    private ConversionSuggestionService $suggestion;

    public function __construct()
    {
        $this->auth = new Auth(Database::connection());
        $this->pipeline = new ProspectPipelineModel();
        $this->stages = new PipelineStageModel();
        $this->prospects = new ProspectModel();
        $this->messages = new MessageModel();
        $this->timeline = new ProspectTimelineModel();
        $this->suggestion = new ConversionSuggestionService();
    }

    public function index(Request $request): void
    {
        unset($request);
        if (!$this->requireAuth()) {
            return;
        }

        $stagesAvailable = $this->stages->isTableAvailable();
        $stages = $stagesAvailable ? $this->stages->all() : [];
        $rows = $stagesAvailable ? $this->pipeline->board() : [];
        $grouped = [];

        foreach ($stages as $stage) {
            $grouped[(int) $stage['id']] = [];
        }

        foreach ($rows as $row) {
            $grouped[(int) $row['stage_id']][] = $row;
        }

        View::render('pipeline/index', [
            'title' => 'Pipeline',
            'stages' => $stages,
            'grouped' => $grouped,
            'pipelineStagesAvailable' => $stagesAvailable,
            'successMessage' => Session::consumeFlash('success'),
            'warningMessage' => Session::consumeFlash('warning'),
        ]);
    }

    public function moveStage(Request $request, int $prospectId): void
    {
        if (!$this->ensureValidCsrf($request) || !$this->requireAuth()) {
            return;
        }

        $stageId = (int) ($request->input()['stage_id'] ?? 0);
        if ($stageId <= 0) {
            Session::flash('warning', 'Étape invalide.');
            Response::redirect('/pipeline');
            return;
        }

        if (!$this->stages->isTableAvailable()) {
            Session::flash('warning', 'Pipeline indisponible : table des étapes manquante.');
            Response::redirect('/pipeline');
            return;
        }

        $stage = $this->stages->find($stageId);
        if ($stage === null) {
            Session::flash('warning', 'Étape introuvable.');
            Response::redirect('/pipeline');
            return;
        }

        $this->pipeline->updateStage($prospectId, $stageId);
        $this->timeline->create($prospectId, 'status_change', 'Pipeline → ' . (string) $stage['name']);
        Session::flash('success', 'Étape mise à jour.');
        Response::redirect('/pipeline');
    }

    public function suggest(Request $request, int $prospectId): void
    {
        if (!$this->ensureValidCsrf($request) || !$this->requireAuth()) {
            return;
        }

        $pipeline = $this->pipeline->byProspect($prospectId);
        $messages = $this->messages->byProspect($prospectId);
        $suggestion = $this->suggestion->suggest($messages, $pipeline);

        $this->pipeline->updateNextAction(
            $prospectId,
            'Suggestion IA générée',
            $suggestion['next_action']
        );

        $this->timeline->create($prospectId, 'update', 'Suggestion IA: ' . $suggestion['next_action']);
        Session::flash('success', 'Suggestion IA ajoutée à la prochaine action.');

        Response::redirect('/prospects/' . $prospectId);
    }

    public function addMessage(Request $request, int $prospectId): void
    {
        if (!$this->ensureValidCsrf($request) || !$this->requireAuth()) {
            return;
        }

        $input = $request->input();
        $content = trim((string) ($input['content'] ?? ''));
        $type = (string) ($input['type'] ?? 'note');
        $direction = (string) ($input['direction'] ?? 'sent');

        if ($content === '') {
            Session::flash('warning', 'Le message est vide.');
            Response::redirect('/prospects/' . $prospectId);
            return;
        }

        if (!$this->messages->isTableAvailable()) {
            Session::flash('warning', 'Messagerie indisponible : table messages manquante.');
            Response::redirect('/prospects/' . $prospectId);
            return;
        }

        $allowedTypes = ['dm', 'reply', 'note'];
        $allowedDirection = ['sent', 'received'];

        if (!in_array($type, $allowedTypes, true) || !in_array($direction, $allowedDirection, true)) {
            Session::flash('warning', 'Format de message invalide.');
            Response::redirect('/prospects/' . $prospectId);
            return;
        }

        $this->messages->create($prospectId, $content, $type, $direction);

        $pipeline = $this->pipeline->byProspect($prospectId);
        $suggestion = $this->suggestion->suggest($this->messages->byProspect($prospectId), $pipeline);
        $this->pipeline->updateNextAction($prospectId, 'Message ajouté', $suggestion['next_action']);

        Session::flash('success', 'Message enregistré.');
        Response::redirect('/prospects/' . $prospectId);
    }

    private function requireAuth(): bool
    {
        if (!$this->auth->check()) {
            Response::redirect('/login');
            return false;
        }

        return true;
    }

    private function ensureValidCsrf(Request $request): bool
    {
        if (!Csrf::verify((string) ($request->input()['_csrf'] ?? ''))) {
            http_response_code(419);
            echo 'Requête expirée. Veuillez recharger la page.';
            return false;
        }

        return true;
    }
}
