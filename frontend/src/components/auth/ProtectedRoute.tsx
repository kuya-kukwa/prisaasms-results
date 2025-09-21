'use client';

import Image from 'next/image';
import { useAuth } from '../../contexts/AuthContext';
import { useRouter } from 'next/navigation';
import { useEffect, ReactNode } from 'react';

interface ProtectedRouteProps {
  children: ReactNode;
  allowedRoles?: ('admin' | 'coach' | 'tournament_manager')[];
  redirectTo?: string;
}

export default function ProtectedRoute({ 
  children, 
  allowedRoles, 
  redirectTo = '/login' 
}: ProtectedRouteProps) {
  const { user, loading, isAuthenticated } = useAuth();
  const router = useRouter();

  useEffect(() => {
    if (!loading) {
      // Not authenticated, redirect to login
      if (!isAuthenticated) {
        router.push(redirectTo);
        return;
      }

      // Check role-based access
      if (allowedRoles && user && !allowedRoles.includes(user.role)) {
        // User doesn't have permission, redirect to their dashboard
        switch (user.role) {
          case 'admin':
            router.push('/dashboard/admin');
            break;
          case 'coach':
            router.push('/dashboard/coach');
            break;
          case 'tournament_manager':
            router.push('/dashboard/tournament');
            break;
          default:
            router.push('/');
        }
        return;
      }
    }
  }, [loading, isAuthenticated, user, allowedRoles, router, redirectTo]);

  // Show loading while checking authentication
  if (loading) {
      return (
        <div
          className="min-h-screen flex items-center justify-center bg-white dark:bg-gray-900"
          aria-busy="true"
        >
          <div className="flex flex-col items-center space-y-4">
            <Image 
              src="/logo.png" 
              alt="PRISAA Logo" 
              width={60}
              height={60}
              priority={true}
              className="border-2 border-gray-300 dark:border-white rounded-full shadow-lg dark:shadow-white/10 animate-[bounce_1.5s_infinite]"
            />
            <p className="text-gray-600 dark:text-gray-300 text-sm animate-pulse">
              Loading your dashboard...
            </p>
          </div>
        </div>
      );
    }

  // Not authenticated
  if (!isAuthenticated) {
    return null; // Will redirect in useEffect
  }

  // Check role permissions
  if (allowedRoles && user && !allowedRoles.includes(user.role)) {
    return null; // Will redirect in useEffect
  }

  return <>{children}</>;
}