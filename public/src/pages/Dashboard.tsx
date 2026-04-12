import { useEffect, useState } from 'react';
import { Users, Star, MessageCircle, TrendingUp, Plus, Upload, Send, ArrowRight } from 'lucide-react';
import { supabase } from '../lib/supabase';
import { useApp } from '../context/AppContext';
import { Prospect } from '../types';
import { seedDemoData } from '../lib/seedData';
import StatusBadge from '../components/ui/StatusBadge';
import ScoreBar from '../components/ui/ScoreBar';

interface KPICardProps {
  label: string;
  value: string | number;
  change: string;
  icon: React.ReactNode;
  color: string;
}

function KPICard({ label, value, change, icon, color }: KPICardProps) {
  return (
    <div className="bg-white rounded-xl border border-slate-100 p-5 flex flex-col gap-3">
      <div className="flex items-center justify-between">
        <span className="text-sm font-medium text-slate-500">{label}</span>
        <div className={`w-9 h-9 rounded-lg flex items-center justify-center ${color}`}>
          {icon}
        </div>
      </div>
      <div>
        <p className="text-2xl font-bold text-slate-800">{value}</p>
        <p className="text-xs text-emerald-600 font-medium mt-0.5">{change}</p>
      </div>
    </div>
  );
}

function MiniChart({ data }: { data: number[] }) {
  const max = Math.max(...data, 1);
  const w = 480;
  const h = 120;
  const pad = 16;
  const pts = data.map((v, i) => {
    const x = pad + (i / (data.length - 1)) * (w - 2 * pad);
    const y = h - pad - (v / max) * (h - 2 * pad);
    return `${x},${y}`;
  }).join(' ');
  const area = `${pad},${h - pad} ${pts} ${w - pad},${h - pad}`;

  return (
    <svg viewBox={`0 0 ${w} ${h}`} className="w-full" style={{ height: 120 }}>
      <defs>
        <linearGradient id="chartGrad" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0%" stopColor="#3B82F6" stopOpacity="0.15" />
          <stop offset="100%" stopColor="#3B82F6" stopOpacity="0" />
        </linearGradient>
      </defs>
      <polygon points={area} fill="url(#chartGrad)" />
      <polyline points={pts} fill="none" stroke="#2563EB" strokeWidth="2" strokeLinejoin="round" />
      {data.map((v, i) => {
        const x = pad + (i / (data.length - 1)) * (w - 2 * pad);
        const y = h - pad - (v / max) * (h - 2 * pad);
        return <circle key={i} cx={x} cy={y} r="3" fill="white" stroke="#2563EB" strokeWidth="2" />;
      })}
    </svg>
  );
}

export default function Dashboard() {
  const { user, navigate } = useApp();
  const [prospects, setProspects] = useState<Prospect[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (!user) return;
    (async () => {
      await seedDemoData(user.id);
      const { data } = await supabase
        .from('prospects')
        .select('*')
        .eq('user_id', user.id)
        .order('created_at', { ascending: false });
      setProspects(data ?? []);
      setLoading(false);
    })();
  }, [user]);

  const total = prospects.length;
  const qualified = prospects.filter(p => ['qualified', 'meeting', 'client'].includes(p.status)).length;
  const contacted = prospects.filter(p => ['contacted', 'follow-up', 'meeting', 'client'].includes(p.status)).length;
  const responseRate = total > 0 ? Math.round((contacted / total) * 100) : 0;

  const chartData = [2, 5, 4, 8, 6, 10, 7, 12, 9, 14, 11, total];
  const recent = prospects.slice(0, 5);

  return (
    <div className="space-y-6 max-w-6xl">
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-xl font-bold text-slate-800">Welcome back!</h2>
          <p className="text-sm text-slate-500 mt-0.5">Here's what's happening with your prospects.</p>
        </div>
        <div className="flex gap-2">
          <button
            onClick={() => navigate('prospects')}
            className="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 transition-colors"
          >
            <Upload size={15} />
            Import
          </button>
          <button
            onClick={() => navigate('campaigns')}
            className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors"
          >
            <Plus size={15} />
            Add Prospect
          </button>
        </div>
      </div>

      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <KPICard label="Total Prospects" value={total} change="+12% this month" icon={<Users size={17} className="text-blue-600" />} color="bg-blue-50" />
        <KPICard label="Qualified Leads" value={qualified} change="+8% this month" icon={<Star size={17} className="text-amber-500" />} color="bg-amber-50" />
        <KPICard label="Contacted" value={contacted} change="+5% this month" icon={<MessageCircle size={17} className="text-emerald-600" />} color="bg-emerald-50" />
        <KPICard label="Response Rate" value={`${responseRate}%`} change="+3% this month" icon={<TrendingUp size={17} className="text-rose-500" />} color="bg-rose-50" />
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div className="lg:col-span-2 bg-white rounded-xl border border-slate-100 p-5">
          <div className="flex items-center justify-between mb-4">
            <div>
              <p className="text-sm font-semibold text-slate-800">Prospect Growth</p>
              <p className="text-xs text-slate-400">Last 12 months</p>
            </div>
            <span className="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">+24%</span>
          </div>
          {loading ? (
            <div className="h-30 animate-pulse bg-slate-50 rounded-lg" />
          ) : (
            <MiniChart data={chartData} />
          )}
        </div>

        <div className="bg-white rounded-xl border border-slate-100 p-5">
          <p className="text-sm font-semibold text-slate-800 mb-4">Pipeline Status</p>
          <div className="space-y-3">
            {[
              { label: 'New', count: prospects.filter(p => p.status === 'new').length, color: 'bg-slate-400' },
              { label: 'Qualified', count: prospects.filter(p => p.status === 'qualified').length, color: 'bg-blue-500' },
              { label: 'Contacted', count: prospects.filter(p => p.status === 'contacted').length, color: 'bg-amber-400' },
              { label: 'Meeting', count: prospects.filter(p => p.status === 'meeting').length, color: 'bg-violet-400' },
              { label: 'Client', count: prospects.filter(p => p.status === 'client').length, color: 'bg-emerald-500' },
            ].map(item => (
              <div key={item.label} className="flex items-center gap-3">
                <div className={`w-2 h-2 rounded-full ${item.color}`} />
                <span className="text-sm text-slate-600 flex-1">{item.label}</span>
                <span className="text-sm font-semibold text-slate-800">{item.count}</span>
              </div>
            ))}
          </div>
          <button
            onClick={() => navigate('pipeline')}
            className="mt-4 w-full text-xs text-blue-600 hover:text-blue-700 font-medium flex items-center justify-center gap-1"
          >
            View Pipeline <ArrowRight size={12} />
          </button>
        </div>
      </div>

      <div className="bg-white rounded-xl border border-slate-100">
        <div className="flex items-center justify-between px-5 py-4 border-b border-slate-50">
          <p className="text-sm font-semibold text-slate-800">Recent Prospects</p>
          <button
            onClick={() => navigate('prospects')}
            className="text-xs text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1"
          >
            View all <ArrowRight size={12} />
          </button>
        </div>
        <div className="divide-y divide-slate-50">
          {loading ? (
            Array.from({ length: 4 }).map((_, i) => (
              <div key={i} className="px-5 py-3.5 animate-pulse flex gap-4">
                <div className="w-8 h-8 bg-slate-100 rounded-full" />
                <div className="flex-1 space-y-2">
                  <div className="h-3 bg-slate-100 rounded w-1/3" />
                  <div className="h-2 bg-slate-100 rounded w-1/4" />
                </div>
              </div>
            ))
          ) : recent.map(p => (
            <div
              key={p.id}
              onClick={() => navigate('prospect-detail', p.id)}
              className="px-5 py-3.5 flex items-center gap-4 hover:bg-slate-50 cursor-pointer transition-colors"
            >
              <div className="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                <span className="text-xs font-bold text-blue-700">
                  {p.first_name[0]}{p.last_name[0]}
                </span>
              </div>
              <div className="flex-1 min-w-0">
                <p className="text-sm font-medium text-slate-800">{p.first_name} {p.last_name}</p>
                <p className="text-xs text-slate-400">{p.activity} · {p.city}</p>
              </div>
              <div className="hidden sm:block w-24">
                <ScoreBar score={p.score} />
              </div>
              <StatusBadge status={p.status} />
            </div>
          ))}
        </div>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <button
          onClick={() => navigate('prospects')}
          className="flex items-center gap-3 p-4 bg-white border border-slate-100 rounded-xl hover:border-blue-200 hover:bg-blue-50 transition-all group"
        >
          <div className="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
            <Upload size={16} className="text-blue-600" />
          </div>
          <div className="text-left">
            <p className="text-sm font-semibold text-slate-700">Import CSV</p>
            <p className="text-xs text-slate-400">Bulk add prospects</p>
          </div>
        </button>
        <button
          onClick={() => navigate('pipeline')}
          className="flex items-center gap-3 p-4 bg-white border border-slate-100 rounded-xl hover:border-blue-200 hover:bg-blue-50 transition-all group"
        >
          <div className="w-9 h-9 bg-emerald-100 rounded-lg flex items-center justify-center group-hover:bg-emerald-200 transition-colors">
            <Plus size={16} className="text-emerald-600" />
          </div>
          <div className="text-left">
            <p className="text-sm font-semibold text-slate-700">Add Prospect</p>
            <p className="text-xs text-slate-400">Manually add one</p>
          </div>
        </button>
        <button
          onClick={() => navigate('campaigns')}
          className="flex items-center gap-3 p-4 bg-white border border-slate-100 rounded-xl hover:border-blue-200 hover:bg-blue-50 transition-all group"
        >
          <div className="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center group-hover:bg-amber-200 transition-colors">
            <Send size={16} className="text-amber-600" />
          </div>
          <div className="text-left">
            <p className="text-sm font-semibold text-slate-700">New Campaign</p>
            <p className="text-xs text-slate-400">Send email outreach</p>
          </div>
        </button>
      </div>
    </div>
  );
}
