import { useEffect, useState } from 'react';
import { Plus, Send, Mail, BarChart2, X, Eye } from 'lucide-react';
import { supabase } from '../lib/supabase';
import { useApp } from '../context/AppContext';
import { Campaign } from '../types';

const statusConfig: Record<string, { label: string; className: string }> = {
  draft: { label: 'Draft', className: 'bg-slate-100 text-slate-600' },
  sending: { label: 'Sending', className: 'bg-blue-50 text-blue-700' },
  sent: { label: 'Sent', className: 'bg-emerald-50 text-emerald-700' },
  paused: { label: 'Paused', className: 'bg-amber-50 text-amber-700' },
};

interface CampaignModalProps {
  campaign?: Campaign | null;
  onClose: () => void;
  onSaved: () => void;
}

function CampaignModal({ campaign, onClose, onSaved }: CampaignModalProps) {
  const { user } = useApp();
  const [form, setForm] = useState({
    name: campaign?.name ?? '',
    subject: campaign?.subject ?? '',
    body: campaign?.body ?? 'Hello {{first_name}},\n\nI hope this message finds you well.\n\nI am reaching out to connect about wellness opportunities...\n\nBest regards,\n{{sender_name}}',
  });
  const [saving, setSaving] = useState(false);

  const handleSave = async () => {
    if (!form.name.trim() || !user) return;
    setSaving(true);
    if (campaign) {
      await supabase.from('campaigns').update({ name: form.name, subject: form.subject, body: form.body }).eq('id', campaign.id);
    } else {
      await supabase.from('campaigns').insert({ ...form, user_id: user.id, status: 'draft', sent_count: 0, opened_count: 0, replied_count: 0 });
    }
    onSaved();
    onClose();
    setSaving(false);
  };

  return (
    <div className="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div className="bg-white rounded-2xl w-full max-w-2xl shadow-xl">
        <div className="flex items-center justify-between px-6 py-4 border-b border-slate-100">
          <h3 className="text-base font-semibold text-slate-800">{campaign ? 'Edit Campaign' : 'New Campaign'}</h3>
          <button onClick={onClose} className="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-500"><X size={16} /></button>
        </div>
        <div className="p-6 space-y-4">
          <div>
            <label className="block text-xs font-medium text-slate-600 mb-1">Campaign Name</label>
            <input
              value={form.name}
              onChange={e => setForm(f => ({ ...f, name: e.target.value }))}
              placeholder="Spring Wellness Outreach..."
              className="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300"
            />
          </div>
          <div>
            <label className="block text-xs font-medium text-slate-600 mb-1">Email Subject</label>
            <input
              value={form.subject}
              onChange={e => setForm(f => ({ ...f, subject: e.target.value }))}
              placeholder="A message for {{first_name}}..."
              className="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300"
            />
          </div>
          <div>
            <label className="block text-xs font-medium text-slate-600 mb-1">Email Body</label>
            <div className="flex gap-1 mb-1.5">
              {['{{first_name}}', '{{last_name}}', '{{activity}}', '{{city}}'].map(tag => (
                <button
                  key={tag}
                  onClick={() => setForm(f => ({ ...f, body: f.body + tag }))}
                  className="text-xs px-2 py-0.5 bg-blue-50 text-blue-700 rounded hover:bg-blue-100 transition-colors font-mono"
                >
                  {tag}
                </button>
              ))}
            </div>
            <textarea
              value={form.body}
              onChange={e => setForm(f => ({ ...f, body: e.target.value }))}
              rows={8}
              className="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg resize-none focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 font-mono"
            />
          </div>
        </div>
        <div className="flex justify-end gap-2 px-6 py-4 border-t border-slate-100">
          <button onClick={onClose} className="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-lg transition-colors">Cancel</button>
          <button
            onClick={handleSave}
            disabled={saving || !form.name.trim()}
            className="px-4 py-2 text-sm font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-60"
          >
            {saving ? 'Saving...' : campaign ? 'Save Changes' : 'Create Campaign'}
          </button>
        </div>
      </div>
    </div>
  );
}

export default function Campaigns() {
  const { user } = useApp();
  const [campaigns, setCampaigns] = useState<Campaign[]>([]);
  const [loading, setLoading] = useState(true);
  const [showModal, setShowModal] = useState(false);
  const [editCampaign, setEditCampaign] = useState<Campaign | null>(null);

  const fetchCampaigns = async () => {
    if (!user) return;
    const { data } = await supabase.from('campaigns').select('*').eq('user_id', user.id).order('created_at', { ascending: false });
    setCampaigns(data ?? []);
    setLoading(false);
  };

  useEffect(() => { fetchCampaigns(); }, [user]);

  const openRate = (c: Campaign) => c.sent_count > 0 ? Math.round((c.opened_count / c.sent_count) * 100) : 0;
  const replyRate = (c: Campaign) => c.sent_count > 0 ? Math.round((c.replied_count / c.sent_count) * 100) : 0;

  const totalSent = campaigns.reduce((s, c) => s + c.sent_count, 0);
  const totalOpened = campaigns.reduce((s, c) => s + c.opened_count, 0);
  const totalReplied = campaigns.reduce((s, c) => s + c.replied_count, 0);
  const avgOpenRate = totalSent > 0 ? Math.round((totalOpened / totalSent) * 100) : 0;

  return (
    <div className="space-y-5 max-w-5xl">
      <div className="flex items-center justify-between">
        <p className="text-sm text-slate-500">{campaigns.length} campaign{campaigns.length !== 1 ? 's' : ''}</p>
        <button
          onClick={() => { setEditCampaign(null); setShowModal(true); }}
          className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors"
        >
          <Plus size={15} />
          New Campaign
        </button>
      </div>

      <div className="grid grid-cols-2 lg:grid-cols-4 gap-3">
        {[
          { label: 'Total Sent', value: totalSent, icon: <Send size={15} className="text-blue-600" />, bg: 'bg-blue-50' },
          { label: 'Total Opened', value: totalOpened, icon: <Eye size={15} className="text-amber-500" />, bg: 'bg-amber-50' },
          { label: 'Replied', value: totalReplied, icon: <Mail size={15} className="text-emerald-600" />, bg: 'bg-emerald-50' },
          { label: 'Avg Open Rate', value: `${avgOpenRate}%`, icon: <BarChart2 size={15} className="text-rose-500" />, bg: 'bg-rose-50' },
        ].map(item => (
          <div key={item.label} className="bg-white rounded-xl border border-slate-100 p-4 flex items-center gap-3">
            <div className={`w-9 h-9 rounded-lg flex items-center justify-center ${item.bg}`}>{item.icon}</div>
            <div>
              <p className="text-xs text-slate-400">{item.label}</p>
              <p className="text-lg font-bold text-slate-800">{item.value}</p>
            </div>
          </div>
        ))}
      </div>

      <div className="bg-white rounded-xl border border-slate-100">
        <div className="px-5 py-4 border-b border-slate-50">
          <p className="text-sm font-semibold text-slate-800">All Campaigns</p>
        </div>
        <div className="divide-y divide-slate-50">
          {loading ? (
            Array.from({ length: 3 }).map((_, i) => (
              <div key={i} className="px-5 py-4 animate-pulse flex gap-4">
                <div className="flex-1 space-y-2">
                  <div className="h-4 bg-slate-100 rounded w-1/3" />
                  <div className="h-3 bg-slate-100 rounded w-1/4" />
                </div>
              </div>
            ))
          ) : campaigns.length === 0 ? (
            <div className="px-5 py-12 text-center">
              <div className="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center mx-auto mb-3">
                <Mail size={20} className="text-slate-300" />
              </div>
              <p className="text-sm font-medium text-slate-500">No campaigns yet</p>
              <p className="text-xs text-slate-400 mt-1">Create your first outreach campaign</p>
            </div>
          ) : campaigns.map(c => (
            <div key={c.id} className="px-5 py-4 flex items-center gap-4 hover:bg-slate-50 transition-colors">
              <div className="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <Mail size={16} className="text-blue-600" />
              </div>
              <div className="flex-1 min-w-0">
                <div className="flex items-center gap-2">
                  <p className="text-sm font-semibold text-slate-800">{c.name}</p>
                  <span className={`text-xs px-2 py-0.5 rounded-full font-medium ${statusConfig[c.status]?.className}`}>
                    {statusConfig[c.status]?.label}
                  </span>
                </div>
                {c.subject && <p className="text-xs text-slate-400 mt-0.5 truncate">{c.subject}</p>}
              </div>
              <div className="hidden md:flex items-center gap-6 text-center">
                <div>
                  <p className="text-sm font-bold text-slate-800">{c.sent_count}</p>
                  <p className="text-xs text-slate-400">Sent</p>
                </div>
                <div>
                  <p className="text-sm font-bold text-slate-800">{openRate(c)}%</p>
                  <p className="text-xs text-slate-400">Open rate</p>
                </div>
                <div>
                  <p className="text-sm font-bold text-slate-800">{replyRate(c)}%</p>
                  <p className="text-xs text-slate-400">Reply rate</p>
                </div>
              </div>
              <button
                onClick={() => { setEditCampaign(c); setShowModal(true); }}
                className="px-3 py-1.5 text-xs font-medium text-slate-600 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors"
              >
                Edit
              </button>
            </div>
          ))}
        </div>
      </div>

      {showModal && (
        <CampaignModal
          campaign={editCampaign}
          onClose={() => setShowModal(false)}
          onSaved={fetchCampaigns}
        />
      )}
    </div>
  );
}
