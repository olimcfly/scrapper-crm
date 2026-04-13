-- ============================================================
-- CRM MVP — Schéma complet
-- Compatible MySQL 8+ / MariaDB 10.6+
-- Charset  : utf8mb4 / utf8mb4_unicode_ci
-- Généré   : 2026-04-12
-- ============================================================
-- Import : mysql -u <user> -p <database> < schema.sql
--          ou via phpMyAdmin > Importer
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- DROP (ordre inverse des clés étrangères)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS strategy_profile_analyses;
DROP TABLE IF EXISTS login_tokens;
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS prospect_pipeline;
DROP TABLE IF EXISTS pipeline_stages;
DROP TABLE IF EXISTS prospect_tag;
DROP TABLE IF EXISTS prospect_events;
DROP TABLE IF EXISTS prospect_notes;
DROP TABLE IF EXISTS generated_contents;
DROP TABLE IF EXISTS prospects;
DROP TABLE IF EXISTS tags;
DROP TABLE IF EXISTS sources;
DROP TABLE IF EXISTS prospect_statuses;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- TABLE : users
-- Authentification + contrôle d'accès.
-- Le code lit : first_name, last_name, email, password,
--               is_active, last_login_at  (UserModel + Auth)
-- ============================================================
CREATE TABLE users (
  id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  first_name    VARCHAR(120)  NOT NULL,
  last_name     VARCHAR(120)  NOT NULL DEFAULT '',
  email         VARCHAR(190)  NOT NULL,
  password      VARCHAR(255)  NOT NULL,          -- hash bcrypt
  role          VARCHAR(50)   NOT NULL DEFAULT 'user',  -- 'admin' | 'user'
  is_active     TINYINT(1)    NOT NULL DEFAULT 1,
  last_login_at DATETIME               DEFAULT NULL,
  created_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email    (email),
  KEY          idx_users_role      (role),
  KEY          idx_users_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : login_tokens
-- Codes OTP à usage unique pour la connexion sans mot de passe.
-- TTL : 15 minutes. Max 5 tentatives par token.
-- ============================================================
CREATE TABLE login_tokens (
  id         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  email      VARCHAR(190)  NOT NULL,
  token_hash VARCHAR(64)   NOT NULL,           -- SHA-256 du code à 6 chiffres
  expires_at DATETIME      NOT NULL,
  attempts   TINYINT       NOT NULL DEFAULT 0, -- invalidé après 5 tentatives
  used_at    DATETIME               DEFAULT NULL,
  created_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_login_tokens_email   (email),
  KEY idx_login_tokens_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : prospect_statuses
-- Référentiel des étapes du pipeline commercial.
-- Trié par sort_order dans ProspectStatusModel::all()
-- ============================================================
CREATE TABLE prospect_statuses (
  id         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  name       VARCHAR(100)  NOT NULL,
  sort_order INT           NOT NULL DEFAULT 0,
  created_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : sources
-- Canaux d'acquisition des prospects.
-- ============================================================
CREATE TABLE sources (
  id         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  name       VARCHAR(120)  NOT NULL,
  created_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : tags
-- Labels libres pour catégoriser les prospects.
-- ============================================================
CREATE TABLE tags (
  id         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  name       VARCHAR(120)  NOT NULL,
  created_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : prospects
-- Table centrale. Fiche complète d'un prospect.
--
-- Notes :
--   full_name  = CONCAT_WS(' ', first_name, last_name)
--                maintenu par ProspectValidator::normalize()
--                (pas de colonne GENERATED pour garder la compat PHP)
--
--   Bloc résaux sociaux (instagram_url … tiktok_url) :
--                présents en DB et dans les requêtes INSERT/UPDATE,
--                mais absents du formulaire HTML actuel — à ajouter
--                au template si on veut les saisir via l'UI.
-- ============================================================
CREATE TABLE prospects (
  id                    INT UNSIGNED                           NOT NULL AUTO_INCREMENT,
  first_name            VARCHAR(120)                          NOT NULL,
  last_name             VARCHAR(120)                          NOT NULL,
  full_name             VARCHAR(255)                          NOT NULL DEFAULT '',
  business_name         VARCHAR(190)                          NOT NULL DEFAULT '',
  activity              VARCHAR(190)                          NOT NULL DEFAULT '',
  city                  VARCHAR(120)                          NOT NULL DEFAULT '',
  country               VARCHAR(120)                          NOT NULL DEFAULT '',
  website               VARCHAR(255)                          NOT NULL DEFAULT '',
  professional_email    VARCHAR(190)                          NOT NULL DEFAULT '',
  professional_phone    VARCHAR(80)                           NOT NULL DEFAULT '',
  -- Réseaux sociaux (gérés par le code, non affichés dans le formulaire actuel)
  instagram_url         VARCHAR(255)                          NOT NULL DEFAULT '',
  facebook_url          VARCHAR(255)                          NOT NULL DEFAULT '',
  linkedin_url          VARCHAR(255)                          NOT NULL DEFAULT '',
  tiktok_url            VARCHAR(255)                          NOT NULL DEFAULT '',
  -- Relations
  source_id             INT UNSIGNED                                   DEFAULT NULL,
  status_id             INT UNSIGNED                                   DEFAULT NULL,
  -- Scoring
  score                 INT                                   NOT NULL DEFAULT 0,
  -- Notes libres (champ de saisie rapide à la création)
  notes_summary         TEXT                                           DEFAULT NULL,
  -- Bloc stratégie (Sprint 2)
  objectif_contact      VARCHAR(255)                          NOT NULL DEFAULT '',
  prochaine_action      VARCHAR(255)                          NOT NULL DEFAULT '',
  date_prochaine_action DATE                                           DEFAULT NULL,
  canal_prioritaire     ENUM('appel','email','sms','whatsapp')         DEFAULT NULL,
  niveau_priorite       ENUM('faible','moyen','eleve')        NOT NULL DEFAULT 'moyen',
  blocages              TEXT                                           DEFAULT NULL,
  -- Audit
  created_at            DATETIME                              NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at            DATETIME                              NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_prospects_status_id         (status_id),
  KEY idx_prospects_source_id         (source_id),
  KEY idx_prospects_full_name         (full_name),
  KEY idx_prospects_city              (city),
  KEY idx_prospects_professional_email (professional_email),
  KEY idx_prospects_updated_at        (updated_at),
  CONSTRAINT fk_prospects_source FOREIGN KEY (source_id) REFERENCES sources(id)           ON DELETE SET NULL,
  CONSTRAINT fk_prospects_status FOREIGN KEY (status_id) REFERENCES prospect_statuses(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : prospect_notes
-- Journal de notes manuelles par prospect.
-- Utilisé dans ProspectNoteModel et ProspectTimelineModel (UNION).
-- Colonnes lues : prospect_id, content, created_at
-- ============================================================
-- ============================================================
-- TABLE : generated_contents
-- Contenus générés par IA depuis l'analyse d'un prospect.
-- type: post | email | message_court
-- ============================================================
CREATE TABLE generated_contents (
  id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  prospect_id      INT UNSIGNED  NOT NULL,
  type             ENUM('post','email','message_court') NOT NULL,
  content          TEXT          NOT NULL,
  hook             VARCHAR(255)  NOT NULL DEFAULT '',
  angle            VARCHAR(255)  NOT NULL DEFAULT '',
  awareness_level  VARCHAR(120)  NOT NULL DEFAULT '',
  payload_json     JSON                   DEFAULT NULL,
  context_json     JSON                   DEFAULT NULL,
  created_at       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_generated_contents_prospect_id (prospect_id),
  KEY idx_generated_contents_type (type),
  CONSTRAINT fk_generated_contents_prospect FOREIGN KEY (prospect_id) REFERENCES prospects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE prospect_notes (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  prospect_id INT UNSIGNED  NOT NULL,
  content     TEXT          NOT NULL,
  created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_notes_prospect_id (prospect_id),
  CONSTRAINT fk_notes_prospect FOREIGN KEY (prospect_id) REFERENCES prospects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : prospect_events
-- Historique automatique des actions sur un prospect.
-- Alimente la timeline (UNION avec prospect_notes).
-- Colonnes lues  : prospect_id, event_type, details, created_at
-- Valeurs connues de event_type :
--   creation | update | status_change | deletion | note
-- ============================================================
CREATE TABLE prospect_events (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  prospect_id INT UNSIGNED  NOT NULL,
  event_type  VARCHAR(50)   NOT NULL,
  details     TEXT          NOT NULL,
  created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_events_prospect_id (prospect_id),
  KEY idx_events_created_at  (created_at),
  CONSTRAINT fk_events_prospect FOREIGN KEY (prospect_id) REFERENCES prospects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : prospect_tag  (liaison M:N prospects <-> tags)
-- Clé composite — pas d'id auto.
-- TagModel::syncProspectTags() fait DELETE + INSERT par prospect_id.
-- ============================================================
CREATE TABLE prospect_tag (
  prospect_id  INT UNSIGNED  NOT NULL,
  tag_id       INT UNSIGNED  NOT NULL,
  PRIMARY KEY (prospect_id, tag_id),
  KEY idx_pt_tag_id (tag_id),
  CONSTRAINT fk_pt_prospect FOREIGN KEY (prospect_id) REFERENCES prospects(id) ON DELETE CASCADE,
  CONSTRAINT fk_pt_tag      FOREIGN KEY (tag_id)      REFERENCES tags(id)      ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- ============================================================
-- TABLE : strategy_profile_analyses
-- Historique des analyses IA générées depuis le module Stratégie.
-- ============================================================
CREATE TABLE strategy_profile_analyses (
  id                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id             INT UNSIGNED NOT NULL,
  profile_text        TEXT         NOT NULL,
  awareness_level     VARCHAR(120) NOT NULL DEFAULT '',
  summary             TEXT         NULL,
  pain_points_json    JSON         NULL,
  desires_json        JSON         NULL,
  content_angles_json JSON         NULL,
  hooks_json          JSON         NULL,
  created_at          DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_strategy_user_id (user_id),
  KEY idx_strategy_created_at (created_at),
  CONSTRAINT fk_strategy_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : pipeline_stages
-- Étapes canonique du pipeline de conversion.
-- ============================================================
CREATE TABLE pipeline_stages (
  id         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  name       VARCHAR(100)  NOT NULL,
  position   INT           NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uq_pipeline_stages_position (position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : prospect_pipeline
-- État actionnable d'un prospect dans le pipeline.
-- ============================================================
CREATE TABLE prospect_pipeline (
  id          INT UNSIGNED                         NOT NULL AUTO_INCREMENT,
  prospect_id INT UNSIGNED                         NOT NULL,
  stage_id    INT UNSIGNED                         NOT NULL,
  last_action VARCHAR(255)                                  DEFAULT NULL,
  next_action VARCHAR(255)                                  DEFAULT NULL,
  status      ENUM('active', 'won', 'lost')       NOT NULL DEFAULT 'active',
  updated_at  DATETIME                             NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_prospect_pipeline_prospect_id (prospect_id),
  KEY idx_prospect_pipeline_stage_id (stage_id),
  CONSTRAINT fk_prospect_pipeline_prospect FOREIGN KEY (prospect_id) REFERENCES prospects(id) ON DELETE CASCADE,
  CONSTRAINT fk_prospect_pipeline_stage FOREIGN KEY (stage_id) REFERENCES pipeline_stages(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : messages
-- Historique de conversation et notes rapides.
-- ============================================================
CREATE TABLE messages (
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

-- ============================================================
-- SEED — Données minimales nécessaires au fonctionnement
-- ============================================================

-- Statuts du pipeline (affichés dans les dropdowns et la liste)
INSERT INTO prospect_statuses (name, sort_order) VALUES
  ('Nouveau',     1),
  ('Qualifié',    2),
  ('Contacté',    3),
  ('Relance',     4),
  ('Rendez-vous', 5),
  ('Client',      6);

-- Sources d'acquisition (affichées dans les dropdowns)
INSERT INTO sources (name) VALUES
  ('Google Maps'),
  ('Doctolib'),
  ('LinkedIn'),
  ('Instagram'),
  ('Referral');

-- Utilisateurs — connexion par code OTP envoyé par email (pas de mot de passe)
-- Le champ password est volontairement invalide ('!DISABLED') pour ces comptes.
INSERT INTO users (first_name, last_name, email, password, role, is_active) VALUES
  ('Coralie', 'Montreuil', 'contact@coraliemontreuil.fr', '!DISABLED', 'user',  1),
  ('Admin',   'CRM',       'admin@coraliemontreuil.fr',   '!DISABLED', 'admin', 1);

-- Pipeline conversion orienté closing naturel
INSERT INTO pipeline_stages (name, position) VALUES
  ('Nouveau', 1),
  ('Interaction', 2),
  ('Conversation', 3),
  ('Opportunité', 4),
  ('Client', 5);

INSERT INTO prospect_pipeline (prospect_id, stage_id, last_action, next_action, status, updated_at)
SELECT p.id, 1, 'Prospect importé', 'Initier une interaction', 'active', NOW()
FROM prospects p;
