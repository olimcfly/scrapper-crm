import { useEffect, useState } from 'react';
import { Search, Filter, ChevronLeft, ChevronRight, Plus, ExternalLink } from 'lucide-react';
import { supabase } from '../lib/supabase';
import { useApp } from '../context/AppContext';
import { Prospect, ProspectStatus } from '../types';
import StatusBadge from '../components/ui/StatusBadge';
import ScoreBar from '../components/ui/ScoreBar';
import AddProspectModal from '../components/AddProspectModal';

const PAGE_SIZE = 8;
const statuses: ProspectStatus[] = ['new', 'qualified', 'contacted', 'follow-up', 'meeting', 'client'];

export default function ProspectsList() {
  const { user, navigate } = useApp();
  const [prospects, setProspects] = useState<Prospect[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [filterStatus, setFilterStatus] = useState('');
  const [filterCity, setFilterCity] = useState('');
  const [page, setPage] = useState(0);
  const [showAdd, setShowAdd] = useState(false);

  const fetchProspects = async () => {
    if (!user) return;
    setLoading(true);
    const { data } = await supabase
      .from('prospects')
      .select('*')
      .eq('user_id', user.id)
      .order('created_at', { ascending: false });
    setProspects(data ?? []);
    setLoading(false);
  };

  useEffect(() => { fetchProspects(); }, [user]);

  const cities = [...new Set(prospects.map(p => p.city).filter(Boolean))].sort();

  const filtered = prospects.filter(p => {
    const q = search.toLowerCase();
    const matchSearch = !q ||
      p.first_name.toLowerCase().includes(q) ||
      p.last_name.toLowerCase().includes(q) ||
      p.activity.toLowerCase().includes(q) ||
      p.city.toLowerCase().includes(q) ||
      p.email.toLowerCase().includes(q);
    const matchStatus = !filterStatus || p.status === filterStatus;
    const matchCity = !filterCity || p.city === filterCity;
    return matchSearch && matchStatus && matchCity;
  });

  const totalPages = Math.ceil(filtered.length / PAGE_SIZE);
  const paginated = filtered.slice(page * PAGE_SIZE, (page + 1) * PAGE_SIZE);

  const resetPage = () => setPage(0);

  return (
    <div className="space-y-4 max-w-7xl">
      <div className="flex items-center justify-between">
        <p className="text-sm text-slate-500">{filtered.length} prospect{filtered.length !== 1 ? 's' : ''}</p>
        <button
          onClick={() => setShowAdd(true)}
          className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors"
        >
          <Plus size={15} />
          Add Prospect
        </button>
      </div>

      <div className="bg-white rounded-xl border border-slate-100">
        <div className="flex flex-wrap items-center gap-3 p-4 border-b border-slate-50">
          <div className="relative flex-1 min-w-48">
            <Search size={14} className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
            <input
              type="text"
              placeholder="Search by name, activity, city..."
              value={search}
              onChange={e => { setSearch(e.target.value); resetPage(); }}
              className="w-full pl-9 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300"
            />
          </div>
          <div className="flex items-center gap-2">
            <Filter size={14} className="text-slate-400" />
            <select
              value={filterStatus}
              onChange={e => { setFilterStatus(e.target.value); resetPage(); }}
              className="text-sm bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-100 text-slate-600"
            >
              <option value="">All statuses</option>
              {statuses.map(s => <option key={s} value={s}>{s.charAt(0).toUpperCase() + s.slice(1)}</option>)}
            </select>
            <select
              value={filterCity}
              onChange={e => { setFilterCity(e.target.value); resetPage(); }}
              className="text-sm bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-100 text-slate-600"
            >
              <option value="">All cities</option>
              {cities.map(c => <option key={c} value={c}>{c}</option>)}
            </select>
          </div>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b border-slate-50">
                <th className="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Name</th>
                <th className="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Activity</th>
                <th className="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide hidden md:table-cell">City</th>
                <th className="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide hidden lg:table-cell">Email</th>
                <th className="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide hidden xl:table-cell">Phone</th>
                <th className="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide hidden lg:table-cell">Source</th>
                <th className="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide w-28">Score</th>
                <th className="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-50">
              {loading ? (
                Array.from({ length: 5 }).map((_, i) => (
                  <tr key={i} className="animate-pulse">
                    {Array.from({ length: 8 }).map((_, j) => (
                      <td key={j} className="px-4 py-3.5">
                        <div className="h-3 bg-slate-100 rounded w-24" />
                      </td>
                    ))}
                  </tr>
                ))
              ) : paginated.length === 0 ? (
                <tr>
                  <td colSpan={8} className="px-4 py-12 text-center text-sm text-slate-400">
                    No prospects found
                  </td>
                </tr>
              ) : paginated.map(p => (
                <tr
                  key={p.id}
                  onClick={() => navigate('prospect-detail', p.id)}
                  className="hover:bg-slate-50 cursor-pointer transition-colors group"
                >
                  <td className="px-4 py-3.5">
                    <div className="flex items-center gap-3">
                      <div className="w-7 h-7 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span className="text-xs font-bold text-blue-700">{p.first_name[0]}{p.last_name[0]}</span>
                      </div>
                      <span className="font-medium text-slate-800">{p.first_name} {p.last_name}</span>
                    </div>
                  </td>
                  <td className="px-4 py-3.5 text-slate-600">{p.activity}</td>
                  <td className="px-4 py-3.5 text-slate-600 hidden md:table-cell">{p.city}</td>
                  <td className="px-4 py-3.5 hidden lg:table-cell">
                    <a
                      href={`mailto:${p.email}`}
                      className="text-blue-600 hover:underline flex items-center gap-1"
                      onClick={e => e.stopPropagation()}
                    >
                      {p.email}
                      <ExternalLink size={11} className="opacity-0 group-hover:opacity-100" />
                    </a>
                  </td>
                  <td className="px-4 py-3.5 text-slate-600 hidden xl:table-cell">{p.phone}</td>
                  <td className="px-4 py-3.5 text-slate-500 hidden lg:table-cell">{p.source}</td>
                  <td className="px-4 py-3.5 w-28">
                    <ScoreBar score={p.score} />
                  </td>
                  <td className="px-4 py-3.5">
                    <StatusBadge status={p.status} />
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        {totalPages > 1 && (
          <div className="flex items-center justify-between px-4 py-3 border-t border-slate-50">
            <span className="text-xs text-slate-500">
              Showing {page * PAGE_SIZE + 1}–{Math.min((page + 1) * PAGE_SIZE, filtered.length)} of {filtered.length}
            </span>
            <div className="flex items-center gap-1">
              <button
                onClick={() => setPage(p => Math.max(0, p - 1))}
                disabled={page === 0}
                className="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed"
              >
                <ChevronLeft size={16} />
              </button>
              {Array.from({ length: totalPages }).map((_, i) => (
                <button
                  key={i}
                  onClick={() => setPage(i)}
                  className={`w-7 h-7 text-xs rounded-lg font-medium ${page === i ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100'}`}
                >
                  {i + 1}
                </button>
              ))}
              <button
                onClick={() => setPage(p => Math.min(totalPages - 1, p + 1))}
                disabled={page >= totalPages - 1}
                className="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed"
              >
                <ChevronRight size={16} />
              </button>
            </div>
          </div>
        )}
      </div>

      {showAdd && <AddProspectModal onClose={() => setShowAdd(false)} onSaved={fetchProspects} />}
    </div>
  );
}
