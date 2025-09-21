"use client";

import React, { useEffect, useState, useCallback } from 'react';
import { useAuth } from '@/src/contexts/AuthContext';
import { useRouter } from 'next/navigation';
import { Separator } from '@/components/ui/separator';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { 
  Users, 
  Trophy, 
  Activity,
  RefreshCw,
  UserPlus,
  Calendar,
  Target,
  Clock
} from 'lucide-react';
import DashboardSkeleton from '@/components/ui/dashboard-skeleton';
import ScrollCounter from '@/components/ui/scroll-counter';
import { coachService, CoachStats, CoachActivity } from '@/src/lib/coach-dashboard-api';

function CoachDashboard() {
  const { user, isAuthenticated } = useAuth();
  const router = useRouter();
  const [stats, setStats] = useState<CoachStats>({
    myAthletes: 0,
    myTeams: 0,
    upcomingMatches: 0,
    activeCompetitions: 0
  });
  const [activities, setActivities] = useState<CoachActivity[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [isRefreshing, setIsRefreshing] = useState(false);

  const loadCoachData = useCallback(async (showRefresh = false) => {
    try {
      if (showRefresh) {
        setIsRefreshing(true);
        
        // Ensure minimum refresh time for better UX
        const refreshPromise = coachService.getCoachDashboardData(user || undefined, true);
        const minTimePromise = new Promise(resolve => setTimeout(resolve, 1000));
        
        const [data] = await Promise.all([refreshPromise, minTimePromise]);
        setStats(data.stats);
        setActivities(data.activities);
      } else {
        setIsLoading(true);
        // Fast load for initial - use cache if available
        const data = await coachService.getCoachDashboardData(user || undefined, false);
        setStats(data.stats);
        setActivities(data.activities);
      }
    } catch (error) {
      console.error('Failed to load coach data:', error);
      // Set fallback data on error
      setStats({
        myAthletes: 0,
        myTeams: 0,
        upcomingMatches: 0,
        activeCompetitions: 0
      });
      setActivities([]);
    } finally {
      setIsLoading(false);
      setIsRefreshing(false);
    }
  }, [user]);

  const handleRefresh = () => {
    loadCoachData(true);
  };

  useEffect(() => {
    if (!isAuthenticated) {
      router.push('/login');
      return;
    }

    loadCoachData();
  }, [isAuthenticated, router, loadCoachData]);

  if (isLoading) {
    return <DashboardSkeleton />;
  }

  // Show skeleton when refreshing
  if (isRefreshing) {
    return <DashboardSkeleton />;
  }

  return (
    <div className="flex-1 flex flex-col">
      <header className="sticky top-0 z-50 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 flex h-12 shrink-0 items-center gap-2 border-b">
        <div className="flex items-center gap-2 px-4">
          <SidebarTrigger className="-ml-1" />
          <Separator orientation="vertical" className="mr-2 h-4" />
          <nav className="flex items-center space-x-2 text-sm">
            <span className="text-muted-foreground">Coach Dashboard</span>
            <span className="text-muted-foreground">/</span>
            <span>Overview</span>
          </nav>
        </div>
        <div className="flex-1" />
        <div className="px-4 flex items-center gap-2">
          <Button
            variant="outline"
            size="sm"
            onClick={handleRefresh}
            disabled={isRefreshing}
            className="gap-2"
          >
            <RefreshCw className={`h-4 w-4 ${isRefreshing ? 'animate-spin' : ''}`} />
            Refresh
          </Button>
        </div>
      </header>

      {/* Main Content */}
      <main className="flex-1 p-6 overflow-auto">
        <div className="space-y-8">
          {/* Welcome Section */}
          <div className="mb-8">
            <h1 className="text-xl font-bold text-gray-900 dark:text-white">
              Welcome back, {user?.first_name || 'User'}!
            </h1>
            <p className="text-sm text-gray-600 dark:text-gray-400 mt-2">
              Manage your athletes and teams for PRISAA competitions. Track performance and lead your teams to victory.
            </p>
          </div>

          {/* Stats Grid */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <Card>
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">My Athletes</CardTitle>
                <Users className="h-4 w-4 text-blue-600" />
              </CardHeader>
              <CardContent>
                <ScrollCounter
                  end={stats.myAthletes}
                  className="text-2xl font-bold"
                />
                <p className="text-xs text-muted-foreground">
                  Active athletes
                </p>
              </CardContent>
            </Card>

            <Card>
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">My Teams</CardTitle>
                <Trophy className="h-4 w-4 text-green-600" />
              </CardHeader>
              <CardContent>
                <ScrollCounter
                  end={stats.myTeams}
                  className="text-2xl font-bold"
                />
                <p className="text-xs text-muted-foreground">
                  Active teams
                </p>
              </CardContent>
            </Card>

            <Card>
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">Upcoming Matches</CardTitle>
                <Calendar className="h-4 w-4 text-orange-600" />
              </CardHeader>
              <CardContent>
                <ScrollCounter
                  end={stats.upcomingMatches}
                  className="text-2xl font-bold"
                />
                <p className="text-xs text-muted-foreground">
                  Next 7 days
                </p>
              </CardContent>
            </Card>

            <Card>
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">Active Competitions</CardTitle>
                <Target className="h-4 w-4 text-purple-600" />
              </CardHeader>
              <CardContent>
                <ScrollCounter
                  end={stats.activeCompetitions}
                  className="text-2xl font-bold"
                />
                <p className="text-xs text-muted-foreground">
                  Currently running
                </p>
              </CardContent>
            </Card>
          </div>

          {/* Quick Actions Grid */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <Card className="hover:shadow-md transition-shadow cursor-pointer">
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <UserPlus className="h-5 w-5 text-blue-600" />
                  Manage Athletes
                </CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground mb-4">
                  Add, edit, and manage your athlete roster
                </p>
                <Button
                  className="w-full"
                    variant="outline"
                      onClick={() => router.push("/dashboard/coach/my-athletes")}>
                      Go to Athletes
                    </Button>

              </CardContent>
            </Card>

            <Card className="hover:shadow-md transition-shadow cursor-pointer">
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Trophy className="h-5 w-5 text-green-600" />
                  Team Management
                </CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground mb-4">
                  Create and organize team compositions
                </p>
                <Button className="w-full" variant="outline">
                  Manage Teams
                </Button>
              </CardContent>
            </Card>

            <Card className="hover:shadow-md transition-shadow cursor-pointer">
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Calendar className="h-5 w-5 text-orange-600" />
                  Match Schedule
                </CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground mb-4">
                  View upcoming matches and results
                </p>
                <Button className="w-full" variant="outline">
                  View Schedule
                </Button>
              </CardContent>
            </Card>

            <Card className="hover:shadow-md transition-shadow cursor-pointer">
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Target className="h-5 w-5 text-purple-600" />
                  Tournament Registration
                </CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground mb-4">
                  Register teams for upcoming tournaments
                </p>
                <Button className="w-full" variant="outline">
                  Register Teams
                </Button>
              </CardContent>
            </Card>

            <Card className="hover:shadow-md transition-shadow cursor-pointer">
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Activity className="h-5 w-5 text-indigo-600" />
                  Training Sessions
                </CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground mb-4">
                  Schedule and track training activities
                </p>
                <Button className="w-full" variant="outline">
                  Manage Training
                </Button>
              </CardContent>
            </Card>
          </div>

          {/* Recent Activities */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Clock className="h-5 w-5" />
                Recent Activities
              </CardTitle>
            </CardHeader>
            <CardContent>
              {activities.length > 0 ? (
                <div className="space-y-4">
                  {activities.map((activity) => (
                    <div key={activity.id} className="flex items-center space-x-4">
                      <div className="flex-shrink-0">
                        {activity.type === 'athlete' && <UserPlus className="h-4 w-4 text-blue-600" />}
                        {activity.type === 'team' && <Trophy className="h-4 w-4 text-green-600" />}
                        {activity.type === 'match' && <Calendar className="h-4 w-4 text-orange-600" />}
                        {activity.type === 'training' && <Activity className="h-4 w-4 text-purple-600" />}
                        {activity.type === 'registration' && <Target className="h-4 w-4 text-indigo-600" />}
                      </div>
                      <div className="flex-1">
                        <p className="text-sm font-medium">{activity.action}</p>
                        <p className="text-xs text-muted-foreground">
                          {activity.description}
                        </p>
                      </div>
                      <div className="text-xs text-muted-foreground">
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

export default CoachDashboard;
