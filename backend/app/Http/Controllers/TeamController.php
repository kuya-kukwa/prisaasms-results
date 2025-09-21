<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Team::with(['school', 'sport']);
            
            // Filter by school if provided
            if ($request->has('school_id')) {
                $query->where('school_id', $request->school_id);
            }
            
            // Filter by sport if provided
            if ($request->has('sport_id')) {
                $query->where('sport_id', $request->sport_id);
            }
            
            // Filter by gender category if provided
            if ($request->has('gender_category')) {
                $query->where('gender_category', $request->gender_category);
            }
            
            // Filter by status if provided
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            // Search by team name or contact person
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('contact_person', 'like', "%{$search}%")
                      ->orWhere('short_name', 'like', "%{$search}%");
                });
            }
            
            // Pagination
            $perPage = $request->input('per_page', 15);
            $teams = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'Teams retrieved successfully',
                'data' => $teams
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve teams',
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
                'team_name' => 'required|string|max:255',
                'short_name' => 'nullable|string|max:255',
                'team_code' => 'nullable|string|max:255|unique:teams',
                'school_id' => 'required|exists:schools,id',
                'sport_id' => 'required|exists:sports,id',
                'coach_id' => 'nullable|exists:users,id',
                'gender_category' => 'required|in:male,female,mixed',
                'division' => 'nullable|in:senior,junior,elementary,college,high_school',
                'season_year' => 'required|integer',
                'team_logo' => 'nullable|string|max:255',
                'wins' => 'nullable|integer|min:0',
                'losses' => 'nullable|integer|min:0',
                'draws' => 'nullable|integer|min:0',
                'status' => 'required|in:active,inactive,disbanded,suspended',
                'contact_person' => 'nullable|string|max:255'
            ]);

            // Map team_name to name field for database
            $validated['name'] = $validated['team_name'];
            unset($validated['team_name']);

            // Set defaults
            if (!isset($validated['division'])) {
                $validated['division'] = 'college';
            }

            $team = Team::create($validated);

            $team->load(['school', 'sport']);

            return response()->json([
                'success' => true,
                'message' => 'Team created successfully',
                'data' => $team
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
                'message' => 'Failed to create team',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team): JsonResponse
    {
        try {
            $team->load(['school', 'sport', 'gameMatches', 'rankings']);

            return response()->json([
                'success' => true,
                'message' => 'Team retrieved successfully',
                'data' => $team
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve team',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team): JsonResponse
    {
        try {
            $validated = $request->validate([
                'team_name' => 'sometimes|required|string|max:255',
                'short_name' => 'sometimes|nullable|string|max:255',
                'team_code' => 'sometimes|nullable|string|max:255|unique:teams,team_code,' . $team->id,
                'school_id' => 'sometimes|required|exists:schools,id',
                'sport_id' => 'sometimes|required|exists:sports,id',
                'coach_id' => 'sometimes|nullable|exists:users,id',
                'gender_category' => 'sometimes|required|in:male,female,mixed',
                'division' => 'sometimes|nullable|in:senior,junior,elementary,college,high_school',
                'season_year' => 'sometimes|required|integer',
                'team_logo' => 'sometimes|nullable|string|max:255',
                'wins' => 'sometimes|nullable|integer|min:0',
                'losses' => 'sometimes|nullable|integer|min:0',
                'draws' => 'sometimes|nullable|integer|min:0',
                'status' => 'sometimes|required|in:active,inactive,disbanded,suspended',
                'contact_person' => 'sometimes|nullable|string|max:255'
            ]);

            // Map team_name to name field for database
            if (isset($validated['team_name'])) {
                $validated['name'] = $validated['team_name'];
                unset($validated['team_name']);
            }

            $team->update($validated);

            $team->load(['school', 'sport']);

            return response()->json([
                'success' => true,
                'message' => 'Team updated successfully',
                'data' => $team
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
                'message' => 'Failed to update team',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team): JsonResponse
    {
        try {
            // Check if team has dependent records
            if ($team->gameMatches()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete team: Team has game match records'
                ], 409);
            }

            if ($team->rankings()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete team: Team has ranking records'
                ], 409);
            }

            $team->delete();

            return response()->json([
                'success' => true,
                'message' => 'Team deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete team',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get teams by school.
     */
    public function getBySchool(int $schoolId): JsonResponse
    {
        try {
            $teams = Team::with(['sport'])
                ->where('school_id', $schoolId)
                ->where('status', 'active')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Teams retrieved successfully',
                'data' => $teams
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve teams by school',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get teams by sport.
     */
    public function getBySport(int $sportId): JsonResponse
    {
        try {
            $teams = Team::with(['school'])
                ->where('sport_id', $sportId)
                ->where('status', 'active')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Teams retrieved successfully',
                'data' => $teams
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve teams by sport',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get team statistics.
     */
    public function getStatistics(Team $team): JsonResponse
    {
        try {
            $statistics = [
                'total_matches' => $team->total_matches,
                'wins' => $team->wins ?? 0,
                'losses' => $team->losses ?? 0,
                'draws' => $team->draws ?? 0,
                'win_percentage' => $team->win_percentage,
                'current_players' => $team->athletes()->count(),
                'season_year' => $team->season_year,
                'status' => $team->status
            ];

            return response()->json([
                'success' => true,
                'message' => 'Team statistics retrieved successfully',
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve team statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update team performance stats.
     */
    public function updatePerformance(Request $request, Team $team): JsonResponse
    {
        try {
            $validated = $request->validate([
                'wins' => 'sometimes|integer|min:0',
                'losses' => 'sometimes|integer|min:0',
                'draws' => 'sometimes|integer|min:0'
            ]);

            $team->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Team performance updated successfully',
                'data' => $team
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
                'message' => 'Failed to update team performance',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
