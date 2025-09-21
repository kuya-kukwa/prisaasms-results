import axios from 'axios';
import { apiClient, School, Athlete, Official, ApiResponse } from './api';

export interface BaseProfile {
  id: number;
  created_at: string;
  updated_at: string;
  status: 'active' | 'inactive' | 'pending';
}

export interface AthleteProfile extends BaseProfile {
  type: 'athlete';
  first_name: string;
  last_name: string;
  email: string;
  contact_number?: string;
  gender: 'male' | 'female';
  school_id: number;
  school?: School;
  avatar?: string;
  athlete_number?: string;
  sport?: { id: number; name: string };
}

export interface CoachProfile extends BaseProfile {
  type: 'coach';
  first_name: string;
  last_name: string;
  email: string;
  contact_number?: string;
  school_id: number;
  school?: School;
  avatar?: string;
}

export interface OfficialProfile extends BaseProfile {
  type: 'official';
  first_name: string;
  last_name: string;
  email: string;
  contact_number?: string;
  certification_level: string;
  official_type?: string;
  avatar?: string;
}

export interface TournamentManagerProfile extends BaseProfile {
  type: 'tournament_manager';
  first_name: string;
  last_name: string;
  email: string;
  contact_number?: string;
  avatar?: string;
  school_id?: number;
  school?: School;
}

export interface SchoolProfile extends BaseProfile {
  type: 'school';
  name: string;
  short_name?: string;
  address?: string;
  region?: { id: number; name: string };
  avatar?: string;
  status: 'active' | 'inactive';
}

export type Profile = AthleteProfile | CoachProfile | OfficialProfile | TournamentManagerProfile | SchoolProfile;

export interface ProfileFilters {
  search?: string;
  type?: string;
  status?: string;
  school_id?: number;
}

export interface ProfileSortOptions {
  field: string;
  direction: 'asc' | 'desc';
}

export interface ProfilesResponse {
  data: Profile[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}

export interface ProfileStats {
  total_profiles: number;
  athletes_count: number;
  coaches_count: number;
  officials_count: number;
  tournament_managers_count: number;
  schools_count: number;
}

// Backend profile response interface
interface BackendProfile {
  id: number;
  type: string;
  name?: string;
  short_name?: string;
  address?: string;
  region?: string | { id: number; name: string };
  avatar?: string;
  logo?: string;
  status: string;
  first_name?: string;
  last_name?: string;
  email?: string;
  contact_number?: string;
  athlete_number?: string;
  school?: School;
  sport?: { id: number; name: string };
  school_id?: number;
  certification_level?: string;
  official_type?: string;
  created_at: string;
  updated_at: string;
}

// Helper function to safely extract data array from API response
function extractDataArray(response: { data: unknown }): unknown[] {
  if (!response.data) return [];
  
  if (Array.isArray(response.data)) return response.data;
  
  if (typeof response.data === 'object' && response.data !== null && 'data' in response.data) {
    const data = (response.data as { data?: unknown }).data;
    return Array.isArray(data) ? data : [];
  }
  
  return [];
}

// Helper type used for create/update payloads coming from forms
type ProfileFormData = Partial<Profile> & {
  avatarFile?: File;
  birthdate?: string;
  sport_id?: number;
  athlete_number?: string;
  school_id?: number;
};

// Admin Profiles Service - Connected to real backend
export const adminProfilesService = {
  // Get all profiles with filtering and pagination - Use backend AdminProfilesController
  async getProfiles(filters?: ProfileFilters, sort?: ProfileSortOptions, page = 1, perPage = 20): Promise<ProfilesResponse> {
    try {
      // Use the backend AdminProfilesController endpoint instead of frontend combining
      const queryParams: Record<string, string | number> = {
        page,
        limit: perPage
      };

      if (filters?.type && filters.type !== 'all') {
        queryParams.type = filters.type;
      }

      if (filters?.search) {
        queryParams.search = filters.search;
      }

      // Call the backend AdminProfilesController directly using axios
      const token = typeof window !== 'undefined' ? localStorage.getItem('prisaa_token') : null;
      const axiosResponse = await axios.get(`${process.env.NEXT_PUBLIC_API_BASE_URL}/admin/profiles`, {
        params: queryParams,
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          ...(token ? { Authorization: `Bearer ${token}` } : {})
        }
      });
      const response = axiosResponse.data as ApiResponse<{
        data: BackendProfile[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
      }>;

      if (response.success && response.data) {
        // Transform backend response to match frontend expectations
        const backendData = response.data;
        
        // Transform the profiles to match frontend Profile interface
        const transformedProfiles: Profile[] = backendData.data.map((profile: BackendProfile): Profile => {
          console.log('Transforming profile:', profile.type, profile.id, profile.name || `${profile.first_name} ${profile.last_name}`);
          switch (profile.type) {
            case 'school':
              return {
                id: profile.id,
                type: 'school' as const,
                name: profile.name || '',
                short_name: profile.short_name,
                address: profile.address,
                region: typeof profile.region === 'string' ? undefined : profile.region,
                avatar: profile.avatar || profile.logo,
                status: (profile.status === 'active' || profile.status === 'inactive') ? profile.status : 'active',
                created_at: profile.created_at,
                updated_at: profile.updated_at
              };
            case 'athlete':
              return {
                id: profile.id,
                type: 'athlete' as const,
                first_name: profile.first_name || '',
                last_name: profile.last_name || '',
                email: profile.email || '',
                athlete_number: profile.athlete_number || '',
                status: (profile.status === 'active' || profile.status === 'inactive' || profile.status === 'pending') ? profile.status : 'active',
                school: profile.school,
                sport: profile.sport,
                avatar: profile.avatar,
                gender: 'male' as const,
                school_id: profile.school_id || 0,
                created_at: profile.created_at,
                updated_at: profile.updated_at
              };
            case 'coach':
              return {
                id: profile.id,
                type: 'coach' as const,
                first_name: profile.first_name || '',
                last_name: profile.last_name || '',
                email: profile.email || '',
                contact_number: profile.contact_number,
                school_id: profile.school_id || 0,
                school: profile.school,
                avatar: profile.avatar,
                status: (profile.status === 'active' || profile.status === 'inactive' || profile.status === 'pending') ? profile.status : 'active',
                created_at: profile.created_at,
                updated_at: profile.updated_at
              };
            case 'official':
              return {
                id: profile.id,
                type: 'official' as const,
                first_name: profile.first_name || '',
                last_name: profile.last_name || '',
                email: profile.email || '',
                contact_number: profile.contact_number,
                certification_level: profile.certification_level || '',
                official_type: profile.official_type,
                avatar: profile.avatar,
                status: (profile.status === 'active' || profile.status === 'inactive' || profile.status === 'pending') ? profile.status : 'active',
                created_at: profile.created_at,
                updated_at: profile.updated_at
              };
            case 'tournament_manager':
              return {
                id: profile.id,
                type: 'tournament_manager' as const,
                first_name: profile.first_name || '',
                last_name: profile.last_name || '',
                email: profile.email || '',
                contact_number: profile.contact_number,
                avatar: profile.avatar,
                school: profile.school,
                status: (profile.status === 'active' || profile.status === 'inactive' || profile.status === 'pending') ? profile.status : 'active',
                created_at: profile.created_at,
                updated_at: profile.updated_at
              };
            default:
              return profile as Profile;
          }
        });

        console.log('Transformed profiles count:', transformedProfiles.length);
        console.log('Transformed profile types:', transformedProfiles.map(p => p.type));

        return {
          data: transformedProfiles,
          meta: {
            current_page: backendData.current_page || page,
            last_page: backendData.last_page || 1,
            per_page: backendData.per_page || perPage,
            total: backendData.total || transformedProfiles.length
          }
        };
      }

      return {
        data: [],
        meta: {
          current_page: 1,
          last_page: 1,
          per_page: perPage,
          total: 0
        }
      };
    } catch (error) {
      console.error('Error fetching profiles from backend:', error);
      console.warn('Falling back to frontend combining logic...');
      return this.getProfilesFallback(filters, sort, page, perPage);
    }
  },

  // Fallback method using frontend combining (original implementation)
  async getProfilesFallback(filters?: ProfileFilters, sort?: ProfileSortOptions, page = 1, perPage = 20): Promise<ProfilesResponse> {
    try {
      const allProfiles: Profile[] = [];
      let totalCount = 0;

      // Fetch data from different endpoints based on filters
      const profileTypes = filters?.type ? [filters.type] : ['athlete', 'coach', 'official', 'tournament_manager', 'school'];

      // Make all API calls in parallel instead of sequential
      const apiCalls = profileTypes.map(async (type) => {
        try {
          let profiles: Profile[] = [];

          switch (type) {
            case 'athlete': {
              const response = await apiClient.protected.athletes.list({
                search: filters?.search,
                school_id: filters?.school_id,
                status: filters?.status,
                page,
                per_page: perPage
              });

              // Handle both direct array and paginated response
              const athletesData = extractDataArray(response) as Athlete[];
              profiles = athletesData.map((athlete) => ({
                ...athlete,
                type: 'athlete' as const,
                email: `${athlete.first_name}.${athlete.last_name}@student.com`
              }));
              break;
            }

            case 'coach': {
              const response = await apiClient.protected.users.byRole('coach');
              profiles = response.data
                .filter(user => {
                  if (filters?.search) {
                    const searchTerm = filters.search.toLowerCase();
                    return user.first_name.toLowerCase().includes(searchTerm) ||
                           user.last_name.toLowerCase().includes(searchTerm) ||
                           user.email.toLowerCase().includes(searchTerm);
                  }
                  return true;
                })
                .filter(user => filters?.school_id ? user.school_id === filters.school_id : true)
                .map(user => ({
                  id: user.id,
                  type: 'coach' as const,
                  first_name: user.first_name,
                  last_name: user.last_name,
                  email: user.email,
                  contact_number: user.contact_number,
                  school_id: user.school_id || 0,
                  status: 'active' as const,
                  created_at: user.created_at,
                  updated_at: user.updated_at
                }));
              break;
            }

            case 'official': {
              const response = await apiClient.protected.officials.list({
                search: filters?.search,
                status: filters?.status
              });

              // Handle both direct array and paginated response
              const officialsData = extractDataArray(response) as Official[];
              profiles = officialsData.map((official) => ({
                ...official,
                type: 'official' as const,
                email: `${official.first_name}.${official.last_name}@official.com`
              }));
              break;
            }

            case 'tournament_manager': {
              const response = await apiClient.protected.users.byRole('tournament_manager');
              profiles = response.data
                .filter(user => {
                  if (filters?.search) {
                    const searchTerm = filters.search.toLowerCase();
                    return user.first_name.toLowerCase().includes(searchTerm) ||
                           user.last_name.toLowerCase().includes(searchTerm) ||
                           user.email.toLowerCase().includes(searchTerm);
                  }
                  return true;
                })
                .map(user => ({
                  id: user.id,
                  type: 'tournament_manager' as const,
                  first_name: user.first_name,
                  last_name: user.last_name,
                  email: user.email,
                  contact_number: user.contact_number,
                  status: 'active' as const,
                  created_at: user.created_at,
                  updated_at: user.updated_at
                }));
              break;
            }

            case 'school': {
              const response = await apiClient.protected.schools.list();

              // Handle both direct array and paginated response
              const schoolsData = extractDataArray(response) as School[];
              profiles = schoolsData.map((school) => ({
                id: school.id,
                type: 'school' as const,
                name: school.name,
                short_name: school.short_name,
                address: school.address,
                region: typeof school.region === 'string' ? undefined : school.region,
                avatar: school.logo,
                status: (school.status === 'active' || school.status === 'inactive') ? school.status : 'active',
                created_at: school.created_at,
                updated_at: school.updated_at
              }));
              break;
            }
          }

          return { profiles, count: profiles.length };
        } catch (error) {
          console.warn(`Failed to fetch ${type} profiles:`, error);
          return { profiles: [], count: 0 };
        }
      });

      // Wait for all API calls to complete in parallel
      const results = await Promise.all(apiCalls);

      // Combine results
      results.forEach(result => {
        allProfiles.push(...result.profiles);
        totalCount += result.count;
      });

      // Apply sorting if specified
      if (sort) {
        allProfiles.sort((a, b) => {
          let aValue: string | number;
          let bValue: string | number;

          if (sort.field === 'name') {
            aValue = 'name' in a ? a.name : `${a.first_name} ${a.last_name}`;
            bValue = 'name' in b ? b.name : `${b.first_name} ${b.last_name}`;
          } else if (sort.field === 'created_at') {
            aValue = new Date(a.created_at).getTime();
            bValue = new Date(b.created_at).getTime();
          } else {
            aValue = (a as unknown as Record<string, unknown>)[sort.field] as string || '';
            bValue = (b as unknown as Record<string, unknown>)[sort.field] as string || '';
          }

          if (sort.direction === 'desc') {
            return aValue < bValue ? 1 : -1;
          }
          return aValue > bValue ? 1 : -1;
        });
      }

      // Handle pagination
      const startIndex = (page - 1) * perPage;
      const endIndex = startIndex + perPage;
      const paginatedProfiles = allProfiles.slice(startIndex, endIndex);

      return {
        data: paginatedProfiles,
        meta: {
          current_page: page,
          last_page: Math.ceil(totalCount / perPage),
          per_page: perPage,
          total: totalCount
        }
      };
    } catch (error) {
      console.error('Error fetching profiles:', error);
      return {
        data: [],
        meta: {
          current_page: 1,
          last_page: 1,
          per_page: perPage,
          total: 0
        }
      };
    }
  },

  // Get profile statistics
  async getProfileStats(): Promise<ProfileStats> {
    try {
      const [athletesResponse, coachesResponse, officialsResponse, tournamentManagersResponse, schoolsResponse] = await Promise.allSettled([
        apiClient.protected.athletes.list({ per_page: 1 }),
        apiClient.protected.users.byRole('coach'),
        apiClient.protected.officials.list({ per_page: 1 }),
        apiClient.protected.users.byRole('tournament_manager'),
        apiClient.protected.schools.list({ per_page: 1 })
      ]);

      let athletes_count = 0;
      let coaches_count = 0;
      let officials_count = 0;
      let tournament_managers_count = 0;
      let schools_count = 0;

        if (athletesResponse.status === 'fulfilled') {
          const response = athletesResponse.value;
          const data = extractDataArray(response);
          athletes_count = data.length;
        }

        if (coachesResponse.status === 'fulfilled') {
          const response = coachesResponse.value;
          const data = extractDataArray(response);
          coaches_count = data.length;
        }

        if (officialsResponse.status === 'fulfilled') {
          const response = officialsResponse.value;
          const data = extractDataArray(response);
          officials_count = data.length;
        }

        if (tournamentManagersResponse.status === 'fulfilled') {
          const response = tournamentManagersResponse.value;
          const data = extractDataArray(response);
          tournament_managers_count = data.length;
        }

        if (schoolsResponse.status === 'fulfilled') {
          const response = schoolsResponse.value;
          const data = extractDataArray(response);
          schools_count = data.length;
        }

        const total_profiles = [athletes_count, coaches_count, officials_count, tournament_managers_count, schools_count]
          .map(n => typeof n === 'number' && !isNaN(n) ? n : 0)
          .reduce((a, b) => a + b, 0);

      return {
        total_profiles,
        athletes_count,
        coaches_count,
        officials_count,
        tournament_managers_count,
        schools_count
      };
    } catch (error) {
      console.error('Error fetching profile stats:', error);
      return {
        total_profiles: 0,
        athletes_count: 0,
        coaches_count: 0,
        officials_count: 0,
        tournament_managers_count: 0,
        schools_count: 0
      };
    }
  },

  // Get single profile by type and ID
  async getProfile(type: string, id: number): Promise<Profile | null> {
    try {
      switch (type) {
        case 'athlete': {
          const response = await apiClient.protected.athletes.get(id);
          return {
            ...response.data,
            type: 'athlete' as const,
            email: `${response.data.first_name}.${response.data.last_name}@student.com`
          };
        }

        case 'coach': {
          const response = await apiClient.protected.users.get(id);
          if (response.data.role !== 'coach') return null;
          return {
            id: response.data.id,
            type: 'coach' as const,
            first_name: response.data.first_name,
            last_name: response.data.last_name,
            email: response.data.email,
            contact_number: response.data.contact_number,
            school_id: response.data.school_id || 0,
            status: 'active' as const,
            created_at: response.data.created_at,
            updated_at: response.data.updated_at
          };
        }

        case 'official': {
          const response = await apiClient.protected.officials.get(id);
          return {
            ...response.data,
            type: 'official' as const,
            email: `${response.data.first_name}.${response.data.last_name}@official.com`
          };
        }

        case 'tournament_manager': {
          const response = await apiClient.protected.users.get(id);
          if (response.data.role !== 'tournament_manager') return null;
          return {
            id: response.data.id,
            type: 'tournament_manager' as const,
            first_name: response.data.first_name,
            last_name: response.data.last_name,
            email: response.data.email,
            contact_number: response.data.contact_number,
            status: 'active' as const,
            created_at: response.data.created_at,
            updated_at: response.data.updated_at
          };
        }

        case 'school': {
          const response = await apiClient.protected.schools.get(id);
          return {
            id: response.data.id,
            type: 'school' as const,
            name: response.data.name,
            short_name: response.data.short_name,
            address: response.data.address,
            region: typeof response.data.region === 'string' ? undefined : response.data.region,
            avatar: response.data.logo, // Backend uses 'logo', frontend uses 'avatar'
            status: (response.data.status === 'active' || response.data.status === 'inactive') ? response.data.status : 'active',
            created_at: response.data.created_at,
            updated_at: response.data.updated_at
          };
        }

        default:
          return null;
      }
    } catch (error) {
      console.error(`Error fetching ${type} profile:`, error);
      return null;
    }
  },

  async uploadAvatar(file?: File): Promise<string | undefined> {
    if (!file) return undefined;
    if (typeof window === 'undefined') return undefined;

    try {
      const form = new FormData();
      form.append('avatar', file);

      const token = typeof window !== 'undefined' ? localStorage.getItem('prisaa_token') : null;
      const res = await fetch(`${process.env.NEXT_PUBLIC_API_BASE_URL}/uploads`, {
        method: 'POST',
        headers: token ? { Authorization: `Bearer ${token}` } : undefined,
        body: form
      });

      const payload = await res.json();
      // Try to find a URL in common shapes
      return payload?.data?.url || payload?.data?.path || payload?.url || undefined;
    } catch (err) {
      console.warn('Avatar upload failed:', err);
      return undefined;
    }
  },

  // Create new profile
  async createProfile(type: string, data: Partial<Profile> & { avatarFile?: File }): Promise<Profile | null> {
    try {
      // Normalize typed input to avoid casting to any
      const payloadData = data as ProfileFormData;
      // If an avatar file is provided, upload first and set avatar url
      let avatarUrl: string | undefined;
      if (payloadData.avatarFile) {
        avatarUrl = await this.uploadAvatar(payloadData.avatarFile);
      }

      switch (type) {
        case 'athlete': {
          const athleteData = payloadData as Partial<AthleteProfile> & { birthdate?: string; sport_id?: number; athlete_number?: string };
          const payload: Record<string, unknown> = {
            first_name: athleteData.first_name!,
            last_name: athleteData.last_name!,
            gender: athleteData.gender!,
            school_id: athleteData.school_id!,
            birthdate: athleteData.birthdate,
            sport_id: athleteData.sport_id,
            athlete_number: athleteData.athlete_number,
            status: athleteData.status || 'active'
          };
          if (avatarUrl) payload.avatar = avatarUrl;

          const response = await apiClient.protected.athletes.create(payload);
          return {
            ...response.data,
            type: 'athlete' as const,
            email: athleteData.email || `${athleteData.first_name}.${athleteData.last_name}@student.com`
          };
        }

        case 'official': {
          const officialData = payloadData as Partial<OfficialProfile> & {
            gender?: string;
            birthdate?: string;
            sports_certified?: string[];
            years_experience?: number;
            available_for_assignment?: boolean;
            availability_schedule?: string[];
          };
          const payload: Record<string, unknown> = {
            first_name: officialData.first_name!,
            last_name: officialData.last_name!,
            gender: officialData.gender!,
            birthdate: officialData.birthdate,
            contact_number: officialData.contact_number!,
            email: officialData.email,
            certification_level: officialData.certification_level!,
            official_type: officialData.official_type!,
            sport_id: 1, // Default sport_id - this might need to be updated based on sports_certified
            sports_certified: officialData.sports_certified || [],
            years_experience: officialData.years_experience || 0,
            status: officialData.status || 'active',
            available_for_assignment: officialData.available_for_assignment || false,
            availability_schedule: officialData.availability_schedule || []
          };
          if (avatarUrl) payload.avatar = avatarUrl;

          const response = await apiClient.protected.officials.create(payload);
          return {
            ...response.data,
            type: 'official' as const,
            email: officialData.email || `${officialData.first_name}.${officialData.last_name}@official.com`
          };
        }

        case 'school': {
          const schoolData = payloadData as Partial<SchoolProfile>;
          const payload: Record<string, unknown> = {
            name: schoolData.name!,
            short_name: schoolData.short_name,
            address: schoolData.address,
            region: schoolData.region,
            status: schoolData.status || 'active'
          };
          if (avatarUrl) payload.avatar = avatarUrl;

          const response = await apiClient.protected.schools.create(payload);
          return {
            id: response.data.id,
            type: 'school' as const,
            name: response.data.name,
            short_name: schoolData.short_name || response.data.short_name,
            address: schoolData.address || response.data.address,
            region: typeof schoolData.region === 'string' ? undefined : schoolData.region,
            status: schoolData.status || 'active' as const,
            avatar: avatarUrl,
            created_at: response.data.created_at,
            updated_at: response.data.updated_at
          };
        }

        case 'coach':
        case 'tournament_manager': {
          // Create via users endpoint
          const u = payloadData as Partial<CoachProfile> | Partial<TournamentManagerProfile> & { school_id?: number };
          const payload: Record<string, unknown> = {
            first_name: u.first_name!,
            last_name: u.last_name!,
            email: u.email!,
            contact_number: u.contact_number,
            role: type === 'coach' ? 'coach' : 'tournament_manager',
            school_id: u.school_id
          };
          // Forward password fields if provided (admin may set initial password)
          const uRec = u as unknown as Record<string, unknown>;
          if (typeof uRec.password === 'string') payload.password = uRec.password as string;
          if (typeof uRec.password_confirmation === 'string') payload.password_confirmation = uRec.password_confirmation as string;
          if (avatarUrl) payload.avatar = avatarUrl;

          const requestPayload = { ...payload } as Record<string, unknown>;
          const response = await apiClient.protected.users.create(requestPayload);

          try {
            if ('password' in requestPayload) requestPayload.password = undefined;
            if ('password_confirmation' in requestPayload) requestPayload.password_confirmation = undefined;
          } catch {
          }
          return {
            id: response.data.id,
            type: (type === 'coach' ? 'coach' : 'tournament_manager'),
            first_name: response.data.first_name,
            last_name: response.data.last_name,
            email: response.data.email,
            contact_number: response.data.contact_number,
            school_id: response.data.school_id || 0,
            status: 'active' as const,
            created_at: response.data.created_at,
            updated_at: response.data.updated_at
          };
        }

        default:
          throw new Error(`Profile type ${type} cannot be created directly`);
      }
    } catch (error) {
      console.error(`Error creating ${type} profile:`, error);
      return null;
    }
  },

  // Update existing profile
  async updateProfile(type: string, id: number, data: Partial<Profile> & { avatarFile?: File }): Promise<Profile | null> {
    try {
      // Normalize typed input and handle avatar upload if present
      const payloadData = data as ProfileFormData;
      let avatarUrl: string | undefined;
      if (payloadData.avatarFile) {
        avatarUrl = await this.uploadAvatar(payloadData.avatarFile);
      }

      switch (type) {
        case 'athlete': {
          const athleteData = payloadData as Partial<AthleteProfile> & { birthdate?: string; sport_id?: number; athlete_number?: string };
          // Filter out profile-specific fields that don't exist in Athlete
          const updateData: Record<string, unknown> = {
            first_name: athleteData.first_name,
            last_name: athleteData.last_name,
            email: athleteData.email,
            gender: athleteData.gender,
            school_id: athleteData.school_id,
            status: athleteData.status === 'pending' ? 'active' : athleteData.status,
            birthdate: athleteData.birthdate,
            sport_id: athleteData.sport_id,
            athlete_number: athleteData.athlete_number
          };
          if (avatarUrl) updateData.avatar = avatarUrl;
          const response = await apiClient.protected.athletes.update(id, updateData);
          return {
            ...response.data,
            type: 'athlete' as const,
            email: athleteData.email || `${response.data.first_name}.${response.data.last_name}@student.com`
          };
        }

        case 'official': {
          const officialData = data as Partial<OfficialProfile>;
          // Filter out profile-specific fields that don't exist in Official
          const updateData: Record<string, unknown> = {
            first_name: officialData.first_name,
            last_name: officialData.last_name,
            certification_level: officialData.certification_level,
            contact_number: officialData.contact_number,
            status: officialData.status === 'pending' ? 'active' : officialData.status
          };
          if (avatarUrl) updateData.avatar = avatarUrl;
          const response = await apiClient.protected.officials.update(id, updateData);
          return {
            ...response.data,
            type: 'official' as const,
            email: `${response.data.first_name}.${response.data.last_name}@official.com`
          };
        }

        case 'school': {
          const payload: Record<string, unknown> = { ...(data as SchoolProfile) };
          if (avatarUrl) payload.avatar = avatarUrl;
          const response = await apiClient.protected.schools.update(id, payload);
          const responseData = response.data as School & Record<string, unknown>;
          return {
            id: response.data.id,
            type: 'school' as const,
            name: response.data.name,
            short_name: (data as SchoolProfile).short_name || (responseData.short_name as string),
            address: (data as SchoolProfile).address || (responseData.address as string),
            region: (data as SchoolProfile).region || (responseData.region as { id: number; name: string }),
            avatar: avatarUrl,
            status: (data as SchoolProfile).status || 'active' as const,
            created_at: response.data.created_at,
            updated_at: response.data.updated_at
          };
        }

        case 'coach':
        case 'tournament_manager': {
          const userData = payloadData as Partial<CoachProfile> | Partial<TournamentManagerProfile> & { school_id?: number };
          const payload: Record<string, unknown> = {
            first_name: userData.first_name,
            last_name: userData.last_name,
            email: userData.email,
            contact_number: userData.contact_number,
            school_id: userData.school_id
          };
          if (avatarUrl) payload.avatar = avatarUrl;
          const response = await apiClient.protected.users.update(id, payload);
          return {
            id: response.data.id,
            type: (type === 'coach' ? 'coach' : 'tournament_manager'),
            first_name: response.data.first_name,
            last_name: response.data.last_name,
            email: response.data.email,
            contact_number: response.data.contact_number,
            school_id: response.data.school_id || 0,
            status: 'active' as const,
            created_at: response.data.created_at,
            updated_at: response.data.updated_at
          };
        }

        default:
          throw new Error(`Profile type ${type} cannot be updated directly`);
      }
    } catch (error) {
      console.error(`Error updating ${type} profile:`, error);
      return null;
    }
  },

  // Delete profile
  async deleteProfile(type: string, id: number): Promise<boolean> {
    try {
      switch (type) {
        case 'athlete': {
          await apiClient.protected.athletes.delete(id);
          return true;
        }

        case 'official': {
          await apiClient.protected.officials.delete(id);
          return true;
        }

        case 'school': {
          await apiClient.protected.schools.delete(id);
          return true;
        }

        case 'coach':
        case 'tournament_manager': {
          await apiClient.protected.users.delete(id);
          return true;
        }

        default:
          throw new Error(`Profile type ${type} cannot be deleted directly`);
      }
    } catch (error) {
      console.error(`Error deleting ${type} profile:`, error);
      return false;
    }
  },

  // Get schools for dropdowns
  async getSchools(): Promise<Array<{ id: number; name: string }>> {
    try {
      const response = await apiClient.public.schools.list();
      
      // Handle direct array response
      if (Array.isArray(response.data)) {
        return response.data.map(school => ({
          id: school.id,
          name: school.name
        }));
      }
      
      // Handle nested data response
      if (response.data && typeof response.data === 'object' && 'data' in response.data) {
        const nestedData = (response.data as { data: Array<{ id: number; name: string }> }).data || [];
        return nestedData.map(school => ({
          id: school.id,
          name: school.name
        }));
      }
      
      return [];
    } catch (error) {
      console.error('Error fetching schools:', error);
      return [];
    }
  }
};