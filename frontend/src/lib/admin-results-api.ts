import { apiClient, ApiResponse } from './api';

// -----------------------------
// Types
// -----------------------------
export interface Result {
  id: number;
  year: number;
  athlete: string;
  sport: string;
  rank: number;
}

export interface MedalTally {
  id: number;
  school: string;
  gold: number;
  silver: number;
  bronze: number;
  total: number;
}

export interface Ranking {
  id: number;
  school: string;
  points: number;
}

export interface OverallChampion {
  id: number;
  school: string;
  year: number;
}

export interface PrisaaYear {
  id: number;
  year: number;
  theme?: string;
}

// -----------------------------
// API
// -----------------------------
export const adminResultsApi = {
  results: {
    list: (): Promise<ApiResponse<Result[]>> =>
      apiClient.request<Result[]>('GET', '/admin/results'),
    byYear: (year: number): Promise<ApiResponse<Result[]>> =>
      apiClient.request<Result[]>('GET', `/admin/results/${year}`),
  },

  medalTallies: {
    list: (): Promise<ApiResponse<MedalTally[]>> =>
      apiClient.request<MedalTally[]>('GET', '/admin/medal-tallies'),
  },

  rankings: {
    list: (): Promise<ApiResponse<Ranking[]>> =>
      apiClient.request<Ranking[]>('GET', '/admin/rankings'),
  },

  overallChampions: {
    list: (): Promise<ApiResponse<OverallChampion[]>> =>
      apiClient.request<OverallChampion[]>('GET', '/admin/overall-champions'),
  },

  prisaaYears: {
    list: (): Promise<ApiResponse<PrisaaYear[]>> =>
      apiClient.request<PrisaaYear[]>('GET', '/admin/prisaa-years'),
  },
};
