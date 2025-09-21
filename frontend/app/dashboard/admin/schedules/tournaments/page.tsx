'use client';

import { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from '@/components/ui/alert-dialog';
import { apiClient, type Region } from '@/src/lib/api';
import { Plus, Edit, Trash2 } from 'lucide-react';
import dynamic from 'next/dynamic';

const TournamentsStats = dynamic(() => import('@/components/admin/TournamentsStats').then(m => m.default), { ssr: false, loading: () => null });
import toast from 'react-hot-toast';

import { Tournament } from '@/src/lib/api';

// Form data type for create/edit dialogs
interface TournamentFormData {
  name: string;
  description: string;
  start_date: string;
  end_date: string;
  level: 'Provincial' | 'Regional' | 'National';
  status: Tournament['status'];
  host_region_id?: number;
  registration_deadline?: string;
  max_participants?: string | number;
  entry_fee?: string | number;
  prize_pool?: string | number;
  venue_id?: string;
  rules?: string;
  contact_info?: string;
}

const tournamentStatuses = [
  { value: 'upcoming', label: 'Upcoming', color: 'secondary' },
  { value: 'ongoing', label: 'Ongoing', color: 'default' },
  { value: 'completed', label: 'Completed', color: 'outline' }
];

export default function TournamentsPage() {
  const [tournaments, setTournaments] = useState<Tournament[]>([]);
  const [venues, setVenues] = useState<Array<{ id: number; name: string; city: string; state: string }>>([]);
  const [regions, setRegions] = useState<Region[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');
  const [statusFilter, setStatusFilter] = useState<string>('all');
  const [isCreateDialogOpen, setIsCreateDialogOpen] = useState(false);
  const [editingTournament, setEditingTournament] = useState<Tournament | null>(null);

  // Form state
  const [formData, setFormData] = useState<TournamentFormData>({
    name: '',
    description: '',
    start_date: '',
    end_date: '',
    level: 'Provincial',
    status: 'upcoming',
    host_region_id: undefined,
    registration_deadline: undefined,
    max_participants: undefined,
    entry_fee: undefined,
    prize_pool: undefined,
  venue_id: undefined,
    rules: undefined,
    contact_info: undefined,
  });
  // Extend formData runtime shape with optional fields used in dialogs
  // (registration_deadline, max_participants, entry_fee, prize_pool, venue_id, rules, contact_info)
  // We use type assertions in handlers where necessary.

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      setLoading(true);
      const [tournamentsRes, venuesRes, regionsRes] = await Promise.all([
        apiClient.admin.tournaments.list(),
        apiClient.admin.venues.list(),
        apiClient.admin.regions.list()
      ]);

      if (tournamentsRes.success) {
        setTournaments(tournamentsRes.data || []);
      }

      if (venuesRes.success) {
        setVenues(venuesRes.data || []);
      }

      if (regionsRes.success) {
        setRegions(regionsRes.data || []);
      }
    } catch (error) {
      console.error('Error loading data:', error);
      toast.error('Failed to load tournament data');
    } finally {
      setLoading(false);
    }
  };

  const loadTournaments = async () => {
    try {
      const response = await apiClient.admin.tournaments.list();
      if (response.success) {
        setTournaments(response.data || []);
      } else {
        toast.error('Failed to load tournaments');
      }
    } catch (error) {
      console.error('Error loading tournaments:', error);
      toast.error('Failed to load tournaments');
    }
  };

  const handleCreate = async () => {
    try {
      const data = {
        ...formData
      };

      const response = await apiClient.admin.tournaments.create(data);
      if (response.success) {
        toast.success('Tournament created successfully');
        setIsCreateDialogOpen(false);
        resetForm();
        loadTournaments();
      } else {
        toast.error(response.message || 'Failed to create tournament');
      }
    } catch (error) {
      console.error('Error creating tournament:', error);
      toast.error('Failed to create tournament');
    }
  };

  const handleUpdate = async () => {
    if (!editingTournament) return;

    try {
      const data = {
        ...formData
      };

      const response = await apiClient.admin.tournaments.update(editingTournament.id, data);
      if (response.success) {
        toast.success('Tournament updated successfully');
        setEditingTournament(null);
        resetForm();
        loadTournaments();
      } else {
        toast.error(response.message || 'Failed to update tournament');
      }
    } catch (error) {
      console.error('Error updating tournament:', error);
      toast.error('Failed to update tournament');
    }
  };

  const handleDelete = async (tournamentId: number) => {
    try {
      const response = await apiClient.admin.tournaments.delete(tournamentId);
      if (response.success) {
        toast.success('Tournament deleted successfully');
        loadTournaments();
      } else {
        toast.error(response.message || 'Failed to delete tournament');
      }
    } catch (error) {
      console.error('Error deleting tournament:', error);
      toast.error('Failed to delete tournament');
    }
  };

  const resetForm = () => {
    setFormData({
      name: '',
      description: '',
      start_date: '',
      end_date: '',
      level: 'Provincial',
      status: 'upcoming',
      host_region_id: undefined
    });
  };

  const openEditDialog = (tournament: Tournament) => {
    setEditingTournament(tournament);
    setFormData({
      name: tournament.name,
      description: tournament.description,
      start_date: tournament.start_date.split('T')[0],
      end_date: tournament.end_date.split('T')[0],
      level: tournament.level,
      status: tournament.status,
      host_region_id: tournament.host_region_id
    });
  };

  const filteredTournaments = tournaments.filter(tournament => {
    const matchesSearch = tournament.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         tournament.description?.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesStatus = statusFilter === 'all' || tournament.status === statusFilter;
    return matchesSearch && matchesStatus;
  });

  const stats = {
    total: tournaments.length,
    upcoming: tournaments.filter(t => t.status === 'upcoming').length,
    ongoing: tournaments.filter(t => t.status === 'ongoing').length,
    completed: tournaments.filter(t => t.status === 'completed').length
  };

  const getStatusBadge = (status: Tournament['status']) => {
    const statusConfig = tournamentStatuses.find(s => s.value === status);
    return (
      <Badge variant={statusConfig?.color as "default" | "secondary" | "outline" | "destructive" || 'secondary'}>
        {statusConfig?.label || status}
      </Badge>
    );
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="text-center">
          <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"></div>
          <p className="mt-2 text-muted-foreground">Loading tournaments...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Stats Cards (lazy-loaded) */}
      <TournamentsStats stats={stats} />

      {/* Main Content */}
      <Card>
        <CardHeader>
          <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
              <CardTitle>Tournament Management</CardTitle>
              <CardDescription>Manage tournament events and competitions</CardDescription>
            </div>

            <Dialog open={isCreateDialogOpen} onOpenChange={setIsCreateDialogOpen}>
              <DialogTrigger asChild>
                <Button>
                  <Plus className="h-4 w-4 mr-2" />
                  Add Tournament
                </Button>
              </DialogTrigger>
              <DialogContent className="max-w-3xl max-h-[90vh] overflow-y-auto">
                <DialogHeader>
                  <DialogTitle>Add New Tournament</DialogTitle>
                  <DialogDescription>
                    Create a new tournament event with all necessary details.
                  </DialogDescription>
                </DialogHeader>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div className="space-y-2 md:col-span-2">
                    <Label htmlFor="name">Tournament Name *</Label>
                    <Input
                      id="name"
                      value={formData.name}
                      onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                      placeholder="e.g., PRISAA National Championship 2025"
                    />
                  </div>

                  <div className="space-y-2 md:col-span-2">
                    <Label htmlFor="description">Description</Label>
                    <Textarea
                      id="description"
                      value={formData.description}
                      onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                      placeholder="Tournament description and objectives"
                      rows={3}
                    />
                  </div>

                  <div className="space-y-2">
                    <Label htmlFor="start_date">Start Date *</Label>
                    <Input
                      id="start_date"
                      type="date"
                      value={formData.start_date}
                      onChange={(e) => setFormData({ ...formData, start_date: e.target.value })}
                    />
                  </div>

                  <div className="space-y-2">
                    <Label htmlFor="end_date">End Date *</Label>
                    <Input
                      id="end_date"
                      type="date"
                      value={formData.end_date}
                      onChange={(e) => setFormData({ ...formData, end_date: e.target.value })}
                    />
                  </div>

                  <div className="space-y-2">
                    <Label htmlFor="level">Level</Label>
                    <Select value={formData.level} onValueChange={(value) => setFormData({ ...formData, level: value as 'Provincial' | 'Regional' | 'National' })}>
                      <SelectTrigger>
                        <SelectValue placeholder="Select level" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="Provincial">Provincial</SelectItem>
                        <SelectItem value="Regional">Regional</SelectItem>
                        <SelectItem value="National">National</SelectItem>
                      </SelectContent>
                    </Select>
                  </div>

                  <div className="space-y-2">
                    <Label htmlFor="host_region">Host Region</Label>
                    <Select value={formData.host_region_id?.toString()} onValueChange={(value) => setFormData({ ...formData, host_region_id: value ? parseInt(value) : undefined })}>
                      <SelectTrigger>
                        <SelectValue placeholder="Select host region (optional)" />
                      </SelectTrigger>
                      <SelectContent>
                        {regions.map((region) => (
                          <SelectItem key={region.id} value={region.id.toString()}>
                            {region.name}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                  </div>












                </div>

                <DialogFooter>
                  <Button variant="outline" onClick={() => setIsCreateDialogOpen(false)}>
                    Cancel
                  </Button>
                  <Button onClick={handleCreate} disabled={!formData.name || !formData.start_date || !formData.end_date}>
                    Create Tournament
                  </Button>
                </DialogFooter>
              </DialogContent>
            </Dialog>
          </div>
        </CardHeader>

        <CardContent>
          <div className="flex flex-col sm:flex-row gap-4 mb-6">
            <div className="flex-1">
              <Input
                placeholder="Search tournaments..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
              />
            </div>

            <Select value={statusFilter} onValueChange={setStatusFilter}>
              <SelectTrigger className="w-full sm:w-[200px]">
                <SelectValue placeholder="Filter by status" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Statuses</SelectItem>
                {tournamentStatuses.map((status) => (
                  <SelectItem key={status.value} value={status.value}>
                    {status.label}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

          <div className="rounded-md border">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Name</TableHead>
                  <TableHead>Dates</TableHead>
                  <TableHead className="hidden sm:table-cell">Level</TableHead>
                  <TableHead className="hidden md:table-cell">Status</TableHead>
                  <TableHead className="w-[100px]">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {filteredTournaments.length === 0 ? (
                  <TableRow>
                    <TableCell colSpan={5} className="text-center py-8 text-muted-foreground">
                      {tournaments.length === 0 ? 'No tournaments found. Create your first tournament!' : 'No tournaments match your search criteria.'}
                    </TableCell>
                  </TableRow>
                ) : (
                  filteredTournaments.map((tournament) => (
                    <TableRow key={tournament.id}>
                      <TableCell className="font-medium">
                        <div>
                          <div>{tournament.name}</div>
                          <div className="text-sm text-muted-foreground">
                            {tournament.description}
                          </div>
                        </div>
                      </TableCell>
                      <TableCell>
                        <div className="text-sm">
                          <div>{new Date(tournament.start_date).toLocaleDateString()}</div>
                          <div className="text-muted-foreground">
                            to {new Date(tournament.end_date).toLocaleDateString()}
                          </div>
                        </div>
                      </TableCell>
                      <TableCell className="hidden sm:table-cell">
                        <Badge variant="outline">{tournament.level}</Badge>
                      </TableCell>
                      <TableCell className="hidden md:table-cell">
                        {getStatusBadge(tournament.status)}
                      </TableCell>
                      <TableCell>
                        <div className="flex items-center gap-2">
                          <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => openEditDialog(tournament)}
                          >
                            <Edit className="h-4 w-4" />
                          </Button>

                          <AlertDialog>
                            <AlertDialogTrigger asChild>
                              <Button variant="ghost" size="sm">
                                <Trash2 className="h-4 w-4" />
                              </Button>
                            </AlertDialogTrigger>
                            <AlertDialogContent>
                              <AlertDialogHeader>
                                <AlertDialogTitle>Delete Tournament</AlertDialogTitle>
                                <AlertDialogDescription>
                                  Are you sure you want to delete &quot;{tournament.name}&quot;? This action cannot be undone.
                                </AlertDialogDescription>
                              </AlertDialogHeader>
                              <AlertDialogFooter>
                                <AlertDialogCancel>Cancel</AlertDialogCancel>
                                <AlertDialogAction onClick={() => handleDelete(tournament.id)}>
                                  Delete
                                </AlertDialogAction>
                              </AlertDialogFooter>
                            </AlertDialogContent>
                          </AlertDialog>
                        </div>
                      </TableCell>
                    </TableRow>
                  ))
                )}
              </TableBody>
            </Table>
          </div>
        </CardContent>
      </Card>

      {/* Edit Dialog */}
      <Dialog open={!!editingTournament} onOpenChange={(open) => !open && setEditingTournament(null)}>
        <DialogContent className="max-w-3xl max-h-[90vh] overflow-y-auto">
          <DialogHeader>
            <DialogTitle>Edit Tournament</DialogTitle>
            <DialogDescription>
              Update the tournament&apos;s information and settings.
            </DialogDescription>
          </DialogHeader>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="space-y-2 md:col-span-2">
              <Label htmlFor="edit-name">Tournament Name *</Label>
              <Input
                id="edit-name"
                value={formData.name}
                onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                placeholder="e.g., PRISAA National Championship 2025"
              />
            </div>

            <div className="space-y-2 md:col-span-2">
              <Label htmlFor="edit-description">Description</Label>
              <Textarea
                id="edit-description"
                value={formData.description}
                onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                placeholder="Tournament description and objectives"
                rows={3}
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="edit-start_date">Start Date *</Label>
              <Input
                id="edit-start_date"
                type="date"
                value={formData.start_date}
                onChange={(e) => setFormData({ ...formData, start_date: e.target.value })}
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="edit-end_date">End Date *</Label>
              <Input
                id="edit-end_date"
                type="date"
                value={formData.end_date}
                onChange={(e) => setFormData({ ...formData, end_date: e.target.value })}
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="edit-registration_deadline">Registration Deadline</Label>
              <Input
                id="edit-registration_deadline"
                type="date"
                value={formData.registration_deadline}
                onChange={(e) => setFormData({ ...formData, registration_deadline: e.target.value })}
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="edit-status">Status</Label>
              <Select value={formData.status} onValueChange={(value) => setFormData({ ...formData, status: value as Tournament['status'] })}>
                <SelectTrigger>
                  <SelectValue placeholder="Select status" />
                </SelectTrigger>
                <SelectContent>
                  {tournamentStatuses.map((status) => (
                    <SelectItem key={status.value} value={status.value}>
                      {status.label}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>

            <div className="space-y-2">
              <Label htmlFor="edit-host_region">Host Region</Label>
              <Select value={formData.host_region_id?.toString()} onValueChange={(value) => setFormData({ ...formData, host_region_id: value ? parseInt(value) : undefined })}>
                <SelectTrigger>
                  <SelectValue placeholder="Select host region (optional)" />
                </SelectTrigger>
                <SelectContent>
                  {regions.map((region) => (
                    <SelectItem key={region.id} value={region.id.toString()}>
                      {region.name}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>

            <div className="space-y-2">
              <Label htmlFor="edit-entry_fee">Entry Fee (₱)</Label>
              <Input
                id="edit-entry_fee"
                type="number"
                step="0.01"
                value={formData.entry_fee}
                onChange={(e) => setFormData({ ...formData, entry_fee: e.target.value })}
                placeholder="Entry fee amount"
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="edit-prize_pool">Prize Pool (₱)</Label>
              <Input
                id="edit-prize_pool"
                type="number"
                step="0.01"
                value={formData.prize_pool}
                onChange={(e) => setFormData({ ...formData, prize_pool: e.target.value })}
                placeholder="Total prize pool"
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="edit-venue_id">Venue</Label>
              <Select value={formData.venue_id} onValueChange={(value) => setFormData({ ...formData, venue_id: value })}>
                <SelectTrigger>
                  <SelectValue placeholder="Select venue" />
                </SelectTrigger>
                <SelectContent>
                  {venues.map((venue) => (
                    <SelectItem key={venue.id} value={venue.id.toString()}>
                      {venue.name} - {venue.city}, {venue.state}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>

            <div className="space-y-2 md:col-span-2">
              <Label htmlFor="edit-rules">Rules & Regulations</Label>
              <Textarea
                id="edit-rules"
                value={formData.rules}
                onChange={(e) => setFormData({ ...formData, rules: e.target.value })}
                placeholder="Tournament rules and regulations"
                rows={4}
              />
            </div>

            <div className="space-y-2 md:col-span-2">
              <Label htmlFor="edit-contact_info">Contact Information</Label>
              <Textarea
                id="edit-contact_info"
                value={formData.contact_info}
                onChange={(e) => setFormData({ ...formData, contact_info: e.target.value })}
                placeholder="Contact details for tournament inquiries"
                rows={2}
              />
            </div>
          </div>

          <DialogFooter>
            <Button variant="outline" onClick={() => setEditingTournament(null)}>
              Cancel
            </Button>
            <Button onClick={handleUpdate} disabled={!formData.name || !formData.start_date || !formData.end_date}>
              Update Tournament
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
}
