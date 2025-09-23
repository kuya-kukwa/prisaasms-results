<?php

namespace App\Http\Controllers\Layer1;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Layer1\User;
use App\Models\Layer2\Sport;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserController extends Controller
{
    // ----------------------------------------------------
    // Helper: apply filters
    // ----------------------------------------------------
    private function applyFilters($query, Request $request)
    {
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
    }

    // ----------------------------------------------------
    // CRUD: Index
    // ----------------------------------------------------
    public function index(Request $request): JsonResponse
    {
        try {
            $query = User::with(['school.province.region']);
            $this->applyFilters($query, $request);

            $perPage = $request->input('per_page', 15);
            $users = $query->paginate($perPage);

            return $this->successResponse($users, 'Users retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve users', [], 500);
        }
    }

    // ----------------------------------------------------
    // CRUD: Store
    // ----------------------------------------------------
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'first_name'      => 'required|string|max:255',
                'last_name'       => 'required|string|max:255',
                'email'           => 'required|email|unique:users,email',
                'password'        => 'required|string|min:8|confirmed',
                'contact_number'  => 'nullable|string|max:255',
                'role'            => 'required|in:admin,coach,tournament_manager,official',
                'avatar'          => 'nullable|string|max:255',
                'school_id'       => 'nullable|exists:schools,id',
                'sports'          => 'sometimes|array',
                'sports.*'        => 'exists:sports,id'
            ]);

            $validated['password'] = Hash::make($validated['password']);
            $validated['email_verified_at'] = now();

            $user = User::create($validated);

            // Attach sports if role = official
            if ($user->role === 'official' && !empty($validated['sports'])) {
                $user->sports()->sync($validated['sports']);
            }

            $user->load(['school.province.region', 'sports']);

            return $this->successResponse($user, 'User created successfully', 201);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed', $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create user', [], 500);
        }
    }

    // ----------------------------------------------------
    // CRUD: Show
    // ----------------------------------------------------
    public function show(string $id): JsonResponse
    {
        try {
            $user = User::with([
                'school.province.region',
                'sports',
                'managedTournaments',
                'createdSchedules',
                'createdMatches',
                'confirmedResults'
            ])->findOrFail($id);

            return $this->successResponse($user, 'User retrieved successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('User not found', [], 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve user', [], 500);
        }
    }

    // ----------------------------------------------------
    // CRUD: Update
    // ----------------------------------------------------
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'first_name'      => 'sometimes|required|string|max:255',
                'last_name'       => 'sometimes|required|string|max:255',
                'email'           => 'sometimes|required|email|unique:users,email,' . $id,
                'password'        => 'sometimes|required|string|min:8|confirmed',
                'contact_number'  => 'sometimes|nullable|string|max:255',
                'role'            => 'sometimes|required|in:admin,coach,tournament_manager,official',
                'avatar'          => 'sometimes|nullable|string|max:255',
                'school_id'       => 'sometimes|nullable|exists:schools,id',
                'sports'          => 'sometimes|array',
                'sports.*'        => 'exists:sports,id'
            ]);

            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            $user->update($validated);

            // Sync sports if official
            if ($user->role === 'official' && isset($validated['sports'])) {
                $user->sports()->sync($validated['sports']);
            }

            $user->load(['school.province.region', 'sports']);

            return $this->successResponse($user, 'User updated successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('User not found', [], 404);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed', $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update user', [], 500);
        }
    }

    // ----------------------------------------------------
    // CRUD: Destroy
    // ----------------------------------------------------
    public function destroy(string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            if ($user->managedTournaments()->count() > 0) {
                return $this->errorResponse('Cannot delete user: User is managing tournaments', [], 409);
            }

            $user->delete();

            return $this->successResponse(null, 'User deleted successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('User not found', [], 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete user', [], 500);
        }
    }

    // ----------------------------------------------------
    // Custom: Assign sports to official
    // ----------------------------------------------------
    public function assignSports(Request $request, string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            if ($user->role !== 'official') {
                return $this->errorResponse('Only officials can be assigned sports', [], 400);
            }

            $validated = $request->validate([
                'sports'   => 'required|array',
                'sports.*' => 'exists:sports,id'
            ]);

            $user->sports()->sync($validated['sports']);
            $user->load('sports');

            return $this->successResponse($user, 'Sports assigned successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('User not found', [], 404);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed', $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to assign sports', [], 500);
        }
    }

    // ----------------------------------------------------
    // Profile-related
    // ----------------------------------------------------
    public function profile(Request $request): JsonResponse
    {
        try {
            $user = $request->user()->load([
                'school.province.region',
                'sports',
                'managedTournaments:id,name,status,start_date,end_date',
                'createdSchedules:id,tournament_id,scheduled_date,venue_id,status',
                'createdMatches:id,tournament_id,sport_id,scheduled_start,status'
            ]);

            return $this->successResponse($user, 'Profile retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve profile', [], 500);
        }
    }

    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $validated = $request->validate([
                'first_name'     => 'sometimes|required|string|max:255',
                'last_name'      => 'sometimes|required|string|max:255',
                'email'          => 'sometimes|required|email|unique:users,email,' . $user->id,
                'contact_number' => 'sometimes|nullable|string|max:255',
                'avatar'         => 'sometimes|nullable|string|max:255'
            ]);

            $user->update($validated);
            $user->load(['school.province.region', 'sports']);

            return $this->successResponse($user, 'Profile updated successfully');
        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed', $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update profile', [], 500);
        }
    }

    public function changePassword(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $validated = $request->validate([
                'current_password' => 'required|string',
                'password'         => 'required|string|min:8|confirmed'
            ]);

            if (!Hash::check($validated['current_password'], $user->password)) {
                return $this->errorResponse('Current password is incorrect', [], 400);
            }

            $user->update(['password' => Hash::make($validated['password'])]);

            return $this->successResponse(null, 'Password changed successfully');
        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed', $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to change password', [], 500);
        }
    }

    // ----------------------------------------------------
    // Online status helpers (kept your implementation)
    // ----------------------------------------------------
    private function getActiveUserIds(int $thresholdMinutes = 30): \Illuminate\Support\Collection
    {
        $threshold = now()->subMinutes($thresholdMinutes);

        return DB::table('personal_access_tokens')
            ->where('created_at', '>=', $threshold)
            ->orWhere('last_used_at', '>=', $threshold)
            ->pluck('tokenable_id')
            ->unique();
    }

    private function determineUserStatus($user, $activeUserIds): string
    {
        $latestToken = DB::table('personal_access_tokens')
            ->where('tokenable_id', $user->id)
            ->whereNotNull('last_used_at')
            ->orderBy('last_used_at', 'desc')
            ->first();

        $lastActivity = $latestToken ?
            Carbon::parse($latestToken->last_used_at) :
            Carbon::parse($user->updated_at);

        $minutesAgo = abs(now()->diffInMinutes($lastActivity));
        $hasActiveToken = $activeUserIds->contains($user->id);

        if ($hasActiveToken && $minutesAgo <= 5) {
            return 'online';
        } elseif ($hasActiveToken && $minutesAgo <= 15) {
            return 'away';
        } elseif ($minutesAgo <= 30) {
            return 'busy';
        } else {
            return 'offline';
        }
    }

    private function transformOnlineUser($user, $activeUserIds)
    {
        $status = $this->determineUserStatus($user, $activeUserIds);

        $latestToken = DB::table('personal_access_tokens')
            ->where('tokenable_id', $user->id)
            ->whereNotNull('last_used_at')
            ->orderBy('last_used_at', 'desc')
            ->first();

        $lastActivity = $latestToken ?
            Carbon::parse($latestToken->last_used_at) :
            Carbon::parse($user->updated_at);

        $minutesAgo = abs(now()->diffInMinutes($lastActivity));
        $hasActiveToken = $activeUserIds->contains($user->id);

        return [
            'id'               => $user->id,
            'first_name'       => $user->first_name,
            'last_name'        => $user->last_name,
            'full_name'        => $user->full_name,
            'email'            => $user->email,
            'role'             => $user->role,
            'avatar'           => $user->avatar,
            'school'           => $user->school ? [
                'id'         => $user->school->id,
                'name'       => $user->school->name,
            ] : null,
            'last_seen'        => $lastActivity->toISOString(),
            'status'           => $status,
            'minutes_ago'      => $minutesAgo,
            'has_active_session' => $hasActiveToken,
        ];
    }

    public function getOnlineUsers(Request $request): JsonResponse
    {
        try {
            $roles = ['coach', 'tournament_manager', 'official'];
            $activeUserIds = $this->getActiveUserIds(30);

            $query = User::with(['school'])
                ->whereIn('role', $roles)
                ->whereIn('id', $activeUserIds)
                ->orderBy('updated_at', 'desc');

            $limit = $request->input('limit', 10);
            $users = $query->limit($limit)->get();

            if ($users->isEmpty()) {
                $recentThreshold = now()->subMinutes(15);
                $users = User::with(['school'])
                    ->whereIn('role', $roles)
                    ->where('updated_at', '>=', $recentThreshold)
                    ->orderBy('updated_at', 'desc')
                    ->limit($limit)
                    ->get();
            }

            $onlineUsers = $users->map(fn($user) => $this->transformOnlineUser($user, $activeUserIds));

            return $this->successResponse($onlineUsers, 'Online users retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve online users', [], 500);
        }
    }

    // ----------------------------------------------------
    // Admin-only utilities
    // ----------------------------------------------------
    public function adminIndex(Request $request): JsonResponse
    {
        try {
            $query = User::with(['school.province.region']);
            $this->applyFilters($query, $request);

            $sortBy = $request->get('sort_by', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortBy, $sortDirection);

            $perPage = $request->get('per_page', 15);
            $users = $query->paginate($perPage);

            return $this->successResponse($users, 'Users retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve users', [], 500);
        }
    }

    public function getProfileStats(): JsonResponse
    {
        try {
            $totalUsers = User::count();
            $usersByRole = User::selectRaw('role, COUNT(*) as count')
                ->groupBy('role')
                ->get()
                ->pluck('count', 'role');

            $verifiedUsers = User::whereNotNull('email_verified_at')->count();
            $usersWithSchools = User::whereNotNull('school_id')->count();

            $data = [
                'total_users'          => $totalUsers,
                'users_by_role'        => $usersByRole,
                'verified_users'       => $verifiedUsers,
                'unverified_users'     => $totalUsers - $verifiedUsers,
                'users_with_schools'   => $usersWithSchools,
                'users_without_schools'=> $totalUsers - $usersWithSchools,
                'recent_users'         => User::where('created_at', '>=', now()->subDays(30))->count(),
            ];

            return $this->successResponse($data, 'User statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve user statistics', [], 500);
        }
    }
}
