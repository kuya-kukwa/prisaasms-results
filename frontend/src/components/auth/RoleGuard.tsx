'use client';

import { useAuth } from '../../contexts/AuthContext';
import { ReactNode } from 'react';

interface RoleGuardProps {
  children: ReactNode;
  allowedRoles: ('admin' | 'coach' | 'tournament_manager')[];
  fallback?: ReactNode;
}

export default function RoleGuard({ 
  children, 
  allowedRoles, 
  fallback = null 
}: RoleGuardProps) {
  const { user, isAuthenticated } = useAuth();

  // Not authenticated
  if (!isAuthenticated || !user) {
    return <>{fallback}</>;
  }

  // Check if user's role is allowed
  if (!allowedRoles.includes(user.role)) {
    return <>{fallback}</>;
  }

  return <>{children}</>;
}

// Specific role guards for convenience
export function AdminOnly({ children, fallback = null }: { children: ReactNode; fallback?: ReactNode }) {
  return (
    <RoleGuard allowedRoles={['admin']} fallback={fallback}>
      {children}
    </RoleGuard>
  );
}

export function CoachOnly({ children, fallback = null }: { children: ReactNode; fallback?: ReactNode }) {
  return (
    <RoleGuard allowedRoles={['coach']} fallback={fallback}>
      {children}
    </RoleGuard>
  );
}

export function TournamentManagerOnly({ children, fallback = null }: { children: ReactNode; fallback?: ReactNode }) {
  return (
    <RoleGuard allowedRoles={['tournament_manager']} fallback={fallback}>
      {children}
    </RoleGuard>
  );
}

export function AdminOrTournamentManager({ children, fallback = null }: { children: ReactNode; fallback?: ReactNode }) {
  return (
    <RoleGuard allowedRoles={['admin', 'tournament_manager']} fallback={fallback}>
      {children}
    </RoleGuard>
  );
}
