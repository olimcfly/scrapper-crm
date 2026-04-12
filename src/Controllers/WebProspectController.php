<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Models\ProspectModel;
use App\Models\ProspectNoteModel;
use App\Models\ProspectPipelineModel;
use App\Models\ProspectStatusModel;
use App\Models\ProspectTimelineModel;
use App\Models\SourceModel;
use App\Models\TagModel;
use App\Models\MessageModel;
use App\Services\Auth;
use App\Services\ConversionSuggestionService;
use App\Services\CsvProspectImportService;
use App\Services\ProspectValidator;
use RuntimeException;

final class WebProspectController
{
    private ProspectModel $prospects;
    private ProspectNoteModel $notes;
    private ProspectStatusModel $statuses;
    private SourceModel $sources;
    private TagModel $tags;
    private ProspectTimelineModel $timeline;
    private ProspectPipelineModel $pipeline;
    private MessageModel $messages;
    private ProspectValidator $validator;
    private CsvProspectImportService $csvImport;
    private Auth $auth;
    private ConversionSuggestionService $suggestion;

    public function __construct()
    {
        $this->prospects = new ProspectModel();
        $this->notes = new ProspectNoteModel();
        $this->statuses = new ProspectStatusModel();
        $this->sources = new SourceModel();
        $this->tags = new TagModel();
        $this->timeline = new ProspectTimelineModel();
        $this->pipeline = new ProspectPipelineModel();
        $this->messages = new MessageModel();
        $this->validator = new ProspectValidator();
        $this->csvImport = new CsvProspectImportService();
        $this->auth = new Auth(Database::connection());
        $this->suggestion = new ConversionSuggestionService();
    }

    public function index(Request $request): void
    {
        $input = $request->input();

        $filters = [
            'q'         => trim((string) ($input['q'] ?? '')),
            'status_id' => (int) ($input['status_id'] ?? 0),
            'source_id' => (int) ($input['source_id'] ?? 0),
        ];

        $page    = max(1, (int) ($input['page'] ?? 1));
        $perPage = 20;

        $result = $this->prospects->search($filters, $page, $perPage);

        View::render('prospects/list', [
            'title'          => 'Prospects',
            'prospects'      => $result['items'],
            'filters'        => $filters,
            'statuses'       => $this->statuses->all(),
            'sources'        => $this->sources->all(),
            'pagination'     => $result,
            'successMessage' => Session::consumeFlash('success'),
            'warningMessage' => Session::consumeFlash('warning'),
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

    public function importForm(Request $request): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        unset($request);
        View::render('prospects/import_upload', [
            'title' => 'Import CSV prospects',
            'errors' => [],
        ]);
    }

    public function importUpload(Request $request): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        unset($request);
        $file = $_FILES['csv_file'] ?? null;

        if (!is_array($file)) {
            View::render('prospects/import_upload', [
                'title' => 'Import CSV prospects',
                'errors' => ['Aucun fichier CSV reçu.'],
            ]);
            return;
        }

        try {
            $preview = $this->csvImport->parseUploadedFile($file);
            $_SESSION['csv_import_preview'] = $preview;

            View::render('prospects/import_mapping', [
                'title' => 'Mapping des colonnes CSV',
                'headers' => $preview['headers'],
                'fileName' => $preview['file_name'],
                'sampleRows' => array_slice($preview['rows'], 0, 5),
                'errors' => [],
                'fieldLabels' => $this->importFieldLabels(),
            ]);
        } catch (RuntimeException $e) {
            View::render('prospects/import_upload', [
                'title' => 'Import CSV prospects',
                'errors' => [$e->getMessage()],
            ]);
        }
    }

    public function importProcess(Request $request): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        $preview = $_SESSION['csv_import_preview'] ?? null;

        if (!is_array($preview) || !isset($preview['headers'], $preview['rows'])) {
            View::render('prospects/import_upload', [
                'title' => 'Import CSV prospects',
                'errors' => ['Session expirée. Rechargez un fichier CSV pour recommencer.'],
            ]);
            return;
        }

        $mapping = [];
        $input = $request->input();

        foreach (array_keys($this->importFieldLabels()) as $field) {
            $mapping[$field] = trim((string) ($input['map_' . $field] ?? ''));
        }

        try {
            $report = $this->csvImport->import($preview['headers'], $preview['rows'], $mapping);
            unset($_SESSION['csv_import_preview']);

            View::render('prospects/import_report', [
                'title' => 'Rapport import CSV',
                'fileName' => $preview['file_name'] ?? 'import.csv',
                'totalRows' => count($preview['rows']),
                'report' => $report,
            ]);
        } catch (RuntimeException $e) {
            View::render('prospects/import_mapping', [
                'title' => 'Mapping des colonnes CSV',
                'headers' => $preview['headers'],
                'fileName' => $preview['file_name'] ?? 'import.csv',
                'sampleRows' => array_slice($preview['rows'], 0, 5),
                'errors' => [$e->getMessage()],
                'fieldLabels' => $this->importFieldLabels(),
                'selectedMapping' => $mapping,
            ]);
        }
    }

    public function store(Request $request): void
    {
        if (!$this->ensureValidCsrf($request)) {
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
            $this->pipeline->ensureForProspect($id);

            $tagIds = array_values(array_filter(array_map('intval', explode(',', (string) ($input['tag_ids'] ?? ''))), static fn (int $tagId): bool => $tagId > 0));
            if ($tagIds !== []) {
                $this->tags->syncProspectTags($id, $tagIds);
            }

            $this->timeline->create($id, 'creation', 'Prospect créé');
            Session::flash('success', 'Prospect créé avec succès.');
            Response::redirect('/prospects/' . $id);
        } catch (\Throwable $e) {
            Session::flash('warning', 'Erreur lors de la création du prospect.');
            View::render('prospects/form', [
                'title' => 'Nouveau prospect',
                'action' => '/prospects/create',
                'prospect' => $input,
                'statuses' => $this->statuses->all(),
                'sources' => $this->sources->all(),
                'errors' => ['Une erreur est survenue, veuillez réessayer.'],
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
            'messages' => $this->messages->byProspect($id),
            'timeline' => $this->timeline->byProspect($id),
            'pipeline' => $this->pipeline->byProspect($id),
            'iaSuggestion' => $this->suggestion->suggest(
                $this->messages->byProspect($id),
                $this->pipeline->byProspect($id)
            ),
            'statuses' => $this->statuses->all(),
            'successMessage' => Session::consumeFlash('success'),
            'warningMessage' => Session::consumeFlash('warning'),
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
        if (!$this->ensureValidCsrf($request)) {
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

        $payload = $this->validator->normalize($input);
        $this->prospects->update($id, $payload);
        $this->timeline->create($id, 'update', 'Fiche prospect mise à jour');
        Session::flash('success', 'Prospect mis à jour.');
        Response::redirect('/prospects/' . $id);
    }

    public function destroy(Request $request, int $id): void
    {
        if (!$this->ensureValidCsrf($request)) {
            return;
        }

        unset($request);
        $prospect = $this->prospects->find($id);
        if ($prospect !== null) {
            $this->timeline->create($id, 'deletion', 'Prospect supprimé');
        }
        $this->prospects->delete($id);
        Session::flash('success', 'Prospect supprimé.');
        Response::redirect('/prospects');
    }

    public function addNote(Request $request, int $id): void
    {
        if (!$this->ensureValidCsrf($request)) {
            return;
        }

        $content = trim((string) ($request->input()['content'] ?? ''));
        if ($content !== '') {
            $this->notes->create($id, $content);
            Session::flash('success', 'Note ajoutée.');
        } else {
            Session::flash('warning', 'La note est vide, aucune modification appliquée.');
        }

        Response::redirect('/prospects/' . $id);
    }

    public function changeStatus(Request $request, int $id): void
    {
        if (!$this->ensureValidCsrf($request)) {
            return;
        }

        $statusId = (int) ($request->input()['status_id'] ?? 0);
        if ($statusId > 0) {
            $this->prospects->updateStatus($id, $statusId);
            $statusName = '';
            foreach ($this->statuses->all() as $status) {
                if ((int) $status['id'] === $statusId) {
                    $statusName = (string) $status['name'];
                    break;
                }
            }
            $detail = $statusName === '' ? 'Statut mis à jour' : 'Statut changé: ' . $statusName;
            $this->timeline->create($id, 'status_change', $detail);
            Session::flash('success', 'Statut mis à jour.');
        } else {
            Session::flash('warning', 'Veuillez sélectionner un statut valide.');
        }

        Response::redirect('/prospects/' . $id);
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
        $input = $request->input();
        if (!Csrf::verify((string) ($input['_csrf'] ?? ''))) {
            http_response_code(419);
            echo 'Requête expirée. Veuillez recharger la page.';
            return false;
        }

        return true;
    }

    /** @return array<string, string> */
    private function importFieldLabels(): array
    {
        return [
            'first_name'           => 'Prénom',
            'last_name'            => 'Nom',
            'business_name'        => 'Société',
            'activity'             => 'Activité',
            'city'                 => 'Ville',
            'country'              => 'Pays',
            'professional_email'   => 'Email pro',
            'professional_phone'   => 'Téléphone pro',
            'website'              => 'Site web',
            'score'                => 'Score',
            'status_id'            => 'Statut',
            'source_id'            => 'Source',
            'notes_summary'        => 'Résumé',
            'objectif_contact'     => 'Objectif de contact',
            'prochaine_action'     => 'Prochaine action',
            'date_prochaine_action'=> 'Date prochaine action',
            'canal_prioritaire'    => 'Canal prioritaire',
            'niveau_priorite'      => 'Niveau priorité',
            'blocages'             => 'Blocages',
        ];
    }

}
