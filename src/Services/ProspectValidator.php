<?php

declare(strict_types=1);

namespace App\Services;

final class ProspectValidator
{
    /** @return array<int, string> */
    public function validate(array $data): array
    {
        $errors = [];

        if (trim((string) ($data['first_name'] ?? '')) === '') {
            $errors[] = 'Le prénom est requis.';
        }

        if (trim((string) ($data['last_name'] ?? '')) === '') {
            $errors[] = 'Le nom est requis.';
        }

        $email = trim((string) ($data['professional_email'] ?? ''));
        if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = 'L’email professionnel est invalide.';
        }

        $score = (int) ($data['score'] ?? 0);
        if ($score < 0 || $score > 100) {
            $errors[] = 'Le score doit être compris entre 0 et 100.';
        }

        return $errors;
    }

    public function normalize(array $data): array
    {
        $firstName = trim((string) ($data['first_name'] ?? ''));
        $lastName = trim((string) ($data['last_name'] ?? ''));

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'full_name' => trim((string) ($data['full_name'] ?? ($firstName . ' ' . $lastName))),
            'business_name' => trim((string) ($data['business_name'] ?? '')),
            'activity' => trim((string) ($data['activity'] ?? '')),
            'city' => trim((string) ($data['city'] ?? '')),
            'country' => trim((string) ($data['country'] ?? '')),
            'website' => trim((string) ($data['website'] ?? '')),
            'professional_email' => trim((string) ($data['professional_email'] ?? '')),
            'professional_phone' => trim((string) ($data['professional_phone'] ?? '')),
            'instagram_url' => trim((string) ($data['instagram_url'] ?? '')),
            'facebook_url' => trim((string) ($data['facebook_url'] ?? '')),
            'linkedin_url' => trim((string) ($data['linkedin_url'] ?? '')),
            'tiktok_url' => trim((string) ($data['tiktok_url'] ?? '')),
            'source_id' => (int) ($data['source_id'] ?? 1),
            'status_id' => (int) ($data['status_id'] ?? 1),
            'score' => (int) ($data['score'] ?? 0),
            'notes_summary' => trim((string) ($data['notes_summary'] ?? '')),
        ];
    }
}
