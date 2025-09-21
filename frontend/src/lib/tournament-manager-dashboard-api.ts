import { apiClient, Tournament, Match, Official, Result, Ranking, MedalTally, Venue } from './api';

export interface TournamentManagerDashboardData {
  tournaments: {
    upcoming: Tournament[];
    ongoing: Tournament[];
    completed: Tournament[];
    total: number;
  };
  matches: {
    upcoming: Match[];
    completed: Match[];
    total: number;
  };
  officials: {
    available: Official[];
    total: number;
  };
  results: {
    recent: Result[];
    pending: Result[];
    total: number;
  };
  venues: {
    available: Venue[];
    total: number;
  };
  rankings: {
    recent: Ranking[];
    total: number;
  };
  medals: {
    recent: MedalTally[];
    total: number;
  };
}

export interface TournamentForm {
  name: string;
  description: string;
  start_date: string;
  end_date: string;
  level: 'Provincial' | 'Regional' | 'National';
  status: 'upcoming' | 'ongoing' | 'completed';
}

export interface MatchForm {
  tournament_id: number;
  team1_id: number;
  team2_id: number;
  scheduled_date: string;
  start_time: string;
  venue_id?: number;
  status: 'scheduled' | 'ongoing' | 'completed' | 'cancelled';
}

export interface OfficialForm {
  first_name: string;
  last_name: string;
  type: string;
  certification_level: string;
  sport_id: number;
  contact_number: string;
  status: 'active' | 'inactive';
}

export interface ResultForm {
  match_id: number;
  athlete_id?: number;
  team_id?: number;
  position: number;
  score?: number;
  time?: string;
  points: number;
  verified: boolean;
}

export interface VenueForm {
  name: string;
  type: string;
  capacity: number;
  location: string;
  school_id?: number;
  status: 'available' | 'maintenance' | 'occupied';
}

export interface VenueAvailability {
  venue_id: number;
  date: string;
  time_slots: {
    start_time: string;
    end_time: string;
    is_available: boolean;
    event?: string;
  }[];
}

class TournamentManagerApi {
  // Dashboard data
  async getDashboardData(): Promise<TournamentManagerDashboardData> {
    try {
      // Try to fetch all data, but handle failures gracefully
      const results = await Promise.allSettled([
        this.getTournaments(),
        this.getUpcomingTournaments(),
        this.getOngoingTournaments(),
        this.getCompletedTournaments(),
        this.getMatches(),
        this.getUpcomingMatches(),
        this.getCompletedMatches(),
        this.getOfficials(),
        this.getAvailableOfficials(),
        this.getResults(),
        this.getRecentResults(),
        this.getVenues(),
        this.getRankings(),
        this.getMedals()
      ]);

      // Safely extract results, defaulting to empty arrays on failure
      const [
        tournaments,
        upcomingTournaments,
        ongoingTournaments,
        completedTournaments,
        matches,
        upcomingMatches,
        completedMatches,
        officials,
        availableOfficials,
        allResults,
        recentResults,
        venues,
        rankings,
        medals
      ] = results.map(result => result.status === 'fulfilled' ? result.value : []);

      // Ensure allResults is an array before filtering
      const resultsArray = Array.isArray(allResults) ? allResults as Result[] : [];
      const pendingResults = resultsArray.filter(result => !result.verified);

      return {
        tournaments: {
          upcoming: Array.isArray(upcomingTournaments) ? upcomingTournaments as Tournament[] : [],
          ongoing: Array.isArray(ongoingTournaments) ? ongoingTournaments as Tournament[] : [],
          completed: Array.isArray(completedTournaments) ? completedTournaments as Tournament[] : [],
          total: Array.isArray(tournaments) ? (tournaments as Tournament[]).length : 0
        },
        matches: {
          upcoming: Array.isArray(upcomingMatches) ? upcomingMatches as Match[] : [],
          completed: Array.isArray(completedMatches) ? completedMatches as Match[] : [],
          total: Array.isArray(matches) ? (matches as Match[]).length : 0
        },
        officials: {
          available: Array.isArray(availableOfficials) ? availableOfficials as Official[] : [],
          total: Array.isArray(officials) ? (officials as Official[]).length : 0
        },
        results: {
          recent: Array.isArray(recentResults) ? recentResults as Result[] : [],
          pending: pendingResults,
          total: resultsArray.length
        },
        venues: {
          available: Array.isArray(venues) ? (venues as Venue[]).filter(venue => venue.status === 'available') : [],
          total: Array.isArray(venues) ? (venues as Venue[]).length : 0
        },
        rankings: {
          recent: Array.isArray(rankings) ? (rankings as Ranking[]).slice(0, 10) : [], // Get top 10 recent rankings
          total: Array.isArray(rankings) ? (rankings as Ranking[]).length : 0
        },
        medals: {
          recent: Array.isArray(medals) ? (medals as MedalTally[]).slice(0, 10) : [], // Get top 10 recent medal tallies
          total: Array.isArray(medals) ? (medals as MedalTally[]).length : 0
        }
      };
    } catch (error) {
      console.error('Error fetching dashboard data:', error);
      // Return empty data structure as fallback
      return {
        tournaments: {
          upcoming: [],
          ongoing: [],
          completed: [],
          total: 0
        },
        matches: {
          upcoming: [],
          completed: [],
          total: 0
        },
        officials: {
          available: [],
          total: 0
        },
        results: {
          recent: [],
          pending: [],
          total: 0
        },
        venues: {
          available: [],
          total: 0
        },
        rankings: {
          recent: [],
          total: 0
        },
        medals: {
          recent: [],
          total: 0
        }
      };
    }
  }

  // Tournament Management
  async getTournaments(): Promise<Tournament[]> {
    const response = await apiClient.protected.tournaments.list();
    return response.data;
  }

  async getUpcomingTournaments(): Promise<Tournament[]> {
    const response = await apiClient.public.tournaments.upcoming();
    return response.data;
  }

  async getOngoingTournaments(): Promise<Tournament[]> {
    const response = await apiClient.public.tournaments.ongoing();
    return response.data;
  }

  async getCompletedTournaments(): Promise<Tournament[]> {
    const response = await apiClient.public.tournaments.completed();
    return response.data;
  }

  async getTournament(id: number): Promise<Tournament> {
    const response = await apiClient.protected.tournaments.get(id);
    return response.data;
  }

  async createTournament(tournament: TournamentForm): Promise<Tournament> {
    const response = await apiClient.protected.tournaments.create(tournament);
    return response.data;
  }

  async updateTournament(id: number, tournament: Partial<TournamentForm>): Promise<Tournament> {
    const response = await apiClient.protected.tournaments.update(id, tournament);
    return response.data;
  }

  async deleteTournament(id: number): Promise<void> {
    await apiClient.protected.tournaments.delete(id);
  }

  async updateTournamentStatus(id: number, status: Tournament['status']): Promise<Tournament> {
    const response = await apiClient.protected.tournaments.updateStatus(id, status);
    return response.data;
  }

  // Match Management
  async getMatches(): Promise<Match[]> {
    const response = await apiClient.protected.matches.list();
    return response.data;
  }

  async getUpcomingMatches(): Promise<Match[]> {
    const response = await apiClient.public.matches.upcoming();
    return response.data;
  }

  async getCompletedMatches(): Promise<Match[]> {
    const response = await apiClient.public.matches.completed();
    return response.data;
  }

  async getMatch(id: number): Promise<Match> {
    const response = await apiClient.protected.matches.get(id);
    return response.data;
  }

  async createMatch(match: MatchForm): Promise<Match> {
    const response = await apiClient.protected.matches.create(match);
    return response.data;
  }

  async updateMatch(id: number, match: Partial<MatchForm>): Promise<Match> {
    const response = await apiClient.protected.matches.update(id, match);
    return response.data;
  }

  async deleteMatch(id: number): Promise<void> {
    await apiClient.protected.matches.delete(id);
  }

  async updateMatchScore(id: number, team1Score: number, team2Score: number): Promise<Match> {
    const response = await apiClient.protected.matches.updateScore(id, { team1_score: team1Score, team2_score: team2Score });
    return response.data;
  }

  // Official Management
  async getOfficials(): Promise<Official[]> {
    const response = await apiClient.protected.officials.list();
    return response.data;
  }

  async getAvailableOfficials(): Promise<Official[]> {
    const response = await apiClient.protected.officials.available();
    return response.data;
  }

  async getOfficial(id: number): Promise<Official> {
    const response = await apiClient.protected.officials.get(id);
    return response.data;
  }

  async createOfficial(official: OfficialForm): Promise<Official> {
    const response = await apiClient.protected.officials.create(official);
    return response.data;
  }

  async updateOfficial(id: number, official: Partial<OfficialForm>): Promise<Official> {
    const response = await apiClient.protected.officials.update(id, official);
    return response.data;
  }

  async deleteOfficial(id: number): Promise<void> {
    await apiClient.protected.officials.delete(id);
  }

  // Result Management
  async getResults(): Promise<Result[]> {
    const response = await apiClient.protected.results.list();
    return response.data;
  }

  async getRecentResults(): Promise<Result[]> {
    const response = await apiClient.public.results.recent();
    return response.data;
  }

  async getResult(id: number): Promise<Result> {
    const response = await apiClient.protected.results.get(id);
    return response.data;
  }

  async createResult(result: ResultForm): Promise<Result> {
    const response = await apiClient.protected.results.create(result);
    return response.data;
  }

  async updateResult(id: number, result: Partial<ResultForm>): Promise<Result> {
    const response = await apiClient.protected.results.update(id, result);
    return response.data;
  }

  async deleteResult(id: number): Promise<void> {
    await apiClient.protected.results.delete(id);
  }

  async verifyResult(id: number): Promise<Result> {
    const response = await apiClient.protected.results.verify(id);
    return response.data;
  }

  // Ranking Management
  async getRankings(): Promise<Ranking[]> {
    const response = await apiClient.protected.rankings.list();
    return response.data;
  }

  async getRanking(id: number): Promise<Ranking> {
    const response = await apiClient.protected.rankings.get(id);
    return response.data;
  }

  async updateRankings(rankings: Partial<Ranking>[]): Promise<Ranking[]> {
    const response = await apiClient.protected.rankings.updateRankings({ rankings });
    return response.data as Ranking[];
  }

  // Medal Management
  async getMedals(): Promise<MedalTally[]> {
    const response = await apiClient.protected.medals.list();
    return response.data;
  }

  async getMedal(id: number): Promise<MedalTally> {
    const response = await apiClient.protected.medals.get(id);
    return response.data;
  }

  async createMedal(medal: Partial<MedalTally>): Promise<MedalTally> {
    const response = await apiClient.protected.medals.create(medal);
    return response.data;
  }

  async updateMedal(id: number, medal: Partial<MedalTally>): Promise<MedalTally> {
    const response = await apiClient.protected.medals.update(id, medal);
    return response.data;
  }

  async deleteMedal(id: number): Promise<void> {
    await apiClient.protected.medals.delete(id);
  }

  // Venue Management
  async getVenues(): Promise<Venue[]> {
    const response = await apiClient.protected.venues.list();
    return response.data;
  }

  async getVenue(id: number): Promise<Venue> {
    const response = await apiClient.protected.venues.get(id);
    return response.data;
  }

  async createVenue(venue: VenueForm): Promise<Venue> {
    const response = await apiClient.protected.venues.create(venue);
    return response.data;
  }

  async updateVenue(id: number, venue: Partial<VenueForm>): Promise<Venue> {
    const response = await apiClient.protected.venues.update(id, venue);
    return response.data;
  }

  async deleteVenue(id: number): Promise<void> {
    await apiClient.protected.venues.delete(id);
  }

  async getVenueAvailability(id: number): Promise<Record<string, unknown>> {
    const response = await apiClient.protected.venues.availability(id);
    return response.data;
  }
}

export const tournamentManagerApi = new TournamentManagerApi();
