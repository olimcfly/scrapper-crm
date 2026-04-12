/*
  # Wellness Prospect CRM - Initial Schema

  ## Overview
  Core database schema for a wellness practitioner CRM application.

  ## Tables Created

  ### prospects
  Stores lead/prospect data for wellness practitioners.
  - id, user_id, first_name, last_name, activity, city
  - email, phone, website, linkedin, instagram, facebook
  - tags (array), score (0-100), status, source
  - created_at, updated_at

  ### prospect_notes
  Notes attached to a specific prospect by the user.
  - id, prospect_id, user_id, content, created_at

  ### prospect_activities
  Timeline of activities/events for a prospect.
  - id, prospect_id, user_id, type, description, created_at

  ### campaigns
  Email campaigns targeting prospects.
  - id, user_id, name, subject, body, status
  - sent_count, opened_count, replied_count, created_at

  ## Security
  - RLS enabled on all tables
  - Users can only access their own data
  - All policies check auth.uid() ownership
*/

-- Prospects table
CREATE TABLE IF NOT EXISTS prospects (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id uuid REFERENCES auth.users(id) ON DELETE CASCADE NOT NULL,
  first_name text NOT NULL DEFAULT '',
  last_name text NOT NULL DEFAULT '',
  activity text DEFAULT '',
  city text DEFAULT '',
  email text DEFAULT '',
  phone text DEFAULT '',
  website text DEFAULT '',
  linkedin text DEFAULT '',
  instagram text DEFAULT '',
  facebook text DEFAULT '',
  tags text[] DEFAULT '{}',
  score integer DEFAULT 0 CHECK (score >= 0 AND score <= 100),
  status text DEFAULT 'new' CHECK (status IN ('new', 'qualified', 'contacted', 'follow-up', 'meeting', 'client')),
  source text DEFAULT '',
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now()
);

ALTER TABLE prospects ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Users can view own prospects"
  ON prospects FOR SELECT
  TO authenticated
  USING (auth.uid() = user_id);

CREATE POLICY "Users can insert own prospects"
  ON prospects FOR INSERT
  TO authenticated
  WITH CHECK (auth.uid() = user_id);

CREATE POLICY "Users can update own prospects"
  ON prospects FOR UPDATE
  TO authenticated
  USING (auth.uid() = user_id)
  WITH CHECK (auth.uid() = user_id);

CREATE POLICY "Users can delete own prospects"
  ON prospects FOR DELETE
  TO authenticated
  USING (auth.uid() = user_id);

-- Prospect notes table
CREATE TABLE IF NOT EXISTS prospect_notes (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  prospect_id uuid REFERENCES prospects(id) ON DELETE CASCADE NOT NULL,
  user_id uuid REFERENCES auth.users(id) ON DELETE CASCADE NOT NULL,
  content text NOT NULL,
  created_at timestamptz DEFAULT now()
);

ALTER TABLE prospect_notes ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Users can view own prospect notes"
  ON prospect_notes FOR SELECT
  TO authenticated
  USING (auth.uid() = user_id);

CREATE POLICY "Users can insert own prospect notes"
  ON prospect_notes FOR INSERT
  TO authenticated
  WITH CHECK (auth.uid() = user_id);

CREATE POLICY "Users can delete own prospect notes"
  ON prospect_notes FOR DELETE
  TO authenticated
  USING (auth.uid() = user_id);

-- Prospect activities table
CREATE TABLE IF NOT EXISTS prospect_activities (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  prospect_id uuid REFERENCES prospects(id) ON DELETE CASCADE NOT NULL,
  user_id uuid REFERENCES auth.users(id) ON DELETE CASCADE NOT NULL,
  type text NOT NULL DEFAULT 'note',
  description text NOT NULL,
  created_at timestamptz DEFAULT now()
);

ALTER TABLE prospect_activities ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Users can view own prospect activities"
  ON prospect_activities FOR SELECT
  TO authenticated
  USING (auth.uid() = user_id);

CREATE POLICY "Users can insert own prospect activities"
  ON prospect_activities FOR INSERT
  TO authenticated
  WITH CHECK (auth.uid() = user_id);

-- Campaigns table
CREATE TABLE IF NOT EXISTS campaigns (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id uuid REFERENCES auth.users(id) ON DELETE CASCADE NOT NULL,
  name text NOT NULL,
  subject text DEFAULT '',
  body text DEFAULT '',
  status text DEFAULT 'draft' CHECK (status IN ('draft', 'sending', 'sent', 'paused')),
  sent_count integer DEFAULT 0,
  opened_count integer DEFAULT 0,
  replied_count integer DEFAULT 0,
  created_at timestamptz DEFAULT now()
);

ALTER TABLE campaigns ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Users can view own campaigns"
  ON campaigns FOR SELECT
  TO authenticated
  USING (auth.uid() = user_id);

CREATE POLICY "Users can insert own campaigns"
  ON campaigns FOR INSERT
  TO authenticated
  WITH CHECK (auth.uid() = user_id);

CREATE POLICY "Users can update own campaigns"
  ON campaigns FOR UPDATE
  TO authenticated
  USING (auth.uid() = user_id)
  WITH CHECK (auth.uid() = user_id);

CREATE POLICY "Users can delete own campaigns"
  ON campaigns FOR DELETE
  TO authenticated
  USING (auth.uid() = user_id);

-- Updated_at trigger function
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
  NEW.updated_at = now();
  RETURN NEW;
END;
$$ language 'plpgsql';

CREATE TRIGGER update_prospects_updated_at
  BEFORE UPDATE ON prospects
  FOR EACH ROW
  EXECUTE FUNCTION update_updated_at_column();

-- Indexes for performance
CREATE INDEX IF NOT EXISTS idx_prospects_user_id ON prospects(user_id);
CREATE INDEX IF NOT EXISTS idx_prospects_status ON prospects(status);
CREATE INDEX IF NOT EXISTS idx_prospect_notes_prospect_id ON prospect_notes(prospect_id);
CREATE INDEX IF NOT EXISTS idx_prospect_activities_prospect_id ON prospect_activities(prospect_id);
CREATE INDEX IF NOT EXISTS idx_campaigns_user_id ON campaigns(user_id);
