'use client';

import { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from '@/components/ui/alert-dialog';
import ScrollCounter from '@/components/ui/scroll-counter';
import toast from 'react-hot-toast';
import { apiClient, Sport } from '@/src/lib/api';
import { Plus, Edit, Trash2, Trophy, Users } from 'lucide-react';

const sportCategories = [
  'Individual',
  'Team',
  'Racquet',
  'Ball',
  'Combat',
  'Athletics',
  'Aquatics',
  'Winter',
  'Other'
];

export default function SportsPage() {
  const [sports, setSports] = useState<Sport[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');
  const [categoryFilter, setCategoryFilter] = useState<string>('all');
  const [isCreateDialogOpen, setIsCreateDialogOpen] = useState(false);
  const [editingSport, setEditingSport] = useState<Sport | null>(null);

  // Form state
  const [formData, setFormData] = useState({
    name: '',
    description: '',
    category: ''
  });

  useEffect(() => {
    loadSports();
  }, []);

  const loadSports = async () => {
    try {
      setLoading(true);
      const response = await apiClient.admin.sports.list();
      if (response.success) {
        setSports(response.data || []);
      } else {
        toast.error('Failed to load sports');
      }
    } catch (error) {
      console.error('Error loading sports:', error);
      toast.error('Failed to load sports');
    } finally {
      setLoading(false);
    }
  };

  const handleCreate = async () => {
    try {
      const data = {
        ...formData
      };

      const response = await apiClient.admin.sports.create(data);
      if (response.success) {
        toast.success('Sport created successfully');
        setIsCreateDialogOpen(false);
        resetForm();
        loadSports();
      } else {
        toast.error(response.message || 'Failed to create sport');
      }
    } catch (error) {
      console.error('Error creating sport:', error);
      toast.error('Failed to create sport');
    }
  };

  const handleUpdate = async () => {
    if (!editingSport) return;

    try {
      const data = {
        ...formData
      };

      const response = await apiClient.admin.sports.update(editingSport.id, data);
      if (response.success) {
        toast.success('Sport updated successfully');
        setEditingSport(null);
        resetForm();
        loadSports();
      } else {
        toast.error(response.message || 'Failed to update sport');
      }
    } catch (error) {
      console.error('Error updating sport:', error);
      toast.error('Failed to update sport');
    }
  };

  const handleDelete = async (sportId: number) => {
    try {
      const response = await apiClient.admin.sports.delete(sportId);
      if (response.success) {
        toast.success('Sport deleted successfully');
        loadSports();
      } else {
        toast.error(response.message || 'Failed to delete sport');
      }
    } catch (error) {
      console.error('Error deleting sport:', error);
      toast.error('Failed to delete sport');
    }
  };

  const resetForm = () => {
    setFormData({
      name: '',
      description: '',
      category: ''
    });
  };

  const openEditDialog = (sport: Sport) => {
    setEditingSport(sport);
    setFormData({
      name: sport.name,
      description: sport.description,
      category: sport.category
    });
  };

  const filteredSports = sports.filter(sport => {
    const matchesSearch = sport.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         sport.description?.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesCategory = categoryFilter === 'all' || sport.category === categoryFilter;
    return matchesSearch && matchesCategory;
  });

  const stats = {
    total: sports.length,
    categories: [...new Set(sports.map(s => s.category))].length
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="text-center">
          <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"></div>
          <p className="mt-2 text-muted-foreground">Loading sports...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total Sports</CardTitle>
            <Trophy className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <ScrollCounter
              end={stats.total}
              className="text-2xl font-bold"
            />
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Categories</CardTitle>
            <Users className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <ScrollCounter
              end={stats.categories}
              className="text-2xl font-bold"
            />
          </CardContent>
        </Card>
      </div>

      {/* Filters and Actions */}
      <Card>
        <CardHeader>
          <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
              <CardTitle>Sports Management</CardTitle>
              <CardDescription>Manage sports categories and configurations</CardDescription>
            </div>

            <Dialog open={isCreateDialogOpen} onOpenChange={setIsCreateDialogOpen}>
              <DialogTrigger asChild>
                <Button>
                  <Plus className="h-4 w-4 mr-2" />
                  Add Sport
                </Button>
              </DialogTrigger>
              <DialogContent className="max-w-2xl">
                <DialogHeader>
                  <DialogTitle>Add New Sport</DialogTitle>
                  <DialogDescription>
                    Create a new sport category with its configuration details.
                  </DialogDescription>
                </DialogHeader>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div className="space-y-2">
                    <Label htmlFor="name">Sport Name *</Label>
                    <Input
                      id="name"
                      value={formData.name}
                      onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                      placeholder="e.g., Basketball"
                    />
                  </div>

                  <div className="space-y-2">
                    <Label htmlFor="category">Category *</Label>
                    <Select value={formData.category} onValueChange={(value) => setFormData({ ...formData, category: value })}>
                      <SelectTrigger>
                        <SelectValue placeholder="Select category" />
                      </SelectTrigger>
                      <SelectContent>
                        {sportCategories.map((category) => (
                          <SelectItem key={category} value={category}>
                            {category}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                  </div>

                  <div className="space-y-2 md:col-span-2">
                    <Label htmlFor="description">Description</Label>
                    <Input
                      id="description"
                      value={formData.description}
                      onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                      placeholder="Brief description of the sport"
                    />
                  </div>
                </div>

                <DialogFooter>
                  <Button variant="outline" onClick={() => setIsCreateDialogOpen(false)}>
                    Cancel
                  </Button>
                  <Button onClick={handleCreate} disabled={!formData.name || !formData.category}>
                    Create Sport
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
                placeholder="Search sports..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
              />
            </div>

            <Select value={categoryFilter} onValueChange={setCategoryFilter}>
              <SelectTrigger className="w-full sm:w-[200px]">
                <SelectValue placeholder="Filter by category" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Categories</SelectItem>
                {sportCategories.map((category) => (
                  <SelectItem key={category} value={category}>
                    {category}
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
                  <TableHead>Category</TableHead>
                  <TableHead className="hidden sm:table-cell">Description</TableHead>
                  <TableHead className="w-[100px]">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {filteredSports.length === 0 ? (
                  <TableRow>
                    <TableCell colSpan={4} className="text-center py-8 text-muted-foreground">
                      {sports.length === 0 ? 'No sports found. Create your first sport!' : 'No sports match your search criteria.'}
                    </TableCell>
                  </TableRow>
                ) : (
                  filteredSports.map((sport) => (
                    <TableRow key={sport.id}>
                      <TableCell className="font-medium">{sport.name}</TableCell>
                      <TableCell>
                        <Badge variant="secondary">{sport.category}</Badge>
                      </TableCell>
                      <TableCell className="hidden sm:table-cell">
                        {sport.description}
                      </TableCell>
                      <TableCell>
                        <div className="flex items-center gap-2">
                          <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => openEditDialog(sport)}
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
                                <AlertDialogTitle>Delete Sport</AlertDialogTitle>
                                <AlertDialogDescription>
                                  Are you sure you want to delete &quot;{sport.name}&quot;? This action cannot be undone.
                                </AlertDialogDescription>
                              </AlertDialogHeader>
                              <AlertDialogFooter>
                                <AlertDialogCancel>Cancel</AlertDialogCancel>
                                <AlertDialogAction onClick={() => handleDelete(sport.id)}>
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
      <Dialog open={!!editingSport} onOpenChange={(open) => !open && setEditingSport(null)}>
        <DialogContent className="max-w-2xl">
          <DialogHeader>
            <DialogTitle>Edit Sport</DialogTitle>
            <DialogDescription>
              Update the sport&apos;s configuration details.
            </DialogDescription>
          </DialogHeader>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="space-y-2">
              <Label htmlFor="edit-name">Sport Name *</Label>
              <Input
                id="edit-name"
                value={formData.name}
                onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                placeholder="e.g., Basketball"
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="edit-category">Category *</Label>
              <Select value={formData.category} onValueChange={(value) => setFormData({ ...formData, category: value })}>
                <SelectTrigger>
                  <SelectValue placeholder="Select category" />
                </SelectTrigger>
                <SelectContent>
                  {sportCategories.map((category) => (
                    <SelectItem key={category} value={category}>
                      {category}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>

            <div className="space-y-2 md:col-span-2">
              <Label htmlFor="edit-description">Description</Label>
              <Input
                id="edit-description"
                value={formData.description}
                onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                placeholder="Brief description of the sport"
              />
            </div>


          </div>

          <DialogFooter>
            <Button variant="outline" onClick={() => setEditingSport(null)}>
              Cancel
            </Button>
            <Button onClick={handleUpdate} disabled={!formData.name || !formData.category}>
              Update Sport
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
}
