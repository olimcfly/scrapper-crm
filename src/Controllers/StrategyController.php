<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Models\StrategyAnalysisModel;
use App\Services\Auth;
use App\Services\Logger;
use App\Services\OpenAiClient;
use Throwable;

final class StrategyController
{
    private OpenAiClient $openAiClient;
    private ?StrategyAnalysisModel $analyses = null;
    private ?Auth $auth = null;

    public function __construct()
    {
        $this->openAiClient = new OpenAiClient();
    }

    public function index(Request $request): void
    {
        unset($request);

        $history = [];
        try {
            if (!$this->auth instanceof Auth) {
                $this->auth = new Auth(Database::connection());
            }
            if (!$this->analyses instanceof StrategyAnalysisModel) {
                $this->analyses = new StrategyAnalysisModel();
            }
            $user = $this->auth->user();
            if (is_array($user) && isset($user['id'])) {
                $history = $this->analyses->latestByUser((int) $user['id'], 15);
            }
        } catch (Throwable) {
            $history = [];
        }

        View::render('strategie/index', [
            'title' => 'Stratégie prospect',
            'history' => $history,
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

            $analysisId = $this->storeAnalysisSafely($profile, $analysis);

            Response::json([
                'success' => true,
                'data' => $analysis,
                'analysis_id' => $analysisId,
                'meta' => ['source' => 'openai'],
            ]);
        } catch (Throwable $e) {
            $fallbackEnabled = filter_var(getenv('OPENAI_FALLBACK_ENABLED') ?: true, FILTER_VALIDATE_BOOL);
            $shouldFallback = $fallbackEnabled || $this->isMissingApiKeyError($e);

            Logger::error(sprintf(
                'Strategy analysis error: %s | fallback=%s',
                $e->getMessage(),
                $shouldFallback ? 'yes' : 'no'
            ));

            if ($shouldFallback) {
                $analysis = $this->buildFallbackAnalysis($profile);
                Response::json([
                    'success' => true,
                    'data' => $analysis,
                    'meta' => [
                        'source' => 'fallback',
                        'warning' => 'Analyse générée en mode dégradé (IA indisponible).',
                    ],
                ]);
                return;
            }

            Response::json([
                'success' => false,
                'error' => 'Analyse IA indisponible. Vérifiez la configuration OPENAI_API_KEY puis réessayez.',
                'code' => 'STRATEGY_ANALYSIS_FAILED',
            ], 500);
        }
    }

    public function bridgeToContent(Request $request): void
    {
        $input = $request->input();
        $csrfToken = (string) ($input['_csrf'] ?? '');
        if (!Csrf::verify($csrfToken)) {
            Response::json(['error' => 'Session expirée. Rechargez la page.'], 419);
            return;
        }

        $analysis = $input['analysis'] ?? null;
        if (!is_array($analysis)) {
            Response::json(['error' => 'Analyse manquante pour ouvrir le module Contenu.'], 422);
            return;
        }

        $normalized = [
            'awareness_level' => trim((string) ($analysis['awareness_level'] ?? 'N/A')),
            'summary' => trim((string) ($analysis['summary'] ?? '')),
            'pain_points' => $this->normalizeStringList($analysis['pain_points'] ?? []),
            'desires' => $this->normalizeStringList($analysis['desires'] ?? []),
            'content_angles' => $this->normalizeStringList($analysis['content_angles'] ?? []),
            'recommended_hooks' => $this->normalizeStringList($analysis['recommended_hooks'] ?? []),
        ];

        Session::put('strategy_to_content_analysis', $normalized);
        Session::put('strategy_to_content_analysis_id', (int) ($input['analysis_id'] ?? 0));
        Session::forget('strategy_to_content_generated');
        Response::json([
            'success' => true,
            'redirect_url' => '/contenu',
        ]);
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

    private function storeAnalysisSafely(string $profile, array $analysis): ?int
    {
        try {
            if (!$this->auth instanceof Auth) {
                $this->auth = new Auth(Database::connection());
            }

            if (!$this->analyses instanceof StrategyAnalysisModel) {
                $this->analyses = new StrategyAnalysisModel();
            }

            $user = $this->auth->user();
            if (is_array($user) && isset($user['id'])) {
                return $this->analyses->create((int) $user['id'], $profile, $analysis);
            }
        } catch (Throwable $e) {
            Logger::error('Strategy analysis persistence skipped: ' . $e->getMessage());
        }

        return null;
    }

    private function isMissingApiKeyError(Throwable $e): bool
    {
        return str_contains(mb_strtolower($e->getMessage()), 'openai_api_key');
    }

    /**
     * @return array{awareness_level:string,summary:string,pain_points:array<int,string>,desires:array<int,string>,content_angles:array<int,string>,recommended_hooks:array<int,string>}
     */
    private function buildFallbackAnalysis(string $profile): array
    {
        $excerpt = mb_substr(trim(preg_replace('/\s+/', ' ', $profile) ?? ''), 0, 220);
        $awareness = 'Problem Aware';

        if (preg_match('/\b(compare|devis|tarif|prix|solution|outil|agence)\b/i', $profile) === 1) {
            $awareness = 'Solution Aware';
        } elseif (preg_match('/\burgent|immediat|rapidement|maintenant\b/i', $profile) === 1) {
            $awareness = 'Most Aware';
        }

        return [
            'awareness_level' => $awareness,
            'summary' => 'Analyse de secours basée sur le texte fourni: ' . ($excerpt !== '' ? $excerpt : 'profil non détaillé.'),
            'pain_points' => [
                'Manque de clarté sur la prochaine action commerciale.',
                'Frustration liée aux résultats irréguliers.',
                'Besoin d’un message simple et convaincant.',
            ],
            'desires' => [
                'Obtenir des prospects qualifiés de manière régulière.',
                'Gagner du temps sur la création de messages.',
                'Avoir un positionnement clair et différenciant.',
            ],
            'content_angles' => [
                'Étude de cas avant/après avec résultats concrets.',
                'Checklist actionnable en 3 étapes.',
                'Démystification des erreurs fréquentes du marché.',
            ],
            'recommended_hooks' => [
                'Vous avez des prospects, mais peu de rendez-vous ?',
                'Ce levier simple peut améliorer vos réponses en 7 jours.',
                'Arrêtez ce réflexe qui affaiblit vos messages commerciaux.',
                'La méthode courte pour clarifier votre offre dès aujourd’hui.',
                'Comment passer d’un profil flou à un message qui convertit.',
            ],
        ];
    }
}
