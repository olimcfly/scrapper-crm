import { useEffect, useState } from 'react';
import { ChevronLeft, Globe, Linkedin, Instagram, Facebook, Mail, Phone, Tag, Clock, Send, CreditCard as Edit2, Check, X } from 'lucide-react';
import { supabase } from '../lib/supabase';
import { useApp } from '../context/AppContext';
import { Prospect, ProspectNote, ProspectActivity, ProspectStatus } from '../types';
import StatusBadge from '../components/ui/StatusBadge';
import ScoreBar from '../components/ui/ScoreBar';

const statuses: ProspectStatus[] = ['new', 'qualified', 'contacted', 'follow-up', 'meeting', 'client'];

export default function ProspectDetail() {
  const { user, selectedProspectId, navigate } = useApp();
  const [prospect, setProspect] = useState<Prospect | null>(null);
  const [notes, setNotes] = useState<ProspectNote[]>([]);
  const [activities, setActivities] = useState<ProspectActivity[]>([]);
  const [newNote, setNewNote] = useState('');
  const [loading, setLoading] = useState(true);
  const [editingStatus, setEditingStatus] = useState(false);

  const fetchData = async () => {
    if (!selectedProspectId || !user) return;
    const [{ data: p }, { data: n }, { data: a }] = await Promise.all([
      supabase.from('prospects').select('*').eq('id', selectedProspectId).maybeSingle(),
      supabase.from('prospect_notes').select('*').eq('prospect_id', selectedProspectId).order('created_at', { ascending: false }),
      supabase.from('prospect_activities').select('*').eq('prospect_id', selectedProspectId).order('created_at', { ascending: false }),
    ]);
    setProspect(p);
    setNotes(n ?? []);
    setActivities(a ?? []);
    setLoading(false);
  };

  useEffect(() => { fetchData(); }, [selectedProspectId]);

  const addNote = async () => {
    if (!newNote.trim() || !user || !prospect) return;
    await supabase.from('prospect_notes').insert({ prospect_id: prospect.id, user_id: user.id, content: newNote });
    await supabase.from('prospect_activities').insert({ prospect_id: prospect.id, user_id: user.id, type: 'note', description: `Note added: "${newNote.slice(0, 60)}${newNote.length > 60 ? '...' : ''}"` });
    setNewNote('');
    fetchData();
  };

  const updateStatus = async (status: ProspectStatus) => {
    if (!prospect) return;
    await supabase.from('prospects').update({ status }).eq('id', prospect.id);
    await supabase.from('prospect_activities').insert({ prospect_id: prospect.id, user_id: user!.id, type: 'status', description: `Status changed to ${status}` });
    setProspect(p => p ? { ...p, status } : null);
    setEditingStatus(false);
    fetchData();
  };

  const formatDate = (d: string) => new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
  const formatTime = (d: string) => new Date(d).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

  if (loading) {
    return (
      <div className="max-w-4xl animate-pulse space-y-4">
        <div className="h-10 bg-slate-100 rounded-lg w-48" />
        <div className="h-32 bg-slate-100 rounded-xl" />
      </div>
    );
  }

  if (!prospect) return <div className="text-slate-500 text-sm">Prospect not found.</div>;

  return (
    <div className="max-w-4xl space-y-5">
      <button
        onClick={() => navigate('prospects')}
        className="flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-700 transition-colors"
      >
        <ChevronLeft size={16} />
        Back to Prospects
      </button>

      <div className="bg-white rounded-xl border border-slate-100 p-6">
        <div className="flex items-start gap-4">
          <div className="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <span className="text-lg font-bold text-blue-700">{prospect.first_name[0]}{prospect.last_name[0]}</span>
          </div>
          <div className="flex-1 min-w-0">
            <div className="flex items-start justify-between gap-4 flex-wrap">
              <div>
                <h2 className="text-xl font-bold text-slate-800">{prospect.first_name} {prospect.last_name}</h2>
                <p className="text-sm text-slate-500 mt-0.5">{prospect.activity} · {prospect.city}</p>
              </div>
              <div className="flex items-center gap-2">
                {editingStatus ? (
                  <div className="flex items-center gap-2">
                    <select
                      defaultValue={prospect.status}
                      onChange={e => updateStatus(e.target.value as ProspectStatus)}
                      className="text-sm border border-slate-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >
                      {statuses.map(s => <option key={s} value={s}>{s.charAt(0).toUpperCase() + s.slice(1)}</option>)}
                    </select>
                    <button onClick={() => setEditingStatus(false)} className="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-500">
                      <X size={14} />
                    </button>
                  </div>
                ) : (
                  <button onClick={() => setEditingStatus(true)} className="flex items-center gap-1.5">
                    <StatusBadge status={prospect.status} />
                    <Edit2 size={12} className="text-slate-400" />
                  </button>
                )}
              </div>
            </div>
            <div className="mt-3 w-48">
              <p className="text-xs text-slate-400 mb-1">Lead Score</p>
              <ScoreBar score={prospect.score} />
            </div>
          </div>
        </div>

        {prospect.tags && prospect.tags.length > 0 && (
          <div className="flex items-center gap-2 mt-4 flex-wrap">
            <Tag size={13} className="text-slate-400" />
            {prospect.tags.map(tag => (
              <span key={tag} className="px-2 py-0.5 bg-slate-100 text-slate-600 text-xs rounded-full">{tag}</span>
            ))}
          </div>
        )}
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div className="bg-white rounded-xl border border-slate-100 p-5">
          <p className="text-sm font-semibold text-slate-800 mb-4">Contact Information</p>
          <div className="space-y-3">
            {prospect.email && (
              <div className="flex items-center gap-3">
                <div className="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                  <Mail size={14} className="text-blue-600" />
                </div>
                <div>
                  <p className="text-xs text-slate-400">Professional Email</p>
                  <a href={`mailto:${prospect.email}`} className="text-sm text-blue-600 hover:underline">{prospect.email}</a>
                </div>
              </div>
            )}
            {prospect.phone && (
              <div className="flex items-center gap-3">
                <div className="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center flex-shrink-0">
                  <Phone size={14} className="text-emerald-600" />
                </div>
                <div>
                  <p className="text-xs text-slate-400">Phone</p>
                  <a href={`tel:${prospect.phone}`} className="text-sm text-slate-700">{prospect.phone}</a>
                </div>
              </div>
            )}
            {prospect.website && (
              <div className="flex items-center gap-3">
                <div className="w-8 h-8 bg-slate-50 rounded-lg flex items-center justify-center flex-shrink-0">
                  <Globe size={14} className="text-slate-600" />
                </div>
                <div>
                  <p className="text-xs text-slate-400">Website</p>
                  <a href={prospect.website} target="_blank" rel="noopener noreferrer" className="text-sm text-blue-600 hover:underline truncate block max-w-xs">{prospect.website}</a>
                </div>
              </div>
            )}
          </div>

          {(prospect.linkedin || prospect.instagram || prospect.facebook) && (
            <div className="mt-4 pt-4 border-t border-slate-50">
              <p className="text-xs text-slate-400 mb-2">Social Media</p>
              <div className="flex items-center gap-2">
                {prospect.linkedin && (
                  <a href={prospect.linkedin} target="_blank" rel="noopener noreferrer" className="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-700 transition-colors">
                    <Linkedin size={14} className="text-white" />
                  </a>
                )}
                {prospect.instagram && (
                  <a href={prospect.instagram} target="_blank" rel="noopener noreferrer" className="w-8 h-8 bg-pink-500 rounded-lg flex items-center justify-center hover:bg-pink-600 transition-colors">
                    <Instagram size={14} className="text-white" />
                  </a>
                )}
                {prospect.facebook && (
                  <a href={prospect.facebook} target="_blank" rel="noopener noreferrer" className="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center hover:bg-blue-600 transition-colors">
                    <Facebook size={14} className="text-white" />
                  </a>
                )}
              </div>
            </div>
          )}
        </div>

        <div className="bg-white rounded-xl border border-slate-100 p-5">
          <p className="text-sm font-semibold text-slate-800 mb-4">Details</p>
          <div className="space-y-2.5">
            {[
              { label: 'Source', value: prospect.source },
              { label: 'Added', value: formatDate(prospect.created_at) },
              { label: 'Last Updated', value: formatDate(prospect.updated_at) },
            ].map(({ label, value }) => value ? (
              <div key={label} className="flex items-center justify-between">
                <span className="text-xs text-slate-400">{label}</span>
                <span className="text-sm text-slate-700">{value}</span>
              </div>
            ) : null)}
          </div>

          <div className="mt-4 pt-4 border-t border-slate-50 flex gap-2">
            <a
              href={`mailto:${prospect.email}`}
              className="flex-1 flex items-center justify-center gap-2 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors"
            >
              <Send size={13} />
              Send Email
            </a>
          </div>
        </div>
      </div>

      <div className="bg-white rounded-xl border border-slate-100 p-5">
        <p className="text-sm font-semibold text-slate-800 mb-4">Notes</p>
        <div className="flex gap-2 mb-4">
          <textarea
            value={newNote}
            onChange={e => setNewNote(e.target.value)}
            placeholder="Add a note about this prospect..."
            rows={2}
            className="flex-1 px-3 py-2 text-sm border border-slate-200 rounded-lg resize-none focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300"
          />
          <button
            onClick={addNote}
            disabled={!newNote.trim()}
            className="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-40 disabled:cursor-not-allowed self-end"
          >
            <Check size={15} />
          </button>
        </div>
        <div className="space-y-3">
          {notes.length === 0 ? (
            <p className="text-sm text-slate-400 text-center py-4">No notes yet. Add one above.</p>
          ) : notes.map(n => (
            <div key={n.id} className="bg-slate-50 rounded-lg px-4 py-3">
              <p className="text-sm text-slate-700">{n.content}</p>
              <p className="text-xs text-slate-400 mt-1">{formatDate(n.created_at)} at {formatTime(n.created_at)}</p>
            </div>
          ))}
        </div>
      </div>

      <div className="bg-white rounded-xl border border-slate-100 p-5">
        <p className="text-sm font-semibold text-slate-800 mb-4">Activity Timeline</p>
        <div className="space-y-3">
          {activities.length === 0 ? (
            <p className="text-sm text-slate-400 text-center py-4">No activity yet.</p>
          ) : activities.map((a, i) => (
            <div key={a.id} className="flex gap-3">
              <div className="flex flex-col items-center">
                <div className="w-7 h-7 bg-blue-50 rounded-full flex items-center justify-center flex-shrink-0">
                  <Clock size={12} className="text-blue-600" />
                </div>
                {i < activities.length - 1 && <div className="w-px flex-1 bg-slate-100 mt-1" />}
              </div>
              <div className="pb-3 flex-1">
                <p className="text-sm text-slate-700">{a.description}</p>
                <p className="text-xs text-slate-400 mt-0.5">{formatDate(a.created_at)}</p>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}
