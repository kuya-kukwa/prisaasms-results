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
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";

type TeamStanding = {
  id: number;
  team: string;
  division: string;
  sport: string;
  wins: number;
  losses: number;
  draws: number;
  points: number;
};

export default function StandingsPage() {
  const [division, setDivision] = useState<string>("All");
  const [sport, setSport] = useState<string>("All");

  const standings: TeamStanding[] = [
    { id: 1, team: "School A", division: "College", sport: "Basketball", wins: 5, losses: 1, draws: 0, points: 15 },
    { id: 2, team: "School B", division: "College", sport: "Basketball", wins: 4, losses: 2, draws: 0, points: 12 },
    { id: 3, team: "School C", division: "Senior High", sport: "Volleyball", wins: 3, losses: 1, draws: 1, points: 10 },
    { id: 4, team: "School D", division: "Senior High", sport: "Soccer", wins: 2, losses: 3, draws: 0, points: 6 },
  ];

  const filtered = standings.filter((s) => {
    return (division === "All" || s.division === division) &&
           (sport === "All" || s.sport === sport);
  });

  // Stats
  const totalTeams = standings.length;
  const totalSports = new Set(standings.map((s) => s.sport)).size;
  const totalDivisions = new Set(standings.map((s) => s.division)).size;

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
            <span>Standings</span>
          </nav>
        </div>
      </header>

      <main className="flex-1 p-4 md:p-6 overflow-auto min-h-0 space-y-6">
        {/* Title */}
        <div className="flex items-start justify-between">
          <div>
            <h1 className="text-2xl font-bold">Team Standings</h1>
            <p className="text-muted-foreground">View rankings by division and sport</p>
          </div>
        </div>

        {/* Status Boxes */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <Card>
            <CardHeader>
              <CardTitle>Total Teams</CardTitle>
            </CardHeader>
            <CardContent className="text-2xl font-bold">{totalTeams}</CardContent>
          </Card>
          <Card>
            <CardHeader>
              <CardTitle>Sports</CardTitle>
            </CardHeader>
            <CardContent className="text-2xl font-bold">{totalSports}</CardContent>
          </Card>
          <Card>
            <CardHeader>
              <CardTitle>Divisions</CardTitle>
            </CardHeader>
            <CardContent className="text-2xl font-bold">{totalDivisions}</CardContent>
          </Card>
        </div>

        {/* Filters */}
        <div className="flex flex-wrap gap-4 items-center">
          <Select onValueChange={setDivision}>
            <SelectTrigger className="w-[180px]">
              <SelectValue placeholder="Filter by Division" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="All">All Divisions</SelectItem>
              <SelectItem value="College">College</SelectItem>
              <SelectItem value="Senior High">Senior High</SelectItem>
            </SelectContent>
          </Select>

          <Select onValueChange={setSport}>
            <SelectTrigger className="w-[180px]">
              <SelectValue placeholder="Filter by Sport" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="All">All Sports</SelectItem>
              <SelectItem value="Basketball">Basketball</SelectItem>
              <SelectItem value="Volleyball">Volleyball</SelectItem>
              <SelectItem value="Soccer">Soccer</SelectItem>
            </SelectContent>
          </Select>

          <Button variant="outline">Reset Filters</Button>
        </div>

        {/* Standings Table */}
        <Card>
          <CardHeader>
            <CardTitle>Standings</CardTitle>
          </CardHeader>
          <CardContent>
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Rank</TableHead>
                  <TableHead>Team</TableHead>
                  <TableHead>Division</TableHead>
                  <TableHead>Sport</TableHead>
                  <TableHead>Wins</TableHead>
                  <TableHead>Losses</TableHead>
                  <TableHead>Draws</TableHead>
                  <TableHead>Points</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {filtered
                  .sort((a, b) => b.points - a.points)
                  .map((team, index) => (
                    <TableRow key={team.id}>
                      <TableCell>{index + 1}</TableCell>
                      <TableCell className="font-medium">{team.team}</TableCell>
                      <TableCell>{team.division}</TableCell>
                      <TableCell>{team.sport}</TableCell>
                      <TableCell>{team.wins}</TableCell>
                      <TableCell>{team.losses}</TableCell>
                      <TableCell>{team.draws}</TableCell>
                      <TableCell>
                        <Badge variant="secondary">{team.points}</Badge>
                      </TableCell>
                    </TableRow>
                  ))}
              </TableBody>
            </Table>
          </CardContent>
        </Card>
      </main>
    </div>
  );
}
