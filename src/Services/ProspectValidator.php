<?php

declare(strict_types=1);

namespace App\Services;

final class ProspectValidator
{
    private const CANAUX_PRIORITAIRES = ['appel', 'email', 'sms', 'whatsapp'];
    private const NIVEAUX_PRIORITE = ['faible', 'moyen', 'eleve'];

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

        $canalPrioritaire = trim((string) ($data['canal_prioritaire'] ?? ''));
        if ($canalPrioritaire !== '' && !in_array($canalPrioritaire, self::CANAUX_PRIORITAIRES, true)) {
            $errors[] = 'Le canal prioritaire est invalide.';
        }

        $niveauPriorite = trim((string) ($data['niveau_priorite'] ?? 'moyen'));
        if (!in_array($niveauPriorite, self::NIVEAUX_PRIORITE, true)) {
            $errors[] = 'Le niveau de priorité est invalide.';
        }

        $dateProchaineAction = trim((string) ($data['date_prochaine_action'] ?? ''));
        if ($dateProchaineAction !== '') {
            $parsedDate = \DateTimeImmutable::createFromFormat('Y-m-d', $dateProchaineAction);
            $dateErrors = \DateTimeImmutable::getLastErrors();
            $hasDateErrors = $dateErrors !== false && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0);
            if ($parsedDate === false || $hasDateErrors) {
                $errors[] = 'La date de prochaine action doit être au format AAAA-MM-JJ.';
            }
        }

        return $errors;
    }

    public function normalize(array $data): array
    {
        $firstName = trim((string) ($data['first_name'] ?? ''));
        $lastName = trim((string) ($data['last_name'] ?? ''));

        $niveauPriorite = trim((string) ($data['niveau_priorite'] ?? 'moyen'));
        if (!in_array($niveauPriorite, self::NIVEAUX_PRIORITE, true)) {
            $niveauPriorite = 'moyen';
        }

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
            'objectif_contact' => trim((string) ($data['objectif_contact'] ?? '')),
            'prochaine_action' => trim((string) ($data['prochaine_action'] ?? '')),
            'date_prochaine_action' => trim((string) ($data['date_prochaine_action'] ?? '')) ?: null,
            'canal_prioritaire' => trim((string) ($data['canal_prioritaire'] ?? '')) ?: null,
            'niveau_priorite' => $niveauPriorite,
            'blocages' => trim((string) ($data['blocages'] ?? '')),
        ];
    }
}
