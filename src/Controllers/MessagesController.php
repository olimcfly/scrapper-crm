<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Models\AiMessageDraftModel;
use App\Services\AiMessageGenerator;
use App\Services\Auth;

final class MessagesController
{
    private AiMessageDraftModel $drafts;
    private Auth $auth;
    private AiMessageGenerator $generator;

    public function __construct()
    {
        $this->drafts = new AiMessageDraftModel();
        $this->auth = new Auth(Database::connection());
        $this->generator = new AiMessageGenerator();
    }

    public function index(Request $request): void
    {
        $query = $request->input();
        $analysis = Session::get('strategy_to_content_analysis', []);
        $analysisId = (int) Session::get('strategy_to_content_analysis_id', 0);
        $history = [];
        $draftText = '';
        $selectedType = 'dm';
        $selectedChannel = 'whatsapp';

        $user = $this->auth->user();
        if (is_array($user) && isset($user['id'])) {
            $history = $this->drafts->latestByUser((int) $user['id'], 20);
            $openDraftId = (int) ($query['draft_id'] ?? 0);
            if ($openDraftId > 0) {
                $draft = $this->drafts->findByIdForUser($openDraftId, (int) $user['id']);
                if (is_array($draft)) {
                    $draftText = (string) ($draft['message_text'] ?? '');
                    $selectedType = (string) ($draft['message_type'] ?? 'dm');
                    $selectedChannel = (string) ($draft['channel'] ?? 'whatsapp');
                    $analysisId = (int) ($draft['analysis_id'] ?? $analysisId);
                }
            }
        }

        View::render('messages/index', [
            'title' => 'Messages IA',
            'analysis' => is_array($analysis) ? $analysis : [],
            'analysisId' => $analysisId,
            'draftText' => $draftText,
            'selectedType' => $selectedType,
            'selectedChannel' => $selectedChannel,
            'history' => $history,
            'successMessage' => Session::consumeFlash('success'),
            'warningMessage' => Session::consumeFlash('warning'),
        ]);
    }

    public function generate(Request $request): void
    {
        $input = $request->input();
        if (!Csrf::verify((string) ($input['_csrf'] ?? ''))) {
            Session::flash('warning', 'Session expirée. Rechargez la page.');
            Response::redirect('/messages-ia');
            return;
        }

        $analysis = Session::get('strategy_to_content_analysis', []);
        $analysisId = (int) Session::get('strategy_to_content_analysis_id', 0);
        if (!is_array($analysis) || $analysis === [] || $analysisId <= 0) {
            Session::flash('warning', 'Analyse requise avant génération de message.');
            Response::redirect('/strategie');
            return;
        }

        $type = trim((string) ($input['message_type'] ?? 'dm'));
        $channel = trim((string) ($input['channel'] ?? 'whatsapp'));
        if (!in_array($type, ['dm', 'relance', 'reponse'], true)) {
            $type = 'dm';
        }

        $text = $this->generator->generate($analysis, ['channel' => $channel, 'message_type' => $type]);

        $user = $this->auth->user();
        if (is_array($user) && isset($user['id'])) {
            $this->drafts->create([
                'user_id' => (int) $user['id'],
                'analysis_id' => $analysisId,
                'message_type' => $type,
                'channel' => $channel,
                'message_text' => $text,
            ]);
        }

        Session::flash('success', 'Message généré et sauvegardé.');
        Response::redirect('/messages-ia');
    }

    public function duplicateDraft(Request $request): void
    {
        $input = $request->input();
        if (!Csrf::verify((string) ($input['_csrf'] ?? ''))) {
            Session::flash('warning', 'Session expirée. Rechargez la page.');
            Response::redirect('/messages-ia');
            return;
        }

        $draftId = (int) ($input['draft_id'] ?? 0);
        $user = $this->auth->user();
        if (!is_array($user) || !isset($user['id']) || $draftId <= 0) {
            Session::flash('warning', 'Duplication impossible.');
            Response::redirect('/messages-ia');
            return;
        }

        $draft = $this->drafts->findByIdForUser($draftId, (int) $user['id']);
        if (!is_array($draft)) {
            Session::flash('warning', 'Brouillon introuvable.');
            Response::redirect('/messages-ia');
            return;
        }

        $this->drafts->create([
            'user_id' => (int) $user['id'],
            'analysis_id' => (int) $draft['analysis_id'],
            'message_type' => (string) $draft['message_type'],
            'channel' => (string) $draft['channel'],
            'message_text' => (string) $draft['message_text'],
        ]);

        Session::flash('success', 'Brouillon message dupliqué.');
        Response::redirect('/messages-ia');
    }
}
