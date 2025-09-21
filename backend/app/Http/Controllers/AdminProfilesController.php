<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Athlete;
use App\Models\User;
use App\Models\Official;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminProfilesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            // Check if user has permission to view at least one type of profile
            $user = $request->user();
            $hasPermission = $user->hasPermissionTo('view-users') || 
                           $user->hasPermissionTo('view-athletes') || 
                           $user->hasPermissionTo('view-officials') || 
                           $user->hasPermissionTo('view-schools');
            
            if (!$hasPermission) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view profiles'
                ], 403);
            }

            $type = $request->query('type', 'all');
            $search = $request->query('search');
            $page = (int) $request->query('page', 1);
            $limit = (int) $request->query('limit', 20);

            // Debug logging
            Log::info('AdminProfilesController: Request params', [
                'type' => $type,
                'search' => $search,
                'page' => $page,
                'limit' => $limit
            ]);

            if ($type === 'all') {
                // For 'all' type, we need to combine results from different tables
                // Use a more efficient approach with proper ordering
                return $this->getAllProfilesPaginated($search, $page, $limit);
            }

            // Handle specific profile types
            switch ($type) {
                case 'school':
                    return $this->getSchoolsPaginated($search, $page, $limit);
                case 'athlete':
                    return $this->getAthletesPaginated($search, $page, $limit);
                case 'coach':
                    return $this->getUsersByRolePaginated('coach', $search, $page, $limit);
                case 'official':
                    return $this->getUsersByRolePaginated('official', $search, $page, $limit);
                case 'tournament_manager':
                    return $this->getUsersByRolePaginated('tournament_manager', $search, $page, $limit);
                default:
                    return $this->getAllProfilesPaginated($search, $page, $limit);
            }
        } catch (\Exception $e) {
            Log::error('AdminProfilesController error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch profiles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getAllProfilesPaginated($search, $page, $limit): JsonResponse
    {
        try {
            // Collect all profiles from different tables
            $allProfiles = [];

            // Get schools
            try {
                $schools = School::when($search, function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('short_name', 'like', "%{$search}%");
                })->get()->map(function($school) {
                    return (object) [
                        'id' => $school->id,
                        'type' => 'school',
                        'avatar' => $school->logo,
                        'logo' => $school->logo,
                        'name' => $school->name,
                        'short_name' => $school->short_name,
                        'address' => $school->address ?? null,
                        'region' => $school->region ?? null,
                        'first_name' => null,
                        'last_name' => null,
                        'email' => null,
                        'contact_number' => null,
                        'athlete_number' => null,
                        'school_id' => null,
                        'certification_level' => null,
                        'official_type' => null,
                        'sports_certified' => null,
                        'years_experience' => null,
                        'availability_schedule' => null,
                        'department' => null,
                        'experience_years' => null,
                        'managed_tournaments' => null,
                        'status' => $school->status,
                        'created_at' => $school->created_at,
                        'updated_at' => $school->updated_at
                    ];
                });
                $allProfiles = array_merge($allProfiles, $schools->toArray());
                Log::info('Schools loaded: ' . $schools->count());
            } catch (\Exception $e) {
                Log::error('Error loading schools: ' . $e->getMessage());
            }

            // Get athletes (without relationships to avoid potential issues)
            try {
                $athletes = Athlete::when($search, function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('athlete_number', 'like', "%{$search}%");
                })->get()->map(function($athlete) {
                    return (object) [
                        'id' => $athlete->id,
                        'type' => 'athlete',
                        'avatar' => $athlete->avatar,
                        'logo' => null,
                        'name' => null,
                        'short_name' => null,
                        'first_name' => $athlete->first_name,
                        'last_name' => $athlete->last_name,
                        'email' => null,
                        'contact_number' => null,
                        'athlete_number' => $athlete->athlete_number,
                        'school_id' => $athlete->school_id,
                        'certification_level' => null,
                        'official_type' => null,
                        'sports_certified' => null,
                        'years_experience' => null,
                        'availability_schedule' => null,
                        'department' => null,
                        'experience_years' => null,
                        'managed_tournaments' => null,
                        'status' => $athlete->status,
                        'created_at' => $athlete->created_at,
                        'updated_at' => $athlete->updated_at
                    ];
                });
                $allProfiles = array_merge($allProfiles, $athletes->toArray());
                Log::info('Athletes loaded: ' . $athletes->count());
            } catch (\Exception $e) {
                Log::error('Error loading athletes: ' . $e->getMessage());
            }

            // Get coaches
            try {
                $coaches = User::with('school')->where('role', 'coach')
                    ->when($search, function($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    })->get()->map(function($coach) {
                        return (object) [
                            'id' => $coach->id,
                            'type' => 'coach',
                            'avatar' => $coach->avatar,
                            'logo' => null,
                            'name' => null,
                            'short_name' => null,
                            'first_name' => $coach->first_name,
                            'last_name' => $coach->last_name,
                            'email' => $coach->email,
                            'contact_number' => $coach->contact_number,
                            'athlete_number' => null,
                            'school_id' => $coach->school_id,
                            'school' => $coach->school ? (object) [
                                'id' => $coach->school->id,
                                'name' => $coach->school->name,
                                'short_name' => $coach->school->short_name,
                            ] : null,
                            'certification_level' => null,
                            'official_type' => null,
                            'sports_certified' => null,
                            'years_experience' => null,
                            'availability_schedule' => null,
                            'department' => null,
                            'experience_years' => null,
                            'managed_tournaments' => null,
                            'status' => 'active',
                            'created_at' => $coach->created_at,
                            'updated_at' => $coach->updated_at
                        ];
                    });
                $allProfiles = array_merge($allProfiles, $coaches->toArray());
                Log::info('Coaches loaded: ' . $coaches->count());
            } catch (\Exception $e) {
                Log::error('Error loading coaches: ' . $e->getMessage());
            }

            // Get officials
            try {
                $officials = Official::when($search, function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->get()->map(function($official) {
                    return (object) [
                        'id' => $official->id,
                        'type' => 'official',
                        'avatar' => $official->avatar,
                        'logo' => null,
                        'name' => null,
                        'short_name' => null,
                        'first_name' => $official->first_name,
                        'last_name' => $official->last_name,
                        'email' => $official->email,
                        'contact_number' => $official->contact_number,
                        'athlete_number' => null,
                        'school_id' => null,
                        'certification_level' => $official->certification_level,
                        'official_type' => $official->official_type,
                        'sports_certified' => $official->sports_certified,
                        'years_experience' => $official->years_experience,
                        'availability_schedule' => $official->availability_schedule,
                        'department' => null,
                        'experience_years' => null,
                        'managed_tournaments' => null,
                        'status' => $official->status,
                        'created_at' => $official->created_at,
                        'updated_at' => $official->updated_at
                    ];
                });
                $allProfiles = array_merge($allProfiles, $officials->toArray());
                Log::info('Officials loaded: ' . $officials->count());
            } catch (\Exception $e) {
                Log::error('Error loading officials: ' . $e->getMessage());
            }

            // Get tournament managers
            try {
                                $tournamentManagers = User::with('school')->where('role', 'tournament_manager')
                    ->when($search, function($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    })->get()->map(function($manager) {
                        return (object) [
                            'id' => $manager->id,
                            'type' => 'tournament_manager',
                            'avatar' => $manager->avatar,
                            'logo' => null,
                            'name' => null,
                            'short_name' => null,
                            'first_name' => $manager->first_name,
                            'last_name' => $manager->last_name,
                            'email' => $manager->email,
                            'contact_number' => $manager->contact_number,
                            'athlete_number' => null,
                            'school_id' => $manager->school_id,
                            'school' => $manager->school ? (object) [
                                'id' => $manager->school->id,
                                'name' => $manager->school->name,
                                'short_name' => $manager->school->short_name,
                            ] : null,
                            'certification_level' => null,
                            'official_type' => null,
                            'sports_certified' => null,
                            'years_experience' => null,
                            'availability_schedule' => null,
                            'department' => null,
                            'experience_years' => null,
                            'managed_tournaments' => null,
                            'status' => 'active',
                            'created_at' => $manager->created_at,
                            'updated_at' => $manager->updated_at
                        ];
                    });
                $allProfiles = array_merge($allProfiles, $tournamentManagers->toArray());
                Log::info('Tournament managers loaded: ' . $tournamentManagers->count());
            } catch (\Exception $e) {
                Log::error('Error loading tournament managers: ' . $e->getMessage());
            }            // Sort by created_at desc
            usort($allProfiles, function($a, $b) {
                return strtotime($b->created_at) - strtotime($a->created_at);
            });

            // Get total count
            $totalCount = count($allProfiles);

            // Apply pagination
            $offset = ($page - 1) * $limit;
            $paginatedProfiles = array_slice($allProfiles, $offset, $limit);

            $totalPages = ceil($totalCount / $limit);

            Log::info('PHP collection method - Total profiles: ' . $totalCount);
            Log::info('PHP collection method - Profile types: ' . json_encode(array_column($paginatedProfiles, 'type')));

            return response()->json([
                'success' => true,
                'data' => [
                    'data' => $paginatedProfiles,
                    'current_page' => $page,
                    'last_page' => $totalPages,
                    'per_page' => $limit,
                    'total' => $totalCount
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getAllProfilesPaginated: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch profiles: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getSchoolsPaginated($search, $page, $limit): JsonResponse
    {
        $query = School::when($search, function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('short_name', 'like', "%{$search}%");
        });

        $schools = $query->paginate($limit, ['*'], 'page', $page);

        $transformedSchools = $schools->getCollection()->map(function($school) {
            return (object) [
                'id' => $school->id,
                'type' => 'school',
                'avatar' => $school->logo,
                'logo' => $school->logo,
                'name' => $school->name,
                'short_name' => $school->short_name,
                'address' => $school->address ?? null,
                'region' => $school->region ?? null,
                'status' => $school->status,
                'created_at' => $school->created_at,
                'updated_at' => $school->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'data' => $transformedSchools,
                'current_page' => $schools->currentPage(),
                'last_page' => $schools->lastPage(),
                'per_page' => $schools->perPage(),
                'total' => $schools->total()
            ]
        ]);
    }

    private function getAthletesPaginated($search, $page, $limit): JsonResponse
    {
        $query = Athlete::with(['school', 'sport'])
            ->when($search, function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('athlete_number', 'like', "%{$search}%");
            });

        $athletes = $query->paginate($limit, ['*'], 'page', $page);

        $transformedAthletes = $athletes->getCollection()->map(function($athlete) {
            return (object) [
                'id' => $athlete->id,
                'type' => 'athlete',
                'avatar' => $athlete->avatar,
                'first_name' => $athlete->first_name,
                'last_name' => $athlete->last_name,
                'email' => $athlete->email,
                'athlete_number' => $athlete->athlete_number,
                'status' => $athlete->status,
                'school' => $athlete->school,
                'sport' => $athlete->sport,
                'created_at' => $athlete->created_at,
                'updated_at' => $athlete->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'data' => $transformedAthletes,
                'current_page' => $athletes->currentPage(),
                'last_page' => $athletes->lastPage(),
                'per_page' => $athletes->perPage(),
                'total' => $athletes->total()
            ]
        ]);
    }

    private function getUsersByRolePaginated($role, $search, $page, $limit): JsonResponse
    {
        // Handle officials separately since they are in a different table
        if ($role === 'official') {
            $query = Official::when($search, function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });

            $officials = $query->paginate($limit, ['*'], 'page', $page);

            $transformedOfficials = $officials->getCollection()->map(function($official) {
                return (object) [
                    'id' => $official->id,
                    'type' => 'official',
                    'avatar' => $official->avatar,
                    'first_name' => $official->first_name,
                    'last_name' => $official->last_name,
                    'email' => $official->email,
                    'contact_number' => $official->contact_number,
                    'certification_level' => $official->certification_level,
                    'official_type' => $official->official_type,
                    'sports_certified' => $official->sports_certified,
                    'years_experience' => $official->years_experience,
                    'availability_schedule' => $official->availability_schedule,
                    'status' => $official->status,
                    'created_at' => $official->created_at,
                    'updated_at' => $official->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'data' => $transformedOfficials,
                    'current_page' => $officials->currentPage(),
                    'last_page' => $officials->lastPage(),
                    'per_page' => $officials->perPage(),
                    'total' => $officials->total()
                ]
            ]);
        }

        // Handle other user roles (coach, tournament_manager)
                $query = User::with('school')->where('role', $role)
            ->when($search, function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });

        $users = $query->paginate($limit, ['*'], 'page', $page);

        $transformedUsers = $users->getCollection()->map(function($user) use ($role) {
            $type = $role === 'tournament_manager' ? 'tournament_manager' : $role;
            return (object) [
                'id' => $user->id,
                'type' => $type,
                'avatar' => $user->avatar,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'contact_number' => $user->contact_number,
                'status' => $user->status,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                // Add role-specific fields
                'certification_level' => $user->certification_level,
                'official_type' => $user->official_type,
                'sports_certified' => $user->sports_certified,
                'years_experience' => $user->experience_years,
                'availability_schedule' => $user->availability_schedule,
                'department' => $user->department,
                'experience_years' => $user->experience_years,
                'managed_tournaments' => $user->managed_tournaments,
                'school_id' => $user->school_id,
                'school' => $user->school ? (object) [
                    'id' => $user->school->id,
                    'name' => $user->school->name,
                    'short_name' => $user->school->short_name,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'data' => $transformedUsers,
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total()
            ]
        ]);
    }
}