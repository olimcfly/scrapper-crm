import { useEffect, useState } from 'react';
import { Plus } from 'lucide-react';
import { supabase } from '../lib/supabase';
import { useApp } from '../context/AppContext';
import { Prospect, ProspectStatus } from '../types';
import StatusBadge from '../components/ui/StatusBadge';
import AddProspectModal from '../components/AddProspectModal';

const COLUMNS: { status: ProspectStatus; label: string; color: string; dot: string }[] = [
  { status: 'new', label: 'New', color: 'bg-slate-50 border-slate-200', dot: 'bg-slate-400' },
  { status: 'qualified', label: 'Qualified', color: 'bg-blue-50 border-blue-200', dot: 'bg-blue-500' },
  { status: 'contacted', label: 'Contacted', color: 'bg-amber-50 border-amber-200', dot: 'bg-amber-400' },
  { status: 'follow-up', label: 'Follow-up', color: 'bg-orange-50 border-orange-200', dot: 'bg-orange-400' },
  { status: 'meeting', label: 'Meeting', color: 'bg-violet-50 border-violet-200', dot: 'bg-violet-400' },
  { status: 'client', label: 'Client', color: 'bg-emerald-50 border-emerald-200', dot: 'bg-emerald-500' },
];

interface KanbanCardProps {
  prospect: Prospect;
  onDragStart: (e: React.DragEvent, id: string) => void;
  onClick: () => void;
}

function KanbanCard({ prospect, onDragStart, onClick }: KanbanCardProps) {
  const scoreColor = prospect.score >= 80 ? 'text-emerald-600' : prospect.score >= 60 ? 'text-amber-500' : 'text-slate-400';
  return (
    <div
      draggable
      onDragStart={e => onDragStart(e, prospect.id)}
      onClick={onClick}
      className="bg-white border border-slate-100 rounded-lg p-3.5 cursor-pointer hover:border-blue-200 hover:shadow-sm transition-all group"
    >
      <div className="flex items-start justify-between gap-2 mb-2">
        <div className="flex items-center gap-2">
          <div className="w-7 h-7 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
            <span className="text-xs font-bold text-blue-700">{prospect.first_name[0]}{prospect.last_name[0]}</span>
          </div>
          <div>
            <p className="text-sm font-semibold text-slate-800 leading-tight">{prospect.first_name} {prospect.last_name}</p>
            <p className="text-xs text-slate-500">{prospect.activity}</p>
          </div>
        </div>
        <span className={`text-xs font-bold ${scoreColor}`}>{prospect.score}</span>
      </div>
      {prospect.city && (
        <p className="text-xs text-slate-400 mt-1">{prospect.city}</p>
      )}
      {prospect.tags && prospect.tags.length > 0 && (
        <div className="flex flex-wrap gap-1 mt-2">
          {prospect.tags.slice(0, 2).map(t => (
            <span key={t} className="px-1.5 py-0.5 bg-slate-50 text-slate-500 text-xs rounded">{t}</span>
          ))}
        </div>
      )}
    </div>
  );
}

export default function Pipeline() {
  const { user, navigate } = useApp();
  const [prospects, setProspects] = useState<Prospect[]>([]);
  const [loading, setLoading] = useState(true);
  const [dragOver, setDragOver] = useState<string | null>(null);
  const [showAdd, setShowAdd] = useState(false);

  const fetchProspects = async () => {
    if (!user) return;
    const { data } = await supabase.from('prospects').select('*').eq('user_id', user.id).order('score', { ascending: false });
    setProspects(data ?? []);
    setLoading(false);
  };

  useEffect(() => { fetchProspects(); }, [user]);

  const handleDragStart = (e: React.DragEvent, id: string) => {
    e.dataTransfer.setData('prospectId', id);
    e.dataTransfer.effectAllowed = 'move';
  };

  const handleDrop = async (e: React.DragEvent, status: ProspectStatus) => {
    e.preventDefault();
    const id = e.dataTransfer.getData('prospectId');
    if (!id) return;
    setDragOver(null);
    setProspects(prev => prev.map(p => p.id === id ? { ...p, status } : p));
    await supabase.from('prospects').update({ status }).eq('id', id);
    await supabase.from('prospect_activities').insert({
      prospect_id: id,
      user_id: user!.id,
      type: 'status',
      description: `Moved to ${status}`,
    });
  };

  const byStatus = (status: ProspectStatus) => prospects.filter(p => p.status === status);

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <p className="text-sm text-slate-500">{prospects.length} prospects in pipeline</p>
        <button
          onClick={() => setShowAdd(true)}
          className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors"
        >
          <Plus size={15} />
          Add Prospect
        </button>
      </div>

      <div className="overflow-x-auto pb-4">
        <div className="flex gap-3 min-w-max">
          {COLUMNS.map(col => {
            const cards = byStatus(col.status);
            const isDragTarget = dragOver === col.status;
            return (
              <div
                key={col.status}
                onDragOver={e => { e.preventDefault(); setDragOver(col.status); }}
                onDragLeave={() => setDragOver(null)}
                onDrop={e => handleDrop(e, col.status)}
                className={`w-64 flex flex-col rounded-xl border ${col.color} transition-all ${isDragTarget ? 'ring-2 ring-blue-400 ring-offset-1' : ''}`}
              >
                <div className="px-4 py-3 border-b border-current border-opacity-20">
                  <div className="flex items-center justify-between">
                    <div className="flex items-center gap-2">
                      <div className={`w-2 h-2 rounded-full ${col.dot}`} />
                      <span className="text-sm font-semibold text-slate-700">{col.label}</span>
                    </div>
                    <span className="text-xs font-medium text-slate-500 bg-white bg-opacity-60 px-2 py-0.5 rounded-full">
                      {cards.length}
                    </span>
                  </div>
                </div>

                <div className="flex-1 p-2.5 space-y-2 min-h-20">
                  {loading ? (
                    Array.from({ length: 2 }).map((_, i) => (
                      <div key={i} className="h-20 bg-white rounded-lg animate-pulse" />
                    ))
                  ) : cards.length === 0 ? (
                    <div className={`h-16 rounded-lg border-2 border-dashed flex items-center justify-center transition-colors ${isDragTarget ? 'border-blue-400 bg-blue-50' : 'border-slate-200'}`}>
                      <p className="text-xs text-slate-300">Drop here</p>
                    </div>
                  ) : cards.map(p => (
                    <KanbanCard
                      key={p.id}
                      prospect={p}
                      onDragStart={handleDragStart}
                      onClick={() => navigate('prospect-detail', p.id)}
                    />
                  ))}
                </div>
              </div>
            );
          })}
        </div>
      </div>

      {showAdd && <AddProspectModal onClose={() => setShowAdd(false)} onSaved={fetchProspects} />}
    </div>
  );
}
