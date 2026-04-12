import { AppProvider, useApp } from './context/AppContext';
import Layout from './components/Layout';
import LoginPage from './pages/LoginPage';
import Dashboard from './pages/Dashboard';
import ProspectsList from './pages/ProspectsList';
import ProspectDetail from './pages/ProspectDetail';
import Pipeline from './pages/Pipeline';
import Campaigns from './pages/Campaigns';
import Settings from './pages/Settings';

function AppContent() {
  const { currentPage, loading } = useApp();

  if (loading) {
    return (
      <div className="min-h-screen bg-slate-50 flex items-center justify-center">
        <div className="flex flex-col items-center gap-3">
          <div className="w-8 h-8 border-2 border-blue-600 border-t-transparent rounded-full animate-spin" />
          <p className="text-sm text-slate-400">Loading...</p>
        </div>
      </div>
    );
  }

  if (currentPage === 'login') {
    return <LoginPage />;
  }

  return (
    <Layout>
      {currentPage === 'dashboard' && <Dashboard />}
      {currentPage === 'prospects' && <ProspectsList />}
      {currentPage === 'prospect-detail' && <ProspectDetail />}
      {currentPage === 'pipeline' && <Pipeline />}
      {currentPage === 'campaigns' && <Campaigns />}
      {currentPage === 'settings' && <Settings />}
    </Layout>
  );
}

export default function App() {
  return (
    <AppProvider>
      <AppContent />
    </AppProvider>
  );
}
