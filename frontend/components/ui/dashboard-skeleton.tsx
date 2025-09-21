import React from 'react';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { SidebarTrigger } from '@/components/ui/sidebar';

interface DashboardSkeletonProps {
  statsCount?: number;
  actionsCount?: number;
  activitiesCount?: number;
  statusCards?: number;
  showSystemStatus?: boolean;
}

const DashboardSkeleton = ({ 
  statsCount = 4,
  actionsCount = 4,
  activitiesCount = 4,
  statusCards = 3,
  showSystemStatus = true
}: DashboardSkeletonProps) => {
  return (
    <div className="flex-1">
      {/* Header Skeleton */}
      <header className="flex h-16 shrink-0 items-center gap-2 border-b">
        <div className="flex items-center gap-2 px-4">
          <SidebarTrigger className="-ml-1" />
          <Separator orientation="vertical" className="mr-2 h-4" />
          <nav className="flex items-center space-x-2 text-sm">
            <div className="h-4 w-24 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
            <span className="text-muted-foreground">/</span>
            <div className="h-4 w-16 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
          </nav>
        </div>
      </header>

      {/* Main Content Skeleton */}
      <main className="flex-1 p-6">
        {/* Welcome Section Skeleton */}
        <div className="mb-8">
          <div className="h-8 w-80 bg-gray-200 dark:bg-gray-700 rounded animate-pulse mb-2"></div>
          <div className="h-4 w-96 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
        </div>

        {/* Stats Grid Skeleton */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          {Array.from({ length: statsCount }).map((_, index) => (
            <Card key={index} className="animate-pulse">
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <div className="h-4 w-24 bg-gray-200 dark:bg-gray-700 rounded"></div>
                <div className="h-4 w-4 bg-gray-200 dark:bg-gray-700 rounded"></div>
              </CardHeader>
              <CardContent>
                <div className="h-8 w-16 bg-gray-200 dark:bg-gray-700 rounded mb-2"></div>
                <div className="h-3 w-32 bg-gray-200 dark:bg-gray-700 rounded"></div>
              </CardContent>
            </Card>
          ))}
        </div>

        {/* Main Content Grid Skeleton */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {/* Quick Actions Skeleton */}
          <Card className="lg:col-span-2 animate-pulse">
            <CardHeader>
              <div className="h-6 w-28 bg-gray-200 dark:bg-gray-700 rounded mb-2"></div>
              <div className="h-4 w-48 bg-gray-200 dark:bg-gray-700 rounded"></div>
            </CardHeader>
            <CardContent className="grid grid-cols-1 md:grid-cols-2 gap-4">
              {Array.from({ length: actionsCount }).map((_, index) => (
                <div key={index} className="h-20 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
              ))}
            </CardContent>
          </Card>

          {/* Recent Activities Skeleton */}
          <Card className="animate-pulse">
            <CardHeader>
              <div className="h-6 w-32 bg-gray-200 dark:bg-gray-700 rounded mb-2"></div>
              <div className="h-4 w-28 bg-gray-200 dark:bg-gray-700 rounded"></div>
            </CardHeader>
            <CardContent className="space-y-4">
              {Array.from({ length: activitiesCount }).map((_, index) => (
                <div key={index} className="flex items-center space-x-3">
                  <div className="h-4 w-4 bg-gray-200 dark:bg-gray-700 rounded"></div>
                  <div className="flex-1">
                    <div className="h-4 w-40 bg-gray-200 dark:bg-gray-700 rounded mb-1"></div>
                    <div className="h-3 w-20 bg-gray-200 dark:bg-gray-700 rounded"></div>
                  </div>
                </div>
              ))}
            </CardContent>
          </Card>
        </div>

        {/* System Status Skeleton */}
        {showSystemStatus && (
          <div className="mt-6">
            <Card className="animate-pulse">
              <CardHeader>
                <div className="h-6 w-28 bg-gray-200 dark:bg-gray-700 rounded mb-2"></div>
                <div className="h-4 w-56 bg-gray-200 dark:bg-gray-700 rounded"></div>
              </CardHeader>
              <CardContent>
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                  {Array.from({ length: statusCards }).map((_, index) => (
                    <div key={index} className="h-16 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                  ))}
                </div>
              </CardContent>
            </Card>
          </div>
        )}
      </main>
    </div>
  );
};

export default DashboardSkeleton;