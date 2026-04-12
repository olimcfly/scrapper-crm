import { Search, Bell } from 'lucide-react';
import { useApp } from '../context/AppContext';

const pageTitles: Record<string, string> = {
  dashboard: 'Dashboard',
  prospects: 'Prospects',
  'prospect-detail': 'Prospect Detail',
  pipeline: 'Pipeline',
  campaigns: 'Campaigns',
  settings: 'Settings',
};

export default function Header() {
  const { currentPage, user } = useApp();
  const initials = user?.email?.slice(0, 2).toUpperCase() ?? 'U';

  return (
    <header className="h-16 bg-white border-b border-slate-100 flex items-center px-6 gap-4 sticky top-0 z-10">
      <div className="flex-1">
        <h1 className="text-base font-semibold text-slate-800">
          {pageTitles[currentPage] ?? ''}
        </h1>
      </div>

      <div className="flex items-center gap-3">
        <div className="relative">
          <Search size={15} className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
          <input
            type="text"
            placeholder="Search prospects..."
            className="pl-9 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg w-56 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition-all"
          />
        </div>

        <button className="relative w-9 h-9 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-50 transition-colors">
          <Bell size={17} />
          <span className="absolute top-1.5 right-1.5 w-2 h-2 bg-blue-500 rounded-full"></span>
        </button>

        <div className="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center cursor-pointer">
          <span className="text-xs font-bold text-white">{initials}</span>
        </div>
      </div>
    </header>
  );
}
