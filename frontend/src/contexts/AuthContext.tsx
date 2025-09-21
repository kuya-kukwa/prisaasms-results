'use client';

import React, { createContext, useContext, useState, useEffect, useCallback, ReactNode } from 'react';
import { apiClient, User } from '../lib/api';
import { useRouter } from 'next/navigation';
import toast from 'react-hot-toast';

interface AuthContextType {
  user: User | null;
  loading: boolean;
  login: (email: string, password: string) => Promise<boolean>;
  logout: () => Promise<void>;
  register: (userData: RegisterData) => Promise<boolean>;
  isAuthenticated: boolean;
  isAdmin: boolean;
  isCoach: boolean;
  isTournamentManager: boolean;
  role: 'admin' | 'coach' | 'tournament_manager' | null;
  redirectToDashboard: () => void;
}

interface RegisterData {
  first_name: string;
  last_name: string;
  email: string;
  password: string;
  password_confirmation: string;
  contact_number?: string;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState(true);
  const router = useRouter();

  useEffect(() => {
    // Check if user is already authenticated
    checkAuth();
  }, []);

  const checkAuth = async () => {
    try {
      // Only check if there's a token present
      const token = typeof window !== 'undefined' ? localStorage.getItem('prisaa_token') : null;
      if (!token) {
        setLoading(false);
        return;
      }

      // Try to get current user if token exists
      const response = await apiClient.auth.me();
      if (response.success) {
        setUser(response.data);
      }
    } catch (error) {
      console.log('Not authenticated:', error);
      // Clear invalid token
      apiClient.clearToken();
    } finally {
      setLoading(false);
    }
  };

  const login = async (email: string, password: string): Promise<boolean> => {
    try {
      const response = await apiClient.auth.login(email, password);
      if (response.success && response.data) {
        const { user: userData, token } = response.data;
        
        // Set token in API client (it handles storage)
        apiClient.setToken(token);
        setUser(userData);
        
        toast.success(`Welcome back, ${userData.first_name}!`);
        return true;
      }
      return false;
    } catch (error) {
      console.error('Login error:', error);
      toast.error('Login failed');
      return false;
    }
  };

  const logout = useCallback(async (): Promise<void> => {
    try {
      await apiClient.auth.logout();
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      setUser(null);
      apiClient.clearToken();
      router.push('/login');
      toast.success('Logged out successfully');
    }
  }, [router]);

  const register = async (userData: RegisterData): Promise<boolean> => {
    try {
      const response = await apiClient.auth.register(userData);
      if (response.success && response.data) {
        setUser(response.data.user);
        toast.success('Registration successful!');
        return true;
      }
      return false;
    } catch (error) {
      console.error('Registration error:', error);
      toast.error('Registration failed');
      return false;
    }
  };

  const redirectToDashboard = () => {
    if (!user) return;
    
    switch (user.role) {
      case 'admin':
        router.push('/dashboard/admin');
        break;
      case 'coach':
        router.push('/dashboard/coach');
        break;
      case 'tournament_manager':
        router.push('/dashboard/tournament-manager');
        break;
      default:
        router.push('/dashboard');
    }
  };

  const value: AuthContextType = {
    user,
    loading,
    login,
    logout,
    register,
    isAuthenticated: !!user,
    isAdmin: user?.role === 'admin',
    isCoach: user?.role === 'coach',
    isTournamentManager: user?.role === 'tournament_manager',
    role: user?.role as 'admin' | 'coach' | 'tournament_manager' | null,
    redirectToDashboard,
  };

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth() {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
}