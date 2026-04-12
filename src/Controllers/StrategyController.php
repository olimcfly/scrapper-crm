<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\StrategyAnalysisModel;
use App\Services\Auth;
use App\Services\Logger;
use App\Services\OpenAiClient;
use Throwable;

final class StrategyController
{
    private OpenAiClient $openAiClient;
    private StrategyAnalysisModel $analyses;
    private Auth $auth;

    public function __construct()
    {
        $this->openAiClient = new OpenAiClient();
        $this->analyses = new StrategyAnalysisModel();
        $this->auth = new Auth(Database::connection());
    }

    public function index(Request $request): void
    {
        unset($request);

        View::render('strategie/index', [
            'title' => 'Stratégie prospect',
        ]);
    }

    public function analyze(Request $request): void
    {
        $input = $request->input();
        $csrfToken = (string) ($input['_csrf'] ?? '');
        if (!Csrf::verify($csrfToken)) {
            Response::json(['error' => 'Session expirée. Rechargez la page.'], 419);
            return;
        }

        $profile = trim((string) ($input['profile'] ?? ''));
        if ($profile === '') {
            Response::json(['error' => 'Le profil est requis.'], 422);
            return;
        }

        if (mb_strlen($profile) < 40) {
            Response::json(['error' => 'Le profil est trop court pour une analyse utile (min 40 caractères).'], 422);
            return;
        }

        try {
            $raw = $this->openAiClient->generateStructuredAnalysis($profile);
            $analysis = $this->normalizeAnalysis($raw['output_text']);

            $user = $this->auth->user();
            if (is_array($user) && isset($user['id'])) {
                $this->analyses->create((int) $user['id'], $profile, $analysis);
            }

            Response::json(['data' => $analysis]);
        } catch (Throwable $e) {
            Logger::error('Strategy analysis error: ' . $e->getMessage());
            Response::json(['error' => 'Impossible de générer l’analyse pour le moment.'], 500);
        }
    }

    /**
     * @return array{awareness_level:string,summary:string,pain_points:array<int,string>,desires:array<int,string>,content_angles:array<int,string>,recommended_hooks:array<int,string>}
     */
    private function normalizeAnalysis(string $jsonText): array
    {
        $decoded = json_decode($jsonText, true);
        if (!is_array($decoded)) {
            throw new \RuntimeException('JSON IA invalide.');
        }

        return [
            'awareness_level' => trim((string) ($decoded['awareness_level'] ?? 'N/A')),
            'summary' => trim((string) ($decoded['summary'] ?? '')),
            'pain_points' => $this->normalizeStringList($decoded['pain_points'] ?? []),
            'desires' => $this->normalizeStringList($decoded['desires'] ?? []),
            'content_angles' => $this->normalizeStringList($decoded['content_angles'] ?? []),
            'recommended_hooks' => array_slice($this->normalizeStringList($decoded['recommended_hooks'] ?? []), 0, 5),
        ];
    }

    /** @return array<int,string> */
    private function normalizeStringList(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $items = [];
        foreach ($value as $item) {
            $text = trim((string) $item);
            if ($text !== '') {
                $items[] = $text;
            }
        }

        return $items;
    }
}
