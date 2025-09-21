'use client';

import { useState, useEffect, Suspense } from 'react';
import Image from 'next/image';
import useSWR, { mutate } from 'swr';
import { apiClient, School } from '@/src/lib/api';
import toast from 'react-hot-toast';

// Loading component for better UX
function LoadingSkeleton() {
  return (
    <div className="min-h-screen flex items-center justify-center bg-white dark:bg-gray-900">
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
          Loading PRISAA Sports Data...
        </p>
      </div>
    </div>
  );
}

// Error boundary component
function ErrorFallback({ error, resetError }: { error: Error; resetError: () => void }) {
  return (
    <div className="min-h-screen flex items-center justify-center bg-white dark:bg-gray-900">
      <div className="text-center">
        <div className="text-red-500 mb-4">
          <svg className="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
        </div>
        <h2 className="text-xl font-semibold text-gray-900 dark:text-white mb-2">
          Something went wrong
        </h2>
        <p className="text-gray-600 dark:text-gray-400 mb-4">
          {error.message || 'Failed to load data'}
        </p>
        <button
          onClick={resetError}
          className="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700"
        >
          Try Again
        </button>
      </div>
    </div>
  );
}

export default function Home() {
  const [error, setError] = useState<Error | null>(null);

  // Use SWR for data fetching with caching
  const { data: schoolsData, error: schoolsError, isLoading: schoolsLoading } = useSWR(
    'schools-list',
    () => apiClient.public.schools.list().then(res => {
      if (res.success) {
        if (Array.isArray(res.data)) {
          return res.data;
        } else if (res.data && typeof res.data === 'object') {
          const dataObj = res.data as { data?: School[]; schools?: School[]; [key: string]: unknown };
          return Array.isArray(dataObj.data) ? dataObj.data : (Array.isArray(dataObj.schools) ? dataObj.schools : []);
        }
      }
      return [];
    }),
    {
      revalidateOnFocus: false,
      dedupingInterval: 30000, // Reduced from 1 minute to 30 seconds for better real-time updates
      errorRetryCount: 2,
      onError: (error) => {
        console.error('SWR Error for schools-list:', error);
      },
    }
  );

  const { data: statsData, error: statsError, isLoading: statsLoading } = useSWR(
    'overall-statistics',
    () => apiClient.public.schools.overallStatistics().then(res => res.success ? res.data : null),
    {
      revalidateOnFocus: false,
      dedupingInterval: 60000, // Reduced from 5 minutes to 1 minute for better real-time updates
      errorRetryCount: 2,
      onError: (error) => {
        console.error('SWR Error for overall-statistics:', error);
      },
    }
  );

  // Handle errors
  useEffect(() => {
    if (schoolsError || statsError) {
      setError(schoolsError || statsError);
    }
  }, [schoolsError, statsError]);

  const resetError = () => {
    setError(null);
  };

  // Manual refresh function for stats
  const refreshStats = async () => {
    try {
      await mutate('schools-list');
      await mutate('overall-statistics');
      toast.success('Stats refreshed successfully!');
    } catch (error) {
      console.error('Error refreshing stats:', error);
      toast.error('Failed to refresh stats');
    }
  };

  // Show error state
  if (error) {
    return <ErrorFallback error={error} resetError={resetError} />;
  }

  // Show loading state
  if (schoolsLoading || statsLoading) {
    return <LoadingSkeleton />;
  }

  const schools = schoolsData?.slice(0, 6) || [];

  // Extract stats from API data
  const totalSchools = statsData?.total_schools || schools.length;
  const totalAthletes = statsData?.total_athletes || 0;
  const yearsOfHistory = statsData?.years_of_history || 9;

  // Calculate unique regions from schools data
  const uniqueRegions = schoolsData ? [...new Set(schoolsData.map(school => school.region).filter(Boolean))].length : 0;
  const totalRegions = statsData?.total_regions || uniqueRegions;

  return (
    <Suspense fallback={<LoadingSkeleton />}>
      <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
        {/* Hero Section */}
        <section className="min-h-screen flex items-center justify-center py-20">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 className="text-5xl font-bold text-gray-900 mb-6">
              Private Schools Athletic Association
            </h2>
            <p className="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
              Comprehensive sports management system for multi-level tournaments,
              profiles, schedules, and real-time results management across Provincial, Regional, and National competitions.
            </p>
            <div className="flex justify-center space-x-4">
              <button className="bg-blue-900 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-blue-500">
                View Live Results
              </button>
              <button className="border border-gray-300 text-gray-700 px-8 py-3 rounded-lg text-lg font-semibold hover:bg-gray-50">
                Tournament Schedule
              </button>
            </div>
          </div>
        </section>

        {/* Stats Section */}
        <section className="py-16 bg-white">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
              <div className="text-center">
                <div className="text-3xl font-bold text-blue-900">{totalAthletes.toLocaleString()}+</div>
                <div className="text-gray-600">Registered Athletes</div>
              </div>
              <div className="text-center">
                <div className="text-3xl font-bold text-blue-900">{totalSchools}+</div>
                <div className="text-gray-600">Participating Schools</div>
              </div>
              <div className="text-center">
                <div className="text-3xl font-bold text-blue-900">{yearsOfHistory}</div>
                <div className="text-gray-600">Years of History</div>
              </div>
              <div className="text-center">
                <div className="text-3xl font-bold text-blue-900">{totalRegions}+</div>
                <div className="text-gray-600">Regions</div>
              </div>
            </div>
          </div>
        </section>

        {/* API Connection Status */}
        <section className="py-8 bg-green-50">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div className="flex items-center justify-center space-x-2">
              <div className="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
              <span className="text-green-700 font-medium">
                Connected to PRISAA Backend API (http://localhost:8000)
              </span>
            </div>
            <p className="text-green-600 text-sm mt-2">
              Displaying live data from {totalSchools} schools, {totalAthletes.toLocaleString()} athletes, and {totalRegions} regions
            </p>
            <div className="mt-4">
              <button
                onClick={refreshStats}
                className="inline-flex items-center px-4 py-2 text-sm font-medium text-green-700 bg-green-100 border border-green-300 rounded-lg hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
              >
                <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh Stats
              </button>
            </div>
          </div>
        </section>

        {/* Participating Schools */}
        <section className="py-16 bg-white">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h3 className="text-3xl font-bold text-gray-900 mb-8 text-center">Participating Schools</h3>
            <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
              {schools.map((school: School) => (
                <div key={school.id} className="bg-gray-50 rounded-lg shadow-sm p-4 text-center hover:shadow-md transition-shadow">
                  <div className="w-16 h-16 bg-indigo-100 rounded-full mx-auto mb-3 flex items-center justify-center">
                    <span className="text-indigo-600 font-bold text-lg">
                      {school.name.charAt(0)}
                    </span>
                  </div>
                  <h4 className="font-medium text-gray-900 text-sm">{school.name}</h4>
                  <p className="text-xs text-gray-500 mt-1">{school.region}</p>
                </div>
              ))}
            </div>
          </div>
        </section>
      </div>
    </Suspense>
  );
}
