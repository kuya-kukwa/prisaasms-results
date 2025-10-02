"use client";

import React, { useEffect, useState } from 'react';
import { useAuth } from '@/src/contexts/AuthContext';
import { useRouter } from 'next/navigation';
import { Separator } from '@/components/ui/separator';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { 
  Users, 
  Trophy, 
  Activity,
  Calendar,
  Clock,
  CheckCircle
} from 'lucide-react';
import HybridOnlineUsers from '@/components/ui/online-users';
import ScrollCounter from '@/components/ui/scroll-counter';
import { dashboardService, RecentActivity } from '@/src/lib/admin-dashboard-api';
import Link from 'next/link';
import { BarChart3 } from 'lucide-react';

function AdminDashboard() {
  const { user, isAuthenticated } = useAuth();
  const router = useRouter();
  const [stats, setStats] = useState({
    totalAthletes: 0,
    activeTournaments: 0,
    upcomingEvents: 0,
    totalSchools: 0,
    totalOfficials: 0,
    totalCoaches: 0,
    totalTournamentManagers: 0,
    pendingRegistrations: 0,
    pendingVerifications: 0
  });
  const [activities, setActivities] = useState<RecentActivity[]>([]);
  const loadDashboardData = async () => {
    try {
      const data = await dashboardService.getDashboardData(true);
      setStats({
        totalAthletes: data.stats.totalAthletes || 0,
        activeTournaments: data.stats.activeTournaments || 0,
        upcomingEvents: data.stats.upcomingEvents || 0,
        totalSchools: data.stats.totalSchools || 0,
        totalOfficials: data.stats.totalOfficials || 0,
        totalCoaches: data.stats.totalCoaches || 0,
        totalTournamentManagers: data.stats.totalTournamentManagers || 0,
        pendingRegistrations: data.stats.pendingRegistrations || 0,
        pendingVerifications: data.stats.pendingVerifications || 0
      });
      setActivities(data.recentActivities);
    } catch (error) {
      console.error('Failed to load dashboard data:', error);
    } finally {
    }
  };

useEffect(() => {
  if (!isAuthenticated) {
    router.push('/login');
    return;
  }

  loadDashboardData();

  // Log activities to debug the structure
  console.log('Activities:', activities);
}, [isAuthenticated, router, activities]);

  return (
    <div className="flex-1 flex flex-col">
      <header className="sticky top-0 z-50 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 flex h-12 shrink-0 items-center gap-2 border-b">
        <div className="flex items-center gap-2 px-4">
          <SidebarTrigger className="-ml-1" />
          <Separator orientation="vertical" className="mr-2 h-4" />
          <nav className="flex items-center space-x-1 text-xs sm:text-sm">
              <span className="text-muted-foreground">Admin Dashboard</span>
              <span className="text-muted-foreground">/</span>
              <span>Overview</span>
          </nav>
        </div>
            <div className="flex-1" />
            <div className="px-2 sm:px-4 flex items-center gap-2">
            {/* Online Users in Header */}
            <HybridOnlineUsers 
                compactMode={true}
                maxDisplayAvatars={3}
                showStatus={true}
                refreshInterval={30000}
                className="mr-2 sm:block"
            />
            </div>
        </header>

    {/* Main Content */}
    <main className="flex-1 p-4 sm:p-6 overflow-auto">
    <div className="space-y-4">
        {/* Welcome Section */}
        <div className="mb-8">
            <h1 className="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">
                Welcome back, {user?.first_name || 'Admin'}!
            </h1>
            <p className="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mt-2">
                Here&apos;s what&apos;s happening with your sports management system today.
            </p>
            {/* Quick Actions Section */}
            <div className="mt-4">
                <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 items-stretch">
                  <Link href="/dashboard/admin/profiles?create=athlete" className="block">
                        <Card className="hover:shadow-lg transition-shadow duration-200 cursor-pointer border-l-8 border-blue-900 h-16 flex items-center justify-center ">
                            <CardContent className="flex items-center space-x-3 sm:space-x-4 p-3 sm:p-4 gap-2">
                              <Users className="h-6 w-6 sm:h-8 sm:w-8 text-blue-600 flex-shrink-0" />
                                <div className="flex-1 min-w-0">
                                    <h3 className="font-medium text-gray-900 dark:text-white text-sm sm:text-base">Add Profile</h3>
                                    <p className="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Add a new profile.</p>
                                </div>
                            </CardContent>
                        </Card>
                    </Link>
                    <Link href="/dashboard/admin/schedules?create=schedule" className="block">
                        <Card className="hover:shadow-lg transition-shadow duration-200 cursor-pointer border-l-8 border-green-900 h-16 flex items-center justify-center">
                            <CardContent className="flex items-center space-x-3 sm:space-x-4 p-3 sm:p-4 gap-2">
                                <Calendar className="h-6 w-6 sm:h-8 sm:w-8 text-purple-600 flex-shrink-0" />
                                <div className="flex-1 min-w-0">
                                    <h3 className="font-medium text-gray-900 dark:text-white text-sm sm:text-base">New Schedule</h3>
                                    <p className="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Add a new event or schedule.</p>
                                </div>
                            </CardContent>
                        </Card>
                    </Link>
                    <Link href="/dashboard/admin/results" className="block">
                        <Card className="hover:shadow-lg transition-shadow duration-200 cursor-pointer border-l-8 border-orange-900 h-16 flex items-center justify-center ">
                            <CardContent className="flex items-center space-x-3 sm:space-x-4 p-3 sm:p-4 gap-2">
                                <Trophy className="h-6 w-6 sm:h-8 sm:w-8 text-orange-500 flex-shrink-0" />
                                <div className="flex-1 min-w-0">
                                    <h3 className="font-medium text-gray-900 dark:text-white text-sm sm:text-base">View Results</h3>
                                    <p className="text-xs sm:text-sm text-gray-600 dark:text-gray-400">View results and rankings.</p>
                                </div>
                            </CardContent>
                        </Card>
                    </Link>
                    <Link href="/dashboard/admin/reports" className="block">
                        <Card className="hover:shadow-lg transition-shadow duration-200 cursor-pointer border-l-8 border-cyan-500 h-16 flex items-center justify-center ">
                            <CardContent className="flex items-center justify-center space-x-3 sm:space-x-4 p-3 sm:p-4 gap-2">
                                <BarChart3 className="h-6 w-6 sm:h-8 sm:w-8 text-green-600 flex-shrink-0" />
                                <div className="flex-1 min-w-0">
                                    <h3 className="font-medium text-gray-900 dark:text-white text-sm sm:text-base">View Reports</h3>
                                    <p className="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Navigate to reports dashboard.</p>
                                </div>
                            </CardContent>
                        </Card>
                    </Link>
                </div>
            </div>
        </div>

        {/* Stats Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
        <Card className="shadow-sm hover:shadow-md transition-shadow duration-200">
            <CardHeader className="flex flex-row items-center justify-between pb-2">
            <CardTitle className="text-sm font-semibold text-gray-700 dark:text-gray-200">
                Total Athletes
            </CardTitle>
            <Users className="h-5 w-5 text-blue-600" />
            </CardHeader>
            <CardContent>
            <ScrollCounter
              end={stats.totalAthletes}
              className="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white"
            />
            <p className="text-sm text-muted-foreground">Registered competitors</p>
            <Link href="/dashboard/admin/profiles?type=athlete" className="text-xs text-blue-600 hover:underline mt-1 inline-block">
              View all →
            </Link>
            </CardContent>
        </Card>

        <Card className="shadow-sm hover:shadow-md transition-shadow duration-200">
            <CardHeader className="flex flex-row items-center justify-between pb-2">
            <CardTitle className="text-sm font-semibold text-gray-700 dark:text-gray-200">
                Pending Registrations
            </CardTitle>
            <Clock className="h-5 w-5 text-orange-500" />
            </CardHeader>
            <CardContent>
            <ScrollCounter
              end={stats.pendingRegistrations}
              className="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white"
            />
            <p className="text-sm text-muted-foreground">Awaiting approval</p>
            {stats.pendingRegistrations > 0 && (
              <Link href="/dashboard/admin/profiles?status=pending" className="mt-2 inline-flex items-center px-3 py-1 text-xs font-medium text-white bg-orange-600 rounded-md hover:bg-orange-700">
                Review ({stats.pendingRegistrations})
              </Link>
            )}
            </CardContent>
        </Card>

        <Card className="shadow-sm hover:shadow-md transition-shadow duration-200">
            <CardHeader className="flex flex-row items-center justify-between pb-2">
            <CardTitle className="text-sm font-semibold text-gray-700 dark:text-gray-200">
                Pending Verifications
            </CardTitle>
            <CheckCircle className="h-5 w-5 text-yellow-500" />
            </CardHeader>
            <CardContent>
            <ScrollCounter
              end={stats.pendingVerifications}
              className="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white"
            />
            <p className="text-sm text-muted-foreground">Need verification</p>
            <Link href="/dashboard/admin/verifications" className="text-xs text-yellow-600 hover:underline mt-1 inline-block">
              Manage →
            </Link>
            </CardContent>
        </Card>

        <Card className="shadow-sm hover:shadow-md transition-shadow duration-200">
            <CardHeader className="flex flex-row items-center justify-between pb-2">
            <CardTitle className="text-sm font-semibold text-gray-700 dark:text-gray-200">
                Active Tournaments
            </CardTitle>
            <Trophy className="h-5 w-5 text-green-600" />
            </CardHeader>
            <CardContent>
            <ScrollCounter
              end={stats.activeTournaments}
              className="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white"
            />
            <p className="text-sm text-muted-foreground">Currently running</p>
            <Link href="/dashboard/admin/tournaments?status=active" className="text-xs text-green-600 hover:underline mt-1 inline-block">
              View live →
            </Link>
            </CardContent>
        </Card>

        <Card className="shadow-sm hover:shadow-md transition-shadow duration-200">
            <CardHeader className="flex flex-row items-center justify-between pb-2">
            <CardTitle className="text-sm font-semibold text-gray-700 dark:text-gray-200">
                Upcoming Events
            </CardTitle>
            <Calendar className="h-5 w-5 text-purple-600" />
            </CardHeader>
            <CardContent>
            <ScrollCounter
              end={stats.upcomingEvents}
              className="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white"
            />
            <p className="text-sm text-muted-foreground">Next 7 days</p>
            <Link href="/dashboard/admin/schedules" className="text-xs text-purple-600 hover:underline mt-1 inline-block">
              View schedule →
            </Link>
            </CardContent>
        </Card>
        </div>
        {/* Recent Activities */}<Card>
  <CardHeader>
    <CardTitle className="text-base sm:text-lg">Recent Activities</CardTitle>
  </CardHeader>
  <CardContent>
    {activities.length > 0 ? (
      <div className="space-y-3 sm:space-y-4">
        {activities.map((activity) => (
          <div key={activity.id} className="flex items-start space-x-3 sm:space-x-4">
            <Activity className="h-4 w-4 text-blue-600 mt-0.5 flex-shrink-0" />
            <div className="flex-1 min-w-0">
              {/* Render action */}
              <p className="text-sm font-medium">
                {typeof activity.action === 'object' && 'name' in activity.action
                  ? activity.action.name
                  : activity.action}
              </p>
              {/* Render description */}
              <p className="text-xs text-muted-foreground">
                {typeof activity.description === 'object' && 'text' in activity.description
                  ? activity.description.text
                  : activity.description}
              </p>
            </div>
            <div className="text-xs text-muted-foreground flex-shrink-0">
              {new Date(activity.timestamp).toLocaleDateString()}
            </div>
          </div>
        ))}
      </div>
    ) : (
      <p className="text-center text-muted-foreground py-4">
        No recent activities
      </p>
    )}
  </CardContent>
</Card>
    
        </div>
      </main>
    </div>
  );
}

export default AdminDashboard;