'use client';

import React, { useState, useEffect, useCallback } from 'react';
import { useAuth } from '@/src/contexts/AuthContext';
import { useRouter } from 'next/navigation';
import { Separator } from '@/components/ui/separator';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { 
  Trophy, 
  Calendar, 
  Users,
  ClipboardCheck,
  MapPin,
  Medal,
  RefreshCw,
  Plus
} from 'lucide-react';
import DashboardSkeleton from '@/components/ui/dashboard-skeleton';
import ScrollCounter from '@/components/ui/scroll-counter';
import { tournamentManagerApi, type TournamentManagerDashboardData } from '@/src/lib/tournament-manager-dashboard-api';

export default function TournamentManagerDashboard() {
  const { user, isAuthenticated } = useAuth();
  const router = useRouter();
  const [dashboardData, setDashboardData] = useState<TournamentManagerDashboardData | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [isRefreshing, setIsRefreshing] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const loadDashboardData = useCallback(async (showRefresh = false) => {
    if (!user) return;
    
    try {
      if (showRefresh) {
        setIsRefreshing(true);
        setError(null);
        
        // Ensure minimum refresh time for better UX
        const refreshPromise = tournamentManagerApi.getDashboardData();
        const minTimePromise = new Promise(resolve => setTimeout(resolve, 1000));
        
        const [data] = await Promise.all([refreshPromise, minTimePromise]);
        setDashboardData(data);
      } else {
        setIsLoading(true);
        setError(null);
        
        const data = await tournamentManagerApi.getDashboardData();
        setDashboardData(data);
      }
    } catch (err) {
      console.error('Error fetching dashboard data:', err);
      // Set empty data instead of error state to show the UI
      setDashboardData({
        tournaments: { upcoming: [], ongoing: [], completed: [], total: 0 },
        matches: { upcoming: [], completed: [], total: 0 },
        officials: { available: [], total: 0 },
        results: { recent: [], pending: [], total: 0 },
        venues: { available: [], total: 0 },
        rankings: { recent: [], total: 0 },
        medals: { recent: [], total: 0 }
      });
      setError('Some data could not be loaded. This may be because the database is empty or you need to check your permissions.');
    } finally {
      setIsLoading(false);
      setIsRefreshing(false);
    }
  }, [user]);

  const handleRefresh = () => {
    loadDashboardData(true);
  };

  useEffect(() => {
    if (!isAuthenticated) {
      router.push('/login');
      return;
    }

    loadDashboardData();
  }, [isAuthenticated, router, loadDashboardData]);

  if (isLoading) {
    return <DashboardSkeleton />;
  }

  // Show skeleton when refreshing
  if (isRefreshing) {
    return <DashboardSkeleton />;
  }

  if (!dashboardData) {
    return (
      <div className="space-y-6">
        <div className="bg-gray-50 border border-gray-200 rounded-lg p-6">
          <h2 className="text-lg font-semibold text-gray-800 mb-2">No Data Available</h2>
          <p className="text-gray-600">Unable to load dashboard data.</p>
          <button 
            onClick={() => loadDashboardData(false)}
            className="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors"
          >
            Retry
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="flex-1 flex flex-col">
      <header className="sticky top-0 z-50 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 flex h-12 shrink-0 items-center gap-2 border-b">
        <div className="flex items-center gap-2 px-4">
          <SidebarTrigger className="-ml-1" />
          <Separator orientation="vertical" className="mr-2 h-4" />
          <nav className="flex items-center space-x-2 text-sm">
            <span className="text-muted-foreground">Tournament Manager Dashboard</span>
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
          {/* Error notification */}
          {error && (
            <Card className="border-yellow-200 bg-yellow-50">
              <CardContent className="pt-6">
                <div className="flex">
                  <div className="flex-shrink-0">
                    <svg className="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                      <path fillRule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
                    </svg>
                  </div>
                  <div className="ml-3">
                    <h3 className="text-sm font-medium text-yellow-800">Data Loading Issues</h3>
                    <div className="mt-2 text-sm text-yellow-700">
                      <p>{error}</p>
                    </div>
                    <div className="mt-4">
                      <Button
                        onClick={() => loadDashboardData(false)}
                        size="sm"
                        variant="outline"
                        className="text-yellow-800 border-yellow-300 hover:bg-yellow-100"
                      >
                        Retry Loading
                      </Button>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>
          )}

          {/* Welcome Section */}
          <div className="mb-8">
            <h1 className="text-xl font-bold text-gray-900 dark:text-white">
              Welcome back, {user?.first_name || 'User'}!
            </h1>
            <p className="text-sm text-gray-600 dark:text-gray-400 mt-2">
              Manage tournaments, matches, and competition results for PRISAA events. Coordinate all aspects of sporting competitions.
            </p>
          </div>

          {/* Stats Grid */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <Card className="shadow-sm hover:shadow-md transition-shadow duration-200">
              <CardHeader className="flex flex-row items-center justify-between pb-2">
                <CardTitle className="text-sm font-semibold text-gray-700 dark:text-gray-200">
                  Active Tournaments
                </CardTitle>
                <Trophy className="h-5 w-5 text-blue-600" />
              </CardHeader>
              <CardContent>
                <ScrollCounter
                  end={dashboardData.tournaments.ongoing.length}
                  className="text-3xl font-bold text-gray-900 dark:text-white"
                />
                <p className="text-sm text-muted-foreground">Currently running</p>
              </CardContent>
            </Card>

            <Card className="shadow-sm hover:shadow-md transition-shadow duration-200">
              <CardHeader className="flex flex-row items-center justify-between pb-2">
                <CardTitle className="text-sm font-semibold text-gray-700 dark:text-gray-200">
                  Upcoming Matches
                </CardTitle>
                <Calendar className="h-5 w-5 text-green-600" />
              </CardHeader>
              <CardContent>
                <ScrollCounter
                  end={dashboardData.matches.upcoming.length}
                  className="text-3xl font-bold text-gray-900 dark:text-white"
                />
                <p className="text-sm text-muted-foreground">Next 7 days</p>
              </CardContent>
            </Card>

            <Card className="shadow-sm hover:shadow-md transition-shadow duration-200">
              <CardHeader className="flex flex-row items-center justify-between pb-2">
                <CardTitle className="text-sm font-semibold text-gray-700 dark:text-gray-200">
                  Available Officials
                </CardTitle>
                <Users className="h-5 w-5 text-purple-600" />
              </CardHeader>
              <CardContent>
                <ScrollCounter
                  end={dashboardData.officials.available.length}
                  className="text-3xl font-bold text-gray-900 dark:text-white"
                />
                <p className="text-sm text-muted-foreground">Ready to assign</p>
              </CardContent>
            </Card>

            <Card className="shadow-sm hover:shadow-md transition-shadow duration-200">
              <CardHeader className="flex flex-row items-center justify-between pb-2">
                <CardTitle className="text-sm font-semibold text-gray-700 dark:text-gray-200">
                  Pending Results
                </CardTitle>
                <ClipboardCheck className="h-5 w-5 text-orange-500" />
              </CardHeader>
              <CardContent>
                <ScrollCounter
                  end={dashboardData.results.pending.length}
                  className="text-3xl font-bold text-gray-900 dark:text-white"
                />
                <p className="text-sm text-muted-foreground">Awaiting entry</p>
              </CardContent>
            </Card>
          </div>

          {/* Quick Actions Grid */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <Card className="hover:shadow-md transition-shadow cursor-pointer">
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Plus className="h-5 w-5 text-blue-600" />
                  Tournament Management
                </CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground mb-4">
                  Create and manage tournament events
                </p>
                <Button className="w-full" variant="outline">
                  Manage Tournaments
                </Button>
              </CardContent>
            </Card>

            <Card className="hover:shadow-md transition-shadow cursor-pointer">
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Calendar className="h-5 w-5 text-green-600" />
                  Match Scheduling
                </CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground mb-4">
                  Schedule matches and assign venues
                </p>
                <Button className="w-full" variant="outline">
                  Schedule Matches
                </Button>
              </CardContent>
            </Card>

            <Card className="hover:shadow-md transition-shadow cursor-pointer">
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <ClipboardCheck className="h-5 w-5 text-purple-600" />
                  Results & Scoring
                </CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground mb-4">
                  Enter and verify match results
                </p>
                <Button className="w-full" variant="outline">
                  Manage Results
                </Button>
              </CardContent>
            </Card>

            <Card className="hover:shadow-md transition-shadow cursor-pointer">
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Users className="h-5 w-5 text-orange-600" />
                  Officials Assignment
                </CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground mb-4">
                  Assign referees and officials to matches
                </p>
                <Button className="w-full" variant="outline">
                  Assign Officials
                </Button>
              </CardContent>
            </Card>

            <Card className="hover:shadow-md transition-shadow cursor-pointer">
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Medal className="h-5 w-5 text-indigo-600" />
                  Rankings & Medals
                </CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground mb-4">
                  Update rankings and medal tallies
                </p>
                <Button className="w-full" variant="outline">
                  Update Rankings
                </Button>
              </CardContent>
            </Card>

            <Card className="hover:shadow-md transition-shadow cursor-pointer">
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <MapPin className="h-5 w-5 text-teal-600" />
                  Venue Management
                </CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground mb-4">
                  Manage competition venues and facilities
                </p>
                <Button className="w-full" variant="outline">
                  Manage Venues
                </Button>
              </CardContent>
            </Card>
          </div>

          {/* Recent Activities */}
          <Card>
            <CardHeader>
              <CardTitle>Recent Activities</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {/* Recent Tournaments */}
                {dashboardData.tournaments.ongoing.length > 0 && (
                  <>
                    <h4 className="text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Active Tournaments</h4>
                    {dashboardData.tournaments.ongoing.slice(0, 3).map((tournament) => (
                      <div key={tournament.id} className="flex items-center space-x-4">
                        <Trophy className="h-4 w-4 text-blue-600" />
                        <div className="flex-1">
                          <p className="text-sm font-medium">{tournament.name}</p>
                          <p className="text-xs text-muted-foreground">
                            {tournament.level} • {tournament.status}
                          </p>
                        </div>
                        <div className="text-xs text-muted-foreground">
                          {new Date(tournament.start_date).toLocaleDateString()}
                        </div>
                      </div>
                    ))}
                  </>
                )}

                {/* Recent Matches */}
                {dashboardData.matches.upcoming.length > 0 && (
                  <>
                    <h4 className="text-sm font-medium text-gray-700 dark:text-gray-200 mb-2 mt-6">Upcoming Matches</h4>
                    {dashboardData.matches.upcoming.slice(0, 3).map((match) => (
                      <div key={match.id} className="flex items-center space-x-4">
                        <Calendar className="h-4 w-4 text-green-600" />
                        <div className="flex-1">
                          <p className="text-sm font-medium">
                            {match.team1?.name || 'Team 1'} vs {match.team2?.name || 'Team 2'}
                          </p>
                          <p className="text-xs text-muted-foreground">
                            {match.venue?.name || 'Venue TBD'} • {match.start_time}
                          </p>
                        </div>
                        <div className="text-xs text-muted-foreground">
                          {new Date(match.scheduled_date).toLocaleDateString()}
                        </div>
                      </div>
                    ))}
                  </>
                )}

                {/* No Data Fallback */}
                {dashboardData.tournaments.ongoing.length === 0 && dashboardData.matches.upcoming.length === 0 && (
                  <p className="text-center text-muted-foreground py-4">
                    No recent activities
                  </p>
                )}
              </div>
            </CardContent>
          </Card>
        </div>
      </main>
    </div>
  );
}
