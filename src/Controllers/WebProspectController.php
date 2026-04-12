<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Models\ProspectModel;
use App\Models\ProspectNoteModel;
use App\Models\ProspectStatusModel;
use App\Models\ProspectTimelineModel;
use App\Models\SourceModel;
use App\Models\TagModel;
use App\Services\Auth;
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
    private ProspectValidator $validator;
    private CsvProspectImportService $csvImport;
    private Auth $auth;

    public function __construct()
    {
        $this->prospects = new ProspectModel();
        $this->notes = new ProspectNoteModel();
        $this->statuses = new ProspectStatusModel();
        $this->sources = new SourceModel();
        $this->tags = new TagModel();
        $this->timeline = new ProspectTimelineModel();
        $this->validator = new ProspectValidator();
        $this->csvImport = new CsvProspectImportService();
        $this->auth = new Auth(Database::connection());
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

        $input = $request->input();
        $filters = [
            'q' => trim((string) ($input['q'] ?? '')),
            'status_id' => (int) ($input['status_id'] ?? 0),
            'source_id' => (int) ($input['source_id'] ?? 0),
        ];
        $page = max(1, (int) ($input['page'] ?? 1));
        $result = $this->prospects->search($filters, $page, 15);

        View::render('prospects/list', [
            'title' => 'Prospects',
            'prospects' => $result['items'],
            'filters' => $filters,
            'statuses' => $this->statuses->all(),
            'sources' => $this->sources->all(),
            'pagination' => $result,
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

        try {
            $payload = $this->validator->normalize($input);
            $id = $this->prospects->create($payload);

            $tagIds = array_values(array_filter(array_map('intval', explode(',', (string) ($input['tag_ids'] ?? ''))), static fn (int $tagId): bool => $tagId > 0));
            if ($tagIds !== []) {
                $this->tags->syncProspectTags($id, $tagIds);
            }

        $this->timeline->create($id, 'creation', 'Prospect créé');
        Session::flash('success', 'Prospect créé avec succès.');
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
            'timeline' => $this->timeline->byProspect($id),
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
        $this->timeline->create($id, 'update', 'Fiche prospect mise à jour');
        Session::flash('success', 'Prospect mis à jour.');
        Response::redirect('/prospects/' . $id);
    }

    public function destroy(Request $request, int $id): void
    {
        if (!$this->requireAuth()) {
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
        if (!$this->requireAuth()) {
            return;
        }

        if (!$this->validateCsrf($request->input())) {
            Response::redirect('/prospects/' . $id);
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
        if (!$this->requireAuth()) {
            return;
        }

        if (!$this->validateCsrf($request->input())) {
            Response::redirect('/prospects/' . $id);
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
        if ($this->auth->check()) {
            return true;
        }

        Response::redirect('/login');
        return false;
    }

    /** @return array<string, string> */
    private function importFieldLabels(): array
    {
        return [
            'first_name' => 'Prénom *',
            'last_name' => 'Nom *',
            'professional_email' => 'Email professionnel',
            'professional_phone' => 'Téléphone professionnel',
            'business_name' => 'Entreprise',
            'activity' => 'Activité',
            'city' => 'Ville',
            'country' => 'Pays',
            'website' => 'Site web',
            'notes_summary' => 'Notes',
        ];
    }
}
