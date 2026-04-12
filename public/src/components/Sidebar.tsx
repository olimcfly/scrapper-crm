import { LayoutDashboard, Users, Kanban, Mail, Settings, Heart, LogOut } from 'lucide-react';
import { useApp } from '../context/AppContext';
import { Page } from '../types';

const navItems: { label: string; icon: React.ReactNode; page: Page }[] = [
  { label: 'Dashboard', icon: <LayoutDashboard size={18} />, page: 'dashboard' },
  { label: 'Prospects', icon: <Users size={18} />, page: 'prospects' },
  { label: 'Pipeline', icon: <Kanban size={18} />, page: 'pipeline' },
  { label: 'Campaigns', icon: <Mail size={18} />, page: 'campaigns' },
  { label: 'Settings', icon: <Settings size={18} />, page: 'settings' },
];

export default function Sidebar() {
  const { currentPage, navigate, signOut, user } = useApp();

  return (
    <aside className="w-60 flex-shrink-0 bg-white border-r border-slate-100 flex flex-col h-screen sticky top-0">
      <div className="px-6 py-5 border-b border-slate-100">
        <div className="flex items-center gap-2.5">
          <div className="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
            <Heart size={16} className="text-white" fill="white" />
          </div>
          <div>
            <p className="text-sm font-bold text-slate-800 leading-tight">Wellness</p>
            <p className="text-xs text-slate-400 leading-tight">Prospect CRM</p>
          </div>
        </div>
      </div>

      <nav className="flex-1 px-3 py-4 space-y-0.5">
        {navItems.map((item) => {
          const isActive = currentPage === item.page || (currentPage === 'prospect-detail' && item.page === 'prospects');
          return (
            <button
              key={item.page}
              onClick={() => navigate(item.page)}
              className={`w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 ${
                isActive
                  ? 'bg-blue-50 text-blue-700'
                  : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700'
              }`}
            >
              <span className={isActive ? 'text-blue-600' : ''}>{item.icon}</span>
              {item.label}
            </button>
          );
        })}
      </nav>

      <div className="px-3 py-4 border-t border-slate-100">
        <div className="px-3 py-2 mb-1">
          <p className="text-xs font-medium text-slate-700 truncate">{user?.email}</p>
          <p className="text-xs text-slate-400">Free plan</p>
        </div>
        <button
          onClick={signOut}
          className="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-500 hover:bg-red-50 hover:text-red-600 transition-all duration-150"
        >
          <LogOut size={18} />
          Sign out
        </button>
      </div>
    </aside>
  );
}
