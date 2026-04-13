<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Services\OpenAiClient;
use Throwable;

final class MessagesIaController
{
    private OpenAiClient $openAiClient;

    /** @var array<int,string> */
    private array $messageTypes = [
        'premier_contact' => 'Premier contact',
        'relance_douce' => 'Relance douce',
        'reponse_objection' => 'Réponse à une objection',
        'reprise_conversation' => 'Reprise de conversation',
        'demande_rendez_vous' => 'Demande de rendez-vous',
    ];

    /** @var array<int,string> */
    private array $channels = [
        'whatsapp' => 'WhatsApp',
        'instagram_dm' => 'Instagram DM',
        'facebook_messenger' => 'Facebook Messenger',
        'linkedin' => 'LinkedIn',
        'email_court' => 'Email court',
    ];

    public function __construct()
    {
        $this->openAiClient = new OpenAiClient();
    }

    public function index(Request $request): void
    {
        $input = $request->input();

        $context = [
            'summary' => trim((string) ($input['summary'] ?? '')),
            'awareness_level' => trim((string) ($input['awareness_level'] ?? '')),
            'pain_points' => $this->normalizeListFromString((string) ($input['pain_points'] ?? '')),
            'main_desire' => trim((string) ($input['main_desire'] ?? '')),
            'recommended_tone' => trim((string) ($input['recommended_tone'] ?? '')),
            'hook_angle' => trim((string) ($input['hook_angle'] ?? '')),
        ];

        View::render('messages_ia/index', [
            'title' => 'Messages IA',
            'messageTypes' => $this->messageTypes,
            'channels' => $this->channels,
            'context' => $context,
            'generated' => null,
            'selectedType' => '',
            'selectedChannel' => '',
            'mode' => null,
            'successMessage' => Session::consumeFlash('success'),
            'warningMessage' => Session::consumeFlash('warning'),
        ]);
    }

    public function generate(Request $request): void
    {
        $input = $request->input();
        if (!Csrf::verify((string) ($input['_csrf'] ?? ''))) {
            Response::json(['error' => 'Session expirée.'], 419);
            return;
        }

        $selectedType = trim((string) ($input['message_type'] ?? ''));
        $selectedChannel = trim((string) ($input['channel'] ?? ''));

        if (!isset($this->messageTypes[$selectedType]) || !isset($this->channels[$selectedChannel])) {
            Session::flash('warning', 'Type de message ou canal invalide.');
            Response::redirect('/messages-ia');
            return;
        }

        $context = [
            'summary' => trim((string) ($input['summary'] ?? '')),
            'awareness_level' => trim((string) ($input['awareness_level'] ?? '')),
            'pain_points' => $this->normalizeListFromString((string) ($input['pain_points'] ?? '')),
            'main_desire' => trim((string) ($input['main_desire'] ?? '')),
            'recommended_tone' => trim((string) ($input['recommended_tone'] ?? '')),
            'hook_angle' => trim((string) ($input['hook_angle'] ?? '')),
        ];

        try {
            $generated = $this->openAiClient->generateMessageVariants($selectedType, $selectedChannel, $context);
            $mode = 'ia';
        } catch (Throwable $e) {
            $generated = $this->buildFallbackVariants($selectedType, $selectedChannel, $context);
            $mode = 'fallback';
            Session::flash('warning', 'IA indisponible: mode dégradé activé.');
        }

        View::render('messages_ia/index', [
            'title' => 'Messages IA',
            'messageTypes' => $this->messageTypes,
            'channels' => $this->channels,
            'context' => $context,
            'generated' => $generated,
            'selectedType' => $selectedType,
            'selectedChannel' => $selectedChannel,
            'mode' => $mode,
            'successMessage' => Session::consumeFlash('success'),
            'warningMessage' => Session::consumeFlash('warning'),
        ]);
    }

    /**
     * @return array<int,string>
     */
    private function normalizeListFromString(string $raw): array
    {
        $parts = array_map('trim', explode('||', $raw));
        return array_values(array_filter($parts, static fn (string $item): bool => $item !== ''));
    }

    /**
     * @return array<int,string>
     */
    private function buildFallbackVariants(string $messageType, string $channel, array $context): array
    {
        $intro = sprintf(
            '[%s · %s] %s',
            $this->channels[$channel] ?? $channel,
            $this->messageTypes[$messageType] ?? $messageType,
            $context['hook_angle'] !== '' ? $context['hook_angle'] : 'Message contextualisé'
        );

        $painPoint = $context['pain_points'][0] ?? 'vos objectifs de conversion';

        return [
            $intro . " — J’ai pensé à vous en voyant votre contexte. Votre priorité semble être: {$painPoint}. Ouvert à un échange rapide ?",
            $intro . " — Je vous contacte avec une idée simple pour avancer sur {$painPoint}. Si vous voulez, je vous partage un plan en 3 points.",
            $intro . " — On peut faire court: je vous propose une approche concrète, adaptée à votre niveau {$context['awareness_level']}. Partant ?",
        ];
    }
}
