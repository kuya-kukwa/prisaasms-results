'use client';

import { useEffect, useState } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Trophy } from 'lucide-react';
import { adminResultsApi, Result } from '@/src/lib/admin-results-api';
import { ApiResponse } from '@/src/lib/api';
import toast from 'react-hot-toast';
export default function PastResultsPage() {
  const [data, setData] = useState<Result[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const year = new Date().getFullYear() - 1; // last year for demo
    adminResultsApi.results
      .byYear(year)
      .then((res: ApiResponse<Result[]>) => {
        setData(res.data || []);
      })
      .catch((err) => {
        console.error('Error loading past results:', err);
        toast.error('Failed to load past results');
      })
      .finally(() => setLoading(false));
  }, []);

  return (
    <div className="space-y-6">
      <Card>
        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle className="text-sm font-medium">Past Results</CardTitle>
          <Trophy className="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent>
          {loading ? (
            <p className="text-muted-foreground">Loading past results...</p>
          ) : data.length === 0 ? (
            <p className="text-muted-foreground">No past results found.</p>
          ) : (
            <div className="rounded-md border">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Year</TableHead>
                    <TableHead>Athlete</TableHead>
                    <TableHead>Sport</TableHead>
                    <TableHead>Rank</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {data.map((r) => (
                    <TableRow key={r.id}>
                      <TableCell>{r.year}</TableCell>
                      <TableCell>{r.athlete}</TableCell>
                      <TableCell>{r.sport}</TableCell>
                      <TableCell>{r.rank}</TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  );
}
