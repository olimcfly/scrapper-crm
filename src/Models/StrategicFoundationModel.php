<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class StrategicFoundationModel
{
    private PDO $db;

    /** @var array<int,string> */
    private array $fields = [
        'business_name','first_name','last_name','role_title','primary_city','service_area','target_client_type','primary_contacts','website_url','social_links',
        'who_i_help','main_problem_solved','target_persona','differentiator','why_choose_me','what_i_do_not_do','communication_tone',
        'core_promise','promised_transformation','core_benefits','expected_result','promise_timeline','short_promise_phrase','long_promise_version',
        'offer_name','offer_subtitle','offer_for_who','offer_problem','offer_content','offer_steps','offer_deliverables','offer_bonus','offer_guarantee','offer_price','offer_terms','offer_common_objections','offer_objection_answers','offer_primary_cta',
        'testimonials','results_obtained','experience_text','certifications','method_process','values_text','reassurance_elements',
        'send_email','email_signature','sender_display_name','primary_domain','desired_public_url','production_main_cta','booking_link','whatsapp_link','download_link','internal_strategy_notes',
    ];

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM strategic_foundations WHERE user_id = :user_id LIMIT 1');
        $stmt->execute(['user_id' => $userId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function upsertForUser(int $userId, array $input): void
    {
        $payload = [];
        foreach ($this->fields as $field) {
            $payload[$field] = trim((string) ($input[$field] ?? ''));
        }

        $setSql = implode(', ', array_map(static fn (string $field): string => $field . ' = :' . $field, $this->fields));

        $sql = 'INSERT INTO strategic_foundations (user_id, ' . implode(', ', $this->fields) . ', created_at, updated_at)
                VALUES (:user_id, ' . implode(', ', array_map(static fn (string $f): string => ':' . $f, $this->fields)) . ', NOW(), NOW())
                ON DUPLICATE KEY UPDATE ' . $setSql . ', updated_at = NOW()';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_merge(['user_id' => $userId], $payload));
    }

    /** @return array<int,string> */
    public function completionFields(): array
    {
        return [
            'business_name','role_title','target_client_type','who_i_help','main_problem_solved','differentiator',
            'core_promise','promised_transformation','offer_name','offer_problem','offer_content','offer_primary_cta',
            'testimonials','method_process','send_email','production_main_cta',
        ];
    }
}
