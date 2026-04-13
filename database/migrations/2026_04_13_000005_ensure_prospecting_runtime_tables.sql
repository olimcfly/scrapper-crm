-- Ensure prospecting runtime tables required by ProspectingController exist.
-- Safe to run multiple times.

CREATE TABLE IF NOT EXISTS connected_accounts (
  id                   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id              INT UNSIGNED NOT NULL,
  source               VARCHAR(80)  NOT NULL,
  external_account_id  VARCHAR(190) DEFAULT NULL,
  status               ENUM('connected','error','pending') NOT NULL DEFAULT 'pending',
  error_message        VARCHAR(255) DEFAULT NULL,
  connected_at         DATETIME DEFAULT NULL,
  created_at           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_connected_accounts_user_source (user_id, source),
  KEY idx_connected_accounts_user (user_id),
  KEY idx_connected_accounts_status (status),
  CONSTRAINT fk_connected_accounts_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS search_runs (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id        INT UNSIGNED NOT NULL,
  source         VARCHAR(80)  NOT NULL,
  search_type    VARCHAR(80)  NOT NULL,
  status         ENUM('running','success','failed') NOT NULL DEFAULT 'running',
  filters_json   JSON DEFAULT NULL,
  results_count  INT NOT NULL DEFAULT 0,
  error_message  VARCHAR(255) DEFAULT NULL,
  started_at     DATETIME DEFAULT NULL,
  ended_at       DATETIME DEFAULT NULL,
  created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_search_runs_user (user_id),
  KEY idx_search_runs_source_status (source, status),
  KEY idx_search_runs_created_at (created_at),
  CONSTRAINT fk_search_runs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS source_results (
  id                       BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  search_run_id            INT UNSIGNED NOT NULL,
  source                   VARCHAR(80) NOT NULL,
  normalized_payload_json  JSON NOT NULL,
  created_at               DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_source_results_run (search_run_id),
  KEY idx_source_results_source (source),
  CONSTRAINT fk_source_results_run FOREIGN KEY (search_run_id) REFERENCES search_runs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
