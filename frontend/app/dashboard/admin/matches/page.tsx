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
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
  DropdownMenuSeparator,
} from "@/components/ui/dropdown-menu";
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
  Search,
  MoreHorizontal,
  Eye,
  Edit,
  Trash2,
} from "lucide-react";
import {
  Select,
  SelectTrigger,
  SelectValue,
  SelectContent,
  SelectItem,
} from "@/components/ui/select";

export default function AdminMatchesPage() {
  const [division, setDivision] = useState("All");
  const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
  const [selectedMatch, setSelectedMatch] = useState<string | null>(null);

  // Hardcoded matches
  const matches = [
    {
      id: "001",
      teams: "School A vs School B",
      date: "2025-10-05",
      division: "Senior High",
      sport: "Basketball",
      venue: "Court 1",
      score: "-",
      status: "Ongoing",
      winner: null,
    },
    {
      id: "002",
      teams: "School C vs School D",
      date: "2025-10-06",
      division: "College",
      sport: "Volleyball",
      venue: "Gym A",
      score: "-",
      status: "Scheduled",
      winner: null,
    },
    {
      id: "003",
      teams: "School E vs School F",
      date: "2025-10-07",
      division: "Junior High",
      sport: "Soccer",
      venue: "Field B",
      score: "-",
      status: "Ongoing",
      winner: null,
    },
    {
      id: "004",
      teams: "School G vs School H",
      date: "2025-10-08",
      division: "College",
      sport: "Baseball",
      venue: "Diamond A",
      score: "89 - 77",
      status: "Completed",
      winner: "School G",
    },
    {
      id: "005",
      teams: "School I vs School J",
      date: "2025-10-04",
      division: "College",
      sport: "Basketball",
      venue: "Court 2",
      score: "102 - 95",
      status: "Completed",
      winner: "School I",
    },
  ];

  const filteredMatches =
    division === "All"
      ? matches
      : matches.filter((match) => match.division === division);

  const handleDelete = (id: string) => {
    setSelectedMatch(id);
    setDeleteDialogOpen(true);
  };

  // Status counts for overview
  const statusCounts = {
    total: filteredMatches.length,
    ongoing: filteredMatches.filter((m) => m.status === "Ongoing").length,
    scheduled: filteredMatches.filter((m) => m.status === "Scheduled").length,
    completed: filteredMatches.filter((m) => m.status === "Completed").length,
  };

  // Recent Matches (only completed ones, sorted latest first)
  const recentMatches = matches
    .filter((m) => m.status === "Completed")
    .slice(0, 3); // only last 3 for preview

  // Most recent completed match for highlight
  const mostRecentMatch = matches.find((m) => m.status === "Completed");

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
            <span>Matches</span>
          </nav>
        </div>
      </header>

      <main className="flex-1 p-4 md:p-6 overflow-auto min-h-0 space-y-6">
        {/* Page heading */}
        <div>
          <h1 className="text-2xl font-bold">Matches Overview</h1>
          <p className="text-muted-foreground">
            View and manage all matches across divisions and sports.
          </p>
        </div>

        {/* Status Overview Boxes */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
          <Card className="text-center p-4 shadow">
            <CardHeader>
              <CardTitle>Total Matches</CardTitle>
            </CardHeader>
            <CardContent className="text-2xl font-bold">
              {statusCounts.total}
            </CardContent>
          </Card>
          <Card className="text-center p-4 shadow">
            <CardHeader>
              <CardTitle>Ongoing</CardTitle>
            </CardHeader>
            <CardContent className="text-2xl font-bold">
              {statusCounts.ongoing}
            </CardContent>
          </Card>
          <Card className="text-center p-4 shadow">
            <CardHeader>
              <CardTitle>Scheduled</CardTitle>
            </CardHeader>
            <CardContent className="text-2xl font-bold">
              {statusCounts.scheduled}
            </CardContent>
          </Card>
          <Card className="text-center p-4 shadow">
            <CardHeader>
              <CardTitle>Completed</CardTitle>
            </CardHeader>
            <CardContent className="text-2xl font-bold">
              {statusCounts.completed}
            </CardContent>
          </Card>
        </div>

        {/* Most Recent Match Highlight */}
{mostRecentMatch && (
  <Card className="shadow-md border-l-4 border-green-500">
    <CardHeader>
      <CardTitle className="text-2xl md:text-3xl font-extrabold text-green-700">
        Most Recent Match
      </CardTitle>
    </CardHeader>
    <CardContent className="space-y-2">
<p className="text-lg md:text-2xl font-bold">{mostRecentMatch.teams}</p>
      <p className="text-sm text-muted-foreground">
        {mostRecentMatch.sport} | 📍 {mostRecentMatch.venue} | 📅{" "}
        {mostRecentMatch.date}
      </p>
      <p className="text-xl font-bold">{mostRecentMatch.score}</p>
    
      <p className="text-sm font-semibold text-green-600">
        🏆 Winner: {mostRecentMatch.winner}
      </p>
      <div className="flex gap-2 mt-2">
        <Button size="sm" variant="secondary">
          Download Report
        </Button>
        <Button size="sm" variant="outline">
          View Highlights
        </Button>
      </div>
    </CardContent>
  </Card>
)}


        {/* Main Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {/* Matches Table */}
          <Card className="lg:col-span-2">
            <CardHeader>
              <CardTitle>All Matches</CardTitle>
            </CardHeader>
            <CardContent>
              {/* Filters */}
              <div className="flex flex-col md:flex-row justify-between items-center mb-4 gap-3">
                <Select
                  onValueChange={(value) => setDivision(value)}
                  defaultValue="All"
                >
                  <SelectTrigger className="w-[200px]">
                    <SelectValue placeholder="Filter by Division" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="All">All Divisions</SelectItem>
                    <SelectItem value="College">College</SelectItem>
                    <SelectItem value="Senior High">Senior High</SelectItem>
                    <SelectItem value="Junior High">Junior High</SelectItem>
                  </SelectContent>
                </Select>

                <div className="relative w-full md:w-64">
                  <Search className="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                  <Input placeholder="Search matches..." className="pl-8" />
                </div>
              </div>

              {/* Table */}
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Match ID</TableHead>
                    <TableHead>Teams</TableHead>
                    <TableHead>Date</TableHead>
                    <TableHead>Division</TableHead>
                    <TableHead>Sport</TableHead>
                    <TableHead>Venue</TableHead>
                    <TableHead>Score</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Winner</TableHead>
                    <TableHead>Actions</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {filteredMatches.map((match) => (
                    <TableRow key={match.id}>
                      <TableCell>{match.id}</TableCell>
                      <TableCell>{match.teams}</TableCell>
                      <TableCell>{match.date}</TableCell>
                      <TableCell>{match.division}</TableCell>
                      <TableCell>{match.sport}</TableCell>
                      <TableCell>{match.venue}</TableCell>
                      <TableCell>{match.score}</TableCell>
                      <TableCell>
                        <Badge
                          variant={
                            match.status === "Ongoing"
                              ? "default"
                              : match.status === "Scheduled"
                              ? "secondary"
                              : "outline"
                          }
                        >
                          {match.status}
                        </Badge>
                      </TableCell>
                      <TableCell>
                        {match.winner ? (
                          <span className="font-semibold text-green-600">
                            {match.winner}
                          </span>
                        ) : (
                          "-"
                        )}
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
                              onClick={() => handleDelete(match.id)}
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
            </CardContent>
          </Card>

          {/* Recent Matches List */}
          <Card className="shadow-md border-l-4 border-blue-500">
            <CardHeader>
              <CardTitle>Recent Matches</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              {recentMatches.map((match) => (
                <div
                  key={match.id}
                  className="p-3 rounded-md border hover:bg-muted/40 transition"
                >
                  <p className="font-semibold">{match.teams}</p>
                  <p className="text-sm text-muted-foreground">
                    {match.sport} | 📅 {match.date}
                  </p>
                  <p className="text-sm font-bold">{match.score}</p>
                 
                  <p className="text-xs font-semibold text-green-600">
                    🏆 Winner: {match.winner}
                  </p>
                  <Button size="sm" variant="outline" className="mt-2">
                    View
                  </Button>
                </div>
              ))}
            </CardContent>
          </Card>
        </div>
      </main>

      {/* Delete Confirmation Dialog */}
      <AlertDialog open={deleteDialogOpen} onOpenChange={setDeleteDialogOpen}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Confirm Deletion</AlertDialogTitle>
            <AlertDialogDescription>
              Are you sure you want to delete match{" "}
              <span className="font-semibold">{selectedMatch}</span>? This action
              cannot be undone.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancel</AlertDialogCancel>
            <AlertDialogAction
              onClick={() => {
                console.log("Deleted:", selectedMatch);
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
