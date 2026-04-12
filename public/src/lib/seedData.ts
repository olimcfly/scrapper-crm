import { supabase } from './supabase';

const sampleProspects = [
  { first_name: 'Sophie', last_name: 'Martin', activity: 'Yoga Instructor', city: 'Paris', email: 'sophie.martin@yogaparis.fr', phone: '+33 6 12 34 56 78', website: 'https://yogaparis.fr', linkedin: 'https://linkedin.com/in/sophiemartin', source: 'Google Maps', score: 82, status: 'qualified', tags: ['yoga', 'studio'] },
  { first_name: 'Lucas', last_name: 'Bernard', activity: 'Nutritionist', city: 'Lyon', email: 'lucas.bernard@nutricoach.fr', phone: '+33 6 98 76 54 32', website: 'https://nutricoach-lyon.fr', linkedin: 'https://linkedin.com/in/lucasbernard', source: 'Doctolib', score: 75, status: 'contacted', tags: ['nutrition', 'health'] },
  { first_name: 'Camille', last_name: 'Dubois', activity: 'Life Coach', city: 'Bordeaux', email: 'camille.dubois@coachvie.fr', phone: '+33 6 55 44 33 22', website: 'https://camille-coach.fr', instagram: 'https://instagram.com/camille_coach', source: 'Instagram', score: 68, status: 'new', tags: ['coaching', 'mindset'] },
  { first_name: 'Antoine', last_name: 'Rousseau', activity: 'Physical Therapist', city: 'Marseille', email: 'antoine.rousseau@kine-marseille.fr', phone: '+33 6 11 22 33 44', website: 'https://kine-marseille.fr', linkedin: 'https://linkedin.com/in/antoinerousseau', source: 'Pages Jaunes', score: 91, status: 'meeting', tags: ['kinesiology', 'sports'] },
  { first_name: 'Emilie', last_name: 'Petit', activity: 'Meditation Teacher', city: 'Toulouse', email: 'emilie.petit@zen-toulouse.fr', phone: '+33 6 77 88 99 00', website: 'https://zen-toulouse.fr', instagram: 'https://instagram.com/emilie_zen', source: 'Google Maps', score: 60, status: 'new', tags: ['meditation', 'mindfulness'] },
  { first_name: 'Thomas', last_name: 'Leroy', activity: 'Osteopath', city: 'Nantes', email: 'thomas.leroy@osteo-nantes.fr', phone: '+33 6 33 44 55 66', website: 'https://osteo-nantes.fr', linkedin: 'https://linkedin.com/in/thomasleroy', source: 'Doctolib', score: 88, status: 'client', tags: ['osteopathy', 'wellness'] },
  { first_name: 'Marine', last_name: 'Simon', activity: 'Naturopath', city: 'Strasbourg', email: 'marine.simon@naturo-stras.fr', phone: '+33 6 22 33 44 55', website: 'https://naturotherapie-strasbourg.fr', facebook: 'https://facebook.com/marinesimon', source: 'Directory', score: 72, status: 'follow-up', tags: ['naturopathy', 'holistic'] },
  { first_name: 'Hugo', last_name: 'Moreau', activity: 'Personal Trainer', city: 'Nice', email: 'hugo.moreau@fitnice.fr', phone: '+33 6 44 55 66 77', website: 'https://fit-nice.fr', instagram: 'https://instagram.com/hugo_fit', source: 'Instagram', score: 55, status: 'new', tags: ['fitness', 'sport'] },
  { first_name: 'Léa', last_name: 'Laurent', activity: 'Psychologist', city: 'Lille', email: 'lea.laurent@psy-lille.fr', phone: '+33 6 66 77 88 99', website: 'https://psylille.fr', linkedin: 'https://linkedin.com/in/lealaurent', source: 'Pages Jaunes', score: 79, status: 'qualified', tags: ['psychology', 'therapy'] },
  { first_name: 'Mathieu', last_name: 'Garcia', activity: 'Acupuncturist', city: 'Montpellier', email: 'mathieu.garcia@acumed.fr', phone: '+33 6 88 99 00 11', website: 'https://acumed-montpellier.fr', source: 'Doctolib', score: 65, status: 'contacted', tags: ['acupuncture', 'traditional'] },
];

export async function seedDemoData(userId: string) {
  const { data: existing } = await supabase
    .from('prospects')
    .select('id')
    .eq('user_id', userId)
    .limit(1);

  if (existing && existing.length > 0) return;

  const prospectsWithUser = sampleProspects.map(p => ({
    ...p,
    user_id: userId,
    linkedin: p.linkedin || '',
    instagram: p.instagram || '',
    facebook: p.facebook || '',
  }));

  await supabase.from('prospects').insert(prospectsWithUser);

  const { data: campaigns } = await supabase.from('campaigns').select('id').eq('user_id', userId).limit(1);
  if (!campaigns || campaigns.length === 0) {
    await supabase.from('campaigns').insert([
      { user_id: userId, name: 'Spring Wellness Outreach', subject: 'Boost your practice this spring', body: 'Hello {{first_name}},\n\nI hope this message finds you well...', status: 'sent', sent_count: 45, opened_count: 28, replied_count: 12 },
      { user_id: userId, name: 'New Services Introduction', subject: 'Introducing our wellness network', body: 'Dear {{first_name}},\n\nWe are pleased to introduce...', status: 'draft', sent_count: 0, opened_count: 0, replied_count: 0 },
    ]);
  }
}
