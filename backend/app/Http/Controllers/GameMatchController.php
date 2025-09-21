<?php

namespace App\Http\Controllers;

use App\Models\GameMatch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GameMatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = GameMatch::with(['teamA.school', 'teamB.school', 'sport', 'venue', 'schedule']);
            
            // Filter by sport if provided
            if ($request->has('sport_id')) {
                $query->where('sport_id', $request->sport_id);
            }
            
            // Filter by venue if provided
            if ($request->has('venue_id')) {
                $query->where('venue_id', $request->venue_id);
            }
            
            // Filter by schedule if provided
            if ($request->has('schedule_id')) {
                $query->where('schedule_id', $request->schedule_id);
            }
            
            // Filter by home team if provided
            if ($request->has('home_team_id')) {
                $query->where('home_team_id', $request->home_team_id);
            }
            
            // Filter by away team if provided
            if ($request->has('away_team_id')) {
                $query->where('away_team_id', $request->away_team_id);
            }
            
            // Filter by match status if provided
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            // Filter by date range
            if ($request->has('start_date')) {
                $query->whereDate('match_date', '>=', $request->start_date);
            }
            
            if ($request->has('end_date')) {
                $query->whereDate('match_date', '<=', $request->end_date);
            }
            
            // Search by match name or notes
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('match_name', 'like', "%{$search}%")
                      ->orWhere('match_notes', 'like', "%{$search}%");
                });
            }
            
            // Order by match date
            $query->orderBy('match_date', 'desc')->orderBy('match_time', 'desc');
            
            // Pagination
            $perPage = $request->input('per_page', 15);
            $gameMatches = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'Game matches retrieved successfully',
                'data' => $gameMatches
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve game matches',
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
                'match_name' => 'required|string|max:255',
                'match_date' => 'required|date',
                'match_time' => 'required|date_format:H:i:s',
                'sport_id' => 'required|exists:sports,id',
                'venue_id' => 'required|exists:venues,id',
                'schedule_id' => 'nullable|exists:schedules,id',
                'home_team_id' => 'required|exists:teams,id',
                'away_team_id' => 'required|exists:teams,id|different:home_team_id',
                'home_team_score' => 'nullable|integer|min:0',
                'away_team_score' => 'nullable|integer|min:0',
                'winner_team_id' => 'nullable|exists:teams,id',
                'match_duration_minutes' => 'nullable|integer|min:1',
                'status' => 'required|in:scheduled,ongoing,completed,cancelled,postponed',
                'match_type' => 'required|in:regular,playoff,championship,friendly,tournament',
                'round_stage' => 'nullable|string|max:255',
                'referee_notes' => 'nullable|string',
                'match_notes' => 'nullable|string',
                'weather_conditions' => 'nullable|string|max:255',
                'attendance' => 'nullable|integer|min:0'
            ]);

            // Validate that home and away teams are different
            if ($validated['home_team_id'] === $validated['away_team_id']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Home team and away team must be different'
                ], 422);
            }

            $gameMatch = GameMatch::create($validated);

            $gameMatch->load(['teamA.school', 'teamB.school', 'sport', 'venue', 'schedule']);

            return response()->json([
                'success' => true,
                'message' => 'Game match created successfully',
                'data' => $gameMatch
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
                'message' => 'Failed to create game match',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(GameMatch $gameMatch): JsonResponse
    {
        try {
            $gameMatch->load(['teamA.school', 'teamB.school', 'sport', 'venue', 'schedule', 'results']);

            return response()->json([
                'success' => true,
                'message' => 'Game match retrieved successfully',
                'data' => $gameMatch
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve game match',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GameMatch $gameMatch): JsonResponse
    {
        try {
            $validated = $request->validate([
                'match_name' => 'sometimes|required|string|max:255',
                'match_date' => 'sometimes|required|date',
                'match_time' => 'sometimes|required|date_format:H:i:s',
                'sport_id' => 'sometimes|required|exists:sports,id',
                'venue_id' => 'sometimes|required|exists:venues,id',
                'schedule_id' => 'sometimes|nullable|exists:schedules,id',
                'home_team_id' => 'sometimes|required|exists:teams,id',
                'away_team_id' => 'sometimes|required|exists:teams,id|different:home_team_id',
                'home_team_score' => 'sometimes|nullable|integer|min:0',
                'away_team_score' => 'sometimes|nullable|integer|min:0',
                'winner_team_id' => 'sometimes|nullable|exists:teams,id',
                'match_duration_minutes' => 'sometimes|nullable|integer|min:1',
                'status' => 'sometimes|required|in:scheduled,ongoing,completed,cancelled,postponed',
                'match_type' => 'sometimes|required|in:regular,playoff,championship,friendly,tournament',
                'round_stage' => 'sometimes|nullable|string|max:255',
                'referee_notes' => 'sometimes|nullable|string',
                'match_notes' => 'sometimes|nullable|string',
                'weather_conditions' => 'sometimes|nullable|string|max:255',
                'attendance' => 'sometimes|nullable|integer|min:0'
            ]);

            // Validate that home and away teams are different (if both are being updated)
            if (isset($validated['home_team_id']) && isset($validated['away_team_id'])) {
                if ($validated['home_team_id'] === $validated['away_team_id']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Home team and away team must be different'
                    ], 422);
                }
            }

            $gameMatch->update($validated);

            $gameMatch->load(['teamA.school', 'teamB.school', 'sport', 'venue', 'schedule']);

            return response()->json([
                'success' => true,
                'message' => 'Game match updated successfully',
                'data' => $gameMatch
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
                'message' => 'Failed to update game match',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GameMatch $gameMatch): JsonResponse
    {
        try {
            // Check if game match has dependent records
            if ($gameMatch->results()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete game match: Match has result records'
                ], 409);
            }

            $gameMatch->delete();

            return response()->json([
                'success' => true,
                'message' => 'Game match deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete game match',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get matches by team.
     */
    public function getByTeam(int $teamId): JsonResponse
    {
        try {
            $matches = GameMatch::with(['teamA.school', 'teamB.school', 'sport', 'venue'])
                ->where(function($query) use ($teamId) {
                    $query->where('team_a_id', $teamId)
                          ->orWhere('team_b_id', $teamId);
                })
                ->orderBy('scheduled_start', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Matches retrieved successfully',
                'data' => $matches
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve matches by team',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get upcoming matches.
     */
    public function getUpcoming(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 10);
            
            $matches = GameMatch::select([
                'id', 'match_code', 'title', 'scheduled_start', 'status',
                'team_a_id', 'team_b_id', 'sport_id', 'venue_id'
            ])
            ->with([
                'teamA:id,name,school_id',
                'teamB:id,name,school_id',
                'sport:id,name',
                'venue:id,name'
            ])
            ->where('scheduled_start', '>=', now())
            ->where('status', 'scheduled')
            ->orderBy('scheduled_start')
            ->limit($limit)
            ->get();

            return response()->json([
                'success' => true,
                'message' => 'Upcoming matches retrieved successfully',
                'data' => $matches
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve upcoming matches',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get completed matches.
     */
    public function getCompleted(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 10);
            
            $matches = GameMatch::select([
                'id', 'match_code', 'title', 'scheduled_start', 'status',
                'team_a_id', 'team_b_id', 'sport_id', 'venue_id',
                'final_score_team_a', 'final_score_team_b'
            ])
            ->with([
                'teamA:id,name,school_id',
                'teamB:id,name,school_id', 
                'sport:id,name',
                'venue:id,name'
            ])
            ->where('status', 'completed')
            ->orderBy('scheduled_start', 'desc')
            ->limit($limit)
            ->get();

            return response()->json([
                'success' => true,
                'message' => 'Completed matches retrieved successfully',
                'data' => $matches
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve completed matches',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update match score.
     */
    public function updateScore(Request $request, GameMatch $gameMatch): JsonResponse
    {
        try {
            $validated = $request->validate([
                'home_team_score' => 'required|integer|min:0',
                'away_team_score' => 'required|integer|min:0',
                'winner_team_id' => 'nullable|exists:teams,id',
                'status' => 'sometimes|in:ongoing,completed'
            ]);

            // Determine winner if not provided
            if (!isset($validated['winner_team_id'])) {
                if ($validated['home_team_score'] > $validated['away_team_score']) {
                    $validated['winner_team_id'] = $gameMatch->home_team_id;
                } elseif ($validated['away_team_score'] > $validated['home_team_score']) {
                    $validated['winner_team_id'] = $gameMatch->away_team_id;
                } else {
                    $validated['winner_team_id'] = null; // Draw
                }
            }

            $gameMatch->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Match score updated successfully',
                'data' => $gameMatch
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
                'message' => 'Failed to update match score',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
