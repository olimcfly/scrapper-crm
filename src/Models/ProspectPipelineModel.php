<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

final class ProspectPipelineModel
{
    private PDO $db;
    private ?bool $areStagesAvailable = null;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function ensureForProspect(int $prospectId): void
    {
        if (!$this->areStagesAvailable()) {
            return;
        }

        $stmt = $this->db->prepare('SELECT id FROM prospect_pipeline WHERE prospect_id = :prospect_id LIMIT 1');
        $stmt->execute(['prospect_id' => $prospectId]);
        if ($stmt->fetch()) {
            return;
        }

        $stageStmt = $this->db->query('SELECT id FROM pipeline_stages ORDER BY position ASC LIMIT 1');
        $firstStageId = (int) ($stageStmt->fetchColumn() ?: 0);

        if ($firstStageId <= 0) {
            return;
        }

        $insert = $this->db->prepare('INSERT INTO prospect_pipeline (prospect_id, stage_id, last_action, next_action, status, updated_at) VALUES (:prospect_id, :stage_id, :last_action, :next_action, :status, NOW())');
        $insert->execute([
            'prospect_id' => $prospectId,
            'stage_id' => $firstStageId,
            'last_action' => 'Prospect ajouté',
            'next_action' => 'Initier une interaction',
            'status' => 'active',
        ]);
    }

    public function byProspect(int $prospectId): ?array
    {
        if (!$this->areStagesAvailable()) {
            return null;
        }

        $this->ensureForProspect($prospectId);

        $stmt = $this->db->prepare('SELECT pp.*, ps.name AS stage_name, ps.position AS stage_position
            FROM prospect_pipeline pp
            INNER JOIN pipeline_stages ps ON ps.id = pp.stage_id
            WHERE pp.prospect_id = :prospect_id
            LIMIT 1');
        $stmt->execute(['prospect_id' => $prospectId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function board(): array
    {
        if (!$this->areStagesAvailable()) {
            return [];
        }

        $this->db->exec(
            "INSERT INTO prospect_pipeline (prospect_id, stage_id, last_action, next_action, status, updated_at)
             SELECT p.id, 1, 'Prospect ajouté', 'Initier une interaction', 'active', NOW()
             FROM prospects p
             LEFT JOIN prospect_pipeline pp ON pp.prospect_id = p.id
             WHERE pp.id IS NULL"
        );

        $sql = 'SELECT pp.id, pp.prospect_id, pp.stage_id, pp.last_action, pp.next_action, pp.status, pp.updated_at,
                       ps.name AS stage_name, ps.position,
                       p.full_name, p.activity, p.instagram_url, p.linkedin_url, p.facebook_url, p.tiktok_url,
                       p.objectif_contact, p.prochaine_action
                FROM prospect_pipeline pp
                INNER JOIN pipeline_stages ps ON ps.id = pp.stage_id
                INNER JOIN prospects p ON p.id = pp.prospect_id
                ORDER BY ps.position ASC, pp.updated_at DESC';

        return $this->db->query($sql)->fetchAll();
    }

    public function updateStage(int $prospectId, int $stageId): bool
    {
        if (!$this->areStagesAvailable()) {
            return false;
        }

        $this->ensureForProspect($prospectId);

        $stmt = $this->db->prepare('UPDATE prospect_pipeline SET stage_id = :stage_id, updated_at = NOW() WHERE prospect_id = :prospect_id');
        return $stmt->execute(['prospect_id' => $prospectId, 'stage_id' => $stageId]);
    }

    public function updateNextAction(int $prospectId, string $lastAction, string $nextAction): bool
    {
        if (!$this->areStagesAvailable()) {
            return false;
        }

        $this->ensureForProspect($prospectId);

        $stmt = $this->db->prepare('UPDATE prospect_pipeline SET last_action = :last_action, next_action = :next_action, updated_at = NOW() WHERE prospect_id = :prospect_id');

        return $stmt->execute([
            'prospect_id' => $prospectId,
            'last_action' => $lastAction,
            'next_action' => $nextAction,
        ]);
    }

    public function areStagesAvailable(): bool
    {
        if ($this->areStagesAvailable !== null) {
            return $this->areStagesAvailable;
        }

        try {
            $stmt = $this->db->query('SHOW TABLES LIKE \'pipeline_stages\'');
            $this->areStagesAvailable = (bool) $stmt->fetchColumn();
        } catch (PDOException) {
            $this->areStagesAvailable = false;
        }

        return $this->areStagesAvailable;
    }
}
