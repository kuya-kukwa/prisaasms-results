'use client';

import { useEffect, useState } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Trophy } from 'lucide-react';
import { adminResultsApi, MedalTally } from '@/src/lib/admin-results-api';
import { ApiResponse } from '@/src/lib/api';
import toast from 'react-hot-toast';

export default function MedalTalliesPage() {
  const [data, setData] = useState<MedalTally[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    adminResultsApi.medalTallies
      .list()
      .then((res: ApiResponse<MedalTally[]>) => {
        setData(res.data || []);
      })
      .catch((err: unknown) => {
        console.error('Error loading medal tallies:', err);
        toast.error('Failed to load medal tallies');
      })
      .finally(() => setLoading(false));
  }, []);

  return (
    <div className="space-y-6">
      <Card>
        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle className="text-sm font-medium">Medal Tallies</CardTitle>
          <Trophy className="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent>
          {loading ? (
            <p className="text-muted-foreground">Loading medal tallies...</p>
          ) : data.length === 0 ? (
            <p className="text-muted-foreground">No medal tallies found.</p>
          ) : (
            <div className="rounded-md border">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>School</TableHead>
                    <TableHead className="text-center">Gold</TableHead>
                    <TableHead className="text-center">Silver</TableHead>
                    <TableHead className="text-center">Bronze</TableHead>
                    <TableHead className="text-center">Total</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {data.map((r) => (
                    <TableRow key={r.id}>
                      <TableCell>{r.school}</TableCell>
                      <TableCell className="text-center">{r.gold}</TableCell>
                      <TableCell className="text-center">{r.silver}</TableCell>
                      <TableCell className="text-center">{r.bronze}</TableCell>
                      <TableCell className="text-center">{r.total}</TableCell>
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
