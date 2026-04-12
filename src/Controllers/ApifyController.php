<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Models\ProspectModel;
use App\Models\SourceModel;
use App\Services\Apify\ApifyDatasetService;
use App\Services\Apify\ApifyMappingService;
use App\Services\Apify\ApifyRunService;
use App\Services\Auth;
use App\Services\Logger;
use App\Services\ProspectValidator;
use RuntimeException;

final class ApifyController
{
    private Auth $auth;
    private ApifyRunService $runs;
    private ApifyDatasetService $datasets;
    private ApifyMappingService $mapping;
    private ProspectModel $prospects;
    private SourceModel $sources;
    private ProspectValidator $validator;

    public function __construct()
    {
        $this->auth = new Auth(Database::connection());
        $this->runs = new ApifyRunService();
        $this->datasets = new ApifyDatasetService();
        $this->mapping = new ApifyMappingService();
        $this->prospects = new ProspectModel();
        $this->sources = new SourceModel();
        $this->validator = new ProspectValidator();
    }

    public function runSource(Request $request, string $source): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        $input = $request->json();
        $runInput = is_array($input['input'] ?? null) ? $input['input'] : $input;
        $waitForFinish = isset($input['wait_for_finish']) ? (int) $input['wait_for_finish'] : null;

        try {
            $result = $this->runs->runActor($source, $runInput, $waitForFinish);
            Response::json([
                'data' => [
                    'source' => $source,
                    'run' => $result,
                ],
            ], 201);
        } catch (RuntimeException $e) {
            Response::json(['error' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            Logger::error($e->getMessage());
            Response::json(['error' => 'Impossible de démarrer le run Apify.'], 500);
        }
    }

    public function getRun(Request $request, string $runId): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        unset($request);
        try {
            Response::json(['data' => $this->runs->getRun($runId)]);
        } catch (\Throwable $e) {
            Logger::error($e->getMessage());
            Response::json(['error' => 'Impossible de récupérer le run Apify.'], 500);
        }
    }

    public function getDataset(Request $request, string $datasetId): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        $input = $request->input();
        $source = trim((string) ($input['source'] ?? ''));
        $limit = (int) ($input['limit'] ?? 50);
        $offset = (int) ($input['offset'] ?? 0);

        try {
            $items = $this->datasets->getDatasetItems($datasetId, $limit, $offset);
            $mapped = $source !== '' ? $this->mapping->mapDataset($source, $items) : $items;

            Response::json([
                'data' => [
                    'dataset_id' => $datasetId,
                    'source' => $source !== '' ? $source : null,
                    'items' => $mapped,
                    'count' => count($mapped),
                ],
            ]);
        } catch (\Throwable $e) {
            Logger::error($e->getMessage());
            Response::json(['error' => 'Impossible de récupérer le dataset Apify.'], 500);
        }
    }

    public function importFromSource(Request $request, string $source): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        $input = $request->json();
        $items = is_array($input['items'] ?? null) ? $input['items'] : [];

        if ($items === []) {
            Response::json(['error' => 'Aucun item à importer.'], 422);
            return;
        }

        $mappedItems = $this->mapping->mapDataset($source, $items);
        $sourceId = $this->resolveSourceId($source);

        $created = [];
        $errors = [];

        foreach ($mappedItems as $index => $mappedItem) {
            try {
                $prospectPayload = $this->buildProspectPayload($mappedItem, $sourceId);
                $validationErrors = $this->validator->validate($prospectPayload);
                if ($validationErrors !== []) {
                    $errors[] = ['index' => $index, 'errors' => $validationErrors];
                    continue;
                }

                $normalized = $this->validator->normalize($prospectPayload);
                $id = $this->prospects->create($normalized);
                $created[] = $this->prospects->find($id);
            } catch (\Throwable $e) {
                $errors[] = ['index' => $index, 'errors' => [$e->getMessage()]];
            }
        }

        Response::json([
            'data' => [
                'source' => $source,
                'created' => $created,
                'created_count' => count($created),
                'errors' => $errors,
            ],
        ], 201);
    }

    private function requireAuth(): bool
    {
        if ($this->auth->check()) {
            return true;
        }

        Response::json(['error' => 'Authentification requise.'], 401);
        return false;
    }

    private function resolveSourceId(string $source): int
    {
        $normalizedName = str_replace('_', ' ', $source);
        foreach ($this->sources->all() as $row) {
            $name = strtolower(trim((string) ($row['name'] ?? '')));
            if ($name === strtolower($normalizedName)) {
                return (int) ($row['id'] ?? 1);
            }
        }

        return 1;
    }

    /** @param array<string, mixed> $mappedItem */
    private function buildProspectPayload(array $mappedItem, int $sourceId): array
    {
        $prospect = is_array($mappedItem['prospect'] ?? null) ? $mappedItem['prospect'] : [];
        $analysis = is_array($mappedItem['analysis'] ?? null) ? $mappedItem['analysis'] : [];

        $fullName = trim((string) ($prospect['full_name'] ?? ''));
        $firstName = trim((string) ($prospect['first_name'] ?? ''));
        $lastName = trim((string) ($prospect['last_name'] ?? ''));

        if ($firstName === '' && $lastName === '') {
            $parts = preg_split('/\s+/', $fullName);
            $firstName = $parts[0] ?? 'Prospect';
            $lastName = $parts[1] ?? 'Apify';
        }

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'full_name' => $fullName !== '' ? $fullName : trim($firstName . ' ' . $lastName),
            'business_name' => (string) ($prospect['business_name'] ?? ''),
            'activity' => (string) ($prospect['activity'] ?? ''),
            'city' => (string) ($prospect['city'] ?? ''),
            'country' => (string) ($prospect['country'] ?? ''),
            'website' => (string) ($prospect['website'] ?? ''),
            'professional_email' => (string) ($prospect['professional_email'] ?? ''),
            'professional_phone' => (string) ($prospect['professional_phone'] ?? ''),
            'instagram_url' => (string) ($prospect['instagram_url'] ?? ''),
            'facebook_url' => (string) ($prospect['facebook_url'] ?? ''),
            'linkedin_url' => (string) ($prospect['linkedin_url'] ?? ''),
            'tiktok_url' => (string) ($prospect['tiktok_url'] ?? ''),
            'source_id' => $sourceId,
            'status_id' => 1,
            'score' => (int) ($analysis['mvp_score'] ?? 0),
            'notes_summary' => (string) ($analysis['summary'] ?? ''),
            'objectif_contact' => 'Découverte du prospect',
            'prochaine_action' => (string) ($analysis['next_action'] ?? 'Lancer enrichissement ciblé.'),
            'date_prochaine_action' => null,
            'canal_prioritaire' => null,
            'niveau_priorite' => 'moyen',
            'blocages' => '',
        ];
    }
}
