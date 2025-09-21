import { apiClient, Athlete, Team, Schedule, User } from './api';

export interface CoachStats {
  myAthletes: number;
  myTeams: number;
  upcomingMatches: number;
  activeCompetitions: number;
}

export interface CoachActivity {
  id: string;
  action: string;
  description: string;
  timestamp: string;
  type: 'athlete' | 'team' | 'match' | 'training' | 'registration';
}

export interface CoachDashboardData {
  stats: CoachStats;
  activities: CoachActivity[];
}

class CoachService {
  private cache: {
    athletes: Athlete[] | null;
    teams: Team[] | null;
    schedules: Schedule[] | null;
    timestamp: number;
    ttl: number;
  } = {
    athletes: null,
    teams: null,
    schedules: null,
    timestamp: 0,
    ttl: 60000 // 1 minute cache
  };

  private isCacheValid(): boolean {
    return (Date.now() - this.cache.timestamp) < this.cache.ttl;
  }

  // Get coach's athletes
  async getMyAthletes(user?: User): Promise<Athlete[]> {
    try {
      if (this.cache.athletes && this.isCacheValid()) {
        return this.cache.athletes;
      }

      // Get athletes by school - assuming coach manages athletes from their school
      if (!user?.school_id) {
        return [];
      }

      const response = await apiClient.protected.athletes.bySchool(user.school_id);
      this.cache.athletes = response.data;
      this.cache.timestamp = Date.now();
      return response.data;
    } catch (error) {
      console.error('Failed to fetch athletes:', error);
      return [];
    }
  }

  // Get coach's teams
  async getMyTeams(user?: User): Promise<Team[]> {
    try {
      if (this.cache.teams && this.isCacheValid()) {
        return this.cache.teams;
      }

      // Get teams by school
      if (!user?.school_id) {
        return [];
      }

      const response = await apiClient.protected.teams.bySchool(user.school_id);
      this.cache.teams = response.data;
      this.cache.timestamp = Date.now();
      return response.data;
    } catch (error) {
      console.error('Failed to fetch teams:', error);
      return [];
    }
  }

  // Get upcoming matches for coach's teams
  async getUpcomingMatches(): Promise<Schedule[]> {
    try {
      if (this.cache.schedules && this.isCacheValid()) {
        return this.cache.schedules;
      }

      const response = await apiClient.public.schedules.upcoming();
      
      // For now, return all upcoming schedules since filtering by school may not be available in this Schedule type
      // In a real implementation, you'd either filter on the backend or have additional API endpoints
      const filteredSchedules = response.data; // Return all for now

      this.cache.schedules = filteredSchedules;
      this.cache.timestamp = Date.now();
      return filteredSchedules;
    } catch (error) {
      console.error('Failed to fetch schedules:', error);
      return [];
    }
  }

  // Get coach dashboard data
  async getCoachDashboardData(user?: User, forceRefresh = false): Promise<CoachDashboardData> {
    try {
      if (!forceRefresh && this.isCacheValid()) {
        // Return cached data if available
        const cachedStats = await this.getCachedStats();
        if (cachedStats) {
          return {
            stats: cachedStats,
            activities: this.generateActivities() // Generate from cached data
          };
        }
      }

      // Fetch fresh data
      const [athletes, teams, schedules] = await Promise.all([
        this.getMyAthletes(user),
        this.getMyTeams(user),
        this.getUpcomingMatches()
      ]);

      // Calculate stats
      const stats: CoachStats = {
        myAthletes: athletes.length,
        myTeams: teams.length,
        upcomingMatches: schedules.length,
        activeCompetitions: this.calculateActiveCompetitions(schedules)
      };

      // Generate activities based on real fetched data
      const activities = this.generateActivities();

      return { stats, activities };
    } catch (error) {
      console.error('Failed to fetch coach dashboard data:', error);
      
      // Return fallback data
      return {
        stats: {
          myAthletes: 0,
          myTeams: 0,
          upcomingMatches: 0,
          activeCompetitions: 0
        },
        activities: []
      };
    }
  }

  private async getCachedStats(): Promise<CoachStats | null> {
    if (!this.cache.athletes || !this.cache.teams || !this.cache.schedules) {
      return null;
    }

    return {
      myAthletes: this.cache.athletes.length,
      myTeams: this.cache.teams.length,
      upcomingMatches: this.cache.schedules.length,
      activeCompetitions: this.calculateActiveCompetitions(this.cache.schedules)
    };
  }

  private calculateActiveCompetitions(schedules: Schedule[]): number {
    // Count unique sports from schedules as a proxy for active competitions
    const sports = new Set(schedules.map(s => s.sport_id).filter(Boolean));
    return sports.size;
  }

  private generateActivities(): CoachActivity[] {
    const activities: CoachActivity[] = [];
    const now = new Date();
    const sevenDaysAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);

    // Generate activities from recently created athletes
    if (this.cache.athletes) {
      this.cache.athletes
        .filter(athlete => new Date(athlete.created_at) > sevenDaysAgo)
        .forEach(athlete => {
          activities.push({
            id: `athlete-${athlete.id}`,
            action: 'New Athlete Added',
            description: `${athlete.first_name} ${athlete.last_name} joined ${athlete.sport?.name || 'the team'}`,
            timestamp: athlete.created_at,
            type: 'athlete'
          });
        });
    }

    // Generate activities from recently created teams
    if (this.cache.teams) {
      this.cache.teams
        .filter(team => new Date(team.created_at) > sevenDaysAgo)
        .forEach(team => {
          activities.push({
            id: `team-${team.id}`,
            action: 'Team Created',
            description: `New ${team.sport?.name || 'sports'} team "${team.name}" was formed`,
            timestamp: team.created_at,
            type: 'team'
          });
        });
    }

    // Generate activities from recently scheduled matches
    if (this.cache.schedules) {
      this.cache.schedules
        .filter(schedule => new Date(schedule.created_at) > sevenDaysAgo)
        .forEach(schedule => {
          activities.push({
            id: `schedule-${schedule.id}`,
            action: 'Match Scheduled',
            description: `${schedule.sport?.name || 'Sports'} match "${schedule.tournament?.name || 'Tournament'}" scheduled for ${new Date(schedule.scheduled_date).toLocaleDateString()}`,
            timestamp: schedule.created_at,
            type: 'match'
          });
        });
    }

    // Sort activities by timestamp (most recent first)
    return activities.sort((a, b) => new Date(b.timestamp).getTime() - new Date(a.timestamp).getTime());
  }

  // Clear cache
  clearCache(): void {
    this.cache = {
      athletes: null,
      teams: null,
      schedules: null,
      timestamp: 0,
      ttl: 60000
    };
  }
}

export const coachService = new CoachService();
