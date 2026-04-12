import React, { createContext, useContext, useState, useEffect } from 'react';
import { User, Session } from '@supabase/supabase-js';
import { supabase } from '../lib/supabase';
import { Page } from '../types';

interface AppContextType {
  user: User | null;
  session: Session | null;
  currentPage: Page;
  selectedProspectId: string | null;
  navigate: (page: Page, prospectId?: string) => void;
  signOut: () => Promise<void>;
  loading: boolean;
}

const AppContext = createContext<AppContextType | undefined>(undefined);

export function AppProvider({ children }: { children: React.ReactNode }) {
  const [user, setUser] = useState<User | null>(null);
  const [session, setSession] = useState<Session | null>(null);
  const [currentPage, setCurrentPage] = useState<Page>('login');
  const [selectedProspectId, setSelectedProspectId] = useState<string | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    supabase.auth.getSession().then(({ data: { session } }) => {
      setSession(session);
      setUser(session?.user ?? null);
      if (session) setCurrentPage('dashboard');
      setLoading(false);
    });

    const { data: { subscription } } = supabase.auth.onAuthStateChange((event, session) => {
      setSession(session);
      setUser(session?.user ?? null);
      if (session) {
        setCurrentPage('dashboard');
      } else {
        setCurrentPage('login');
      }
    });

    return () => subscription.unsubscribe();
  }, []);

  const navigate = (page: Page, prospectId?: string) => {
    setCurrentPage(page);
    if (prospectId !== undefined) setSelectedProspectId(prospectId);
  };

  const signOut = async () => {
    await supabase.auth.signOut();
    setCurrentPage('login');
  };

  return (
    <AppContext.Provider value={{ user, session, currentPage, selectedProspectId, navigate, signOut, loading }}>
      {children}
    </AppContext.Provider>
  );
}

export function useApp() {
  const context = useContext(AppContext);
  if (!context) throw new Error('useApp must be used within AppProvider');
  return context;
}
