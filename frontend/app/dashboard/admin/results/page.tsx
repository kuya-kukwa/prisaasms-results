"use client";

import React, { useState } from "react";
import { SidebarTrigger } from "@/components/ui/sidebar";
import { Separator } from "@/components/ui/separator";
import {
  Card,
  CardHeader,
  CardTitle,
  CardContent,
} from "@/components/ui/card";
import {
  Table,
  TableHeader,
  TableRow,
  TableHead,
  TableBody,
  TableCell,
} from "@/components/ui/table";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import {
  Tabs,
  TabsList,
  TabsTrigger,
} from "@/components/ui/tabs";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
  DropdownMenuSeparator,
} from "@/components/ui/dropdown-menu";
import ScrollCounter from "@/components/ui/scroll-counter";
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from "@/components/ui/alert-dialog";
import {
  Trophy,
  Search,
  MoreHorizontal,
  Eye,
  Edit,
  Trash2,
  Medal,
  CalendarDays,
  MapPin,
} from "lucide-react";

export default function AdminResultsPage() {
  const [error, setError] = useState<string | null>(null);
  const [activeTab, setActiveTab] = useState("overview");

  // 🔹 Dialog state for delete
  const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
  const [selectedItem, setSelectedItem] = useState<string | null>(null);

  const handleDelete = (item: string) => {
    setSelectedItem(item);
    setDeleteDialogOpen(true);
  };

  // 🔹 Placeholder data — replace with API later
  const medalTally = [
    { school: "School A", gold: 10, silver: 5, bronze: 3 },
    { school: "School B", gold: 8, silver: 7, bronze: 4 },
    { school: "School C", gold: 6, silver: 4, bronze: 2 },
  ];

  const pastResults = [
    {
      sport: "Basketball",
      champion: "School A",
      runnerUp: "School B",
      date: "2025-10-05",
      venue: "Gym A",
      officials: "Referee X, Referee Y",
      status: "Completed",
    },
    {
      sport: "Volleyball",
      champion: "School C",
      runnerUp: "School D",
      date: "2025-10-06",
      venue: "Court 1",
      officials: "Referee A, Referee B",
      status: "Completed",
    },
  ];

  return (
    <div className="flex-1 flex flex-col">
      {/* Sticky header */}
      <header className="sticky top-0 z-50 bg-background/95 backdrop-blur flex h-12 shrink-0 items-center gap-2 border-b">
        <div className="flex items-center gap-2 px-4">
          <SidebarTrigger className="-ml-1" />
          <Separator orientation="vertical" className="mr-2 h-4" />
          <nav className="flex items-center space-x-2 text-sm">
            <span className="text-muted-foreground">Admin Dashboard</span>
            <span className="text-muted-foreground">/</span>
            <span>Results</span>
          </nav>
        </div>
      </header>

      <main className="flex-1 p-4 md:p-6 overflow-auto min-h-0 space-y-6">
        {/* Error message */}
        {error && (
          <Card className="bg-red-50 border-red-200">
            <CardHeader>
              <CardTitle className="text-red-600">Error</CardTitle>
            </CardHeader>
            <CardContent className="flex justify-between items-center">
              <p>{error}</p>
              <Button variant="outline" size="sm" onClick={() => setError(null)}>
                Dismiss
              </Button>
            </CardContent>
          </Card>
        )}

        {/* Page heading */}
        <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center">
          <div>
            <h1 className="text-2xl font-bold">Results Management</h1>
            <p className="text-muted-foreground">
              Manage medal tallies, past competitions, and match records.
            </p>
          </div>
          <div className="flex gap-2 mt-4 sm:mt-0">
            <Button variant="default">
              <Trophy className="w-4 h-4 mr-1" /> Add Result
            </Button>
            <Button variant="outline">Export Report</Button>
          </div>
        </div>

        {/* Stats cards */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          <Card className="text-center">
            <CardHeader>
              <CardTitle>Total Medals</CardTitle>
            </CardHeader>
            <CardContent>
              <ScrollCounter
                end={medalTally.reduce(
                  (a, b) => a + b.gold + b.silver + b.bronze,
                  0
                )}
              />
            </CardContent>
          </Card>
          <Card className="text-center">
            <CardHeader>
              <CardTitle>Gold</CardTitle>
            </CardHeader>
            <CardContent>
              <ScrollCounter end={medalTally.reduce((a, b) => a + b.gold, 0)} />
            </CardContent>
          </Card>
          <Card className="text-center">
            <CardHeader>
              <CardTitle>Silver</CardTitle>
            </CardHeader>
            <CardContent>
              <ScrollCounter
                end={medalTally.reduce((a, b) => a + b.silver, 0)}
              />
            </CardContent>
          </Card>
          <Card className="text-center">
            <CardHeader>
              <CardTitle>Bronze</CardTitle>
            </CardHeader>
            <CardContent>
              <ScrollCounter
                end={medalTally.reduce((a, b) => a + b.bronze, 0)}
              />
            </CardContent>
          </Card>
        </div>

        {/* Filters + Tabs */}
        <Card>
          <CardHeader>
            <CardTitle>Competition Results</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="flex flex-col md:flex-row justify-between items-center mb-4 gap-3">
              <Tabs value={activeTab} onValueChange={setActiveTab}>
                <TabsList>
                  <TabsTrigger value="overview">Overview</TabsTrigger>
                  <TabsTrigger value="medal-tallies">Medal Tallies</TabsTrigger>
                  <TabsTrigger value="past-results">Past Results</TabsTrigger>
                </TabsList>
              </Tabs>

              <div className="relative w-full md:w-64">
                <Search className="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                <Input placeholder="Search results..." className="pl-8" />
              </div>
            </div>

            {/* Overview */}
            {activeTab === "overview" && (
              <p className="text-muted-foreground">
                A quick snapshot of ongoing competitions and final standings.
              </p>
            )}

            {/* Medal Tallies */}
            {activeTab === "medal-tallies" && (
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>School</TableHead>
                    <TableHead>Gold</TableHead>
                    <TableHead>Silver</TableHead>
                    <TableHead>Bronze</TableHead>
                    <TableHead>Total</TableHead>
                    <TableHead>Actions</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {medalTally.map((row, i) => (
                    <TableRow key={i}>
                      <TableCell>{row.school}</TableCell>
                      <TableCell>
                        <Badge variant="destructive">{row.gold}</Badge>
                      </TableCell>
                      <TableCell>
                        <Badge variant="secondary">{row.silver}</Badge>
                      </TableCell>
                      <TableCell>
                        <Badge variant="outline">{row.bronze}</Badge>
                      </TableCell>
                      <TableCell>{row.gold + row.silver + row.bronze}</TableCell>
                      <TableCell>
                        <DropdownMenu>
                          <DropdownMenuTrigger asChild>
                            <Button variant="ghost" size="icon">
                              <MoreHorizontal className="h-4 w-4" />
                            </Button>
                          </DropdownMenuTrigger>
                          <DropdownMenuContent>
                            <DropdownMenuItem>
                              <Eye className="w-4 h-4 mr-2" /> View
                            </DropdownMenuItem>
                            <DropdownMenuItem>
                              <Edit className="w-4 h-4 mr-2" /> Edit
                            </DropdownMenuItem>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem
                              className="text-red-600"
                              onClick={() => handleDelete(row.school)}
                            >
                              <Trash2 className="w-4 h-4 mr-2" /> Delete
                            </DropdownMenuItem>
                          </DropdownMenuContent>
                        </DropdownMenu>
                      </TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            )}

            {/* Past Results */}
            {activeTab === "past-results" && (
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Sport</TableHead>
                    <TableHead>Champion</TableHead>
                    <TableHead>Runner Up</TableHead>
                    <TableHead>
                      <CalendarDays className="inline w-4 h-4 mr-1" /> Date
                    </TableHead>
                    <TableHead>
                      <MapPin className="inline w-4 h-4 mr-1" /> Venue
                    </TableHead>
                    <TableHead>Officials</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Actions</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {pastResults.map((row, i) => (
                    <TableRow key={i}>
                      <TableCell>{row.sport}</TableCell>
                      <TableCell>
                        <Badge>{row.champion}</Badge>
                      </TableCell>
                      <TableCell>{row.runnerUp}</TableCell>
                      <TableCell>{row.date}</TableCell>
                      <TableCell>{row.venue}</TableCell>
                      <TableCell>{row.officials}</TableCell>
                      <TableCell>
                        <Badge
                          variant={row.status === "Completed" ? "secondary" : "outline"}
                        >
                          {row.status}
                        </Badge>
                      </TableCell>
                      <TableCell>
                        <DropdownMenu>
                          <DropdownMenuTrigger asChild>
                            <Button variant="ghost" size="icon">
                              <MoreHorizontal className="h-4 w-4" />
                            </Button>
                          </DropdownMenuTrigger>
                          <DropdownMenuContent>
                            <DropdownMenuItem>
                              <Eye className="w-4 h-4 mr-2" /> View
                            </DropdownMenuItem>
                            <DropdownMenuItem>
                              <Edit className="w-4 h-4 mr-2" /> Edit
                            </DropdownMenuItem>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem
                              className="text-red-600"
                              onClick={() => handleDelete(row.sport)}
                            >
                              <Trash2 className="w-4 h-4 mr-2" /> Delete
                            </DropdownMenuItem>
                          </DropdownMenuContent>
                        </DropdownMenu>
                      </TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            )}
          </CardContent>
        </Card>
      </main>

      {/* Delete Confirmation Dialog */}
      <AlertDialog open={deleteDialogOpen} onOpenChange={setDeleteDialogOpen}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Confirm Deletion</AlertDialogTitle>
            <AlertDialogDescription>
              Are you sure you want to delete{" "}
              <span className="font-semibold">{selectedItem}</span>? This action
              cannot be undone.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancel</AlertDialogCancel>
            <AlertDialogAction
              onClick={() => {
                console.log("Deleted:", selectedItem);
                setDeleteDialogOpen(false);
              }}
            >
              Delete
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </div>
  );
}
