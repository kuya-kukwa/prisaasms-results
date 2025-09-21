"use client";

import { useEffect } from "react";
import { useRouter } from "next/navigation";
import { useAuth } from "@/src/contexts/AuthContext";
import { Activity } from "lucide-react";

export default function DashboardRedirect() {
  const router = useRouter();
  const { user, loading, isAuthenticated, redirectToDashboard } = useAuth();

  useEffect(() => {
    if (!loading) {
      if (!isAuthenticated) {
        router.push('/login');
      } else if (user) {
        redirectToDashboard();
      }
    }
  }, [loading, isAuthenticated, user, redirectToDashboard, router]);

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center">
        <div className="text-center">
          <Activity className="w-8 h-8 animate-spin text-blue-600 mx-auto mb-4" />
          <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-2">
            Loading dashboard...
          </h2>
          <p className="text-gray-600 dark:text-gray-400">
            Redirecting to your dashboard
          </p>
        </div>
      </div>
    );
  }

  return null;
}