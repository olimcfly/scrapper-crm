export type ProspectStatus = 'new' | 'qualified' | 'contacted' | 'follow-up' | 'meeting' | 'client';

export type Page =
  | 'login'
  | 'dashboard'
  | 'prospects'
  | 'prospect-detail'
  | 'pipeline'
  | 'campaigns'
  | 'settings';

export interface Prospect {
  id: string;
  user_id: string;
  first_name: string;
  last_name: string;
  activity: string;
  city: string;
  email: string;
  phone: string;
  website: string;
  linkedin: string;
  instagram: string;
  facebook: string;
  tags: string[];
  score: number;
  status: ProspectStatus;
  source: string;
  created_at: string;
  updated_at: string;
}

export interface ProspectNote {
  id: string;
  prospect_id: string;
  user_id: string;
  content: string;
  created_at: string;
}

export interface ProspectActivity {
  id: string;
  prospect_id: string;
  user_id: string;
  type: string;
  description: string;
  created_at: string;
}

export interface Campaign {
  id: string;
  user_id: string;
  name: string;
  subject: string;
  body: string;
  status: 'draft' | 'sending' | 'sent' | 'paused';
  sent_count: number;
  opened_count: number;
  replied_count: number;
  created_at: string;
}
