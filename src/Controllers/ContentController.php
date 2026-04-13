<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Services\ProspectContentGenerator;

final class ContentController
{
    private const SESSION_KEY_ANALYSIS = 'strategy_to_content_analysis';
    private const SESSION_KEY_GENERATED = 'strategy_to_content_generated';
    private const SESSION_KEY_OPTIONS = 'strategy_to_content_options';

    private ProspectContentGenerator $generator;

    public function __construct()
    {
        $this->generator = new ProspectContentGenerator();
    }

    public function index(Request $request): void
    {
        unset($request);

        $analysis = Session::get(self::SESSION_KEY_ANALYSIS, []);
        $generated = Session::get(self::SESSION_KEY_GENERATED, null);
        $options = Session::get(self::SESSION_KEY_OPTIONS, $this->defaultOptions());

        View::render('content/index', [
            'title' => 'Contenu',
            'analysis' => is_array($analysis) ? $analysis : [],
            'generated' => is_array($generated) ? $generated : null,
            'options' => is_array($options) ? $options : $this->defaultOptions(),
            'warningMessage' => Session::consumeFlash('warning'),
            'successMessage' => Session::consumeFlash('success'),
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

        $generated = $this->generator->generate($analysis, $options);
        Session::put(self::SESSION_KEY_GENERATED, $generated);
        Session::flash('success', 'Contenu généré.');

        Response::redirect('/contenu');
    }

    /** @return array{content_type:string,channel:string,objective:string,tone:string,length:string} */
    private function defaultOptions(): array
    {
        return [
            'content_type' => 'post',
            'channel' => 'linkedin',
            'objective' => 'attirer',
            'tone' => 'simple',
            'length' => 'moyenne',
        ];
    }

    /** @return array{content_type:string,channel:string,objective:string,tone:string,length:string} */
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

        return $options;
    }
}
