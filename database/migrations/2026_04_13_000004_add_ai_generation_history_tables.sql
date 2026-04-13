-- Historique minimal IA: analyses, contenus, messages (brouillons)

CREATE TABLE IF NOT EXISTS content_generation_drafts (
  id                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id            INT UNSIGNED NOT NULL,
  analysis_id        INT UNSIGNED NOT NULL,
  content_type       ENUM('post','email','message_court') NOT NULL,
  channel            VARCHAR(80) NOT NULL,
  objective          VARCHAR(120) NOT NULL,
  tone               VARCHAR(80) NOT NULL,
  generated_content  TEXT NOT NULL,
  variant_label      VARCHAR(120) NOT NULL DEFAULT 'Variante 1',
  created_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_content_drafts_user_id (user_id),
  KEY idx_content_drafts_analysis_id (analysis_id),
  KEY idx_content_drafts_created_at (created_at),
  CONSTRAINT fk_content_drafts_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_content_drafts_analysis FOREIGN KEY (analysis_id) REFERENCES strategy_profile_analyses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ai_message_drafts (
  id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id          INT UNSIGNED NOT NULL,
  analysis_id      INT UNSIGNED NOT NULL,
  message_type     VARCHAR(80) NOT NULL,
  channel          VARCHAR(80) NOT NULL,
  message_text     TEXT NOT NULL,
  created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_message_drafts_user_id (user_id),
  KEY idx_message_drafts_analysis_id (analysis_id),
  KEY idx_message_drafts_created_at (created_at),
  CONSTRAINT fk_message_drafts_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_message_drafts_analysis FOREIGN KEY (analysis_id) REFERENCES strategy_profile_analyses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
