<?php

namespace App\Http\Controllers;

use App\Models\Sport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Sport::query();
            
            // Filter by category if provided
            if ($request->has('category')) {
                $query->where('category', $request->category);
            }
            
            // Filter by gender category if provided
            if ($request->has('gender_category')) {
                $query->where('gender_category', $request->gender_category);
            }
            
            // Filter by status if provided
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            // Search by name
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }
            
            // Pagination
            $perPage = $request->input('per_page', 15);
            $sports = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'Sports retrieved successfully',
                'data' => $sports
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sports',
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
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'category' => 'required|in:team_sport,individual_sport,combat_sport,track_field,swimming,ball_games,racket_sports,gymnastics,weightlifting,martial_arts,athletics,aquatics,cycling,other',
                'gender_category' => 'required|in:male,female,mixed',
                'max_players_per_team' => 'nullable|integer|min:1',
                'min_players_per_team' => 'nullable|integer|min:1',
                'scoring_system' => 'nullable|array',
                'game_duration_minutes' => 'nullable|integer|min:1',
                'tournament_format' => 'required|in:single_elimination,double_elimination,round_robin,swiss,league,group_stage_knockout,ladder,time_based,best_of_series,pool_play',
                'has_ranking_system' => 'required|boolean',
                'status' => 'required|in:active,inactive',
                'icon' => 'nullable|string|max:255'
            ]);

            $sport = Sport::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Sport created successfully',
                'data' => $sport
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
                'message' => 'Failed to create sport',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sport $sport): JsonResponse
    {
        try {
            $sport->load(['athletes', 'teams', 'schedules', 'gameMatches']);

            return response()->json([
                'success' => true,
                'message' => 'Sport retrieved successfully',
                'data' => $sport
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sport',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sport $sport): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|nullable|string',
                'category' => 'sometimes|required|in:team_sport,individual_sport,combat_sport,track_field,swimming,ball_games,racket_sports,gymnastics,weightlifting,martial_arts,athletics,aquatics,cycling,other',
                'gender_category' => 'sometimes|required|in:male,female,mixed',
                'max_players_per_team' => 'sometimes|nullable|integer|min:1',
                'min_players_per_team' => 'sometimes|nullable|integer|min:1',
                'scoring_system' => 'sometimes|nullable|array',
                'game_duration_minutes' => 'sometimes|nullable|integer|min:1',
                'tournament_format' => 'sometimes|required|in:single_elimination,double_elimination,round_robin,swiss,league,group_stage_knockout,ladder,time_based,best_of_series,pool_play',
                'has_ranking_system' => 'sometimes|required|boolean',
                'status' => 'sometimes|required|in:active,inactive',
                'icon' => 'sometimes|nullable|string|max:255'
            ]);

            $sport->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Sport updated successfully',
                'data' => $sport
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
                'message' => 'Failed to update sport',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sport $sport): JsonResponse
    {
        try {
            // Check if sport has dependent records
            if ($sport->athletes()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete sport: Sport has registered athletes'
                ], 409);
            }

            if ($sport->teams()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete sport: Sport has registered teams'
                ], 409);
            }

            $sport->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sport deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete sport',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sports by category.
     */
    public function getByCategory(string $category): JsonResponse
    {
        try {
            $sports = Sport::where('category', $category)
                ->where('status', 'active')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Sports retrieved successfully',
                'data' => $sports
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sports by category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sport statistics.
     */
    public function getStatistics(Sport $sport): JsonResponse
    {
        try {
            $statistics = [
                'total_athletes' => $sport->athletes()->count(),
                'total_teams' => $sport->teams()->count(),
                'total_matches' => $sport->gameMatches()->count(),
                'total_schedules' => $sport->schedules()->count(),
                'active_athletes' => $sport->athletes()->where('status', 'active')->count(),
                'active_teams' => $sport->teams()->where('status', 'active')->count()
            ];

            return response()->json([
                'success' => true,
                'message' => 'Sport statistics retrieved successfully',
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sport statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all sports for admin management
     */
    public function adminIndex(Request $request): JsonResponse
    {
        try {
            $query = Sport::query();

            // Apply filters
            if ($request->has('category') && $request->category !== '') {
                $query->where('category', $request->category);
            }

            if ($request->has('gender_category') && $request->gender_category !== '') {
                $query->where('gender_category', $request->gender_category);
            }

            if ($request->has('status') && $request->status !== '') {
                $query->where('status', $request->status);
            }

            if ($request->has('search') && $request->search !== '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortBy, $sortDirection);

            // Paginate results
            $perPage = $request->get('per_page', 15);
            $sports = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Sports retrieved successfully',
                'data' => $sports->items(),
                'meta' => [
                    'current_page' => $sports->currentPage(),
                    'last_page' => $sports->lastPage(),
                    'per_page' => $sports->perPage(),
                    'total' => $sports->total(),
                    'from' => $sports->firstItem(),
                    'to' => $sports->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sports',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sports statistics for admin dashboard
     */
    public function getAdminStats(): JsonResponse
    {
        try {
            $totalSports = Sport::count();
            $sportsByCategory = Sport::selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->get()
                ->pluck('count', 'category');

            $sportsByGender = Sport::selectRaw('gender_category, COUNT(*) as count')
                ->groupBy('gender_category')
                ->get()
                ->pluck('count', 'gender_category');

            $activeSports = Sport::where('status', 'active')->count();
            $inactiveSports = $totalSports - $activeSports;

            $sportsWithAthletes = Sport::whereHas('athletes')->count();
            $sportsWithTeams = Sport::whereHas('teams')->count();
            $sportsWithSchedules = Sport::whereHas('schedules')->count();

            return response()->json([
                'success' => true,
                'message' => 'Sports statistics retrieved successfully',
                'data' => [
                    'total_sports' => $totalSports,
                    'sports_by_category' => $sportsByCategory,
                    'sports_by_gender' => $sportsByGender,
                    'active_sports' => $activeSports,
                    'inactive_sports' => $inactiveSports,
                    'sports_with_athletes' => $sportsWithAthletes,
                    'sports_with_teams' => $sportsWithTeams,
                    'sports_with_schedules' => $sportsWithSchedules,
                    'activity_rate' => $totalSports > 0 ? round(($activeSports / $totalSports) * 100, 2) : 0,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sports statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update sport status
     */
    public function updateStatus(Request $request, Sport $sport): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:active,inactive'
            ]);

            $sport->update(['status' => $validated['status']]);

            return response()->json([
                'success' => true,
                'message' => 'Sport status updated successfully',
                'data' => $sport
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
                'message' => 'Failed to update sport status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
