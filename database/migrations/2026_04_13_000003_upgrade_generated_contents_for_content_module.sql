-- Upgrade generated_contents for reusable Content module
ALTER TABLE generated_contents
  MODIFY COLUMN type ENUM('post','email','message_court') NOT NULL,
  ADD COLUMN payload_json JSON NULL AFTER awareness_level,
  ADD COLUMN context_json JSON NULL AFTER payload_json;
