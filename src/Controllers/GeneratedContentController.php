<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Models\GeneratedContentModel;
use App\Models\ProspectModel;
use App\Services\OpenAiContentGenerator;

final class GeneratedContentController
{
    private ProspectModel $prospects;
    private GeneratedContentModel $contents;
    private OpenAiContentGenerator $ai;

    public function __construct()
    {
        $this->prospects = new ProspectModel();
        $this->contents = new GeneratedContentModel();
        $this->ai = new OpenAiContentGenerator();
    }

    public function create(Request $request, int $prospectId): void
    {
        $prospect = $this->prospects->find($prospectId);
        if ($prospect === null) {
            View::render('errors/not-found', ['title' => 'Introuvable']);
            return;
        }

        if (!$this->hasAnalysis($prospect)) {
            Session::flash('warning', 'Analyse requise: remplissez au moins les champs stratégie avant de générer.');
            Response::redirect('/prospects/' . $prospectId);
            return;
        }

        $type = trim((string) ($request->input()['type'] ?? ''));
        $allowedTypes = ['post', 'video', 'story', 'dm'];
        if (!in_array($type, $allowedTypes, true)) {
            $type = '';
        }

        $generatedId = (int) ($request->input()['generated_id'] ?? 0);
        $generated = null;
        if ($generatedId > 0) {
            $generated = $this->contents->findById($generatedId, $prospectId);
        }

        $awareness = $this->awarenessLevel($prospect);
        View::render('prospects/generated_content', [
            'title' => 'Génération contenu IA',
            'prospect' => $prospect,
            'type' => $type,
            'generated' => $generated,
            'awarenessLevel' => $awareness,
            'recommendedType' => $this->recommendedType($awareness),
            'successMessage' => Session::consumeFlash('success'),
            'warningMessage' => Session::consumeFlash('warning'),
        ]);
    }

    public function generate(Request $request, int $prospectId): void
    {
        if (!Csrf::verify((string) ($request->input()['_csrf'] ?? ''))) {
            http_response_code(419);
            echo 'Requête expirée. Veuillez recharger la page.';
            return;
        }

        $prospect = $this->prospects->find($prospectId);
        if ($prospect === null || !$this->hasAnalysis($prospect)) {
            Session::flash('warning', 'Analyse requise avant toute génération.');
            Response::redirect('/prospects/' . $prospectId);
            return;
        }

        $type = trim((string) ($request->input()['type'] ?? ''));
        if (!in_array($type, ['post', 'video', 'story', 'dm'], true)) {
            Session::flash('warning', 'Type de contenu invalide.');
            Response::redirect('/prospects/' . $prospectId . '/generated-contents');
            return;
        }

        try {
            $awareness = $this->awarenessLevel($prospect);
            $context = [
                'full_name' => trim((string) ($prospect['full_name'] ?? '')),
                'activity' => trim((string) ($prospect['activity'] ?? '')),
                'objectif_contact' => trim((string) ($prospect['objectif_contact'] ?? '')),
                'blocages' => trim((string) ($prospect['blocages'] ?? '')),
                'desirs' => trim((string) ($prospect['notes_summary'] ?? '')),
                'interaction' => trim((string) ($request->input()['interaction'] ?? 'Like récent sur un post.')),
                'awareness_level' => $awareness,
            ];

            $generated = $this->ai->generate($type, $context);

            $id = $this->contents->create([
                'prospect_id' => $prospectId,
                'type' => $type,
                'content' => $generated['content'],
                'hook' => $generated['hook'],
                'angle' => $generated['angle'],
                'awareness_level' => $awareness,
            ]);

            Session::flash('success', 'Contenu généré. Prêt à être copié.');
            Response::redirect('/prospects/' . $prospectId . '/generated-contents?type=' . urlencode($type) . '&generated_id=' . $id);
        } catch (\Throwable $e) {
            Session::flash('warning', 'Impossible de générer le contenu: ' . $e->getMessage());
            Response::redirect('/prospects/' . $prospectId . '/generated-contents?type=' . urlencode($type));
        }
    }

    private function hasAnalysis(array $prospect): bool
    {
        return trim((string) ($prospect['objectif_contact'] ?? '')) !== ''
            || trim((string) ($prospect['blocages'] ?? '')) !== ''
            || trim((string) ($prospect['notes_summary'] ?? '')) !== '';
    }

    private function awarenessLevel(array $prospect): string
    {
        $status = mb_strtolower(trim((string) ($prospect['status_name'] ?? '')));

        if (in_array($status, ['nouveau'], true)) {
            return 'inconscient';
        }

        if (in_array($status, ['qualifié', 'contacté', 'relance'], true)) {
            return 'conscient problème';
        }

        return 'conscient solution';
    }

    private function recommendedType(string $awareness): string
    {
        if ($awareness === 'inconscient') {
            return 'Storytelling';
        }

        if ($awareness === 'conscient problème') {
            return 'Erreur / miroir';
        }

        return 'Comparaison';
    }
}
