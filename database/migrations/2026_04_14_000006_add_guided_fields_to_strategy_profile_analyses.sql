ALTER TABLE strategy_profile_analyses
  ADD COLUMN objective VARCHAR(80) NOT NULL DEFAULT '' AFTER profile_text,
  ADD COLUMN persona_group VARCHAR(80) NOT NULL DEFAULT '' AFTER objective,
  ADD COLUMN persona_subtype VARCHAR(80) NOT NULL DEFAULT '' AFTER persona_group,
  ADD COLUMN offer_type VARCHAR(80) NOT NULL DEFAULT '' AFTER persona_subtype,
  ADD COLUMN maturity_level VARCHAR(80) NOT NULL DEFAULT '' AFTER offer_type,
  ADD COLUMN contact_intention VARCHAR(80) NOT NULL DEFAULT '' AFTER maturity_level;

CREATE INDEX idx_strategy_objective ON strategy_profile_analyses (objective);
CREATE INDEX idx_strategy_persona_group ON strategy_profile_analyses (persona_group);
