"use client";

import React, { useState, useEffect, useCallback } from 'react';
import { useAuth } from '@/src/contexts/AuthContext';
import { useRouter, useSearchParams } from 'next/navigation';
import { Separator } from '@/components/ui/separator';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { Avatar, AvatarImage, AvatarFallback } from '@/components/ui/avatar';
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
  Users, 
  Plus, 
  Search,
  Filter,
  MoreHorizontal,
  Edit,
  Trash2,
  UserPlus,
  ChevronLeft,
  ChevronRight,
  User,
  Eye
} from 'lucide-react';
import { 
  adminProfilesService, 
  type Profile, 
  type AthleteProfile,
  type CoachProfile,
  type OfficialProfile,
  type TournamentManagerProfile,
  type SchoolProfile
} from '@/src/lib/admin-profiles-api';
import { apiClient } from '@/src/lib/api';
import toast from 'react-hot-toast';
import dynamic from 'next/dynamic';

// Lazy-load heavier UI components so they don't inflate the initial bundle for this page
const AddProfileModal = dynamic(() => import('./AddProfileModal').then(mod => mod.AddProfileModal), { ssr: false, loading: () => <div /> });
const RegisterAuthorizedUserModal = dynamic(() => import('./RegisterAuthorizedUserModal').then(mod => mod.RegisterAuthorizedUserModal), { ssr: false, loading: () => <div /> });
const EditProfileModal = dynamic(() => import('./EditProfileModal').then(mod => mod.EditProfileModal), { ssr: false, loading: () => <div /> });
const ViewProfileModal = dynamic(() => import('./ViewProfileModal').then(mod => mod.ViewProfileModal), { ssr: false, loading: () => <div /> });
const HybridOnlineUsers = dynamic(() => import('@/components/ui/online-users').then(mod => mod.default), { ssr: false, loading: () => null });

export default function AdminProfilesPage() {
  const [addModalOpen, setAddModalOpen] = useState(false);
  const { isAuthenticated } = useAuth();
  const router = useRouter();
  const searchParams = useSearchParams();
  
  // State management
  const [profiles, setProfiles] = useState<Profile[]>([]);
  const [registerModalOpen, setRegisterModalOpen] = useState(false);
  const [editModalOpen, setEditModalOpen] = useState(false);
  const [viewModalOpen, setViewModalOpen] = useState(false);
  const [selectedProfile, setSelectedProfile] = useState<Profile | null>(null);
  // Tab state for per-type views
  const [activeTab, setActiveTab] = useState<string>('all');
  
  // Pagination state
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [totalItems, setTotalItems] = useState(0);
  const [perPage] = useState(10);
  const [isLoading, setIsLoading] = useState(false);
  
  // Filter and sort state
  const [searchQuery, setSearchQuery] = useState('');
  
  // Dialog states
  const [deleteDialog, setDeleteDialog] = useState<{ open: boolean; profile?: Profile }>({ open: false });

  // Helper function to filter duplicate profiles using composite key (type-id)
  // Some different profile types share the same numeric ID (e.g., user id 2 and school id 2),
  // so de-duping by numeric id alone removes non-duplicates. Use `type-id` to disambiguate.
  const getUniqueProfiles = useCallback((profiles: Profile[]) => {
    const seen = new Set<string>();
    return profiles.filter(profile => {
      const key = `${profile.type}-${profile.id}`;
      if (seen.has(key)) return false;
      seen.add(key);
      return true;
    });
  }, []);
  
  // Available options for filters
  const [schools, setSchools] = useState<Array<{ id: number; name: string; short_name?: string }>>([]);
  const [sportsList, setSportsList] = useState<Array<{ id: number; name: string }>>([]);
  
  const handleProfileCreated = async (profile: Profile | null) => {
    if (!profile) {
      toast.error('Profile was not created.');
      return;
    }
    toast.success(`Added ${getProfileName(profile)}.`);
    
    // Reset to page 1 and reload current tab's data
    setCurrentPage(1);
    await loadProfilesOnly(1);
  };

  const handleProfileUpdated = async (profile: Profile) => {
    toast.success(`${getProfileName(profile)} updated successfully.`);
    
    // Reload current tab's data
    await loadProfilesOnly(currentPage);
  };

  const handleEditProfile = (profile: Profile) => {
    setSelectedProfile(profile);
    setEditModalOpen(true);
  };

  const handleViewProfile = (profile: Profile) => {
    setSelectedProfile(profile);
    setViewModalOpen(true);
  };

  // Load profiles only (for tab changes and pagination)
  // This function requires the caller to pass an explicit page number to avoid
  // relying on component state (`currentPage`) inside the hook, which can
  // produce effect/dependency loops. Callers should call with `1` for first
  // page or `currentPage` when reloading the current page.
  const loadProfilesOnly = useCallback(async (page: number, tabOverride?: string) => {
    const targetPage = Number.isInteger(page) ? page : 1;
    const activeTabValue = tabOverride || activeTab;

    // Ensure filters match current active tab
    const currentFilters = {
      search: searchQuery,
      type: activeTabValue === 'all' ? 'all' : activeTabValue
    };

    setIsLoading(true);
    // Optimistically set the current page so UI updates immediately
    setCurrentPage(targetPage);

    try {
      console.debug('Requesting profiles with filters:', currentFilters, 'page:', targetPage, 'perPage:', perPage);
      const profilesResponse = await adminProfilesService.getProfiles(
        currentFilters,
        undefined,
        targetPage,
        perPage
      );

      // Debug: log backend returned profile types
      try {
        console.debug('Backend returned types:', profilesResponse.data.map((p: Profile) => `${p.type}-${p.id}`));
      } catch (e) {
        console.debug('Unable to log backend profile types', e);
      }

      const validProfiles = getUniqueProfiles(profilesResponse.data || []);

      setProfiles(validProfiles);
      setTotalPages(profilesResponse.meta?.last_page || 1);
      setTotalItems(profilesResponse.meta?.total || validProfiles.length);
    } catch (err) {
      console.error('Error loading profiles:', err);
      setProfiles([]);
      setCurrentPage(1);
      setTotalPages(1);
      setTotalItems(0);
    } finally {
      setIsLoading(false);
    }
  }, [searchQuery, perPage, getUniqueProfiles, activeTab]);

  // Load filter options
  const loadFilterOptions = useCallback(async () => {
    try {
      const [schoolsData, sportsRes] = await Promise.all([
        adminProfilesService.getSchools(),
        apiClient.public.sports.list(),
      ]);

      const schools = schoolsData || [];
      let sportsArray: Array<{ id: number; name: string }> = [];
      if (Array.isArray(sportsRes?.data)) {
        sportsArray = sportsRes.data;
      } else if (sportsRes?.data && typeof sportsRes.data === 'object' && 'data' in sportsRes.data) {
        sportsArray = (sportsRes.data as { data: Array<{ id: number; name: string }> }).data || [];
      }
      const sports = sportsArray.map((s: { id: number; name: string }) => ({ id: s.id, name: s.name }));

      setSchools(schools);
      setSportsList(sports);
    } catch (err) {
      console.error('Error loading filter options:', err);
    }
  }, []);

  const handleTabChange = useCallback((value: string) => {
    setActiveTab(value);
    setCurrentPage(1);
    loadProfilesOnly(1, value);
  }, [loadProfilesOnly]);

  // Search with debounce - simplified to trigger load directly
  useEffect(() => {
    const timer = setTimeout(() => {
      setCurrentPage(1);
      loadProfilesOnly(1);
    }, 500);

    return () => clearTimeout(timer);
  }, [searchQuery, loadProfilesOnly]);

  const loadInitialData = useCallback(async () => {
    if (!isAuthenticated) {
      router.push('/login');
      return;
    }

    try {
      await loadFilterOptions();
      await loadProfilesOnly(1);
    } catch (err) {
      console.error('Error loading initial data:', err);
      setProfiles([]);
    } finally {
      // Removed setIsLoading(false) to improve performance
    }
  }, [isAuthenticated, router, loadFilterOptions, loadProfilesOnly]);

  // Load initial data on mount
  useEffect(() => {
    if (isAuthenticated) {
      loadInitialData();
    }
  }, [isAuthenticated, loadInitialData]);

  // Load filter options on mount
  useEffect(() => {
    loadFilterOptions();
  }, [loadFilterOptions]);

  // Check for create query param to open add modal and type param to set active tab
  useEffect(() => {
    if (searchParams.get('create') === 'athlete') {
      setAddModalOpen(true);
    }
    
    // Set active tab from URL parameter
    const typeParam = searchParams.get('type');
    if (typeParam && ['all', 'athlete', 'coach', 'official', 'tournament_manager', 'school'].includes(typeParam)) {
      setActiveTab(typeParam);
    }
  }, [searchParams]);

  // Handle single delete
  const handleDelete = async (profile: Profile) => {
    try {
      await adminProfilesService.deleteProfile(profile.type, profile.id);
      toast.success(`${getProfileName(profile)} deleted successfully.`);
      setDeleteDialog({ open: false });
      
      // Check if we need to adjust page after deletion
      const newTotalItems = totalItems - 1;
      const maxPage = Math.ceil(newTotalItems / perPage);
      if (currentPage > maxPage && maxPage > 0) {
        setCurrentPage(maxPage);
        await loadProfilesOnly(maxPage);
      } else {
        await loadProfilesOnly(currentPage);
      }
    } catch (err) {
      console.error('Error deleting profile:', err);
      toast.error('Failed to delete profile');
    }
  };

  // Utility functions
  const getProfileName = (profile: Profile): string => {
    if (profile.type === 'school') {
      return (profile as SchoolProfile).name;
    }
    const p = profile as AthleteProfile | CoachProfile | OfficialProfile | TournamentManagerProfile;
    return `${p.first_name} ${p.last_name}`;
  };

  const getStatusBadge = (status: string) => {
    const variants = {
      active: 'default',
      inactive: 'secondary',
      pending: 'outline'
    } as const;

    const colors = {
      active: 'text-green-700 bg-green-100',
      inactive: 'text-red-700 bg-red-100',
      pending: 'text-yellow-700 bg-yellow-100'
    };

    return (
      <Badge variant={variants[status as keyof typeof variants]} className={colors[status as keyof typeof colors]}>
        {status.charAt(0).toUpperCase() + status.slice(1)}
      </Badge>
    );
  };

  const getInitials = (profile: Profile) => {
    const name = getProfileName(profile).trim();
    if (!name) return '';
    const parts = name.split(/\s+/);
    if (parts.length === 1) return parts[0].charAt(0).toUpperCase();
    return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
  };

  const getSportName = (profile: Profile) => {
    const sportField = (profile as unknown as { sport?: string | { name?: string } }).sport;
    if (!sportField) return '';

    if (typeof sportField === 'string') {
      const cleaned = sportField.replace(/[_\-]+/g, ' ');
      return cleaned.split(/\s+/).map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
    }

    const name = sportField.name;
    if (!name) return '';
    return name.split(/[_\-\s]+/).map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
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
            <span>Profiles</span>
          </nav>
        </div>
        <div className="flex-1" />
          <div className="px-4 flex items-center gap-2">
          <HybridOnlineUsers 
              compactMode={true}
              maxDisplayAvatars={5}
              showStatus={true}
              refreshInterval={30000}
              className="mr-2"
          />
        </div>
      </header>

      <main className="flex-1 p-4 md:p-6 overflow-auto min-h-0">
        <div className="space-y-6 md:space-y-8 max-w-full">
          {/* Header */}
          <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
              <h1 className="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">
                Profile Management
              </h1>
              <p className="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Manage all participant profiles in the PRISAA system
              </p>
            </div>
            <div className="flex flex-col sm:flex-row items-start sm:items-center gap-2">
              <Button size="sm" className="gap-2 w-full bg-blue-900 hover:bg-blue-500 sm:w-auto" onClick={() => setAddModalOpen(true)}>
                <Plus className="h-4 w-4" />
                Add Profile
              </Button>
              <Button size="sm" variant="outline" className="gap-2 w-full sm:w-auto" onClick={() => setRegisterModalOpen(true)}>
                <UserPlus className="h-4 w-4" />
                Register User
              </Button>
              {/* Add Profile Modal */}
              <AddProfileModal
                open={addModalOpen}
                onClose={() => setAddModalOpen(false)}
                onProfileCreated={handleProfileCreated}
                schools={schools}
                sportsList={sportsList}
              />
              <RegisterAuthorizedUserModal
                open={registerModalOpen}
                onClose={() => setRegisterModalOpen(false)}
                onRegistered={(p) => { if (p) { toast.success('User registered'); loadProfilesOnly(currentPage); } }}
                schools={schools}
              />
              <EditProfileModal
                open={editModalOpen}
                onClose={() => setEditModalOpen(false)}
                onProfileUpdated={handleProfileUpdated}
                profile={selectedProfile}
                schools={schools}
                sportsList={sportsList}
              />
              <ViewProfileModal
                open={viewModalOpen}
                onClose={() => setViewModalOpen(false)}
                profile={selectedProfile}
              />
            </div>
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
                      <TabsTrigger value="athlete">Athletes</TabsTrigger>
                      <TabsTrigger value="coach">Coaches</TabsTrigger>
                      <TabsTrigger value="official">Officials</TabsTrigger>
                      <TabsTrigger value="tournament_manager">Tournament Managers</TabsTrigger>
                      <TabsTrigger value="school">Schools</TabsTrigger>
                    </TabsList>
                  </Tabs>
                </div>
                <div className="w-full md:w-[38%]">
                  <div className="relative">
                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                    <Input
                      placeholder="Search profiles..."
                      value={searchQuery}
                      onChange={(e) => setSearchQuery(e.target.value)}
                      className="pl-10"
                    />
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <User className="h-5 w-5" />
                {activeTab.charAt(0).toUpperCase() + activeTab.slice(1)} Profiles
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="overflow-x-auto">
                {activeTab === 'all' && (
                  <Table className="table-fixed w-full">
                    <TableHeader>
                      <TableRow>
                        <TableHead className="w-12">Profile</TableHead>
                        <TableHead className="w-32">Name</TableHead>
                        <TableHead className="w-24 hidden sm:table-cell">Type</TableHead>
                        <TableHead className="w-16">Status</TableHead>
                        <TableHead className="w-16">Actions</TableHead>
                      </TableRow>
                    </TableHeader>
                    <TableBody>
                      {profiles.length === 0 ? (
                        <TableRow>
                          <TableCell colSpan={5} className="text-center py-8">
                            <div className="flex flex-col items-center gap-2">
                              <Users className="h-8 w-8 text-muted-foreground" />
                              <p className="text-muted-foreground">No profiles found</p>
                            </div>
                          </TableCell>
                        </TableRow>
                      ) : (
                        getUniqueProfiles(profiles).map((profile) => (
                          <TableRow key={`${profile.type}-${profile.id}`}>
                            <TableCell className="w-14">
                              <Avatar className="h-8 w-8">
                                {profile.avatar ? (
                                  <AvatarImage src={profile.avatar} alt={getProfileName(profile)} />
                                ) : (
                                  <AvatarFallback className="text-xs font-medium">{getInitials(profile)}</AvatarFallback>
                                )}
                              </Avatar>
                            </TableCell>
                            <TableCell className="min-w-[12rem] font-medium">{getProfileName(profile)}</TableCell>
                            <TableCell className="w-32 hidden sm:table-cell">
                              <Badge variant="outline" className="text-xs">
                                {profile.type === 'tournament_manager' ? 'Tournament Manager' : 
                                 profile.type === 'official' ? 'Official' : 
                                 profile.type.charAt(0).toUpperCase() + profile.type.slice(1)}
                              </Badge>
                            </TableCell>
                            <TableCell className="w-20">{getStatusBadge(profile.status)}</TableCell>
                            <TableCell className="w-16">
                              <DropdownMenu>
                                <DropdownMenuTrigger asChild>
                                  <Button variant="ghost" size="sm"><MoreHorizontal className="h-4 w-4" /></Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent>
                                  <DropdownMenuItem onClick={() => handleViewProfile(profile)}><Eye className="h-4 w-4 mr-2" />View Details</DropdownMenuItem>
                                  <DropdownMenuItem onClick={() => handleEditProfile(profile)}><Edit className="h-4 w-4 mr-2" />Edit</DropdownMenuItem>
                                  <DropdownMenuSeparator />
                                  <DropdownMenuItem onClick={() => setDeleteDialog({ open: true, profile })} className="text-red-600"><Trash2 className="h-4 w-4 mr-2" />Delete</DropdownMenuItem>
                                </DropdownMenuContent>
                              </DropdownMenu>
                            </TableCell>
                          </TableRow>
                        ))
                      )}
                    </TableBody>
                  </Table>
                )}

                {/* Per-type tables */}
                {activeTab === 'athlete' && (
                  <Table className="table-fixed w-full">
                    <TableHeader>
                      <TableRow>
                        <TableHead className="w-12">Profile</TableHead>
                        <TableHead className="w-32">Name</TableHead>
                        <TableHead className="w-24 hidden sm:table-cell">Email</TableHead>
                        <TableHead className="w-12 hidden md:table-cell">School</TableHead>
                        <TableHead className="w-12 hidden lg:table-cell">Sport</TableHead>
                        <TableHead className="w-12">Status</TableHead>
                        <TableHead className="w-12">Actions</TableHead>
                      </TableRow>
                    </TableHeader>
                    <TableBody>
                      {getUniqueProfiles(profiles).map(p => (
                        <TableRow key={`${p.type}-${p.id}`}>
                          <TableCell><Avatar className="h-8 w-8"><AvatarFallback>{getInitials(p)}</AvatarFallback></Avatar></TableCell>
                          <TableCell className="font-medium">{getProfileName(p)}</TableCell>
                          <TableCell className="hidden sm:table-cell">{(p as AthleteProfile).email || '-'}</TableCell>
                          <TableCell className="hidden md:table-cell">{(p as AthleteProfile).school?.short_name || 'No school assigned'}</TableCell>
                          <TableCell className="hidden lg:table-cell">{getSportName(p)}</TableCell>
                          <TableCell>{getStatusBadge(p.status)}</TableCell>
                          <TableCell>
                            <DropdownMenu>
                              <DropdownMenuTrigger asChild>
                                <Button variant="ghost" size="sm"><MoreHorizontal className="h-4 w-4" /></Button>
                              </DropdownMenuTrigger>
                              <DropdownMenuContent>
                                <DropdownMenuItem onClick={() => handleViewProfile(p)}><Eye className="h-4 w-4 mr-2" />View Details</DropdownMenuItem>
                                <DropdownMenuItem onClick={() => handleEditProfile(p)}><Edit className="h-4 w-4 mr-2" />Edit</DropdownMenuItem>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem onClick={() => setDeleteDialog({ open: true, profile: p })} className="text-red-600"><Trash2 className="h-4 w-4 mr-2"/>Delete</DropdownMenuItem>
                              </DropdownMenuContent>
                            </DropdownMenu>
                          </TableCell>
                        </TableRow>
                      ))}
                    </TableBody>
                  </Table>
                )}

                {activeTab === 'official' && (
                  <div>
                    <Table className="table-fixed w-full">
                      <TableHeader>
                        <TableRow>
                          <TableHead className="w-8">Profile</TableHead>
                          <TableHead className="w-32">Name</TableHead>
                          <TableHead className="w-24 hidden sm:table-cell">Email</TableHead>
                          <TableHead className="w-16 hidden lg:table-cell">Certification</TableHead>
                          <TableHead className="w-12">Status</TableHead>
                          <TableHead className="w-12">Actions</TableHead>
                        </TableRow>
                      </TableHeader>
                      <TableBody>
                        {getUniqueProfiles(profiles).map(p => (
                          <TableRow key={`${p.type}-${p.id}`}>
                            <TableCell><Avatar className="h-8 w-8"><AvatarFallback>{getInitials(p)}</AvatarFallback></Avatar></TableCell>
                            <TableCell className="font-medium">{getProfileName(p)}</TableCell>
                            <TableCell className="hidden sm:table-cell">{(p as OfficialProfile).email || '-'}</TableCell>
                            <TableCell className="hidden lg:table-cell">{(p as OfficialProfile).certification_level || '-'}</TableCell>
                            <TableCell>{getStatusBadge(p.status)}</TableCell>
                            <TableCell>
                              <DropdownMenu>
                                <DropdownMenuTrigger asChild>
                                  <Button variant="ghost" size="sm"><MoreHorizontal className="h-4 w-4" /></Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent>
                                  <DropdownMenuItem onClick={() => handleViewProfile(p)}><Eye className="h-4 w-4 mr-2" />View Details</DropdownMenuItem>
                                  <DropdownMenuItem onClick={() => handleEditProfile(p)}><Edit className="h-4 w-4 mr-2" />Edit</DropdownMenuItem>
                                  <DropdownMenuSeparator />
                                  <DropdownMenuItem onClick={() => setDeleteDialog({ open: true, profile: p })} className="text-red-600"><Trash2 className="h-4 w-4 mr-2"/>Delete</DropdownMenuItem>
                                </DropdownMenuContent>
                              </DropdownMenu>
                            </TableCell>
                          </TableRow>
                        ))}
                      </TableBody>
                    </Table>
                  </div>
                )}

                {activeTab === 'school' && (
                  <Table className="table-fixed w-full">
                    <TableHeader>
                      <TableRow>
                        <TableHead className="w-8">Logo</TableHead>
                        <TableHead className="w-40">School Name</TableHead>
                        <TableHead className="w-12 hidden sm:table-cell">Short Name</TableHead>
                        <TableHead className="w-32 hidden md:table-cell">Address</TableHead>
                        <TableHead className="w-12 hidden lg:table-cell">Region</TableHead>
                        <TableHead className="w-12">Status</TableHead>
                        <TableHead className="w-12">Actions</TableHead>
                      </TableRow>
                    </TableHeader>
                    <TableBody>
                      {getUniqueProfiles(profiles).map(p => (
                        <TableRow key={`${p.type}-${p.id}`}>
                          <TableCell><Avatar className="h-8 w-8"><AvatarFallback>{getInitials(p)}</AvatarFallback></Avatar></TableCell>
                          <TableCell className="font-medium">{(p as SchoolProfile).name}</TableCell>
                          <TableCell className="hidden sm:table-cell">{(p as SchoolProfile).short_name || '-'}</TableCell>
                          <TableCell className="hidden md:table-cell">{(p as SchoolProfile).address || '-'}</TableCell>
                          <TableCell className="hidden lg:table-cell">{(p as SchoolProfile).region?.name || '-'}</TableCell>
                          <TableCell>{getStatusBadge((p as SchoolProfile).status)}</TableCell>
                          <TableCell>
                            <DropdownMenu>
                              <DropdownMenuTrigger asChild>
                                <Button variant="ghost" size="sm"><MoreHorizontal className="h-4 w-4" /></Button>
                              </DropdownMenuTrigger>
                              <DropdownMenuContent>
                                <DropdownMenuItem onClick={() => handleViewProfile(p)}><Eye className="h-4 w-4 mr-2" />View Details</DropdownMenuItem>
                                <DropdownMenuItem onClick={() => handleEditProfile(p)}><Edit className="h-4 w-4 mr-2" />Edit</DropdownMenuItem>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem onClick={() => setDeleteDialog({ open: true, profile: p })} className="text-red-600"><Trash2 className="h-4 w-4 mr-2"/>Delete</DropdownMenuItem>
                              </DropdownMenuContent>
                            </DropdownMenu>
                          </TableCell>
                        </TableRow>
                      ))}
                    </TableBody>
                  </Table>
                )}

                {activeTab === 'coach' && (
                  <div>
                    <Table className="table-fixed w-full">
                      <TableHeader>
                        <TableRow>
                          <TableHead className="w-8">Profile</TableHead>
                          <TableHead className="w-32">Name</TableHead>
                          <TableHead className="w-24 hidden sm:table-cell">Email</TableHead>
                          <TableHead className="w-40 hidden md:table-cell">School</TableHead>
                          <TableHead className="w-12">Status</TableHead>
                          <TableHead className="w-12">Actions</TableHead>
                        </TableRow>
                      </TableHeader>
                      <TableBody>
                        {getUniqueProfiles(profiles).map(p => (
                          <TableRow key={`${p.type}-${p.id}`}>
                            <TableCell><Avatar className="h-8 w-8"><AvatarFallback>{getInitials(p)}</AvatarFallback></Avatar></TableCell>
                            <TableCell className="font-medium">{getProfileName(p)}</TableCell>
                            <TableCell className="hidden sm:table-cell">{(p as CoachProfile).email || '-'}</TableCell>
                            <TableCell className="hidden md:table-cell">{(p as CoachProfile).school?.name || 'No school assigned'}</TableCell>
                            <TableCell>{getStatusBadge(p.status)}</TableCell>
                            <TableCell>
                              <DropdownMenu>
                                <DropdownMenuTrigger asChild>
                                  <Button variant="ghost" size="sm"><MoreHorizontal className="h-4 w-4" /></Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent>
                                  <DropdownMenuItem onClick={() => handleViewProfile(p)}><Eye className="h-4 w-4 mr-2" />View Details</DropdownMenuItem>
                                  <DropdownMenuItem onClick={() => handleEditProfile(p)}><Edit className="h-4 w-4 mr-2" />Edit</DropdownMenuItem>
                                  <DropdownMenuSeparator />
                                  <DropdownMenuItem onClick={() => setDeleteDialog({ open: true, profile: p })} className="text-red-600"><Trash2 className="h-4 w-4 mr-2"/>Delete</DropdownMenuItem>
                                </DropdownMenuContent>
                              </DropdownMenu>
                            </TableCell>
                          </TableRow>
                        ))}
                      </TableBody>
                    </Table>
                  </div>
                )}

                {activeTab === 'tournament_manager' && (
                  <div>
                    <Table className="table-fixed w-full">
                      <TableHeader>
                        <TableRow>
                          <TableHead className="w-8">Profile</TableHead>
                          <TableHead className="w-32">Name</TableHead>
                          <TableHead className="w-24 hidden sm:table-cell">Email</TableHead>
                          <TableHead className="w-12">Status</TableHead>
                          <TableHead className="w-12">Actions</TableHead>
                        </TableRow>
                      </TableHeader>
                      <TableBody>
                        {getUniqueProfiles(profiles).map(p => (
                          <TableRow key={`${p.type}-${p.id}`}>
                            <TableCell><Avatar className="h-8 w-8"><AvatarFallback>{getInitials(p)}</AvatarFallback></Avatar></TableCell>
                            <TableCell className="font-medium">{getProfileName(p)}</TableCell>
                            <TableCell className="hidden sm:table-cell">{(p as TournamentManagerProfile).email || '-'}</TableCell>
                            <TableCell>{getStatusBadge(p.status)}</TableCell>
                            <TableCell>
                              <DropdownMenu>
                                <DropdownMenuTrigger asChild>
                                  <Button variant="ghost" size="sm"><MoreHorizontal className="h-4 w-4" /></Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent>
                                  <DropdownMenuItem onClick={() => handleViewProfile(p)}><Eye className="h-4 w-4 mr-2" />View Details</DropdownMenuItem>
                                  <DropdownMenuItem onClick={() => handleEditProfile(p)}><Edit className="h-4 w-4 mr-2" />Edit</DropdownMenuItem>
                                  <DropdownMenuSeparator />
                                  <DropdownMenuItem onClick={() => setDeleteDialog({ open: true, profile: p })} className="text-red-600"><Trash2 className="h-4 w-4 mr-2"/>Delete</DropdownMenuItem>
                                </DropdownMenuContent>
                              </DropdownMenu>
                            </TableCell>
                          </TableRow>
                        ))}
                      </TableBody>
                    </Table>
                  </div>
                )}

                {/* Modern Pagination */}
                {totalPages > 1 && (
                  <div className="flex items-center justify-between mt-6">
                    <div className="flex items-center gap-2">
                      <span className="text-sm text-muted-foreground">
                        Showing {(currentPage - 1) * perPage + 1} to {Math.min(currentPage * perPage, totalItems)} of {totalItems} results
                      </span>
                    </div>
                    <div className="flex items-center gap-2">
                      <Button
                        variant="outline"
                        size="sm"
                        onClick={() => !isLoading && loadProfilesOnly(Math.max(1, currentPage - 1))}
                        disabled={currentPage === 1 || isLoading}
                        className="flex items-center gap-1"
                      >
                        <ChevronLeft className="h-4 w-4" />
                        Previous
                      </Button>

                      <div className="flex items-center gap-1">
                        {(() => {
                          const pages: React.ReactNode[] = [];
                          const maxVisiblePages = 5;
                          let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
                          const endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

                          if (endPage - startPage + 1 < maxVisiblePages) {
                            startPage = Math.max(1, endPage - maxVisiblePages + 1);
                          }

                          if (startPage > 1) {
                            pages.push(
                              <Button key={1} variant={currentPage === 1 ? "default" : "outline"} size="sm" onClick={() => loadProfilesOnly(1)} className="w-10 h-10 p-0">1</Button>
                            );
                            if (startPage > 2) {
                              pages.push(<span key="ellipsis1" className="px-2 text-muted-foreground">...</span>);
                            }
                          }

                          for (let i = startPage; i <= endPage; i++) {
                            pages.push(
                              <Button key={i} variant={currentPage === i ? "default" : "outline"} size="sm" onClick={() => loadProfilesOnly(i)} className="w-10 h-10 p-0">{i}</Button>
                            );
                          }

                          if (endPage < totalPages) {
                            if (endPage < totalPages - 1) {
                              pages.push(<span key="ellipsis2" className="px-2 text-muted-foreground">...</span>);
                            }
                            pages.push(
                              <Button key={totalPages} variant={currentPage === totalPages ? "default" : "outline"} size="sm" onClick={() => loadProfilesOnly(totalPages)} className="w-10 h-10 p-0">{totalPages}</Button>
                            );
                          }

                          return pages;
                        })()}
                      </div>

                      <Button
                        variant="outline"
                        size="sm"
                        onClick={() => !isLoading && loadProfilesOnly(Math.min(totalPages, currentPage + 1))}
                        disabled={currentPage === totalPages || isLoading}
                        className="flex items-center gap-1"
                      >
                        Next
                        <ChevronRight className="h-4 w-4" />
                      </Button>
                    </div>
                  </div>
                )}
              </div>
            </CardContent>
          </Card>

          {/* Delete Confirmation Dialog */}
          <AlertDialog open={deleteDialog.open} onOpenChange={(open) => setDeleteDialog({ open })}>
            <AlertDialogContent>
              <AlertDialogHeader>
                <AlertDialogTitle>Are you sure?</AlertDialogTitle>
                <AlertDialogDescription>
                  This action cannot be undone. This will permanently delete the profile for{' '}
                  <strong>{deleteDialog.profile && getProfileName(deleteDialog.profile)}</strong>.
                </AlertDialogDescription>
              </AlertDialogHeader>
              <AlertDialogFooter>
                <AlertDialogCancel>Cancel</AlertDialogCancel>
                <AlertDialogAction
                  onClick={() => deleteDialog.profile && handleDelete(deleteDialog.profile)}
                  className="bg-red-900 hover:bg-red-500"
                >
                  Delete
                </AlertDialogAction>
              </AlertDialogFooter>
            </AlertDialogContent>
          </AlertDialog>
        </div>
      </main>
    </div>
  );
}