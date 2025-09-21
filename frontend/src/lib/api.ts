import axios, { AxiosInstance } from 'axios';

// Types for API responses
export interface ApiResponse<T = unknown> {
  success: boolean;
  data: T;
  message?: string;
  meta?: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}

// User and Auth Types
export interface User {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  contact_number?: string;
  school_id?: number;
  role: 'admin' | 'coach' | 'tournament_manager';
  created_at: string;
  updated_at: string;
}

export interface OnlineUser {
  id: number;
  first_name: string;
  last_name: string;
  full_name: string;
  email: string;
  role: string;
  avatar?: string;
  school?: {
    id: number;
    name: string;
    short_name?: string;
  };
  last_seen: string;
  status: 'online' | 'busy' | 'away' | 'offline';
  minutes_ago?: number | null;
  has_active_session?: boolean;
}

// Core Entity Types
export interface School {
  id: number;
  name: string;
  short_name?: string;
  address?: string;
  region_id?: number;
  region?: Region;
  logo?: string;
  status?: string;
  contact_person?: string;
  contact_number?: string;
  created_at: string;
  updated_at: string;
}

export interface Sport {
  id: number;
  name: string;
  description: string;
  category: string;
  created_at: string;
  updated_at: string;
}

export interface Athlete {
  id: number;
  first_name: string;
  last_name: string;
  gender: 'male' | 'female';
  birthdate: string;
  avatar?: string;
  school_id: number;
  sport_id: number;
  athlete_number: string;
  status: 'active' | 'inactive';
  school?: School;
  sport?: Sport;
  created_at: string;
  updated_at: string;
}

export interface Team {
  id: number;
  name: string;
  school_id: number;
  sport_id: number;
  coach_name?: string;
  status: 'active' | 'inactive';
  school?: School;
  sport?: Sport;
  created_at: string;
  updated_at: string;
}

export interface Venue {
  id: number;
  name: string;
  address: string;
  city: string;
  state: string;
  zip_code?: string;
  country: string;
  capacity?: number;
  facilities?: string[];
  contact_person?: string;
  contact_phone?: string;
  contact_email?: string;
  description?: string;
  status?: 'available' | 'maintenance' | 'occupied' | 'inactive';
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

export interface Tournament {
  id: number;
  name: string;
  description: string;
  start_date: string;
  end_date: string;
  level: 'Provincial' | 'Regional' | 'National';
  scope: 'single_region' | 'multi_region' | 'inter_regional' | 'national';
  status: 'upcoming' | 'ongoing' | 'completed';
  host_region_id?: number;
  regions?: Region[];
  host_region?: Region;
  created_at: string;
  updated_at: string;
}

export interface Schedule {
  id: number;
  tournament_id: number;
  sport_id: number;
  venue_id: number;
  scheduled_date: string;
  start_time: string;
  end_time: string;
  status: 'scheduled' | 'ongoing' | 'completed' | 'cancelled';
  notes?: string;
  tournament?: Tournament;
  sport?: Sport;
  venue?: Venue;
  created_at: string;
  updated_at: string;
}

export interface Match {
  id: number;
  tournament_id: number;
  team1_id: number;
  team2_id: number;
  scheduled_date: string;
  start_time: string;
  venue_id?: number;
  status: 'scheduled' | 'ongoing' | 'completed' | 'cancelled';
  team1_score?: number;
  team2_score?: number;
  tournament?: Tournament;
  team1?: Team;
  team2?: Team;
  venue?: Venue;
  created_at: string;
  updated_at: string;
}

export interface Official {
  id: number;
  first_name: string;
  last_name: string;
  type: string;
  certification_level: string;
  sport_id: number;
  contact_number: string;
  status: 'active' | 'inactive';
  sport?: Sport;
  created_at: string;
  updated_at: string;
}

export interface Result {
  id: number;
  match_id: number;
  athlete_id?: number;
  team_id?: number;
  position: number;
  score?: number;
  time?: string;
  points: number;
  verified: boolean;
  match?: Match;
  athlete?: Athlete;
  team?: Team;
  created_at: string;
  updated_at: string;
}

export interface Ranking {
  id: number;
  sport_id: number;
  athlete_id?: number;
  team_id?: number;
  rank: number;
  points: number;
  sport?: Sport;
  athlete?: Athlete;
  team?: Team;
  created_at: string;
  updated_at: string;
}

export interface MedalTally {
  id: number;
  school_id: number;
  sport_id: number;
  gold: number;
  silver: number;
  bronze: number;
  total: number;
  school?: School;
  sport?: Sport;
  created_at: string;
  updated_at: string;
}

export interface Region {
  id: number;
  name: string;
  code: string;
  status: 'active' | 'inactive';
  created_at: string;
  updated_at: string;
}
export interface PrisaaYear {
  id: number;
  year: string;
  active: boolean;
  created_at: string;
  updated_at: string;
}

export interface OverallChampion {
  id: number;
  school_id: number;
  prisaa_year_id: number;
  points: number;
  rank: number;
  created_at: string;
  updated_at: string;
}

class ApiClient {
  private client: AxiosInstance;
  private token: string | null = null;

  constructor() {
    this.client = axios.create({
      baseURL: process.env.NEXT_PUBLIC_API_BASE_URL,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      withCredentials: false,
    });

    // Add token to requests if available
    this.client.interceptors.request.use((config) => {
      if (this.token) {
        config.headers.Authorization = `Bearer ${this.token}`;
      }
      return config;
    });

    // Handle responses and errors
    this.client.interceptors.response.use(
      (response) => response,
      (error) => {
        // Handle authentication errors
        if (error.response?.status === 401) {
          this.clearToken();
          // Only redirect to login if not already on login page
          if (typeof window !== 'undefined' && !window.location.pathname.includes('/login')) {
            window.location.href = '/login';
          }
        }
        
        return Promise.reject(error);
      }
    );

    // Load token from localStorage on client side
    if (typeof window !== 'undefined') {
      this.token = localStorage.getItem('prisaa_token');
    }
  }

  setToken(token: string) {
    this.token = token;
    if (typeof window !== 'undefined') {
      localStorage.setItem('prisaa_token', token);
    }
  }

  clearToken() {
    this.token = null;
    if (typeof window !== 'undefined') {
      localStorage.removeItem('prisaa_token');
    }
  }

  // Authentication Methods
  auth = {
    login: async (email: string, password: string): Promise<ApiResponse<{ user: User; token: string }>> => {
      const response = await this.client.post('/auth/login', { email, password });
      if (response.data.success && response.data.data.token) {
        this.setToken(response.data.data.token);
      }
      return response.data;
    },

    register: async (userData: {
      first_name: string;
      last_name: string;
      email: string;
      password: string;
      password_confirmation: string;
      contact_number?: string;
    }): Promise<ApiResponse<{ user: User; token: string }>> => {
      const response = await this.client.post('/auth/register', userData);
      if (response.data.success && response.data.data.token) {
        this.setToken(response.data.data.token);
      }
      return response.data;
    },

    me: async (): Promise<ApiResponse<User>> => {
      const response = await this.client.get('/auth/me');
      return response.data;
    },

    logout: async (): Promise<ApiResponse<unknown>> => {
      const response = await this.client.post('/auth/logout');
      this.clearToken();
      return response.data;
    },

    forgotPassword: async (email: string): Promise<ApiResponse<unknown>> => {
      const response = await this.client.post('/auth/forgot-password', { email });
      return response.data;
    },

    resetPassword: async (data: {
      token: string;
      email: string;
      password: string;
      password_confirmation: string;
    }): Promise<ApiResponse<unknown>> => {
      const response = await this.client.post('/auth/reset-password', data);
      return response.data;
    },
  };

  // Public API Methods (No authentication required)
  public = {
    schools: {
      list: async (params?: Record<string, unknown>): Promise<ApiResponse<School[]>> => {
        const response = await this.client.get('/public/schools', { params });
        return response.data;
      },
      get: async (id: number): Promise<ApiResponse<School>> => {
        const response = await this.client.get(`/public/schools/${id}`);
        return response.data;
      },
      byRegion: async (regionId: number): Promise<ApiResponse<School[]>> => {
        const response = await this.client.get(`/public/schools/region/${regionId}`);
        return response.data;
      },
      overallStatistics: async (): Promise<ApiResponse<Record<string, number>>> => {
        const response = await this.client.get('/public/schools/statistics/overall');
        return response.data;
      },
    },

    sports: {
      list: async (): Promise<ApiResponse<Sport[]>> => {
        const response = await this.client.get('/public/sports');
        return response.data;
      },
      get: async (id: number): Promise<ApiResponse<Sport>> => {
        const response = await this.client.get(`/public/sports/${id}`);
        return response.data;
      },
      byCategory: async (category: string): Promise<ApiResponse<Sport[]>> => {
        const response = await this.client.get(`/public/sports/category/${category}`);
        return response.data;
      },
    },

    matches: {
      list: async (): Promise<ApiResponse<Match[]>> => {
        const response = await this.client.get('/public/matches');
        return response.data;
      },
      upcoming: async (): Promise<ApiResponse<Match[]>> => {
        const response = await this.client.get('/public/matches/upcoming');
        return response.data;
      },
      completed: async (): Promise<ApiResponse<Match[]>> => {
        const response = await this.client.get('/public/matches/completed');
        return response.data;
      },
    },

    results: {
      list: async (): Promise<ApiResponse<Result[]>> => {
        const response = await this.client.get('/public/results');
        return response.data;
      },
      recent: async (): Promise<ApiResponse<Result[]>> => {
        const response = await this.client.get('/public/results/recent');
        return response.data;
      },
    },

    tournaments: {
      list: async (): Promise<ApiResponse<Tournament[]>> => {
        const response = await this.client.get('/public/tournaments');
        return response.data;
      },
      upcoming: async (): Promise<ApiResponse<Tournament[]>> => {
        const response = await this.client.get('/public/tournaments/upcoming');
        return response.data;
      },
      ongoing: async (): Promise<ApiResponse<Tournament[]>> => {
        const response = await this.client.get('/public/tournaments/ongoing');
        return response.data;
      },
      completed: async (): Promise<ApiResponse<Tournament[]>> => {
        const response = await this.client.get('/public/tournaments/completed');
        return response.data;
      },
    },

    schedules: {
      list: async (): Promise<ApiResponse<Schedule[]>> => {
        const response = await this.client.get('/public/schedules');
        return response.data;
      },
      upcoming: async (): Promise<ApiResponse<Schedule[]>> => {
        const response = await this.client.get('/public/schedules/upcoming');
        return response.data;
      },
    },

    medals: {
      schoolRanking: async (): Promise<ApiResponse<MedalTally[]>> => {
        const response = await this.client.get('/public/medals/school-ranking');
        return response.data;
      },
      statistics: async (): Promise<ApiResponse<Record<string, unknown>>> => {
        const response = await this.client.get('/public/medals/statistics');
        return response.data;
      },
    },

    rankings: {
      list: async (): Promise<ApiResponse<Ranking[]>> => {
        const response = await this.client.get('/public/rankings');
        return response.data;
      },
      topPerformers: async (): Promise<ApiResponse<Ranking[]>> => {
        const response = await this.client.get('/public/rankings/top-performers');
        return response.data;
      },
    },

    users: {
      online: async (limit = 10): Promise<ApiResponse<OnlineUser[]>> => {
        const response = await this.client.get(`/public/users/online?limit=${limit}`);
        return response.data;
      },
    },
  };

  // Protected API Methods (Require authentication)
  protected = {
    profile: {
      get: async (): Promise<ApiResponse<User>> => {
        const response = await this.client.get('/users/profile');
        return response.data;
      },
      update: async (data: Partial<User>): Promise<ApiResponse<User>> => {
        const response = await this.client.put('/users/profile', data);
        return response.data;
      },
      changePassword: async (data: {
        current_password: string;
        password: string;
        password_confirmation: string;
      }): Promise<ApiResponse<unknown>> => {
        const response = await this.client.post('/users/change-password', data);
        return response.data;
      },
    },

    users: {
      list: async (params?: Record<string, unknown>): Promise<ApiResponse<User[]>> => {
        const response = await this.client.get('/users', { params });
        return response.data;
      },
      create: async (data: Partial<User>): Promise<ApiResponse<User>> => {
        const response = await this.client.post('/users', data);
        return response.data;
      },
      get: async (id: number): Promise<ApiResponse<User>> => {
        const response = await this.client.get(`/users/${id}`);
        return response.data;
      },
      update: async (id: number, data: Partial<User>): Promise<ApiResponse<User>> => {
        const response = await this.client.put(`/users/${id}`, data);
        return response.data;
      },
      delete: async (id: number): Promise<ApiResponse<unknown>> => {
        const response = await this.client.delete(`/users/${id}`);
        return response.data;
      },
      byRole: async (role: string): Promise<ApiResponse<User[]>> => {
        const response = await this.client.get(`/users/role/${role}`);
        return response.data;
      },
      online: async (limit = 10): Promise<ApiResponse<OnlineUser[]>> => {
        const response = await this.client.get(`/users/online?limit=${limit}`);
        return response.data;
      },
    },

    schools: {
      list: async (params?: Record<string, unknown>): Promise<ApiResponse<School[]>> => {
        const response = await this.client.get('/schools', { params });
        return response.data;
      },
      create: async (data: Partial<School>): Promise<ApiResponse<School>> => {
        const response = await this.client.post('/schools', data);
        return response.data;
      },
      get: async (id: number): Promise<ApiResponse<School>> => {
        const response = await this.client.get(`/schools/${id}`);
        return response.data;
      },
      update: async (id: number, data: Partial<School>): Promise<ApiResponse<School>> => {
        const response = await this.client.put(`/schools/${id}`, data);
        return response.data;
      },
      delete: async (id: number): Promise<ApiResponse<unknown>> => {
        const response = await this.client.delete(`/schools/${id}`);
        return response.data;
      },
      statistics: async (id: number): Promise<ApiResponse<Record<string, unknown>>> => {
        const response = await this.client.get(`/schools/${id}/statistics`);
        return response.data;
      },
    },

    athletes: {
      list: async (params?: Record<string, unknown>): Promise<ApiResponse<Athlete[]>> => {
        const response = await this.client.get('/athletes', { params });
        return response.data;
      },
      create: async (data: Partial<Athlete>): Promise<ApiResponse<Athlete>> => {
        const response = await this.client.post('/athletes', data);
        return response.data;
      },
      get: async (id: number): Promise<ApiResponse<Athlete>> => {
        const response = await this.client.get(`/athletes/${id}`);
        return response.data;
      },
      update: async (id: number, data: Partial<Athlete>): Promise<ApiResponse<Athlete>> => {
        const response = await this.client.put(`/athletes/${id}`, data);
        return response.data;
      },
      delete: async (id: number): Promise<ApiResponse<unknown>> => {
        const response = await this.client.delete(`/athletes/${id}`);
        return response.data;
      },
      bySchool: async (schoolId: number): Promise<ApiResponse<Athlete[]>> => {
        const response = await this.client.get(`/athletes/school/${schoolId}`);
        return response.data;
      },
      bySport: async (sportId: number): Promise<ApiResponse<Athlete[]>> => {
        const response = await this.client.get(`/athletes/sport/${sportId}`);
        return response.data;
      },
      performance: async (id: number): Promise<ApiResponse<Record<string, unknown>>> => {
        const response = await this.client.get(`/athletes/${id}/performance`);
        return response.data;
      },
    },

    teams: {
      list: async (params?: Record<string, unknown>): Promise<ApiResponse<Team[]>> => {
        const response = await this.client.get('/teams', { params });
        return response.data;
      },
      create: async (data: Partial<Team>): Promise<ApiResponse<Team>> => {
        const response = await this.client.post('/teams', data);
        return response.data;
      },
      get: async (id: number): Promise<ApiResponse<Team>> => {
        const response = await this.client.get(`/teams/${id}`);
        return response.data;
      },
      update: async (id: number, data: Partial<Team>): Promise<ApiResponse<Team>> => {
        const response = await this.client.put(`/teams/${id}`, data);
        return response.data;
      },
      delete: async (id: number): Promise<ApiResponse<unknown>> => {
        const response = await this.client.delete(`/teams/${id}`);
        return response.data;
      },
      bySchool: async (schoolId: number): Promise<ApiResponse<Team[]>> => {
        const response = await this.client.get(`/teams/school/${schoolId}`);
        return response.data;
      },
      bySport: async (sportId: number): Promise<ApiResponse<Team[]>> => {
        const response = await this.client.get(`/teams/sport/${sportId}`);
        return response.data;
      },
      statistics: async (id: number): Promise<ApiResponse<Record<string, unknown>>> => {
        const response = await this.client.get(`/teams/${id}/statistics`);
        return response.data;
      },
    },

    sports: {
      list: async (params?: Record<string, unknown>): Promise<ApiResponse<Sport[]>> => {
        const response = await this.client.get('/sports', { params });
        return response.data;
      },
      create: async (data: Partial<Sport>): Promise<ApiResponse<Sport>> => {
        const response = await this.client.post('/sports', data);
        return response.data;
      },
      get: async (id: number): Promise<ApiResponse<Sport>> => {
        const response = await this.client.get(`/sports/${id}`);
        return response.data;
      },
      update: async (id: number, data: Partial<Sport>): Promise<ApiResponse<Sport>> => {
        const response = await this.client.put(`/sports/${id}`, data);
        return response.data;
      },
      delete: async (id: number): Promise<ApiResponse<unknown>> => {
        const response = await this.client.delete(`/sports/${id}`);
        return response.data;
      },
      statistics: async (id: number): Promise<ApiResponse<Record<string, unknown>>> => {
        const response = await this.client.get(`/sports/${id}/statistics`);
        return response.data;
      },
    },

    venues: {
      list: async (params?: Record<string, unknown>): Promise<ApiResponse<Venue[]>> => {
        const response = await this.client.get('/venues', { params });
        return response.data;
      },
      create: async (data: Partial<Venue>): Promise<ApiResponse<Venue>> => {
        const response = await this.client.post('/venues', data);
        return response.data;
      },
      get: async (id: number): Promise<ApiResponse<Venue>> => {
        const response = await this.client.get(`/venues/${id}`);
        return response.data;
      },
      update: async (id: number, data: Partial<Venue>): Promise<ApiResponse<Venue>> => {
        const response = await this.client.put(`/venues/${id}`, data);
        return response.data;
      },
      delete: async (id: number): Promise<ApiResponse<unknown>> => {
        const response = await this.client.delete(`/venues/${id}`);
        return response.data;
      },
      bySchool: async (schoolId: number): Promise<ApiResponse<Venue[]>> => {
        const response = await this.client.get(`/venues/school/${schoolId}`);
        return response.data;
      },
      byType: async (type: string): Promise<ApiResponse<Venue[]>> => {
        const response = await this.client.get(`/venues/type/${type}`);
        return response.data;
      },
      availability: async (id: number): Promise<ApiResponse<Record<string, unknown>>> => {
        const response = await this.client.get(`/venues/${id}/availability`);
        return response.data;
      },
    },

    tournaments: {
      list: async (params?: Record<string, unknown>): Promise<ApiResponse<Tournament[]>> => {
        const response = await this.client.get('/tournaments', { params });
        return response.data;
      },
      create: async (data: Partial<Tournament>): Promise<ApiResponse<Tournament>> => {
        const response = await this.client.post('/tournaments', data);
        return response.data;
      },
      get: async (id: number): Promise<ApiResponse<Tournament>> => {
        const response = await this.client.get(`/tournaments/${id}`);
        return response.data;
      },
      update: async (id: number, data: Partial<Tournament>): Promise<ApiResponse<Tournament>> => {
        const response = await this.client.put(`/tournaments/${id}`, data);
        return response.data;
      },
      delete: async (id: number): Promise<ApiResponse<unknown>> => {
        const response = await this.client.delete(`/tournaments/${id}`);
        return response.data;
      },
      bySport: async (sportId: number): Promise<ApiResponse<Tournament[]>> => {
        const response = await this.client.get(`/tournaments/sport/${sportId}`);
        return response.data;
      },
      statistics: async (id: number): Promise<ApiResponse<Record<string, unknown>>> => {
        const response = await this.client.get(`/tournaments/${id}/statistics`);
        return response.data;
      },
      register: async (id: number, data: Record<string, unknown>): Promise<ApiResponse<unknown>> => {
        const response = await this.client.post(`/tournaments/${id}/register`, data);
        return response.data;
      },
      updateStatus: async (id: number, status: string): Promise<ApiResponse<Tournament>> => {
        const response = await this.client.patch(`/tournaments/${id}/status`, { status });
        return response.data;
      },
    },

    matches: {
      list: async (params?: Record<string, unknown>): Promise<ApiResponse<Match[]>> => {
        const response = await this.client.get('/matches', { params });
        return response.data;
      },
      create: async (data: Partial<Match>): Promise<ApiResponse<Match>> => {
        const response = await this.client.post('/matches', data);
        return response.data;
      },
      get: async (id: number): Promise<ApiResponse<Match>> => {
        const response = await this.client.get(`/matches/${id}`);
        return response.data;
      },
      update: async (id: number, data: Partial<Match>): Promise<ApiResponse<Match>> => {
        const response = await this.client.put(`/matches/${id}`, data);
        return response.data;
      },
      delete: async (id: number): Promise<ApiResponse<unknown>> => {
        const response = await this.client.delete(`/matches/${id}`);
        return response.data;
      },
      byTeam: async (teamId: number): Promise<ApiResponse<Match[]>> => {
        const response = await this.client.get(`/matches/team/${teamId}`);
        return response.data;
      },
      updateScore: async (id: number, data: { team1_score: number; team2_score: number }): Promise<ApiResponse<Match>> => {
        const response = await this.client.patch(`/matches/${id}/score`, data);
        return response.data;
      },
    },

    schedules: {
      list: async (params?: Record<string, unknown>): Promise<ApiResponse<Schedule[]>> => {
        const response = await this.client.get('/schedules', { params });
        return response.data;
      },
      create: async (data: Partial<Schedule>): Promise<ApiResponse<Schedule>> => {
        const response = await this.client.post('/schedules', data);
        return response.data;
      },
      get: async (id: number): Promise<ApiResponse<Schedule>> => {
        const response = await this.client.get(`/schedules/${id}`);
        return response.data;
      },
      update: async (id: number, data: Partial<Schedule>): Promise<ApiResponse<Schedule>> => {
        const response = await this.client.put(`/schedules/${id}`, data);
        return response.data;
      },
      delete: async (id: number): Promise<ApiResponse<unknown>> => {
        const response = await this.client.delete(`/schedules/${id}`);
        return response.data;
      },
      bySport: async (sportId: number): Promise<ApiResponse<Schedule[]>> => {
        const response = await this.client.get(`/schedules/sport/${sportId}`);
        return response.data;
      },
      byVenue: async (venueId: number): Promise<ApiResponse<Schedule[]>> => {
        const response = await this.client.get(`/schedules/venue/${venueId}`);
        return response.data;
      },
      byDateRange: async (startDate: string, endDate: string): Promise<ApiResponse<Schedule[]>> => {
        const response = await this.client.get('/schedules/date-range', {
          params: { start_date: startDate, end_date: endDate }
        });
        return response.data;
      },
      updateStatus: async (id: number, status: string): Promise<ApiResponse<Schedule>> => {
        const response = await this.client.patch(`/schedules/${id}/status`, { status });
        return response.data;
      },
    },

    officials: {
      list: async (params?: Record<string, unknown>): Promise<ApiResponse<Official[]>> => {
        const response = await this.client.get('/officials', { params });
        return response.data;
      },
      create: async (data: Partial<Official>): Promise<ApiResponse<Official>> => {
        const response = await this.client.post('/officials', data);
        return response.data;
      },
      get: async (id: number): Promise<ApiResponse<Official>> => {
        const response = await this.client.get(`/officials/${id}`);
        return response.data;
      },
      update: async (id: number, data: Partial<Official>): Promise<ApiResponse<Official>> => {
        const response = await this.client.put(`/officials/${id}`, data);
        return response.data;
      },
      delete: async (id: number): Promise<ApiResponse<unknown>> => {
        const response = await this.client.delete(`/officials/${id}`);
        return response.data;
      },
      bySport: async (sportId: number): Promise<ApiResponse<Official[]>> => {
        const response = await this.client.get(`/officials/sport/${sportId}`);
        return response.data;
      },
      byType: async (type: string): Promise<ApiResponse<Official[]>> => {
        const response = await this.client.get(`/officials/type/${type}`);
        return response.data;
      },
      byCertification: async (level: string): Promise<ApiResponse<Official[]>> => {
        const response = await this.client.get(`/officials/certification/${level}`);
        return response.data;
      },
      available: async (): Promise<ApiResponse<Official[]>> => {
        const response = await this.client.get('/officials/available');
        return response.data;
      },
    },

    results: {
      list: async (params?: Record<string, unknown>): Promise<ApiResponse<Result[]>> => {
        const response = await this.client.get('/results', { params });
        return response.data;
      },
      create: async (data: Partial<Result>): Promise<ApiResponse<Result>> => {
        const response = await this.client.post('/results', data);
        return response.data;
      },
      get: async (id: number): Promise<ApiResponse<Result>> => {
        const response = await this.client.get(`/results/${id}`);
        return response.data;
      },
      update: async (id: number, data: Partial<Result>): Promise<ApiResponse<Result>> => {
        const response = await this.client.put(`/results/${id}`, data);
        return response.data;
      },
      delete: async (id: number): Promise<ApiResponse<unknown>> => {
        const response = await this.client.delete(`/results/${id}`);
        return response.data;
      },
      byTeam: async (teamId: number): Promise<ApiResponse<Result[]>> => {
        const response = await this.client.get(`/results/team/${teamId}`);
        return response.data;
      },
      bySport: async (sportId: number): Promise<ApiResponse<Result[]>> => {
        const response = await this.client.get(`/results/sport/${sportId}`);
        return response.data;
      },
      matchStatistics: async (matchId: number): Promise<ApiResponse<Record<string, unknown>>> => {
        const response = await this.client.get(`/results/match/${matchId}/statistics`);
        return response.data;
      },
      verify: async (id: number): Promise<ApiResponse<Result>> => {
        const response = await this.client.patch(`/results/${id}/verify`);
        return response.data;
      },
      teamPerformance: async (teamId: number): Promise<ApiResponse<Record<string, unknown>>> => {
        const response = await this.client.get(`/results/team/${teamId}/performance`);
        return response.data;
      },
    },

    rankings: {
      list: async (params?: Record<string, unknown>): Promise<ApiResponse<Ranking[]>> => {
        const response = await this.client.get('/rankings', { params });
        return response.data;
      },
      create: async (data: Partial<Ranking>): Promise<ApiResponse<Ranking>> => {
        const response = await this.client.post('/rankings', data);
        return response.data;
      },
      get: async (id: number): Promise<ApiResponse<Ranking>> => {
        const response = await this.client.get(`/rankings/${id}`);
        return response.data;
      },
      update: async (id: number, data: Partial<Ranking>): Promise<ApiResponse<Ranking>> => {
        const response = await this.client.put(`/rankings/${id}`, data);
        return response.data;
      },
      delete: async (id: number): Promise<ApiResponse<unknown>> => {
        const response = await this.client.delete(`/rankings/${id}`);
        return response.data;
      },
      teamRankings: async (sportId: number): Promise<ApiResponse<Ranking[]>> => {
        const response = await this.client.get(`/rankings/sport/${sportId}/teams`);
        return response.data;
      },
      individualRankings: async (sportId: number): Promise<ApiResponse<Ranking[]>> => {
        const response = await this.client.get(`/rankings/sport/${sportId}/individuals`);
        return response.data;
      },
      updateRankings: async (data: Record<string, unknown>): Promise<ApiResponse<unknown>> => {
        const response = await this.client.post('/rankings/update', data);
        return response.data;
      },
      statistics: async (sportId: number): Promise<ApiResponse<Record<string, unknown>>> => {
        const response = await this.client.get(`/rankings/sport/${sportId}/statistics`);
        return response.data;
      },
    },

    medals: {
      list: async (params?: Record<string, unknown>): Promise<ApiResponse<MedalTally[]>> => {
        const response = await this.client.get('/medals', { params });
        return response.data;
      },
      create: async (data: Partial<MedalTally>): Promise<ApiResponse<MedalTally>> => {
        const response = await this.client.post('/medals', data);
        return response.data;
      },
      get: async (id: number): Promise<ApiResponse<MedalTally>> => {
        const response = await this.client.get(`/medals/${id}`);
        return response.data;
      },
      update: async (id: number, data: Partial<MedalTally>): Promise<ApiResponse<MedalTally>> => {
        const response = await this.client.put(`/medals/${id}`, data);
        return response.data;
      },
      delete: async (id: number): Promise<ApiResponse<unknown>> => {
        const response = await this.client.delete(`/medals/${id}`);
        return response.data;
      },
      bySchool: async (schoolId: number): Promise<ApiResponse<MedalTally[]>> => {
        const response = await this.client.get(`/medals/school/${schoolId}`);
        return response.data;
      },
      bySport: async (sportId: number): Promise<ApiResponse<MedalTally[]>> => {
        const response = await this.client.get(`/medals/sport/${sportId}`);
        return response.data;
      },
      topPerformers: async (): Promise<ApiResponse<MedalTally[]>> => {
        const response = await this.client.get('/medals/top-performers');
        return response.data;
      },
    },
  };

  // Admin API Methods (Require admin authentication)
  admin = {
    dashboard: {
      stats: async (): Promise<ApiResponse<{
        totalAthletes: number;
        activeAthletes: number;
        injuredAthletes: number;
        suspendedAthletes: number;
        totalSchools: number;
        activeSchools: number;
        totalTournaments: number;
        activeTournaments: number;
        upcomingTournaments: number;
        totalSchedules: number;
        upcomingEvents: number;
        totalOfficials: number;
        activeOfficials: number;
        totalCoaches: number;
        activeCoaches: number;
        totalTournamentManagers: number;
        activeTournamentManagers: number;
        totalUsers: number;
        verifiedUsers: number;
        pendingRegistrations: number;
        pendingVerifications: number;
      }>> => {
        const response = await this.client.get('/admin/dashboard/stats');
        return response.data;
      },
    },
    schedules: {
      list: async (params?: Record<string, unknown>): Promise<ApiResponse<Schedule[]>> => {
        const response = await this.client.get('/admin/schedules', { params });
        return response.data;
      },
      stats: async (): Promise<ApiResponse<{
        total_schedules: number;
        scheduled_count: number;
        ongoing_count: number;
        completed_count: number;
        cancelled_count: number;
      }>> => {
        const response = await this.client.get('/admin/schedules/stats');
        return response.data;
      },
      create: async (data: Partial<Schedule>): Promise<ApiResponse<Schedule>> => {
        const response = await this.client.post('/admin/schedules', data);
        return response.data;
      },
      get: async (id: number): Promise<ApiResponse<Schedule>> => {
        const response = await this.client.get(`/admin/schedules/${id}`);
        return response.data;
      },
      update: async (id: number, data: Partial<Schedule>): Promise<ApiResponse<Schedule>> => {
        const response = await this.client.put(`/admin/schedules/${id}`, data);
        return response.data;
      },
      delete: async (id: number): Promise<ApiResponse<unknown>> => {
        const response = await this.client.delete(`/admin/schedules/${id}`);
        return response.data;
      },
    },

    profiles: {
      list: async (params?: Record<string, unknown>): Promise<ApiResponse<User[]>> => {
        const response = await this.client.get('/admin/profiles', { params });
        return response.data;
      },
      stats: async (): Promise<ApiResponse<{
        total_profiles: number;
        athletes_count: number;
        coaches_count: number;
        officials_count: number;
        schools_count: number;
      }>> => {
        const response = await this.client.get('/admin/profiles/stats');
        return response.data;
      },
      create: async (data: Partial<User>): Promise<ApiResponse<User>> => {
        const response = await this.client.post('/admin/profiles', data);
        return response.data;
      },
      get: async (id: number): Promise<ApiResponse<User>> => {
        const response = await this.client.get(`/admin/profiles/${id}`);
        return response.data;
      },
      update: async (id: number, data: Partial<User>): Promise<ApiResponse<User>> => {
        const response = await this.client.put(`/admin/profiles/${id}`, data);
        return response.data;
      },
      delete: async (id: number, type: string): Promise<ApiResponse<unknown>> => {
        const response = await this.client.delete(`/admin/profiles/${type}/${id}`);
        return response.data;
      },
    },

    tournaments: {
      list: async (params?: Record<string, unknown>): Promise<ApiResponse<Tournament[]>> => {
        const response = await this.client.get('/admin/tournaments', { params });
        return response.data;
      },
      create: async (data: Partial<Tournament>): Promise<ApiResponse<Tournament>> => {
        const response = await this.client.post('/admin/tournaments', data);
        return response.data;
      },
      get: async (id: number): Promise<ApiResponse<Tournament>> => {
        const response = await this.client.get(`/admin/tournaments/${id}`);
        return response.data;
      },
      update: async (id: number, data: Partial<Tournament>): Promise<ApiResponse<Tournament>> => {
        const response = await this.client.put(`/admin/tournaments/${id}`, data);
        return response.data;
      },
      delete: async (id: number): Promise<ApiResponse<unknown>> => {
        const response = await this.client.delete(`/admin/tournaments/${id}`);
        return response.data;
      },
    },

    sports: {
      list: async (params?: Record<string, unknown>): Promise<ApiResponse<Sport[]>> => {
        const response = await this.client.get('/admin/sports', { params });
        return response.data;
      },
      stats: async (): Promise<ApiResponse<{
        total_sports: number;
        sports_by_category: Record<string, number>;
        sports_by_gender: Record<string, number>;
        active_sports: number;
        inactive_sports: number;
        sports_with_athletes: number;
        sports_with_teams: number;
        sports_with_schedules: number;
        activity_rate: number;
      }>> => {
        const response = await this.client.get('/admin/sports/stats');
        return response.data;
      },
      create: async (data: Partial<Sport>): Promise<ApiResponse<Sport>> => {
        const response = await this.client.post('/admin/sports', data);
        return response.data;
      },
      get: async (id: number): Promise<ApiResponse<Sport>> => {
        const response = await this.client.get(`/admin/sports/${id}`);
        return response.data;
      },
      update: async (id: number, data: Partial<Sport>): Promise<ApiResponse<Sport>> => {
        const response = await this.client.put(`/admin/sports/${id}`, data);
        return response.data;
      },
      delete: async (id: number): Promise<ApiResponse<unknown>> => {
        const response = await this.client.delete(`/admin/sports/${id}`);
        return response.data;
      },
      updateStatus: async (id: number, status: 'active' | 'inactive'): Promise<ApiResponse<Sport>> => {
        const response = await this.client.patch(`/admin/sports/${id}/status`, { status });
        return response.data;
      },
      getByCategory: async (category: string): Promise<ApiResponse<Sport[]>> => {
        const response = await this.client.get(`/admin/sports/category/${category}`);
        return response.data;
      },
    },

    

    venues: {
      list: async (params?: Record<string, unknown>): Promise<ApiResponse<Venue[]>> => {
        const response = await this.client.get('/admin/venues', { params });
        return response.data;
      },
      stats: async (): Promise<ApiResponse<{
        total_venues: number;
        active_venues: number;
        total_capacity: number;
        venues_by_region: Record<string, number>;
        venues_with_facilities: number;
        average_capacity: number;
        utilization_rate: number;
      }>> => {
        const response = await this.client.get('/admin/venues/stats');
        return response.data;
      },
      create: async (data: Partial<Venue>): Promise<ApiResponse<Venue>> => {
        const response = await this.client.post('/admin/venues', data);
        return response.data;
      },
      get: async (id: number): Promise<ApiResponse<Venue>> => {
        const response = await this.client.get(`/admin/venues/${id}`);
        return response.data;
      },
      update: async (id: number, data: Partial<Venue>): Promise<ApiResponse<Venue>> => {
        const response = await this.client.put(`/admin/venues/${id}`, data);
        return response.data;
      },
      delete: async (id: number): Promise<ApiResponse<unknown>> => {
        const response = await this.client.delete(`/admin/venues/${id}`);
        return response.data;
      },
      updateStatus: async (id: number, status: 'active' | 'inactive'): Promise<ApiResponse<Venue>> => {
        const response = await this.client.patch(`/admin/venues/${id}/status`, { status });
        return response.data;
      },
      getByRegion: async (region: string): Promise<ApiResponse<Venue[]>> => {
        const response = await this.client.get(`/admin/venues/region/${region}`);
        return response.data;
      },
    },

  };

  // Utility method for custom requests
  async request<T = unknown>(
    method: 'GET' | 'POST' | 'PUT' | 'DELETE' | 'PATCH', 
    url: string, 
    data?: Record<string, unknown>
  ): Promise<ApiResponse<T>> {
    const response = await this.client.request({
      method,
      url,
      data,
    });
    return response.data;
  }

  // Health check
  async health(): Promise<ApiResponse<{ status: string; database: string; timestamp: string }>> {
    const response = await this.client.get('/health');
    return response.data;
  }
}

// Create singleton instance
export const apiClient = new ApiClient();
export default apiClient;