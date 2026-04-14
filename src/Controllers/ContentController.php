<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Models\ContentDraftModel;
use App\Services\Auth;
use App\Services\ProspectContentGenerator;
use App\Services\StrategicFoundationContextService;

final class ContentController
{
    private const SESSION_KEY_ANALYSIS = 'strategy_to_content_analysis';
    private const SESSION_KEY_ANALYSIS_ID = 'strategy_to_content_analysis_id';
    private const SESSION_KEY_GENERATED = 'strategy_to_content_generated';
    private const SESSION_KEY_OPTIONS = 'strategy_to_content_options';

    private ProspectContentGenerator $generator;
    private ContentDraftModel $drafts;
    private Auth $auth;
    private StrategicFoundationContextService $foundationContext;

    public function __construct()
    {
        $this->generator = new ProspectContentGenerator();
        $this->drafts = new ContentDraftModel();
        $this->auth = new Auth(Database::connection());
        $this->foundationContext = new StrategicFoundationContextService();
    }

    public function index(Request $request): void
    {
        $query = $request->input();
        $analysis = Session::get(self::SESSION_KEY_ANALYSIS, []);
        $generated = Session::get(self::SESSION_KEY_GENERATED, null);
        $options = Session::get(self::SESSION_KEY_OPTIONS, $this->defaultOptions());

        $user = $this->auth->user();
        $history = [];
        $foundationSummary = [];
        $foundationIncomplete = true;
        if (is_array($user) && isset($user['id'])) {
            $foundation = $this->foundationContext->getForUser((int) $user['id']);
            $foundationSummary = $this->foundationContext->quickSummary($foundation);
            $foundationIncomplete = !$this->foundationContext->completionStats($foundation)['is_complete'];
        }

        if (is_array($user) && isset($user['id'])) {
            $history = $this->drafts->latestByUser((int) $user['id'], 20);

            $openDraftId = (int) ($query['draft_id'] ?? 0);
            if ($openDraftId > 0) {
                $draft = $this->drafts->findByIdForUser($openDraftId, (int) $user['id']);
                if (is_array($draft)) {
                    $options = [
                        'content_type' => (string) ($draft['content_type'] ?? 'post'),
                        'channel' => (string) ($draft['channel'] ?? 'linkedin'),
                        'objective' => (string) ($draft['objective'] ?? 'attirer'),
                        'tone' => (string) ($draft['tone'] ?? 'simple'),
                        'length' => 'moyenne',
                        'framework' => '',
                        'focus_input' => '',
                        'guided_mode' => '0',
                    ];
                    $generated = [
                        'content' => (string) ($draft['generated_content'] ?? ''),
                        'meta' => [
                            'source' => 'historique',
                            'warning' => 'Brouillon historique rechargé.',
                        ],
                    ];
                    Session::put(self::SESSION_KEY_OPTIONS, $options);
                    Session::put(self::SESSION_KEY_GENERATED, $generated);
                }
            }
        }

        View::render('content/index', [
            'title' => 'Studio de Contenu IA',
            'analysis' => is_array($analysis) ? $analysis : [],
            'generated' => is_array($generated) ? $generated : null,
            'options' => is_array($options) ? $options : $this->defaultOptions(),
            'history' => $history,
            'warningMessage' => Session::consumeFlash('warning'),
            'successMessage' => Session::consumeFlash('success'),
            'foundationSummary' => $foundationSummary,
            'foundationIncomplete' => $foundationIncomplete,
        ]);
    }

    public function generate(Request $request): void
    {
        if (!Csrf::verify((string) ($request->input()['_csrf'] ?? ''))) {
            Session::flash('warning', 'Session expirée. Rechargez la page.');
            Response::redirect('/contenu');
            return;
        }

        $analysis = Session::get(self::SESSION_KEY_ANALYSIS, []);
        if (!is_array($analysis) || $analysis === []) {
            Session::flash('warning', 'Aucune analyse disponible. Commencez depuis le module Stratégie.');
            Response::redirect('/strategie');
            return;
        }

        $options = $this->sanitizeOptions($request->input());
        Session::put(self::SESSION_KEY_OPTIONS, $options);

        $foundationContext = [];
        $user = $this->auth->user();
        if (is_array($user) && isset($user['id'])) {
            $foundationContext = $this->foundationContext->quickSummary($this->foundationContext->getForUser((int) $user['id']));
        }

        $generated = $this->generator->generate($analysis, $options, $foundationContext);
        Session::put(self::SESSION_KEY_GENERATED, $generated);

        $this->persistDraft($analysis, $options, $generated, $request->input());

        Session::flash('success', 'Contenu généré et sauvegardé dans l’historique.');
        Response::redirect('/contenu');
    }

    public function duplicateDraft(Request $request): void
    {
        if (!Csrf::verify((string) ($request->input()['_csrf'] ?? ''))) {
            Session::flash('warning', 'Session expirée. Rechargez la page.');
            Response::redirect('/contenu');
            return;
        }

        $draftId = (int) ($request->input()['draft_id'] ?? 0);
        $user = $this->auth->user();
        if (!is_array($user) || !isset($user['id']) || $draftId <= 0) {
            Session::flash('warning', 'Duplication impossible.');
            Response::redirect('/contenu');
            return;
        }

        $draft = $this->drafts->findByIdForUser($draftId, (int) $user['id']);
        if (!is_array($draft)) {
            Session::flash('warning', 'Brouillon introuvable.');
            Response::redirect('/contenu');
            return;
        }

        $this->drafts->create([
            'user_id' => (int) $user['id'],
            'analysis_id' => (int) $draft['analysis_id'],
            'content_type' => (string) $draft['content_type'],
            'channel' => (string) $draft['channel'],
            'objective' => (string) $draft['objective'],
            'tone' => (string) $draft['tone'],
            'generated_content' => (string) $draft['generated_content'],
            'variant_label' => 'Copie de ' . (string) ($draft['variant_label'] ?? 'Variante 1'),
        ]);

        Session::flash('success', 'Brouillon dupliqué.');
        Response::redirect('/contenu');
    }

    private function persistDraft(array $analysis, array $options, array $generated, array $input): void
    {
        $user = $this->auth->user();
        $analysisId = (int) Session::get(self::SESSION_KEY_ANALYSIS_ID, 0);

        if (!is_array($user) || !isset($user['id']) || $analysisId <= 0) {
            return;
        }

        $variant = trim((string) ($input['variant'] ?? ''));
        $framework = trim((string) ($input['framework'] ?? ''));
        if ($variant === '' && $framework !== '') {
            $variant = 'Méthode ' . $framework;
        }
        if ($variant === '') {
            $variant = 'Variante 1';
        }

        $this->drafts->create([
            'user_id' => (int) $user['id'],
            'analysis_id' => $analysisId,
            'content_type' => $options['content_type'],
            'channel' => $options['channel'],
            'objective' => $options['objective'],
            'tone' => $options['tone'],
            'generated_content' => (string) ($generated['content'] ?? ''),
            'variant_label' => $variant,
        ]);
    }

    /** @return array{content_type:string,channel:string,objective:string,tone:string,length:string,framework:string,focus_input:string,guided_mode:string} */
    private function defaultOptions(): array
    {
        return [
            'content_type' => 'post',
            'channel' => 'linkedin',
            'objective' => 'attirer',
            'tone' => 'simple',
            'length' => 'moyenne',
            'framework' => '',
            'focus_input' => '',
            'guided_mode' => '0',
        ];
    }

    /** @return array{content_type:string,channel:string,objective:string,tone:string,length:string,framework:string,focus_input:string,guided_mode:string} */
    private function sanitizeOptions(array $input): array
    {
        $defaults = $this->defaultOptions();
        $allowed = [
            'content_type' => ['post', 'email', 'message_court'],
            'channel' => ['facebook', 'instagram', 'linkedin', 'tiktok', 'email', 'whatsapp', 'sms'],
            'objective' => ['attirer', 'faire_reagir', 'rassurer', 'prendre_rendez_vous', 'relancer', 'convertir'],
            'tone' => ['simple', 'directe', 'rassurante', 'experte', 'chaleureuse'],
            'length' => ['courte', 'moyenne', 'longue'],
        ];

        $options = $defaults;
        foreach ($allowed as $field => $choices) {
            $value = trim((string) ($input[$field] ?? $defaults[$field]));
            if (in_array($value, $choices, true)) {
                $options[$field] = $value;
            }
        }

        $options['framework'] = trim((string) ($input['framework'] ?? ''));
        $options['focus_input'] = trim((string) ($input['focus_input'] ?? ''));
        $options['guided_mode'] = (($input['guided_mode'] ?? '') === '1') ? '1' : '0';

        return $options;
    }
}
