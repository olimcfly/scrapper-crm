-- Migration MVP: création des tables manquantes pipeline_stages + messages
-- Compatible MySQL 8+ / MariaDB 10.6+

CREATE TABLE IF NOT EXISTS pipeline_stages (
  id         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  name       VARCHAR(100)  NOT NULL,
  position   INT           NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uq_pipeline_stages_position (position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO pipeline_stages (name, position)
SELECT seed.name, seed.position
FROM (
  SELECT 'Nouveau' AS name, 1 AS position
  UNION ALL SELECT 'Interaction', 2
  UNION ALL SELECT 'Conversation', 3
  UNION ALL SELECT 'Opportunité', 4
  UNION ALL SELECT 'Client', 5
) AS seed
WHERE NOT EXISTS (
  SELECT 1
  FROM pipeline_stages ps
  WHERE ps.position = seed.position
);

CREATE TABLE IF NOT EXISTS messages (
  id          INT UNSIGNED                            NOT NULL AUTO_INCREMENT,
  prospect_id INT UNSIGNED                            NOT NULL,
  content     TEXT                                    NOT NULL,
  type        ENUM('dm', 'reply', 'note')            NOT NULL DEFAULT 'note',
  direction   ENUM('sent', 'received')               NOT NULL DEFAULT 'sent',
  created_at  DATETIME                                NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_messages_prospect_id (prospect_id),
  KEY idx_messages_created_at  (created_at),
  CONSTRAINT fk_messages_prospect FOREIGN KEY (prospect_id) REFERENCES prospects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
