'use client';

import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import ScrollCounter from '@/components/ui/scroll-counter';
import { Calendar, Trophy, Users, BarChart3, TrendingUp, Clock, CheckCircle } from 'lucide-react';

export default function SportsStatsPage() {
  // Mock data - in real app, this would come from API
  const stats = {
    totalSports: 12,
    activeSports: 10,
    categories: {
      'Individual': 4,
      'Team': 6,
      'Ball': 3,
      'Combat': 2,
      'Other': 1
    },
    recentActivity: [
      { action: 'Added new sport', item: 'Table Tennis', time: '2 hours ago' },
      { action: 'Updated sport', item: 'Basketball', time: '5 hours ago' },
      { action: 'Deactivated sport', item: 'Boxing', time: '1 day ago' },
    ],
    topSports: [
      { name: 'Basketball', participants: 245, events: 12 },
      { name: 'Volleyball', participants: 198, events: 8 },
      { name: 'Badminton', participants: 156, events: 6 },
      { name: 'Table Tennis', participants: 134, events: 5 },
    ]
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold">Sports Statistics</h1>
          <p className="text-muted-foreground">
            Overview of sports categories and participation metrics
          </p>
        </div>
        <Button>
          <BarChart3 className="h-4 w-4 mr-2" />
          Export Report
        </Button>
      </div>

      {/* Key Metrics */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total Sports</CardTitle>
            <Trophy className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <ScrollCounter
              end={stats.totalSports}
              className="text-2xl font-bold"
            />
            <p className="text-xs text-muted-foreground">
              {stats.activeSports} active
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Categories</CardTitle>
            <BarChart3 className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <ScrollCounter
              end={Object.keys(stats.categories).length}
              className="text-2xl font-bold"
            />
            <p className="text-xs text-muted-foreground">
              Sport categories
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total Participants</CardTitle>
            <Users className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <ScrollCounter
              end={stats.topSports.reduce((sum, sport) => sum + sport.participants, 0)}
              className="text-2xl font-bold"
            />
            <p className="text-xs text-muted-foreground">
              Across all sports
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Active Events</CardTitle>
            <Calendar className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <ScrollCounter
              end={stats.topSports.reduce((sum, sport) => sum + sport.events, 0)}
              className="text-2xl font-bold"
            />
            <p className="text-xs text-muted-foreground">
              This month
            </p>
          </CardContent>
        </Card>
      </div>

      {/* Category Breakdown */}
      <Card>
        <CardHeader>
          <CardTitle>Sports by Category</CardTitle>
          <CardDescription>
            Distribution of sports across different categories
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            {Object.entries(stats.categories).map(([category, count]) => (
              <div key={category} className="flex items-center justify-between">
                <div className="flex items-center gap-2">
                  <Badge variant="outline">{category}</Badge>
                  <span className="text-sm text-muted-foreground">
                    {count} sport{count !== 1 ? 's' : ''}
                  </span>
                </div>
                <div className="flex items-center gap-2">
                  <div className="w-24 bg-secondary rounded-full h-2">
                    <div
                      className="bg-primary h-2 rounded-full"
                      style={{
                        width: `${(count / stats.totalSports) * 100}%`
                      }}
                    />
                  </div>
                  <span className="text-sm font-medium w-12 text-right">
                    {Math.round((count / stats.totalSports) * 100)}%
                  </span>
                </div>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>

      {/* Top Sports */}
      <Card>
        <CardHeader>
          <CardTitle>Top Performing Sports</CardTitle>
          <CardDescription>
            Sports with highest participation and event frequency
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            {stats.topSports.map((sport, index) => (
              <div key={sport.name} className="flex items-center justify-between p-4 border rounded-lg">
                <div className="flex items-center gap-4">
                  <div className="flex items-center justify-center w-8 h-8 rounded-full bg-primary/10 text-primary font-bold">
                    {index + 1}
                  </div>
                  <div>
                    <h3 className="font-medium">{sport.name}</h3>
                    <p className="text-sm text-muted-foreground">
                      {sport.events} events • {sport.participants} participants
                    </p>
                  </div>
                </div>
                <div className="flex items-center gap-2">
                  <TrendingUp className="h-4 w-4 text-green-500" />
                  <span className="text-sm font-medium text-green-600">
                    Active
                  </span>
                </div>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>

      {/* Recent Activity */}
      <Card>
        <CardHeader>
          <CardTitle>Recent Activity</CardTitle>
          <CardDescription>
            Latest changes and updates to sports data
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            {stats.recentActivity.map((activity, index) => (
              <div key={index} className="flex items-center gap-4 p-3 border rounded-lg">
                <div className="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100">
                  <CheckCircle className="h-4 w-4 text-blue-600" />
                </div>
                <div className="flex-1">
                  <p className="text-sm">
                    <span className="font-medium">{activity.action}</span>
                    {' • '}
                    <span className="text-muted-foreground">{activity.item}</span>
                  </p>
                  <p className="text-xs text-muted-foreground">{activity.time}</p>
                </div>
                <Badge variant="secondary" className="text-xs">
                  <Clock className="h-3 w-3 mr-1" />
                  {activity.time}
                </Badge>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
