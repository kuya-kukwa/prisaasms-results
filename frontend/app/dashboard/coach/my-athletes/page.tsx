"use client";

import React, { useEffect, useState } from "react";
import { useAuth } from "@/src/contexts/AuthContext";
import { useRouter } from "next/navigation";
import { Card, CardHeader, CardTitle, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Users, RefreshCw } from "lucide-react";
import DashboardSkeleton from "@/components/ui/dashboard-skeleton";
import axios from "axios";
import AddAthleteModal from "./AddAthleteModal";

interface Athlete {
  id: number;
  first_name: string;
  last_name: string;
  status: string;
  sport?: { id: number; name: string };
  school?: { id: number; name: string };
}

interface School {
  id: number;
  name: string;
}

interface Sport {
  id: number;
  name: string;
}

export default function MyAthletesPage() {
  const { user, isAuthenticated } = useAuth();
  const router = useRouter();
  const [athletes, setAthletes] = useState<Athlete[]>([]);
  const [schools, setSchools] = useState<School[]>([]);
  const [sportsList, setSportsList] = useState<Sport[]>([]);
  const [loading, setLoading] = useState(true);
  const [isRefreshing, setIsRefreshing] = useState(false);

  // 🟢 Load Athletes
  const loadAthletes = async (refresh = false) => {
    try {
      if (refresh) setIsRefreshing(true);
      setLoading(true);

      const res = await axios.get("http://localhost:8000/api/athletes", {
        withCredentials: true,
      });

      setAthletes(res.data.data.data || []); // pagination wrapper
    } catch (error) {
      console.error("Failed to fetch athletes:", error);
    } finally {
      setLoading(false);
      setIsRefreshing(false);
    }
  };

  // 🟢 Load Schools
  const loadSchools = async () => {
    try {
      const res = await axios.get("http://localhost:8000/api/schools", {
        withCredentials: true,
      });
      setSchools(res.data.data || []);
    } catch (error) {
      console.error("Failed to fetch schools:", error);
    }
  };

  // 🟢 Load Sports
  const loadSports = async () => {
    try {
      const res = await axios.get("http://localhost:8000/api/sports", {
        withCredentials: true,
      });
      setSportsList(res.data.data || []);
    } catch (error) {
      console.error("Failed to fetch sports:", error);
    }
  };

  useEffect(() => {
    if (!isAuthenticated) {
      router.push("/login");
      return;
    }
    if (user?.role !== "coach") {
      router.push("/403"); // forbidden
      return;
    }

    loadAthletes();
    loadSchools();
    loadSports();
  }, [isAuthenticated, user, router]);

  if (loading) return <DashboardSkeleton />;

  return (
    <div className="flex-1 flex flex-col">
      <header className="sticky top-0 z-50 bg-background/95 backdrop-blur flex h-12 items-center gap-2 border-b px-4">
        <Users className="h-4 w-4 text-blue-600" />
        <nav className="flex items-center space-x-2 text-sm">
          <span className="text-muted-foreground">Coach Dashboard</span>
          <span className="text-muted-foreground">/</span>
          <span>My Athletes</span>
        </nav>
        <div className="flex-1" />
        <Button
          variant="outline"
          size="sm"
          onClick={() => loadAthletes(true)}
          disabled={isRefreshing}
          className="gap-2"
        >
          <RefreshCw
            className={`h-4 w-4 ${isRefreshing ? "animate-spin" : ""}`}
          />
          Refresh
        </Button>
      </header>

      <main className="flex-1 p-6 overflow-auto">
        <div className="flex justify-between items-center mb-6">
          <h1 className="text-xl font-bold">My Athletes</h1>
          <AddAthleteModal
            schools={schools}
            sportsList={sportsList}
            onAthleteAdded={() => loadAthletes(true)}
          />
        </div>

        {athletes.length === 0 ? (
          <p>No athletes found for your school.</p>
        ) : (
          <div className="overflow-x-auto border rounded-lg shadow-sm">
            <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead className="bg-gray-50 dark:bg-gray-800">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                    Name
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                    Sport
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                    School
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                    Status
                  </th>
                  <th className="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200 dark:divide-gray-700">
                {athletes.map((a) => (
                  <tr key={a.id} className="hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td className="px-6 py-4 whitespace-nowrap">
                      {a.first_name} {a.last_name}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      {a.sport?.name ?? "Unassigned"}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      {a.school?.name ?? "Unknown"}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      {a.status}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-right">
                      <Button size="sm" variant="outline">
                        Edit
                      </Button>
                      <Button
                        size="sm"
                        variant="destructive"
                        className="ml-2"
                      >
                        Delete
                      </Button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </main>
    </div>
  );
}
