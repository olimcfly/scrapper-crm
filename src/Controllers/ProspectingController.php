<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\ConnectedAccountModel;
use App\Models\SearchRunModel;
use App\Models\SourceResultModel;
use App\Services\Auth;
use App\Services\Logger;
use App\Services\Prospecting\ConnectorRegistry;
use RuntimeException;

final class ProspectingController
{
    private Auth $auth;
    private ConnectorRegistry $registry;
    private ConnectedAccountModel $connectedAccounts;
    private SearchRunModel $searchRuns;
    private SourceResultModel $sourceResults;

    public function __construct()
    {
        $this->auth = new Auth(Database::connection());
        $this->registry = new ConnectorRegistry();
        $this->connectedAccounts = new ConnectedAccountModel();
        $this->searchRuns = new SearchRunModel();
        $this->sourceResults = new SourceResultModel();
    }

    // ✅ PAGE UI (avec sidebar)
    public function sources(Request $request): void
    {
        unset($request);

        $userId = (int) ($this->auth->id() ?? 0);

        View::render('prospects/source_selector', [
            'title' => 'Trouver des prospects - Multi-sources',
            'sources' => $this->registry->all(),
            'connectedAccounts' => $this->connectedAccounts->byUser($userId),
            'searchRuns' => $this->searchRuns->latestByUser($userId),
        ]);
    }

    // ✅ API JSON (séparée)
    public function sourcesApi(Request $request): void
    {
        unset($request);
        Response::json(['data' => $this->registry->all()]);
    }

    public function testConnection(Request $request): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        $input = $request->json();
        $source = (string) ($input['source'] ?? '');
        $credentials = is_array($input['credentials'] ?? null) ? $input['credentials'] : [];

        try {
            $connector = $this->registry->connector($source);
            $result = $connector->connect($credentials);

            $status = (string) ($result['status'] ?? 'error');

            $this->connectedAccounts->upsert(
                (int) $this->auth->id(),
                $source,
                $status,
                isset($result['account_id']) ? (string) $result['account_id'] : null,
                $status === 'connected' ? null : (string) ($result['message'] ?? 'Connexion impossible')
            );

            Response::json(['data' => $result]);
        } catch (RuntimeException $e) {
            Response::json(['error' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            Logger::error($e->getMessage());
            Response::json(['error' => 'Erreur de connexion source.'], 500);
        }
    }

    public function runSearch(Request $request): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        $input = $request->json();
        $source = (string) ($input['source'] ?? '');
        $searchType = (string) ($input['search_type'] ?? 'manual');
        $filters = is_array($input['filters'] ?? null) ? $input['filters'] : [];

        $runId = null;
        $userId = (int) ($this->auth->id() ?? 0);
        try {
            Logger::info(sprintf(
                '[Prospecting] runSearch started user_id=%d source=%s search_type=%s',
                $userId,
                $source,
                $searchType
            ));

            $runId = $this->searchRuns->create($userId, $source, $searchType, $filters);
            $connector = $this->registry->connector($source);
            $result = $connector->search($filters);
            $results = is_array($result['results'] ?? null) ? $result['results'] : [];

            $this->sourceResults->bulkInsert($runId, $source, $results);
            $this->searchRuns->finish($runId, 'success', count($results));

            Logger::info(sprintf(
                '[Prospecting] runSearch success run_id=%d source=%s results=%d',
                $runId,
                $source,
                count($results)
            ));

            Response::json([
                'data' => [
                    'run_id' => $runId,
                    'run_status' => 'success',
                    'results_count' => count($results),
                ],
            ], 201);
        } catch (RuntimeException $e) {
            if (is_int($runId)) {
                $this->searchRuns->finish($runId, 'failed', 0, $e->getMessage());
            }

            Logger::error(sprintf(
                '[Prospecting] runSearch runtime_error source=%s run_id=%s message=%s',
                $source,
                is_int($runId) ? (string) $runId : 'n/a',
                $e->getMessage()
            ));

            Response::json([
                'error' => $e->getMessage(),
                'error_type' => 'prospecting_runtime_error',
                'data' => [
                    'run_id' => $runId,
                    'run_status' => 'failed',
                ],
            ], 422);
        } catch (\Throwable $e) {
            if (is_int($runId)) {
                $this->searchRuns->finish($runId, 'failed', 0, $e->getMessage());
            }

            Logger::error(sprintf(
                '[Prospecting] runSearch unexpected_error source=%s run_id=%s message=%s',
                $source,
                is_int($runId) ? (string) $runId : 'n/a',
                $e->getMessage()
            ));

            Response::json([
                'error' => 'Erreur interne pendant la recherche.',
                'error_type' => 'prospecting_internal_error',
                'data' => [
                    'run_id' => $runId,
                    'run_status' => 'failed',
                ],
            ], 500);
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
