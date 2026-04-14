<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\StrategicFoundationModel;

final class StrategicFoundationContextService
{
    private StrategicFoundationModel $model;

    public function __construct()
    {
        $this->model = new StrategicFoundationModel();
    }

    public function getForUser(int $userId): array
    {
        return $this->model->findByUserId($userId) ?? [];
    }

    public function completionStats(array $foundation): array
    {
        $required = $this->model->completionFields();
        $filled = 0;

        foreach ($required as $field) {
            if (trim((string) ($foundation[$field] ?? '')) !== '') {
                $filled++;
            }
        }

        return [
            'required_total' => count($required),
            'filled_total' => $filled,
            'percent' => count($required) > 0 ? (int) round(($filled / count($required)) * 100) : 0,
            'is_complete' => $filled === count($required),
        ];
    }

    public function toPromptContext(array $foundation): string
    {
        if ($foundation === []) {
            return '';
        }

        $lines = [
            'Business: ' . ($foundation['business_name'] ?? ''),
            'Positioning: ' . ($foundation['who_i_help'] ?? ''),
            'Problem solved: ' . ($foundation['main_problem_solved'] ?? ''),
            'Differentiator: ' . ($foundation['differentiator'] ?? ''),
            'Promise: ' . ($foundation['core_promise'] ?? ''),
            'Offer: ' . ($foundation['offer_name'] ?? '') . ' - ' . ($foundation['offer_content'] ?? ''),
            'Objections: ' . ($foundation['offer_common_objections'] ?? ''),
            'Answers: ' . ($foundation['offer_objection_answers'] ?? ''),
            'Tone: ' . ($foundation['communication_tone'] ?? ''),
            'Primary CTA: ' . ($foundation['production_main_cta'] ?? ''),
        ];

        return trim(implode("\n", array_filter(array_map('trim', $lines), static fn (string $line): bool => $line !== '')));
    }

    public function quickSummary(array $foundation): array
    {
        return [
            'business_name' => (string) ($foundation['business_name'] ?? ''),
            'promise' => (string) ($foundation['short_promise_phrase'] ?? $foundation['core_promise'] ?? ''),
            'offer_name' => (string) ($foundation['offer_name'] ?? ''),
            'tone' => (string) ($foundation['communication_tone'] ?? ''),
            'cta' => (string) ($foundation['production_main_cta'] ?? ''),
        ];
    }
}
