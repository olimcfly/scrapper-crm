<?php

declare(strict_types=1);

namespace App\Services;

final class ConversionSuggestionService
{
    /**
     * @param array<int, array<string,mixed>> $messages
     * @param array<string,mixed>|null $pipeline
     * @return array{next_action:string,message_suggestion:string,heat:string,prompt:string}
     */
    public function suggest(array $messages, ?array $pipeline): array
    {
        $stageName = strtolower((string) ($pipeline['stage_name'] ?? 'nouveau'));
        $lastSentDm = false;
        $conversationActive = false;

        foreach ($messages as $message) {
            $type = (string) ($message['type'] ?? '');
            $direction = (string) ($message['direction'] ?? '');
            if ($type === 'dm' && $direction === 'sent') {
                $lastSentDm = true;
            }
            if (($type === 'dm' || $type === 'reply') && $direction === 'received') {
                $conversationActive = true;
            }
        }

        $nextAction = 'Envoyer un message personnalisé.';
        $messageSuggestion = 'Salut ! J\'ai vu ton activité et j\'aimerais te partager une idée utile, tu veux ?';
        $heat = '❄️ froid';

        if ($stageName === 'interaction') {
            $nextAction = 'Envoyer un DM court de prise de contact.';
            $messageSuggestion = 'Merci pour ton interaction 👋 Je peux te proposer une idée simple pour gagner des leads cette semaine, tu veux ?';
            $heat = '🟡 tiède';
        }

        if ($lastSentDm) {
            $nextAction = 'Relancer avec une question ouverte orientée résultat.';
            $messageSuggestion = 'Petit follow-up : aujourd\'hui, ton plus gros blocage pour convertir c\'est plutôt la réponse aux DMs ou le manque de temps ?';
            $heat = '🟡 tiède';
        }

        if ($conversationActive || $stageName === 'conversation') {
            $nextAction = 'Poser une question de qualification pour détecter l\'intérêt réel.';
            $messageSuggestion = 'Si on débloque ce point, quel impact concret tu veux voir dans les 30 prochains jours ?';
            $heat = '🔥 chaud';
        }

        if ($stageName === 'opportunité' || str_contains($stageName, 'opportun')) {
            $nextAction = 'Proposer un call court pour closer naturellement.';
            $messageSuggestion = 'On peut faire un call de 15 min pour cadrer ton plan d\'action ? Je te montre exactement quoi lancer en premier.';
            $heat = '🔥 chaud';
        }

        if ($stageName === 'client') {
            $nextAction = 'Lancer l\'onboarding et demander une recommandation.';
            $messageSuggestion = 'Top, on démarre 🎯 Je t\'envoie le plan des 7 prochains jours et on cale le premier point.';
            $heat = '🔥 chaud';
        }

        $prompt = "Tu es un expert en closing naturel.\n\nAnalyse :\n- historique conversation\n- niveau de conscience\n- position dans le pipeline\n\nObjectif :\n→ proposer la meilleure prochaine action\n\nRéponds :\n{\nnext_action: \"\",\nmessage_suggestion: \"\"\n}";

        return [
            'next_action' => $nextAction,
            'message_suggestion' => $messageSuggestion,
            'heat' => $heat,
            'prompt' => $prompt,
        ];
    }
}
