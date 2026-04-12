-- CRM MVP schema (MySQL 8+)

CREATE TABLE IF NOT EXISTS prospect_statuses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS sources (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tags (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS prospects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(120) NOT NULL,
  last_name VARCHAR(120) NOT NULL,
  full_name VARCHAR(255) NOT NULL,
  business_name VARCHAR(190) DEFAULT '',
  activity VARCHAR(190) DEFAULT '',
  city VARCHAR(120) DEFAULT '',
  country VARCHAR(120) DEFAULT '',
  website VARCHAR(255) DEFAULT '',
  professional_email VARCHAR(190) DEFAULT '',
  professional_phone VARCHAR(80) DEFAULT '',
  instagram_url VARCHAR(255) DEFAULT '',
  facebook_url VARCHAR(255) DEFAULT '',
  linkedin_url VARCHAR(255) DEFAULT '',
  tiktok_url VARCHAR(255) DEFAULT '',
  source_id INT NULL,
  status_id INT NULL,
  score INT NOT NULL DEFAULT 0,
  notes_summary TEXT,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_prospects_status_id (status_id),
  INDEX idx_prospects_source_id (source_id),
  CONSTRAINT fk_prospects_source FOREIGN KEY (source_id) REFERENCES sources(id) ON DELETE SET NULL,
  CONSTRAINT fk_prospects_status FOREIGN KEY (status_id) REFERENCES prospect_statuses(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS prospect_notes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  prospect_id INT NOT NULL,
  content TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_notes_prospect_id (prospect_id),
  CONSTRAINT fk_notes_prospect FOREIGN KEY (prospect_id) REFERENCES prospects(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS prospect_tag (
  prospect_id INT NOT NULL,
  tag_id INT NOT NULL,
  PRIMARY KEY (prospect_id, tag_id),
  CONSTRAINT fk_prospect_tag_prospect FOREIGN KEY (prospect_id) REFERENCES prospects(id) ON DELETE CASCADE,
  CONSTRAINT fk_prospect_tag_tag FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(50) NOT NULL DEFAULT 'user',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_users_email (email),
  INDEX idx_users_role (role),
  INDEX idx_users_is_active (is_active)
);

INSERT INTO prospect_statuses (name, sort_order)
SELECT * FROM (
  SELECT 'Nouveau', 1 UNION ALL
  SELECT 'Qualifié', 2 UNION ALL
  SELECT 'Contacté', 3 UNION ALL
  SELECT 'Relance', 4 UNION ALL
  SELECT 'Rendez-vous', 5 UNION ALL
  SELECT 'Client', 6
) AS defaults
WHERE NOT EXISTS (SELECT 1 FROM prospect_statuses LIMIT 1);

INSERT INTO sources (name)
SELECT * FROM (
  SELECT 'Google Maps' UNION ALL
  SELECT 'Doctolib' UNION ALL
  SELECT 'LinkedIn' UNION ALL
  SELECT 'Instagram' UNION ALL
  SELECT 'Referral'
) AS defaults
WHERE NOT EXISTS (SELECT 1 FROM sources LIMIT 1);
