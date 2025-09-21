import { apiClient } from './api';

export interface Schedule {
  id: number;
  title: string;
  description?: string;
  event_date: string;
  start_time: string;
  end_time: string;
  venue?: string;
  sport_id?: number;
  tournament_id?: number;
  status: 'scheduled' | 'ongoing' | 'completed' | 'cancelled';
  created_at: string;
  updated_at: string;
}

export interface ScheduleFilters {
  search?: string;
  status?: string;
  sport_id?: number;
  tournament_id?: number;
  date_from?: string;
  date_to?: string;
}

export interface ScheduleStats {
  total_schedules: number;
  scheduled_count: number;
  ongoing_count: number;
  completed_count: number;
  cancelled_count: number;
}

export const adminSchedulesService = {
  // Get all schedules with pagination and filters
  async getSchedules(
    filters: ScheduleFilters = {},
    page: number = 1,
    perPage: number = 20
  ): Promise<{ data: Schedule[]; meta: { total: number; last_page: number } }> {
    const params = new URLSearchParams({
      page: page.toString(),
      per_page: perPage.toString(),
      ...Object.fromEntries(
        Object.entries(filters).filter(([, value]) => value !== undefined && value !== '')
      )
    });

  const response = await apiClient.request<{ data: Schedule[]; meta: { total: number; last_page: number } }>('GET', `/admin/schedules?${params}`);
  return response.data;
  },

  // Get schedule statistics
  async getStats(): Promise<ScheduleStats> {
  const response = await apiClient.request<ScheduleStats>('GET', '/admin/schedules/stats');
  return response.data;
  },

  // Get single schedule
  async getSchedule(id: number): Promise<Schedule> {
  const response = await apiClient.request<Schedule>('GET', `/admin/schedules/${id}`);
  return response.data;
  },

  // Create new schedule
  async createSchedule(scheduleData: Omit<Schedule, 'id' | 'created_at' | 'updated_at'>): Promise<Schedule> {
  const response = await apiClient.request<Schedule>('POST', '/admin/schedules', scheduleData as unknown as Record<string, unknown>);
  return response.data;
  },

  // Update schedule
  async updateSchedule(id: number, scheduleData: Partial<Schedule>): Promise<Schedule> {
  const response = await apiClient.request<Schedule>('PUT', `/admin/schedules/${id}`, scheduleData as unknown as Record<string, unknown>);
  return response.data;
  },

  // Delete schedule
  async deleteSchedule(id: number): Promise<void> {
  await apiClient.request('DELETE', `/admin/schedules/${id}`);
  },

  // Get sports for filter dropdown
  async getSports(): Promise<Array<{ id: number; name: string }>> {
  const response = await apiClient.request<Array<{ id: number; name: string }>>('GET', '/public/sports');
  return Array.isArray(response.data) ? response.data : ((response.data as unknown) as { data?: Array<{ id: number; name: string }> }).data || [];
  },

  // Get tournaments for filter dropdown
  async getTournaments(): Promise<Array<{ id: number; name: string }>> {
  const response = await apiClient.request<Array<{ id: number; name: string }>>('GET', '/admin/tournaments');
  return Array.isArray(response.data) ? response.data : ((response.data as unknown) as { data?: Array<{ id: number; name: string }> }).data || [];
  }
};