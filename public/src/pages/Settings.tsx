import { useState, useRef } from 'react';
import { Upload, Globe, Database, User, Check, AlertCircle } from 'lucide-react';
import { supabase } from '../lib/supabase';
import { useApp } from '../context/AppContext';
import { Prospect } from '../types';

function Section({ title, icon, children }: { title: string; icon: React.ReactNode; children: React.ReactNode }) {
  return (
    <div className="bg-white rounded-xl border border-slate-100 p-6">
      <div className="flex items-center gap-2.5 mb-5">
        <div className="w-8 h-8 bg-slate-50 rounded-lg flex items-center justify-center">{icon}</div>
        <h3 className="text-sm font-semibold text-slate-800">{title}</h3>
      </div>
      {children}
    </div>
  );
}

export default function Settings() {
  const { user } = useApp();
  const fileRef = useRef<HTMLInputElement>(null);
  const [csvStatus, setCsvStatus] = useState<{ type: 'success' | 'error' | null; message: string }>({ type: null, message: '' });
  const [importing, setImporting] = useState(false);
  const [sources] = useState([
    { name: 'Google Maps', enabled: true },
    { name: 'Doctolib', enabled: true },
    { name: 'Pages Jaunes', enabled: true },
    { name: 'LinkedIn', enabled: false },
    { name: 'Instagram', enabled: false },
  ]);

  const handleCSVImport = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file || !user) return;
    setImporting(true);
    setCsvStatus({ type: null, message: '' });

    try {
      const text = await file.text();
      const lines = text.split('\n').filter(l => l.trim());
      if (lines.length < 2) throw new Error('CSV must have a header row and at least one data row.');

      const headers = lines[0].split(',').map(h => h.trim().toLowerCase().replace(/\s+/g, '_').replace(/"/g, ''));
      const rows: Partial<Prospect>[] = [];

      for (let i = 1; i < lines.length; i++) {
        const vals = lines[i].split(',').map(v => v.trim().replace(/^"|"$/g, ''));
        const row: Record<string, string> = {};
        headers.forEach((h, j) => { row[h] = vals[j] ?? ''; });

        if (!row.first_name && !row.firstname && !row.name) continue;

        rows.push({
          user_id: user.id,
          first_name: row.first_name || row.firstname || (row.name?.split(' ')[0] ?? ''),
          last_name: row.last_name || row.lastname || (row.name?.split(' ').slice(1).join(' ') ?? ''),
          activity: row.activity || row.profession || row.job || '',
          city: row.city || row.ville || '',
          email: row.email || row.professional_email || '',
          phone: row.phone || row.telephone || row.tel || '',
          website: row.website || row.site || '',
          linkedin: row.linkedin || '',
          source: 'CSV Import',
          score: Number(row.score) || 50,
          status: 'new' as const,
          tags: [],
        });
      }

      if (rows.length === 0) throw new Error('No valid rows found. Make sure your CSV has first_name and last_name columns.');

      const { error } = await supabase.from('prospects').insert(rows);
      if (error) throw new Error(error.message);

      setCsvStatus({ type: 'success', message: `Successfully imported ${rows.length} prospect${rows.length !== 1 ? 's' : ''}!` });
    } catch (err) {
      setCsvStatus({ type: 'error', message: (err as Error).message });
    }

    setImporting(false);
    if (fileRef.current) fileRef.current.value = '';
  };

  return (
    <div className="space-y-5 max-w-2xl">
      <Section title="User Profile" icon={<User size={15} className="text-slate-600" />}>
        <div className="space-y-4">
          <div>
            <label className="block text-xs font-medium text-slate-600 mb-1">Email Address</label>
            <input
              value={user?.email ?? ''}
              disabled
              className="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg bg-slate-50 text-slate-500 cursor-not-allowed"
            />
          </div>
          <div>
            <label className="block text-xs font-medium text-slate-600 mb-1">Display Name</label>
            <input
              placeholder="Your name..."
              className="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300"
            />
          </div>
          <button className="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
            Save Profile
          </button>
        </div>
      </Section>

      <Section title="CSV Import" icon={<Upload size={15} className="text-slate-600" />}>
        <div className="space-y-4">
          <p className="text-sm text-slate-500">
            Import prospects from a CSV file. Supported columns:
          </p>
          <div className="bg-slate-50 rounded-lg p-3">
            <p className="text-xs font-mono text-slate-600 leading-relaxed">
              first_name, last_name, activity, city, email, phone, website, linkedin, score
            </p>
          </div>

          {csvStatus.type && (
            <div className={`flex items-start gap-2 p-3 rounded-lg text-sm ${csvStatus.type === 'success' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600'}`}>
              {csvStatus.type === 'success' ? <Check size={15} className="mt-0.5 flex-shrink-0" /> : <AlertCircle size={15} className="mt-0.5 flex-shrink-0" />}
              {csvStatus.message}
            </div>
          )}

          <div
            onClick={() => fileRef.current?.click()}
            className="border-2 border-dashed border-slate-200 rounded-xl p-8 text-center cursor-pointer hover:border-blue-300 hover:bg-blue-50 transition-all group"
          >
            <div className="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-blue-100 transition-colors">
              <Upload size={18} className="text-slate-400 group-hover:text-blue-600" />
            </div>
            <p className="text-sm font-medium text-slate-700">
              {importing ? 'Importing...' : 'Click to upload CSV'}
            </p>
            <p className="text-xs text-slate-400 mt-1">Supports .csv files up to 10MB</p>
          </div>
          <input ref={fileRef} type="file" accept=".csv" className="hidden" onChange={handleCSVImport} disabled={importing} />
        </div>
      </Section>

      <Section title="Data Sources" icon={<Globe size={15} className="text-slate-600" />}>
        <div className="space-y-3">
          <p className="text-sm text-slate-500 mb-4">Configure where to find wellness practitioners online.</p>
          {sources.map(source => (
            <div key={source.name} className="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
              <div className="flex items-center gap-2">
                <div className="w-7 h-7 bg-slate-50 rounded-lg flex items-center justify-center">
                  <Globe size={13} className="text-slate-500" />
                </div>
                <span className="text-sm text-slate-700">{source.name}</span>
              </div>
              <div className={`w-10 h-5 rounded-full transition-colors ${source.enabled ? 'bg-blue-500' : 'bg-slate-200'} relative cursor-pointer`}>
                <div className={`w-4 h-4 bg-white rounded-full shadow absolute top-0.5 transition-all ${source.enabled ? 'right-0.5' : 'left-0.5'}`} />
              </div>
            </div>
          ))}
        </div>
      </Section>

      <Section title="Custom Fields" icon={<Database size={15} className="text-slate-600" />}>
        <div className="space-y-3">
          <p className="text-sm text-slate-500">Extend prospect profiles with custom fields.</p>
          <div className="grid grid-cols-2 gap-2">
            {['Specialty', 'Years of Experience', 'Certification', 'Availability'].map(field => (
              <div key={field} className="flex items-center gap-2 p-2.5 bg-slate-50 rounded-lg">
                <div className="w-2 h-2 bg-blue-400 rounded-full" />
                <span className="text-xs text-slate-600">{field}</span>
              </div>
            ))}
          </div>
          <button className="flex items-center gap-2 text-sm text-blue-600 font-medium hover:text-blue-700 mt-1">
            <span className="w-5 h-5 bg-blue-50 rounded flex items-center justify-center text-lg leading-none">+</span>
            Add custom field
          </button>
        </div>
      </Section>
    </div>
  );
}
