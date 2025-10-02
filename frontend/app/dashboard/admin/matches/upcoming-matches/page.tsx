"use client";

import React, { useState } from "react";
import { SidebarTrigger } from "@/components/ui/sidebar";
import { Separator } from "@/components/ui/separator";
import { Card, CardHeader, CardTitle, CardContent } from "@/components/ui/card";
import {
  Table,
  TableHeader,
  TableRow,
  TableHead,
  TableBody,
  TableCell,
} from "@/components/ui/table";
import { Button } from "@/components/ui/button";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
  DropdownMenuSeparator,
} from "@/components/ui/dropdown-menu";
import {
  Sheet,
  SheetContent,
  SheetHeader,
  SheetTitle,
} from "@/components/ui/sheet";
import {
  MoreHorizontal,
  Eye,
  Edit,
  Trash2,
  Bell,
  FileDown,
  PlusCircle,
  CalendarDays,
  Clock,
  CheckCircle,
  Activity,
} from "lucide-react";

type Match = {
  id: number;
  teams: string;
  date: string; // YYYY-MM-DD
  time?: string; // HH:mm (24hr)
  division?: string;
  sport?: string;
  venue?: string;
  score?: string | null;
  status: "Scheduled" | "Ongoing" | "Completed" | "Rescheduled" | "Upcoming";
  winner?: string | null;
};

type DrawerMode = "add" | "view" | "edit" | null;

export default function UpcomingMatchesPage() {
  const [matches] = useState<Match[]>([
    {
      id: 1,
      teams: "School A vs School B",
      date: "2025-09-30",
      time: "13:25",
      division: "Senior High",
      sport: "Basketball",
      venue: "Court 1",
      score: "-",
      status: "Upcoming",
      winner: null,
    },
    {
      id: 2,
      teams: "School C vs School D",
      date: "2025-10-02",
      time: "15:00",
      division: "College",
      sport: "Volleyball",
      venue: "Gym A",
      score: "-",
      status: "Scheduled",
      winner: null,
    },
    {
      id: 3,
      teams: "School E vs School F",
      date: "2025-10-03",
      time: "16:30",
      division: "College",
      sport: "Soccer",
      venue: "Field B",
      score: "-",
      status: "Scheduled",
      winner: null,
    },
    {
      id: 4,
      teams: "School G vs School H",
      date: "2025-09-28",
      time: "13:00",
      division: "College",
      sport: "Baseball",
      venue: "Diamond A",
      score: "89 - 77",
      status: "Completed",
      winner: "School G",
    },
    {
      id: 5,
      teams: "School I vs School J",
      date: "2025-09-29",
      time: "15:30",
      division: "College",
      sport: "Basketball",
      venue: "Court 2",
      score: "102 - 95",
      status: "Completed",
      winner: "School I",
    },
  ]);

  const [drawerMode, setDrawerMode] = useState<DrawerMode>(null);
  const [selectedMatch, setSelectedMatch] = useState<Match | null>(null);

  // Stats
  const totalMatches = matches.length;
  const completed = matches.filter((m) => m.status === "Completed").length;
  const upcoming = matches.filter((m) =>
    ["Upcoming", "Scheduled", "Ongoing"].includes(m.status)
  ).length;
  const ongoing = matches.filter((m) => m.status === "Ongoing").length;

  // Drawer helpers
  const openAddDrawer = () => {
    setSelectedMatch(null);
    setDrawerMode("add");
  };
  const openViewDrawer = (match: Match) => {
    setSelectedMatch(match);
    setDrawerMode("view");
  };
  const openEditDrawer = (match: Match) => {
    setSelectedMatch(match);
    setDrawerMode("edit");
  };

  // Status color map
  const statusColors: Record<Match["status"], string> = {
    Scheduled: "bg-blue-100 text-blue-700",
    Ongoing: "bg-yellow-100 text-yellow-700",
    Completed: "bg-green-100 text-green-700",
    Rescheduled: "bg-orange-100 text-orange-700",
    Upcoming: "bg-purple-100 text-purple-700",
  };

  // Format time to 12hr AM/PM
  const format12Hour = (time?: string) => {
    if (!time) return "--:--";
    const [hours, minutes] = time.split(":").map(Number);
    const period = hours >= 12 ? "PM" : "AM";
    const h12 = hours % 12 || 12;
    return `${h12}:${minutes.toString().padStart(2, "0")} ${period}`;
  };

  // Check if score entry is allowed (from match time until +24h)
  const canEnterScore = (match: Match) => {
    if (!match.date || !match.time) return false;
    const matchDateTime = new Date(`${match.date}T${match.time}:00`);
    const now = new Date();
    const oneDayAfter = new Date(matchDateTime.getTime() + 24 * 60 * 60 * 1000);
    return now >= matchDateTime && now <= oneDayAfter;
  };

  return (
    <div className="flex-1 flex flex-col">
      {/* Header */}
      <header className="sticky top-0 z-50 bg-background/95 backdrop-blur flex h-12 items-center gap-2 border-b px-4">
        <div className="flex items-center gap-2">
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
        {/* Title + actions */}
        <div className="flex items-start justify-between">
          <div>
            <h1 className="text-2xl font-bold">Matches Overview</h1>
            <p className="text-muted-foreground">
              Track and manage all matches with live updates
            </p>
          </div>
          <div className="flex items-center gap-2">
            <Button size="sm" onClick={openAddDrawer}>
              <PlusCircle className="h-4 w-4 mr-2" />
              Add Match
            </Button>
            <Button size="sm" variant="outline">
              <FileDown className="h-4 w-4 mr-2" />
              Export
            </Button>
            <Button size="sm" variant="secondary">
              <Bell className="h-4 w-4 mr-2" />
              Notify All
            </Button>
          </div>
        </div>

        {/* Status Boxes */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <Card className="bg-blue-50 border-blue-200">
            <CardHeader className="flex flex-row items-center justify-between pb-2">
              <CardTitle>Total Matches</CardTitle>
              <CalendarDays className="h-5 w-5 text-blue-600" />
            </CardHeader>
            <CardContent className="text-2xl font-bold">{totalMatches}</CardContent>
          </Card>
          <Card className="bg-green-50 border-green-200">
            <CardHeader className="flex flex-row items-center justify-between pb-2">
              <CardTitle>Completed</CardTitle>
              <CheckCircle className="h-5 w-5 text-green-600" />
            </CardHeader>
            <CardContent className="text-2xl font-bold">{completed}</CardContent>
          </Card>
          <Card className="bg-purple-50 border-purple-200">
            <CardHeader className="flex flex-row items-center justify-between pb-2">
              <CardTitle>Upcoming</CardTitle>
              <Clock className="h-5 w-5 text-purple-600" />
            </CardHeader>
            <CardContent className="text-2xl font-bold">{upcoming}</CardContent>
          </Card>
          <Card className="bg-yellow-50 border-yellow-200">
            <CardHeader className="flex flex-row items-center justify-between pb-2">
              <CardTitle>Ongoing</CardTitle>
              <Activity className="h-5 w-5 text-yellow-600" />
            </CardHeader>
            <CardContent className="text-2xl font-bold">{ongoing}</CardContent>
          </Card>
        </div>

        {/* Layout: Table + Upcoming Side Panel */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {/* Match List */}
          <Card className="lg:col-span-2 shadow-md">
            <CardHeader>
              <CardTitle>All Matches</CardTitle>
            </CardHeader>
            <CardContent>
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>ID</TableHead>
                    <TableHead>Teams</TableHead>
                    <TableHead>Date</TableHead>
                    <TableHead>Time</TableHead>
                    <TableHead>Venue</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Winner</TableHead>
                    <TableHead>Actions</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {matches.map((m) => (
                    <TableRow key={m.id} className="hover:bg-accent/30">
                      <TableCell>{m.id}</TableCell>
                      <TableCell>{m.teams}</TableCell>
                      <TableCell>{m.date}</TableCell>
                      <TableCell>{format12Hour(m.time)}</TableCell>
                      <TableCell>{m.venue ?? "-"}</TableCell>
                      <TableCell>
                        <span
                          className={`px-2 py-1 rounded-full text-xs font-medium ${statusColors[m.status]}`}
                        >
                          {m.status}
                        </span>
                      </TableCell>
                      <TableCell>{m.winner ?? "—"}</TableCell>
                      <TableCell>
                        <DropdownMenu>
                          <DropdownMenuTrigger asChild>
                            <Button variant="ghost" size="icon">
                              <MoreHorizontal className="h-4 w-4" />
                            </Button>
                          </DropdownMenuTrigger>
                          <DropdownMenuContent>
                            <DropdownMenuItem onClick={() => openViewDrawer(m)}>
                              <Eye className="w-4 h-4 mr-2" /> View
                            </DropdownMenuItem>
                            <DropdownMenuItem onClick={() => openEditDrawer(m)}>
                              <Edit className="w-4 h-4 mr-2" /> Edit
                            </DropdownMenuItem>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem className="text-red-600">
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

          {/* Upcoming Matches Side Card */}
          <Card className="shadow-md">
            <CardHeader>
              <CardTitle>Upcoming Matches</CardTitle>
            </CardHeader>
            <CardContent className="space-y-3">
              {matches
                .filter((m) =>
                  ["Upcoming", "Scheduled", "Ongoing"].includes(m.status)
                )
                .map((m) => (
                  <div
                    key={m.id}
                    className="p-4 border rounded-lg hover:bg-purple-50 transition cursor-pointer"
                    onClick={() => openViewDrawer(m)}
                  >
                    <h3 className="font-semibold">{m.teams}</h3>
                    <p className="text-sm text-muted-foreground">
                      📅 {m.date} • ⏰ {format12Hour(m.time)} • 🏟 {m.venue}
                    </p>
                    <p className="text-xs text-purple-600 font-medium mt-1">
                      {m.sport} • {m.division}
                    </p>
                  </div>
                ))}
              {upcoming === 0 && (
                <p className="text-sm text-muted-foreground">No upcoming matches</p>
              )}
            </CardContent>
          </Card>
        </div>
      </main>

      {/* Drawer */}
      <Sheet
        open={drawerMode !== null}
        onOpenChange={() => {
          setDrawerMode(null);
          setSelectedMatch(null);
        }}
      >
        <SheetContent className="w-1/2 min-w-[420px] max-w-3xl p-0 flex flex-col overflow-hidden">
          <SheetHeader className="px-6 pt-6 pb-2 flex-shrink-0">
            <SheetTitle>
              {drawerMode === "add" && "Add Match"}
              {drawerMode === "view" && "Match Details"}
              {drawerMode === "edit" && "Edit Match"}
            </SheetTitle>
          </SheetHeader>
          <div className="px-6 pb-6 overflow-y-auto">
            {drawerMode === "view" && selectedMatch && (
              <div className="space-y-3">
                <h2 className="text-2xl font-bold">{selectedMatch.teams}</h2>
                <p className="text-sm text-muted-foreground">
                  {selectedMatch.sport} • {selectedMatch.division}
                </p>
                <p className="text-sm text-muted-foreground">
                  📍 {selectedMatch.venue} • 📅 {selectedMatch.date} ⏰{" "}
                  {format12Hour(selectedMatch.time)}
                </p>

                {canEnterScore(selectedMatch) ? (
                  <div className="space-y-2">
                    <label className="block text-sm font-medium">Enter Score</label>
                    <input
                      type="text"
                      className="border rounded px-2 py-1 w-full"
                      placeholder="e.g. 102 - 95"
                      defaultValue={selectedMatch.score ?? ""}
                    />
                    <Button size="sm">Save Score</Button>
                  </div>
                ) : (
                  <p className="text-sm text-muted-foreground italic">
                    ⏳ Score entry is only open during and 24h after the match
                  </p>
                )}

                <p className="text-sm font-semibold text-green-600">
                  🏆 {selectedMatch.winner ?? "TBD"}
                </p>
              </div>
            )}
            {drawerMode === "edit" && selectedMatch && (
              <p>Edit form placeholder</p>
            )}
            {drawerMode === "add" && <p>Add form placeholder</p>}
          </div>
        </SheetContent>
      </Sheet>
    </div>
  );
}
