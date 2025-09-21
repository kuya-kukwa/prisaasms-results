'use client';

import React, { useState, useEffect, useCallback } from 'react';
import { useAuth } from '@/src/contexts/AuthContext';
import { useRouter } from 'next/navigation';
import { Separator } from '@/components/ui/separator';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import ScrollCounter from '@/components/ui/scroll-counter';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import {
  Calendar,
  Search,
  Filter,
  MoreHorizontal,
  Edit,
  Trash2,
  Eye,
  Clock,
  Users,
  Trophy,
  ChevronLeft,
  ChevronRight,
  XCircle,
  CalendarDays,
  PlusIcon
} from 'lucide-react';
import { adminSchedulesService, Schedule } from '@/src/lib/admin-schedules-api';
import { apiClient } from '@/src/lib/api';
import toast from 'react-hot-toast';
import { type ScheduleFormValues } from './ScheduleForm';
import dynamic from 'next/dynamic';

const AddScheduleModal = dynamic(() => import('./AddScheduleModal').then(mod => mod.AddScheduleModal), { ssr: false, loading: () => <div /> });

interface ScheduleFilters {
  search?: string;
  status?: string;
  sport_id?: number;
  tournament_id?: number;
  date_from?: string;
  date_to?: string;
}

interface ScheduleStats {
  total_schedules: number;
  scheduled_count: number;
  ongoing_count: number;
  completed_count: number;
  cancelled_count: number;
}

export default function AdminSchedulesPage() {
  const { isAuthenticated } = useAuth();
  const router = useRouter();

  // State management
  const [schedules, setSchedules] = useState<Schedule[]>([]);
  const [stats, setStats] = useState<ScheduleStats | null>(null);
  const [error, setError] = useState<string | null>(null);

  // Pagination state
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [totalItems, setTotalItems] = useState(0);
  const [perPage] = useState(20);

  // Filter and sort state
  const [filters, setFilters] = useState<ScheduleFilters>({});
  const [searchQuery, setSearchQuery] = useState('');
  const [activeTab, setActiveTab] = useState<string>('all');

  // Dialog states
  const [deleteDialog, setDeleteDialog] = useState<{ open: boolean; schedule?: Schedule }>({ open: false });
  const [calendarModalOpen, setCalendarModalOpen] = useState(false);
  const [addScheduleModalOpen, setAddScheduleModalOpen] = useState(false);
  const [selectedDate, setSelectedDate] = useState<Date | null>(null);
  const [selectedDateSchedules, setSelectedDateSchedules] = useState<Schedule[]>([]);
  const [currentMonth, setCurrentMonth] = useState(new Date());

  // Form data state
  const [venues, setVenues] = useState<Array<{ id: number; name: string; capacity?: number; is_indoor?: boolean; address?: string; region?: string }>>([]);
  const [sports, setSports] = useState<Array<{ id: number; name: string; category?: string; gender_category?: string }>>([]);
  const [tournaments, setTournaments] = useState<Array<{ id: number; name: string; sport_id?: number }>>([]);

  // Load schedules
  const loadSchedules = useCallback(async () => {
    try {
      setError(null);

      const response = await adminSchedulesService.getSchedules(filters, currentPage, perPage);

      setSchedules(response.data || []);
      setTotalPages(response.meta?.last_page || 1);
      setTotalItems(response.meta?.total || 0);
    } catch (err: unknown) {
      setError(`Failed to load schedules. Please try again. ${err}`);
      setSchedules([]);
    }
  }, [filters, currentPage, perPage]);

  // Load stats
  const loadStats = useCallback(async () => {
    try {
      const response = await adminSchedulesService.getStats();
      setStats(response);
    } catch (err: unknown) {
      console.error('Error loading stats:', err);
    }
  }, []);

  // Load filter options
  const loadFilterOptions = useCallback(async () => {
    try {
      // Commented out for future use when filters are implemented
      // const [sportsRes, tournamentsRes] = await Promise.all([
      //   apiClient.public.sports.list(),
      //   apiClient.get('/admin/tournaments')
      // ]);

      // const sportsArray = Array.isArray(sportsRes?.data)
      //   ? sportsRes.data
      //   : sportsRes?.data?.data || [];
      // const tournamentsArray = Array.isArray(tournamentsRes?.data)
      //   ? tournamentsRes.data
      //   : tournamentsRes?.data?.data || [];

      // setSports(sportsArray.map((s: { id: number; name: string }) => ({ id: s.id, name: s.name })));
      // setTournaments(tournamentsArray.map((t: { id: number; name: string }) => ({ id: t.id, name: t.name })));
    } catch (err: unknown) {
      console.error('Error loading filter options:', err);
    }
  }, []);

  // Load form data
  const loadFormData = useCallback(async () => {
    try {
      // Load venues
      try {
        const venuesRes = await apiClient.admin.venues.list();
        const venuesArray = Array.isArray(venuesRes?.data)
          ? venuesRes.data
          : [];
        setVenues(venuesArray);
      } catch (venuesErr) {
        console.error('Error loading venues:', venuesErr);
        setVenues([]);
      }

      // Load sports
      try {
        const sportsRes = await apiClient.admin.sports.list();
        const sportsArray = Array.isArray(sportsRes?.data)
          ? sportsRes.data
          : [];
        setSports(sportsArray);
      } catch (sportsErr) {
        console.error('Error loading sports:', sportsErr);
        setSports([]);
      }

      // Load tournaments
      try {
        const tournamentsRes = await apiClient.admin.tournaments.list();
        const tournamentsArray = Array.isArray(tournamentsRes?.data)
          ? tournamentsRes.data
          : [];
        setTournaments(tournamentsArray);
      } catch (tournamentsErr) {
        console.error('Error loading tournaments:', tournamentsErr);
        setTournaments([]);
      }
    } catch (err: unknown) {
      console.error('Error loading form data:', err);
    }
  }, []);

  // Get schedules for a specific date
  const getSchedulesForDate = (date: Date) => {
    const dateString = date.toISOString().split('T')[0];
    return schedules.filter(schedule => schedule.event_date === dateString);
  };

  // Handle date click in calendar
  const handleDateClick = (date: Date) => {
    const daySchedules = getSchedulesForDate(date);
    setSelectedDate(date);
    setSelectedDateSchedules(daySchedules);
  };

  // Handle tab change
  const handleTabChange = (value: string) => {
    setActiveTab(value);
    setFilters(prev => ({
      ...prev,
      status: value === 'all' ? undefined : value
    }));
    setCurrentPage(1);
    loadSchedules();
  };

  // Search with debounce
  useEffect(() => {
    const timer = setTimeout(() => {
      if (searchQuery !== (filters.search || '')) {
        setFilters(prev => ({ ...prev, search: searchQuery }));
        setCurrentPage(1);
      }
    }, 500);

    return () => clearTimeout(timer);
  }, [searchQuery, filters.search]);

  // Load initial data
  const loadInitialData = useCallback(async () => {
    if (!isAuthenticated) {
      router.push('/login');
      return;
    }

    setError(null);

    try {
      await loadFilterOptions();
      await loadFormData();
      await Promise.all([loadSchedules(), loadStats()]);
    } catch (err) {
      console.error('Error loading initial data:', err);
      setError('Failed to load data. Please try again.');
    }
  }, [isAuthenticated, router, loadFilterOptions, loadFormData, loadSchedules, loadStats]);

  // Load data when dependencies change
  useEffect(() => {
    loadInitialData();
  }, [loadInitialData]);

  // Handle delete
  const handleDelete = async (schedule: Schedule) => {
    try {
      await adminSchedulesService.deleteSchedule(schedule.id);
      toast.success('Schedule deleted successfully.');
      setDeleteDialog({ open: false });
      await loadSchedules();
      await loadStats();
    } catch (err: unknown) {
      console.error('Error deleting schedule:', err);
      setError('Failed to delete schedule. Please try again.');
    }
  };

  // Handle schedule created
  const handleScheduleCreated = async (data: ScheduleFormValues) => {
    try {
      await adminSchedulesService.createSchedule(data);
      toast.success('Schedule created successfully.');
      setAddScheduleModalOpen(false);
      await loadSchedules();
      await loadStats();
    } catch (err: unknown) {
      console.error('Error creating schedule:', err);
      toast.error('Failed to create schedule. Please try again.');
    }
  };

  // Utility functions
  const getStatusBadge = (status: string) => {
    const variants = {
      scheduled: 'default',
      ongoing: 'secondary',
      completed: 'outline',
      cancelled: 'destructive'
    } as const;

    const colors = {
      scheduled: 'text-blue-700 bg-blue-100',
      ongoing: 'text-green-700 bg-green-100',
      completed: 'text-gray-700 bg-gray-100',
      cancelled: 'text-red-700 bg-red-100'
    };

    return (
      <Badge variant={variants[status as keyof typeof variants]} className={colors[status as keyof typeof colors]}>
        {status.charAt(0).toUpperCase() + status.slice(1)}
      </Badge>
    );
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  };

  const formatTime = (timeString: string) => {
    return new Date(`2000-01-01T${timeString}`).toLocaleTimeString('en-US', {
      hour: 'numeric',
      minute: '2-digit',
      hour12: true
    });
  };

  return (
    <div className="flex-1 flex flex-col">
      <header className="sticky top-0 z-50 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 flex h-12 shrink-0 items-center gap-2 border-b">
        <div className="flex items-center gap-2 px-4">
          <SidebarTrigger className="-ml-1" />
          <Separator orientation="vertical" className="mr-2 h-4" />
          <nav className="flex items-center space-x-2 text-sm">
            <span className="text-muted-foreground">Admin Dashboard</span>
            <span className="text-muted-foreground">/</span>
            <span>Schedules</span>
          </nav>
        </div>
      </header>

      <main className="flex-1 p-4 md:p-6 overflow-auto min-h-0">
        <div className="space-y-6 md:space-y-8 max-w-full">
          {/* Error notification */}
          {error && (
            <Card className="border-red-200 bg-red-50">
              <CardContent className="pt-6">
                <div className="flex">
                  <div className="flex-shrink-0">
                    <XCircle className="h-5 w-5 text-red-400" />
                  </div>
                  <div className="ml-3">
                    <h3 className="text-sm font-medium text-red-800">Error</h3>
                    <div className="mt-2 text-sm text-red-700">
                      <p>{error}</p>
                    </div>
                    <div className="mt-4">
                      <Button
                        onClick={() => setError(null)}
                        size="sm"
                        variant="outline"
                        className="text-red-800 border-red-300 hover:bg-red-100"
                      >
                        Dismiss
                      </Button>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>
          )}

          {/* Header */}
          <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
              <h1 className="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">
                Schedule Management
              </h1>
              <p className="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Manage tournament schedules and match timings
              </p>
            </div>
            <div className="flex flex-col sm:flex-row items-start sm:items-center gap-2">
              <Button size="sm" className="gap-2 bg-blue-900 text-white" onClick={() => setAddScheduleModalOpen(true)}>
                <PlusIcon className="h-4 w-4" />
                Add New Schedule
              </Button>
              <Button size="sm" className="gap-2 bg-blue-900 text-white" onClick={() => {
                setCalendarModalOpen(true);
                const today = new Date();
                handleDateClick(today);
              }}>
                <Calendar className="h-4 w-4" />
                View Calendar Schedule
              </Button>
            </div>
            
          </div>

          {/* Stats Cards */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <Card className="shadow-sm hover:shadow-md transition-shadow duration-200">
              <CardHeader className="flex flex-row items-center justify-between pb-2">
                <CardTitle className="text-sm font-semibold text-gray-700 dark:text-gray-200">
                  Total Schedules
                </CardTitle>
                <Calendar className="h-5 w-5 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <ScrollCounter
                  end={stats?.total_schedules || 0}
                  className="text-3xl font-bold text-gray-900 dark:text-white"
                />
                <p className="text-sm text-muted-foreground">All scheduled events</p>
              </CardContent>
            </Card>

            <Card className="shadow-sm hover:shadow-md transition-shadow duration-200">
              <CardHeader className="flex flex-row items-center justify-between pb-2">
                <CardTitle className="text-sm font-semibold text-gray-700 dark:text-gray-200">
                  Scheduled
                </CardTitle>
                <Clock className="h-5 w-5 text-blue-600" />
              </CardHeader>
              <CardContent>
                <ScrollCounter
                  end={stats?.scheduled_count || 0}
                  className="text-3xl font-bold text-gray-900 dark:text-white"
                />
                <p className="text-sm text-muted-foreground">Upcoming events</p>
              </CardContent>
            </Card>

            <Card className="shadow-sm hover:shadow-md transition-shadow duration-200">
              <CardHeader className="flex flex-row items-center justify-between pb-2">
                <CardTitle className="text-sm font-semibold text-gray-700 dark:text-gray-200">
                  Ongoing
                </CardTitle>
                <Users className="h-5 w-5 text-green-600" />
              </CardHeader>
              <CardContent>
                <ScrollCounter
                  end={stats?.ongoing_count || 0}
                  className="text-3xl font-bold text-gray-900 dark:text-white"
                />
                <p className="text-sm text-muted-foreground">Currently active</p>
              </CardContent>
            </Card>

            <Card className="shadow-sm hover:shadow-md transition-shadow duration-200">
              <CardHeader className="flex flex-row items-center justify-between pb-2">
                <CardTitle className="text-sm font-semibold text-gray-700 dark:text-gray-200">
                  Completed
                </CardTitle>
                <Trophy className="h-5 w-5 text-purple-600" />
              </CardHeader>
              <CardContent>
                <ScrollCounter
                  end={stats?.completed_count || 0}
                  className="text-3xl font-bold text-gray-900 dark:text-white"
                />
                <p className="text-sm text-muted-foreground">Finished events</p>
              </CardContent>
            </Card>

            <Card className="shadow-sm hover:shadow-md transition-shadow duration-200">
              <CardHeader className="flex flex-row items-center justify-between pb-2">
                <CardTitle className="text-sm font-semibold text-gray-700 dark:text-gray-200">
                  Cancelled
                </CardTitle>
                <XCircle className="h-5 w-5 text-red-600" />
              </CardHeader>
              <CardContent>
                <ScrollCounter
                  end={stats?.cancelled_count || 0}
                  className="text-3xl font-bold text-gray-900 dark:text-white"
                />
                <p className="text-sm text-muted-foreground">Cancelled events</p>
              </CardContent>
            </Card>
          </div>

          {/* Filters and Search */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Filter className="h-5 w-5" />
                Filters & Search
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div className="w-full md:w-auto">
                  <Tabs value={activeTab} onValueChange={handleTabChange}>
                    <TabsList className="flex flex-wrap gap-1">
                      <TabsTrigger value="all">All</TabsTrigger>
                      <TabsTrigger value="scheduled">Scheduled</TabsTrigger>
                      <TabsTrigger value="ongoing">In Progress</TabsTrigger>
                      <TabsTrigger value="completed">Completed</TabsTrigger>
                      <TabsTrigger value="cancelled">Cancelled</TabsTrigger>
                    </TabsList>
                  </Tabs>
                </div>
                <div className="w-full md:w-[38%]">
                  <div className="relative">
                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                    <Input
                      placeholder="Search schedules..."
                      value={searchQuery}
                      onChange={(e) => setSearchQuery(e.target.value)}
                      className="pl-10"
                    />
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Schedules Table */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <CalendarDays className="h-5 w-5" />
                Schedules ({totalItems})
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="overflow-x-auto">
                <Table className="table-fixed w-full">
                  <TableHeader>
                    <TableRow>
                      <TableHead className="w-16">Event</TableHead>
                      <TableHead className="min-w-[12rem]">Title</TableHead>
                      <TableHead className="w-24">Date</TableHead>
                      <TableHead className="w-20">Time</TableHead>
                      <TableHead className="min-w-[8rem] hidden sm:table-cell">Venue</TableHead>
                      <TableHead className="w-24 hidden md:table-cell">Sport</TableHead>
                      <TableHead className="w-32 hidden lg:table-cell">Tournament</TableHead>
                      <TableHead className="w-20">Status</TableHead>
                      <TableHead className="w-16">Actions</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    {schedules.length === 0 ? (
                      <TableRow>
                        <TableCell colSpan={9} className="text-center py-8">
                          <div className="flex flex-col items-center gap-2">
                            <Calendar className="h-8 w-8 text-muted-foreground" />
                            <p className="text-muted-foreground">No schedules found</p>
                          </div>
                        </TableCell>
                      </TableRow>
                    ) : (
                      schedules.map((schedule) => (
                        <TableRow key={schedule.id}>
                          <TableCell className="w-16">
                            <div className="flex items-center justify-center">
                              <Calendar className="h-5 w-5 text-muted-foreground" />
                            </div>
                          </TableCell>
                          <TableCell className="min-w-[12rem] font-medium">
                            <div>
                              <div className="font-medium">{schedule.title}</div>
                              {schedule.description && (
                                <div className="text-sm text-muted-foreground truncate max-w-xs">
                                  {schedule.description}
                                </div>
                              )}
                            </div>
                          </TableCell>
                          <TableCell className="w-24">
                            {formatDate(schedule.event_date)}
                          </TableCell>
                          <TableCell className="w-20">
                            <div className="text-sm">
                              <div>{formatTime(schedule.start_time)}</div>
                              <div className="text-muted-foreground">to</div>
                              <div>{formatTime(schedule.end_time)}</div>
                            </div>
                          </TableCell>
                          <TableCell className="min-w-[8rem] hidden sm:table-cell">
                            {schedule.venue || '-'}
                          </TableCell>
                          <TableCell className="w-24 hidden md:table-cell">
                            {schedule.sport_id ? `Sport ${schedule.sport_id}` : '-'}
                          </TableCell>
                          <TableCell className="w-32 hidden lg:table-cell">
                            {schedule.tournament_id ? `Tournament ${schedule.tournament_id}` : '-'}
                          </TableCell>
                          <TableCell className="w-20">
                            {getStatusBadge(schedule.status)}
                          </TableCell>
                          <TableCell className="w-16">
                            <DropdownMenu>
                              <DropdownMenuTrigger asChild>
                                <Button variant="ghost" size="sm">
                                  <MoreHorizontal className="h-4 w-4" />
                                </Button>
                              </DropdownMenuTrigger>
                              <DropdownMenuContent>
                                <DropdownMenuItem>
                                  <Eye className="h-4 w-4 mr-2" />
                                  View Details
                                </DropdownMenuItem>
                                <DropdownMenuItem>
                                  <Edit className="h-4 w-4 mr-2" />
                                  Edit
                                </DropdownMenuItem>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem
                                  onClick={() => setDeleteDialog({ open: true, schedule })}
                                  className="text-red-600"
                                >
                                  <Trash2 className="h-4 w-4 mr-2" />
                                  Delete
                                </DropdownMenuItem>
                              </DropdownMenuContent>
                            </DropdownMenu>
                          </TableCell>
                        </TableRow>
                      ))
                    )}
                  </TableBody>
                </Table>
              </div>

              {/* Pagination */}
              {totalPages > 1 && (
                <div className="flex items-center justify-between mt-4">
                  <div className="flex items-center gap-2">
                    <span className="text-sm text-muted-foreground">
                      Showing {(currentPage - 1) * perPage + 1} to {Math.min(currentPage * perPage, totalItems)} of {totalItems} results
                    </span>
                  </div>
                  <div className="flex items-center gap-2">
                    <Button
                      variant="outline"
                      size="sm"
                      onClick={() => setCurrentPage(prev => Math.max(1, prev - 1))}
                      disabled={currentPage === 1}
                    >
                      <ChevronLeft className="h-4 w-4" />
                      Previous
                    </Button>
                    <span className="text-sm px-3 py-2">
                      Page {currentPage} of {totalPages}
                    </span>
                    <Button
                      variant="outline"
                      size="sm"
                      onClick={() => setCurrentPage(prev => Math.min(totalPages, prev + 1))}
                      disabled={currentPage === totalPages}
                    >
                      Next
                      <ChevronRight className="h-4 w-4" />
                    </Button>
                  </div>
                </div>
              )}
            </CardContent>
          </Card>

        </div>
      </main>

      {/* Delete Confirmation Dialog */}
      <AlertDialog open={deleteDialog.open} onOpenChange={(open) => setDeleteDialog({ open, schedule: open ? deleteDialog.schedule : undefined })}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Are you sure?</AlertDialogTitle>
            <AlertDialogDescription>
              This action cannot be undone. This will permanently delete the schedule for{' '}
              <strong>{deleteDialog.schedule?.title}</strong>.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancel</AlertDialogCancel>
            <AlertDialogAction
              onClick={() => deleteDialog.schedule && handleDelete(deleteDialog.schedule)}
              className="bg-red-600 hover:bg-red-700"
            >
              Delete
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>

      {/* Calendar Modal */}
      <Dialog open={calendarModalOpen} onOpenChange={setCalendarModalOpen}>
        <DialogContent className="max-w-7xl max-h-[95vh] overflow-hidden bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-800">
          <DialogHeader className="pb-4">
            <div className="flex items-center justify-between">
              <div>
                <DialogTitle className="text-2xl font-bold text-blue-900 dark:text-blue-300">
                  📅 Schedule Calendar
                </DialogTitle>
                <p className="text-sm text-muted-foreground mt-1">
                  View and manage tournament schedules by date
                </p>
              </div>
              <div className="flex gap-2">
                <Button
                  variant="outline"
                  size="sm"
                  onClick={() => {
                    const today = new Date();
                    handleDateClick(today);
                  }}
                  className="gap-2"
                >
                  <CalendarDays className="h-4 w-4" />
                  Today
                </Button>
                <Button
                  variant="outline"
                  size="sm"
                  onClick={() => {
                    // Find next date with schedules
                    const today = new Date();
                    for (let i = 1; i <= 30; i++) {
                      const checkDate = new Date(today);
                      checkDate.setDate(today.getDate() + i);
                      const schedules = getSchedulesForDate(checkDate);
                      if (schedules.length > 0) {
                        handleDateClick(checkDate);
                        break;
                      }
                    }
                  }}
                  className="gap-2"
                >
                  <ChevronRight className="h-4 w-4" />
                  Next Event
                </Button>
              </div>
            </div>
          </DialogHeader>

          <div className="flex flex-col lg:flex-row gap-6 h-[75vh]">
            {/* Enhanced Calendar Section */}
            <div className="flex-1 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
              <div className="p-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700">
                <h3 className="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                  <Calendar className="h-5 w-5 text-blue-600" />
                  Calendar View
                </h3>
                <p className="text-sm text-gray-600 dark:text-gray-300 mt-1">
                  Click on any date to view schedules
                </p>
              </div>

              <div className="p-6">
                {/* Custom Calendar Component */}
                <div className="w-full max-w-4xl mx-auto">
                  {/* Calendar Header */}
                  <div className="flex items-center justify-between mb-6">
                    <div className="flex items-center gap-4">
                      <Button
                        variant="outline"
                        size="sm"
                        onClick={() => {
                          const prevMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() - 1, 1);
                          setCurrentMonth(prevMonth);
                        }}
                        className="h-9 w-9 p-0 hover:bg-blue-50 dark:hover:bg-blue-950/20"
                      >
                        <ChevronLeft className="h-4 w-4" />
                      </Button>
                      
                      <h2 className="text-xl font-semibold text-gray-900 dark:text-white min-w-48 text-center">
                        {currentMonth.toLocaleDateString('en-US', { 
                          month: 'long', 
                          year: 'numeric' 
                        })}
                      </h2>
                      
                      <Button
                        variant="outline"
                        size="sm"
                        onClick={() => {
                          const nextMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 1);
                          setCurrentMonth(nextMonth);
                        }}
                        className="h-9 w-9 p-0 hover:bg-blue-50 dark:hover:bg-blue-950/20"
                      >
                        <ChevronRight className="h-4 w-4" />
                      </Button>
                    </div>
                    
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={() => {
                        const today = new Date();
                        setCurrentMonth(new Date(today.getFullYear(), today.getMonth(), 1));
                        handleDateClick(today);
                      }}
                      className="text-blue-900 hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/20"
                    >
                      Today
                    </Button>
                  </div>

                  {/* Weekdays Header */}
                  <div className="grid grid-cols-7 gap-1 mb-2">
                    {['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].map((day) => (
                      <div
                        key={day}
                        className="h-10 flex items-center justify-center text-sm font-medium text-gray-500 dark:text-gray-400"
                      >
                        {day}
                      </div>
                    ))}
                  </div>

                  {/* Calendar Grid */}
                  <div className="grid grid-cols-7 gap-1">
                    {(() => {
                      const year = currentMonth.getFullYear();
                      const month = currentMonth.getMonth();
                      const firstDayOfMonth = new Date(year, month, 1);
                      const startDate = new Date(firstDayOfMonth);
                      startDate.setDate(startDate.getDate() - firstDayOfMonth.getDay());
                      
                      const days = [];
                      const today = new Date();
                      today.setHours(0, 0, 0, 0);
                      
                      for (let i = 0; i < 42; i++) {
                        const currentDate = new Date(startDate);
                        currentDate.setDate(startDate.getDate() + i);
                        
                        const isCurrentMonth = currentDate.getMonth() === month;
                        const isToday = currentDate.getTime() === today.getTime();
                        const isSelected = selectedDate && 
                          currentDate.getFullYear() === selectedDate.getFullYear() &&
                          currentDate.getMonth() === selectedDate.getMonth() &&
                          currentDate.getDate() === selectedDate.getDate();
                        
                        const schedules = getSchedulesForDate(currentDate);
                        const hasEvents = schedules.length > 0;
                        const hasLiveEvents = schedules.some(s => s.status === 'ongoing');
                        
                        days.push(
                          <button
                            key={currentDate.toISOString()}
                            onClick={() => handleDateClick(currentDate)}
                            className={`
                              relative h-12 w-full rounded-lg border-2 transition-all duration-200 group
                              ${isSelected 
                                ? 'bg-blue-900 text-white border-gray-100 shadow-lg scale-105' 
                                : isToday
                                  ? 'bg-green-50 dark:bg-green-950/30 border-gray-700 text-green-700 dark:text-green-300'
                                  : hasEvents
                                    ? 'bg-blue-50 dark:bg-blue-950/30 border-blue-200 dark:border-blue-800 hover:bg-blue-100 dark:hover:bg-blue-950/50'
                                    : 'border-transparent hover:bg-gray-50 dark:hover:bg-gray-700'
                              }
                              ${!isCurrentMonth 
                                ? 'opacity-30 text-gray-400' 
                                : isSelected || isToday
                                  ? ''
                                  : 'text-gray-700 dark:text-gray-300'
                              }
                              ${hasEvents ? 'hover:scale-105' : 'hover:scale-102'}
                            `}
                            disabled={!isCurrentMonth}
                          >
                            {/* Date Number */}
                            <span className={`
                              text-sm font-medium
                              ${isSelected ? 'text-white' : ''}
                            `}>
                              {currentDate.getDate()}
                            </span>
                            
                            {/* Event Indicators */}
                            {hasEvents && isCurrentMonth && (
                              <div className="absolute bottom-1 left-1/2 transform -translate-x-1/2 flex gap-0.5">
                                {schedules.length <= 3 ? (
                                  // Show individual dots for few events
                                  schedules.slice(0, 3).map((schedule, idx) => (
                                    <div
                                      key={idx}
                                      className={`w-1.5 h-1.5 rounded-full ${
                                        schedule.status === 'ongoing' 
                                          ? 'bg-green-400 animate-pulse'
                                          : schedule.status === 'completed'
                                            ? 'bg-purple-400'
                                            : schedule.status === 'cancelled'
                                              ? 'bg-red-400'
                                              : isSelected 
                                                ? 'bg-white/80' 
                                                : 'bg-blue-500'
                                      }`}
                                    />
                                  ))
                                ) : (
                                  // Show count for many events
                                  <div className={`
                                    text-xs font-bold rounded-full min-w-4 h-4 flex items-center justify-center px-1
                                    ${isSelected 
                                      ? 'bg-white/20 text-white' 
                                      : 'bg-blue-500 text-white'
                                    }
                                    ${hasLiveEvents ? 'animate-pulse' : ''}
                                  `}>
                                    {schedules.length}
                                  </div>
                                )}
                              </div>
                            )}
                            
                            {/* Live Event Pulse */}
                            {hasLiveEvents && isCurrentMonth && (
                              <div className="absolute top-1 right-1">
                                <div className="w-2 h-2 bg-green-400 rounded-full animate-ping"></div>
                              </div>
                            )}
                            
                            {/* Today Indicator */}
                            {isToday && !isSelected && (
                              <div className="absolute -top-1 -right-1">
                                <div className="w-3 h-3 bg-green-500 rounded-full border-2 border-white dark:border-gray-800"></div>
                              </div>
                            )}
                          </button>
                        );
                      }
                      
                      return days;
                    })()}
                  </div>

                  {/* Quick Stats */}
                  <div className="mt-6 p-4 bg-gradient-to-r from-gray-50 to-blue-50 dark:from-gray-800 dark:to-blue-950/20 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                      <div className="text-center">
                        <div className="text-lg font-bold text-blue-600 dark:text-blue-400">
                          {(() => {
                            const year = currentMonth.getFullYear();
                            const month = currentMonth.getMonth();
                            let totalEvents = 0;
                            for (let day = 1; day <= new Date(year, month + 1, 0).getDate(); day++) {
                              const date = new Date(year, month, day);
                              totalEvents += getSchedulesForDate(date).length;
                            }
                            return totalEvents;
                          })()}
                        </div>
                        <div className="text-gray-600 dark:text-gray-400">Total Events</div>
                      </div>
                      <div className="text-center">
                        <div className="text-lg font-bold text-green-600 dark:text-green-400">
                          {(() => {
                            const year = currentMonth.getFullYear();
                            const month = currentMonth.getMonth();
                            let liveEvents = 0;
                            for (let day = 1; day <= new Date(year, month + 1, 0).getDate(); day++) {
                              const date = new Date(year, month, day);
                              liveEvents += getSchedulesForDate(date).filter(s => s.status === 'ongoing').length;
                            }
                            return liveEvents;
                          })()}
                        </div>
                        <div className="text-gray-600 dark:text-gray-400">Live Events</div>
                      </div>
                      <div className="text-center">
                        <div className="text-lg font-bold text-purple-600 dark:text-purple-400">
                          {(() => {
                            const year = currentMonth.getFullYear();
                            const month = currentMonth.getMonth();
                            let completedEvents = 0;
                            for (let day = 1; day <= new Date(year, month + 1, 0).getDate(); day++) {
                              const date = new Date(year, month, day);
                              completedEvents += getSchedulesForDate(date).filter(s => s.status === 'completed').length;
                            }
                            return completedEvents;
                          })()}
                        </div>
                        <div className="text-gray-600 dark:text-gray-400">Completed</div>
                      </div>
                      <div className="text-center">
                        <div className="text-lg font-bold text-orange-600 dark:text-orange-400">
                          {(() => {
                            const year = currentMonth.getFullYear();
                            const month = currentMonth.getMonth();
                            let daysWithEvents = 0;
                            for (let day = 1; day <= new Date(year, month + 1, 0).getDate(); day++) {
                              const date = new Date(year, month, day);
                              if (getSchedulesForDate(date).length > 0) daysWithEvents++;
                            }
                            return daysWithEvents;
                          })()}
                        </div>
                        <div className="text-gray-600 dark:text-gray-400">Active Days</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {/* Schedule Details Section */}
            <div className="w-full lg:w-96 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
              <div className="p-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-gray-800 dark:to-gray-700">
                <div className="flex items-center justify-between mb-2">
                  <h3 className="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <Clock className="h-5 w-5 text-green-600" />
                    Schedule Details
                  </h3>
                  <Badge variant="secondary" className="text-xs">
                    {selectedDateSchedules.length} event{selectedDateSchedules.length !== 1 ? 's' : ''}
                  </Badge>
                </div>
                <div className="mt-2">
                  <p className="text-sm font-medium text-gray-900 dark:text-white">
                    {selectedDate ? (
                      <>
                        {selectedDate.toLocaleDateString('en-US', {
                          weekday: 'long',
                          year: 'numeric',
                          month: 'long',
                          day: 'numeric'
                        })}
                      </>
                    ) : (
                      'Select a date'
                    )}
                  </p>
                  {selectedDateSchedules.length > 0 && (
                    <div className="flex gap-2 mt-2">
                      {selectedDateSchedules.some(s => s.status === 'ongoing') && (
                        <div className="flex items-center gap-1 text-xs text-green-600">
                          <div className="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                          <span>Live</span>
                        </div>
                      )}
                      {selectedDateSchedules.some(s => s.status === 'scheduled') && (
                        <div className="flex items-center gap-1 text-xs text-blue-600">
                          <div className="w-2 h-2 bg-blue-500 rounded-full"></div>
                          <span>Scheduled</span>
                        </div>
                      )}
                    </div>
                  )}
                </div>
              </div>

              <div className="p-4 max-h-96 overflow-y-auto">
                {selectedDateSchedules.length === 0 ? (
                  <div className="text-center py-8">
                    <Calendar className="h-12 w-12 text-gray-300 dark:text-gray-600 mx-auto mb-4" />
                    <p className="text-gray-500 dark:text-gray-400 font-medium">No schedules</p>
                    <p className="text-sm text-gray-400 dark:text-gray-500 mt-1">
                      No events scheduled for this date
                    </p>
                  </div>
                ) : (
                  <div className="space-y-3">
                    {selectedDateSchedules.map((schedule) => (
                      <Card key={schedule.id} className="p-4 hover:shadow-lg transition-all duration-300 border-l-4 hover:scale-[1.02] bg-gradient-to-r from-blue-50/50 to-transparent dark:from-blue-950/20 dark:to-transparent group">
                        <div className="space-y-3">
                          <div className="flex items-start justify-between">
                            <div className="flex-1">
                              <h4 className="font-semibold text-gray-900 dark:text-white text-sm leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                {schedule.title}
                              </h4>
                              {schedule.description && (
                                <p className="text-xs text-gray-600 dark:text-gray-300 mt-1 line-clamp-2">
                                  {schedule.description}
                                </p>
                              )}
                            </div>
                            <Badge
                              variant={
                                schedule.status === 'completed' ? 'default' :
                                schedule.status === 'ongoing' ? 'secondary' :
                                schedule.status === 'cancelled' ? 'destructive' : 'outline'
                              }
                              className={`text-xs ml-2 shrink-0 ${
                                schedule.status === 'ongoing' ? 'animate-pulse bg-green-100 text-green-800 border-green-300' : ''
                              }`}
                            >
                              {schedule.status === 'ongoing' && '● '}
                              {schedule.status.replace('_', ' ')}
                            </Badge>
                          </div>

                          <div className="grid grid-cols-2 gap-2 text-xs text-gray-600 dark:text-gray-300">
                            <div className="flex items-center gap-1">
                              <Clock className="h-3 w-3 text-blue-500" />
                              <span className="font-medium">
                                {formatTime(schedule.start_time)} - {formatTime(schedule.end_time)}
                              </span>
                            </div>

                            {schedule.venue && (
                              <div className="flex items-center gap-1">
                                <Users className="h-3 w-3 text-green-500" />
                                <span className="truncate">{schedule.venue}</span>
                              </div>
                            )}

                            {schedule.sport_id && (
                              <div className="flex items-center gap-1">
                                <Trophy className="h-3 w-3 text-purple-500" />
                                <span className="truncate">Sport {schedule.sport_id}</span>
                              </div>
                            )}

                            {schedule.tournament_id && (
                              <div className="flex items-center gap-1">
                                <Calendar className="h-3 w-3 text-orange-500" />
                                <span className="truncate">Tournament {schedule.tournament_id}</span>
                              </div>
                            )}
                          </div>

                          <div className="flex gap-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                            <Button size="sm" variant="outline" className="h-7 text-xs flex-1 hover:bg-blue-50 dark:hover:bg-blue-950/20">
                              <Eye className="h-3 w-3 mr-1" />
                              View
                            </Button>
                            <Button size="sm" variant="outline" className="h-7 text-xs flex-1 hover:bg-green-50 dark:hover:bg-green-950/20">
                              <Edit className="h-3 w-3 mr-1" />
                              Edit
                            </Button>
                          </div>
                        </div>
                      </Card>
                    ))}
                  </div>
                )}
              </div>
            </div>
          </div>

          {/* Enhanced Legend */}
          <div className="mt-4 p-4 bg-gradient-to-r from-gray-50 to-blue-50 dark:from-gray-800 dark:to-blue-950/20 rounded-lg border border-gray-200 dark:border-gray-700">
            <h4 className="font-medium text-sm text-gray-900 dark:text-white mb-3 flex items-center gap-2">
              <Calendar className="h-4 w-4" />
              Calendar Legend
            </h4>
            <div className="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
              <div className="flex items-center gap-2">
                <div className="w-3 h-3 bg-blue-500 rounded-full"></div>
                <span className="text-gray-600 dark:text-gray-300">Scheduled</span>
              </div>
              <div className="flex items-center gap-2">
                <div className="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                <span className="text-gray-600 dark:text-gray-300">In Progress</span>
              </div>
              <div className="flex items-center gap-2">
                <div className="w-3 h-3 bg-purple-500 rounded-full"></div>
                <span className="text-gray-600 dark:text-gray-300">Completed</span>
              </div>
              <div className="flex items-center gap-2">
                <div className="w-3 h-3 bg-red-500 rounded-full"></div>
                <span className="text-gray-600 dark:text-gray-300">Cancelled</span>
              </div>
              <div className="flex items-center gap-2">
                <div className="w-4 h-4 bg-gradient-to-r from-blue-500 to-indigo-500 rounded"></div>
                <span className="text-gray-600 dark:text-gray-300">Selected Date</span>
              </div>
              <div className="flex items-center gap-2">
                <div className="w-3 h-3 bg-green-500 rounded-full border-2 border-green-600"></div>
                <span className="text-gray-600 dark:text-gray-300">Today</span>
              </div>
              <div className="flex items-center gap-2">
                <div className="bg-blue-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                  3
                </div>
                <span className="text-gray-600 dark:text-gray-300">Event Count</span>
              </div>
              <div className="flex items-center gap-2">
                <div className="relative w-4 h-4">
                  <div className="w-2 h-2 bg-green-400 rounded-full animate-ping absolute top-0 right-0"></div>
                </div>
                <span className="text-gray-600 dark:text-gray-300">Live Indicator</span>
              </div>
            </div>
          </div>
        </DialogContent>
      </Dialog>

      {/* Add Schedule Modal */}
      <AddScheduleModal
        open={addScheduleModalOpen}
        onClose={() => setAddScheduleModalOpen(false)}
        onScheduleCreated={handleScheduleCreated}
        venues={venues}
        sports={sports}
        tournaments={tournaments}
      />
    </div>
  );
}