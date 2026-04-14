<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Models\PublicPageModel;
use App\Services\Auth;
use App\Services\StrategicFoundationContextService;

final class PublicPagesController
{
    private Auth $auth;
    private PublicPageModel $pages;
    private StrategicFoundationContextService $foundation;

    public function __construct()
    {
        $this->auth = new Auth(Database::connection());
        $this->pages = new PublicPageModel();
        $this->foundation = new StrategicFoundationContextService();
    }

    public function index(Request $request): void
    {
        $user = $this->auth->user();
        if (!is_array($user) || !isset($user['id'])) {
            Response::redirect('/login');
            return;
        }

        $query = $request->input();
        if (($query['action'] ?? '') === 'create') {
            $this->createFromFoundation((int) $user['id'], (string) ($query['type'] ?? 'offer'));
            Response::redirect('/pages-publiques');
            return;
        }

        $foundation = $this->foundation->getForUser((int) $user['id']);

        View::render('public_pages/index', [
            'title' => 'Pages publiques',
            'pages' => $this->pages->latestByUser((int) $user['id']),
            'foundationSummary' => $this->foundation->quickSummary($foundation),
            'completion' => $this->foundation->completionStats($foundation),
            'successMessage' => Session::consumeFlash('success'),
            'warningMessage' => Session::consumeFlash('warning'),
        ]);
    }

    public function edit(Request $request, int $id): void
    {
        unset($request);
        $user = $this->auth->user();
        if (!is_array($user) || !isset($user['id'])) {
            Response::redirect('/login');
            return;
        }

        $page = $this->pages->findByIdForUser($id, (int) $user['id']);
        if (!is_array($page)) {
            Session::flash('warning', 'Page introuvable.');
            Response::redirect('/pages-publiques');
            return;
        }

        View::render('public_pages/edit', [
            'title' => 'Modifier page publique',
            'page' => $page,
            'successMessage' => Session::consumeFlash('success'),
            'warningMessage' => Session::consumeFlash('warning'),
        ]);
    }

    public function update(Request $request, int $id): void
    {
        $input = $request->input();
        if (!Csrf::verify((string) ($input['_csrf'] ?? ''))) {
            Session::flash('warning', 'Session expirée. Rechargez la page.');
            Response::redirect('/pages-publiques/' . $id . '/edit');
            return;
        }

        $user = $this->auth->user();
        if (!is_array($user) || !isset($user['id'])) {
            Response::redirect('/login');
            return;
        }

        $slug = $this->normalizeSlug((string) ($input['slug'] ?? ''));
        if ($slug === '') {
            Session::flash('warning', 'Slug invalide.');
            Response::redirect('/pages-publiques/' . $id . '/edit');
            return;
        }

        if ($this->pages->slugExists($slug, $id)) {
            Session::flash('warning', 'Slug déjà utilisé.');
            Response::redirect('/pages-publiques/' . $id . '/edit');
            return;
        }

        $status = (($input['status'] ?? 'draft') === 'published') ? 'published' : 'draft';
        $type = in_array(($input['type'] ?? ''), ['offer', 'presentation', 'promise'], true) ? (string) $input['type'] : 'offer';

        $this->pages->updateForUser($id, (int) $user['id'], [
            'type' => $type,
            'title' => trim((string) ($input['title'] ?? 'Page publique')),
            'subtitle' => trim((string) ($input['subtitle'] ?? '')),
            'slug' => $slug,
            'status' => $status,
            'body_html' => trim((string) ($input['body_html'] ?? '')),
            'snapshot' => [],
        ]);

        Session::flash('success', 'Page publique mise à jour.');
        Response::redirect('/pages-publiques');
    }

    public function showPublic(Request $request, string $slug): void
    {
        unset($request);
        $page = $this->pages->findPublishedBySlug($slug);
        if (!is_array($page)) {
            http_response_code(404);
            View::render('errors/not-found', ['title' => 'Page introuvable']);
            return;
        }

        View::render('public_pages/show', [
            'title' => (string) ($page['title'] ?? 'Page publique'),
            'page' => $page,
        ]);
    }

    private function createFromFoundation(int $userId, string $type): void
    {
        $safeType = in_array($type, ['offer', 'presentation', 'promise'], true) ? $type : 'offer';
        $foundation = $this->foundation->getForUser($userId);
        if ($foundation === []) {
            Session::flash('warning', 'Complète ta Fondation stratégique avant de générer une page.');
            return;
        }

        $titleByType = [
            'offer' => (string) ($foundation['offer_name'] ?? 'Mon offre'),
            'presentation' => (string) ($foundation['business_name'] ?? 'Présentation'),
            'promise' => (string) ($foundation['short_promise_phrase'] ?? 'Promesse'),
        ];

        $title = trim($titleByType[$safeType] ?? 'Page publique');
        if ($title === '') {
            $title = 'Page publique';
        }

        $slugBase = $this->normalizeSlug($safeType . '-' . $title);
        $slug = $slugBase;
        $index = 2;
        while ($this->pages->slugExists($slug)) {
            $slug = $slugBase . '-' . $index;
            $index++;
        }

        $this->pages->create([
            'user_id' => $userId,
            'type' => $safeType,
            'title' => $title,
            'subtitle' => (string) ($foundation['core_promise'] ?? ''),
            'slug' => $slug,
            'status' => 'draft',
            'body_html' => $this->buildBodyFromFoundation($safeType, $foundation),
            'snapshot' => $this->foundation->quickSummary($foundation),
        ]);

        Session::flash('success', 'Page brouillon générée depuis ta Fondation stratégique.');
    }

    private function normalizeSlug(string $value): string
    {
        $slug = mb_strtolower(trim($value));
        $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug) ?? '';

        return trim($slug, '-');
    }

    private function buildBodyFromFoundation(string $type, array $f): string
    {
        $base = '<section><h2>Pour qui</h2><p>' . htmlspecialchars((string) ($f['offer_for_who'] ?? $f['target_persona'] ?? '')) . '</p></section>';
        $base .= '<section><h2>Problème traité</h2><p>' . htmlspecialchars((string) ($f['offer_problem'] ?? $f['main_problem_solved'] ?? '')) . '</p></section>';
        $base .= '<section><h2>Méthode</h2><p>' . nl2br(htmlspecialchars((string) ($f['method_process'] ?? $f['offer_steps'] ?? ''))) . '</p></section>';
        $base .= '<section><h2>Bénéfices</h2><p>' . nl2br(htmlspecialchars((string) ($f['core_benefits'] ?? ''))) . '</p></section>';
        $base .= '<section><h2>Preuves</h2><p>' . nl2br(htmlspecialchars((string) ($f['testimonials'] ?? $f['results_obtained'] ?? ''))) . '</p></section>';
        $base .= '<section><h2>Passer à l’action</h2><p>' . htmlspecialchars((string) ($f['production_main_cta'] ?? $f['offer_primary_cta'] ?? '')) . '</p></section>';

        if ($type === 'presentation') {
            return '<section><h2>Qui suis-je ?</h2><p>' . htmlspecialchars((string) (($f['first_name'] ?? '') . ' ' . ($f['last_name'] ?? '') . ' — ' . ($f['role_title'] ?? ''))) . '</p></section>' . $base;
        }

        if ($type === 'promise') {
            return '<section><h2>Promesse</h2><p>' . nl2br(htmlspecialchars((string) ($f['long_promise_version'] ?? $f['core_promise'] ?? ''))) . '</p></section>' . $base;
        }

        return $base;
    }
}
