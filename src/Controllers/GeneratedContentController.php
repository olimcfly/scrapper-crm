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
            Session::flash('warning', 'Analyse requise: renseignez stratégie (pain points, désirs, angle, hook) avant de générer.');
            Response::redirect('/prospects/' . $prospectId);
            return;
        }

        $type = trim((string) ($request->input()['type'] ?? ''));
        $allowedTypes = ['post', 'email', 'message_court'];
        if (!in_array($type, $allowedTypes, true)) {
            $type = '';
        }

        $generatedId = (int) ($request->input()['generated_id'] ?? 0);
        $generated = null;
        if ($generatedId > 0) {
            $generated = $this->contents->findById($generatedId, $prospectId);
        }

        $payload = $this->contents->decodePayload($generated);
        $contextUsed = $this->contents->decodeContext($generated);

        View::render('prospects/generated_content', [
            'title' => 'Génération contenu IA',
            'prospect' => $prospect,
            'type' => $type,
            'generated' => $generated,
            'payload' => $payload,
            'contextUsed' => $contextUsed,
            'awarenessLevel' => $this->awarenessLevel($prospect),
            'recommendedType' => $this->recommendedType($this->awarenessLevel($prospect)),
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
        if (!in_array($type, ['post', 'email', 'message_court'], true)) {
            Session::flash('warning', 'Type de contenu invalide.');
            Response::redirect('/prospects/' . $prospectId . '/generated-contents');
            return;
        }

        $context = $this->buildGenerationContext($prospect, $request->input(), $type);
        $generated = $this->ai->generate($type, $context);

        $primary = $generated['primary'] ?? [];
        $id = $this->contents->create([
            'prospect_id' => $prospectId,
            'type' => $type,
            'content' => trim((string) ($primary['body'] ?? '')),
            'hook' => trim((string) ($primary['title'] ?? $primary['opening'] ?? '')),
            'angle' => trim((string) ($context['angle'] ?? '')),
            'awareness_level' => $context['awareness_level'],
            'payload_json' => json_encode($generated, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'context_json' => json_encode($generated['context_used'] ?? $context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);

        if (($generated['source'] ?? '') === 'fallback') {
            Session::flash('warning', (string) ($generated['warning'] ?? 'Mode dégradé activé.'));
        } else {
            Session::flash('success', '3 brouillons générés. Prêts à être copiés ou réutilisés.');
        }

        Response::redirect('/prospects/' . $prospectId . '/generated-contents?type=' . urlencode($type) . '&generated_id=' . $id);
    }

    private function buildGenerationContext(array $prospect, array $input, string $type): array
    {
        $painPoints = $this->toStringList($input['pain_points'] ?? $prospect['blocages'] ?? '');
        $desires = $this->toStringList($input['desires'] ?? $prospect['notes_summary'] ?? '');

        return [
            'full_name' => trim((string) ($prospect['full_name'] ?? '')),
            'activity' => trim((string) ($prospect['activity'] ?? '')),
            'objectif_contact' => trim((string) ($prospect['objectif_contact'] ?? '')),
            'pain_points' => $painPoints,
            'pain_points_text' => implode(' | ', $painPoints),
            'desires' => $desires,
            'desires_text' => implode(' | ', $desires),
            'channel' => trim((string) ($input['channel'] ?? $input['interaction'] ?? 'contact précédent')),
            'awareness_level' => trim((string) ($input['awareness_level'] ?? $this->awarenessLevel($prospect))),
            'angle' => trim((string) ($input['angle'] ?? '')), 
            'hook' => trim((string) ($input['hook'] ?? '')),
            'content_type' => $type,
        ];
    }

    private function toStringList(mixed $value): array
    {
        if (is_array($value)) {
            $items = [];
            foreach ($value as $item) {
                $text = trim((string) $item);
                if ($text !== '') {
                    $items[] = $text;
                }
            }

            return $items;
        }

        $text = trim((string) $value);
        if ($text === '') {
            return [];
        }

        $parts = preg_split('/[\n,;|]+/', $text) ?: [];
        $clean = [];
        foreach ($parts as $part) {
            $item = trim((string) $part);
            if ($item !== '') {
                $clean[] = $item;
            }
        }

        return $clean;
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
            return 'Post pédagogique';
        }

        if ($awareness === 'conscient problème') {
            return 'Email utile';
        }

        return 'Message court contextualisé';
    }
}
