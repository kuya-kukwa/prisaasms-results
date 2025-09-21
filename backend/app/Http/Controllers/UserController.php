<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = User::with(['school']);
            
            // Filter by role if provided
            if ($request->has('role')) {
                $query->where('role', $request->role);
            }
            
            // Filter by school if provided
            if ($request->has('school_id')) {
                $query->where('school_id', $request->school_id);
            }
            
            // Search by name or email
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            
            // Pagination
            $perPage = $request->input('per_page', 15);
            $users = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'Users retrieved successfully',
                'data' => $users
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'contact_number' => 'nullable|string|max:255',
                'role' => 'required|in:admin,coach,tournament_manager',
                'avatar' => 'nullable|string|max:255',
                'school_id' => 'nullable|exists:schools,id'
            ]);

            $validated['password'] = Hash::make($validated['password']);
            
            // For admin-created users, set email as verified since admin is creating the account
            $validated['email_verified_at'] = now();
            
            $user = User::create($validated);
            $user->load('school');

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $user = User::with([
                'school',
                'managedTournaments',
                'createdSchedules',
                'createdMatches',
                'confirmedResults'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'User retrieved successfully',
                'data' => $user
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'first_name' => 'sometimes|required|string|max:255',
                'last_name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:users,email,' . $id,
                'password' => 'sometimes|required|string|min:8|confirmed',
                'contact_number' => 'sometimes|nullable|string|max:255',
                'role' => 'sometimes|required|in:admin,coach,tournament_manager',
                'avatar' => 'sometimes|nullable|string|max:255',
                'school_id' => 'sometimes|nullable|exists:schools,id'
            ]);

            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            $user->update($validated);
            $user->load('school');

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            // Check if user has dependent records
            if ($user->managedTournaments()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete user: User is managing tournaments'
                ], 409);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get users by role.
     */
    public function getByRole(string $role): JsonResponse
    {
        try {
            if (!in_array($role, ['admin', 'coach', 'tournament_manager'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid role specified'
                ], 400);
            }

            $users = User::with('school')
                ->where('role', $role)
                ->orderBy('first_name')
                ->get();

            return response()->json([
                'success' => true,
                'message' => ucfirst($role) . 's retrieved successfully',
                'data' => $users
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve users by role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user profile (for authenticated user).
     */
    public function profile(Request $request): JsonResponse
    {
        try {
            $user = $request->user()->load([
                'school:id,name,region',
                'managedTournaments:id,name,status,start_date,end_date',
                'createdSchedules:id,tournament_id,scheduled_date,venue_id,status',
                'createdMatches:id,tournament_id,sport_id,scheduled_start,status'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profile retrieved successfully',
                'data' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user profile (for authenticated user).
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $validated = $request->validate([
                'first_name' => 'sometimes|required|string|max:255',
                'last_name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
                'contact_number' => 'sometimes|nullable|string|max:255',
                'avatar' => 'sometimes|nullable|string|max:255'
            ]);

            $user->update($validated);
            $user->load('school');

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => $user
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change user password.
     */
    public function changePassword(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $validated = $request->validate([
                'current_password' => 'required|string',
                'password' => 'required|string|min:8|confirmed'
            ]);

            // Verify current password
            if (!Hash::check($validated['current_password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 400);
            }

            $user->update([
                'password' => Hash::make($validated['password'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to change password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get online coaches and tournament managers.
     */
    public function getOnlineUsers(Request $request): JsonResponse
    {
        try {
            $roles = ['coach', 'tournament_manager'];
            
            // Get users who have active sessions (valid tokens created recently)
            // This is a more accurate way to determine "online" status
            $onlineThreshold = now()->subMinutes(30); // 30 minutes for session timeout
            
            // Get users who have active tokens (indicating they're logged in)
            $activeUserIds = DB::table('personal_access_tokens')
                ->where('created_at', '>=', $onlineThreshold)
                ->orWhere('last_used_at', '>=', $onlineThreshold)
                ->pluck('tokenable_id')
                ->unique();
            
            $query = User::with(['school'])
                ->whereIn('role', $roles)
                ->whereIn('id', $activeUserIds)
                ->orderBy('updated_at', 'desc');
            
            // Limit results to prevent excessive data
            $limit = $request->input('limit', 10);
            $users = $query->limit($limit)->get();
            
            // If no active tokens found, fall back to recent activity check
            if ($users->isEmpty()) {
                $recentThreshold = now()->subMinutes(15);
                $users = User::with(['school'])
                    ->whereIn('role', $roles)
                    ->where('updated_at', '>=', $recentThreshold)
                    ->orderBy('updated_at', 'desc')
                    ->limit($limit)
                    ->get();
            }
            
            // Transform the data to include status and format for frontend
            $onlineUsers = $users->map(function ($user) use ($activeUserIds) {
                // Get the user's most recent token activity
                $latestToken = DB::table('personal_access_tokens')
                    ->where('tokenable_id', $user->id)
                    ->whereNotNull('last_used_at')
                    ->orderBy('last_used_at', 'desc')
                    ->first();
                
                $lastActivity = $latestToken ? 
                    Carbon::parse($latestToken->last_used_at) : 
                    Carbon::parse($user->updated_at);
                
                $minutesAgo = abs(now()->diffInMinutes($lastActivity));
                
                // Determine status - if user has active token, they're truly online
                $hasActiveToken = $activeUserIds->contains($user->id);
                
                if ($hasActiveToken && $minutesAgo <= 5) {
                    $status = 'online';
                } elseif ($hasActiveToken && $minutesAgo <= 15) {
                    $status = 'away';
                } elseif ($minutesAgo <= 30) {
                    $status = 'busy';
                } else {
                    $status = 'offline';
                }
                
                return [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'full_name' => trim($user->first_name . ' ' . $user->last_name),
                    'email' => $user->email,
                    'role' => $user->role,
                    'avatar' => $user->avatar,
                    'school' => $user->school ? [
                        'id' => $user->school->id,
                        'name' => $user->school->name,
                        'short_name' => $user->school->short_name,
                    ] : null,
                    'last_seen' => $lastActivity->toISOString(),
                    'status' => $status,
                    'minutes_ago' => $minutesAgo,
                    'has_active_session' => $hasActiveToken,
                ];
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Online users retrieved successfully',
                'data' => $onlineUsers,
                'meta' => [
                    'total_count' => $onlineUsers->count(),
                    'threshold_minutes' => 30,
                    'detection_method' => $users->isNotEmpty() ? 'active_tokens' : 'recent_activity',
                    'updated_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve online users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all users for admin management
     */
    public function adminIndex(Request $request)
    {
        try {
            $query = User::with(['school']);

            // Apply filters
            if ($request->has('role') && $request->role !== '') {
                $query->where('role', $request->role);
            }

            if ($request->has('school_id') && $request->school_id !== '') {
                $query->where('school_id', $request->school_id);
            }

            if ($request->has('search') && $request->search !== '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortBy, $sortDirection);

            // Paginate results
            $perPage = $request->get('per_page', 15);
            $users = $query->paginate($perPage);

            // Transform data
            $users->getCollection()->transform(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'avatar' => $user->avatar,
                    'school' => $user->school ? [
                        'id' => $user->school->id,
                        'name' => $user->school->name,
                        'short_name' => $user->school->short_name,
                    ] : null,
                    'email_verified_at' => $user->email_verified_at,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Users retrieved successfully',
                'data' => $users->items(),
                'meta' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user statistics for admin dashboard
     */
    public function getProfileStats()
    {
        try {
            $totalUsers = User::count();
            $usersByRole = User::selectRaw('role, COUNT(*) as count')
                ->groupBy('role')
                ->get()
                ->pluck('count', 'role');

            $verifiedUsers = User::whereNotNull('email_verified_at')->count();
            $unverifiedUsers = $totalUsers - $verifiedUsers;

            $usersWithSchools = User::whereNotNull('school_id')->count();
            $usersWithoutSchools = $totalUsers - $usersWithSchools;

            $recentUsers = User::where('created_at', '>=', now()->subDays(30))->count();

            return response()->json([
                'success' => true,
                'message' => 'User statistics retrieved successfully',
                'data' => [
                    'total_users' => $totalUsers,
                    'users_by_role' => $usersByRole,
                    'verified_users' => $verifiedUsers,
                    'unverified_users' => $unverifiedUsers,
                    'users_with_schools' => $usersWithSchools,
                    'users_without_schools' => $usersWithoutSchools,
                    'recent_users' => $recentUsers,
                    'verification_rate' => $totalUsers > 0 ? round(($verifiedUsers / $totalUsers) * 100, 2) : 0,
                    'school_assignment_rate' => $totalUsers > 0 ? round(($usersWithSchools / $totalUsers) * 100, 2) : 0,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
