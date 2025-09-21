<?php

namespace App\Http\Controllers;

use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ResultController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Result::with(['sport', 'match', 'tournament', 'school']);
            
            // Filter by sport if provided
            if ($request->has('sport_id')) {
                $query->where('sport_id', $request->sport_id);
            }
            
            // Filter by match if provided
            if ($request->has('match_id')) {
                $query->where('match_id', $request->match_id);
            }
            
            // Filter by tournament if provided
            if ($request->has('tournament_id')) {
                $query->where('tournament_id', $request->tournament_id);
            }
            
            // Filter by medal type if provided
            if ($request->has('medal_type')) {
                $query->where('medal_type', $request->medal_type);
            }
            
            // Filter by participant type if provided
            if ($request->has('participant_type')) {
                $query->where('participant_type', $request->participant_type);
            }
            
            // Filter by date range
            if ($request->has('start_date')) {
                $query->whereDate('competition_date', '>=', $request->start_date);
            }
            
            if ($request->has('end_date')) {
                $query->whereDate('competition_date', '<=', $request->end_date);
            }
            
            // Search by participant name or event name
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('participant_name', 'like', "%{$search}%")
                      ->orWhere('event_name', 'like', "%{$search}%");
                });
            }
            
            // Order by competition date
            $query->orderBy('competition_date', 'desc');
            
            // Pagination
            $perPage = $request->input('per_page', 15);
            $results = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'Results retrieved successfully',
                'data' => $results
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve results',
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
                'sport_id' => 'required|exists:sports,id',
                'game_match_id' => 'required|exists:game_matches,id',
                'winner_team_id' => 'nullable|exists:teams,id',
                'final_score_home' => 'required|integer|min:0',
                'final_score_away' => 'required|integer|min:0',
                'result_type' => 'required|in:win,draw,loss,forfeit,cancelled,postponed',
                'result_summary' => 'required|string',
                'detailed_scores' => 'nullable|array',
                'performance_stats' => 'nullable|array',
                'key_events' => 'nullable|array',
                'player_stats' => 'nullable|array',
                'officiating_crew' => 'nullable|array',
                'weather_conditions' => 'nullable|string|max:255',
                'attendance_count' => 'nullable|integer|min:0',
                'result_notes' => 'nullable|string',
                'verified_by' => 'nullable|string|max:255',
                'verification_date' => 'nullable|date',
                'is_official' => 'required|boolean'
            ]);

            $result = Result::create($validated);

            $result->load(['sport', 'gameMatch.homeTeam.school', 'gameMatch.awayTeam.school', 'gameMatch.venue']);

            return response()->json([
                'success' => true,
                'message' => 'Result created successfully',
                'data' => $result
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
                'message' => 'Failed to create result',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Result $result): JsonResponse
    {
        try {
            $result->load(['sport', 'gameMatch.homeTeam.school', 'gameMatch.awayTeam.school', 'gameMatch.venue']);

            return response()->json([
                'success' => true,
                'message' => 'Result retrieved successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve result',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Result $result): JsonResponse
    {
        try {
            $validated = $request->validate([
                'sport_id' => 'sometimes|required|exists:sports,id',
                'game_match_id' => 'sometimes|required|exists:game_matches,id',
                'winner_team_id' => 'sometimes|nullable|exists:teams,id',
                'final_score_home' => 'sometimes|required|integer|min:0',
                'final_score_away' => 'sometimes|required|integer|min:0',
                'result_type' => 'sometimes|required|in:win,draw,loss,forfeit,cancelled,postponed',
                'result_summary' => 'sometimes|required|string',
                'detailed_scores' => 'sometimes|nullable|array',
                'performance_stats' => 'sometimes|nullable|array',
                'key_events' => 'sometimes|nullable|array',
                'player_stats' => 'sometimes|nullable|array',
                'officiating_crew' => 'sometimes|nullable|array',
                'weather_conditions' => 'sometimes|nullable|string|max:255',
                'attendance_count' => 'sometimes|nullable|integer|min:0',
                'result_notes' => 'sometimes|nullable|string',
                'verified_by' => 'sometimes|nullable|string|max:255',
                'verification_date' => 'sometimes|nullable|date',
                'is_official' => 'sometimes|required|boolean'
            ]);

            $result->update($validated);

            $result->load(['sport', 'gameMatch.homeTeam.school', 'gameMatch.awayTeam.school', 'gameMatch.venue']);

            return response()->json([
                'success' => true,
                'message' => 'Result updated successfully',
                'data' => $result
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
                'message' => 'Failed to update result',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Result $result): JsonResponse
    {
        try {
            $result->delete();

            return response()->json([
                'success' => true,
                'message' => 'Result deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete result',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get results by team.
     */
    public function getByTeam(int $teamId): JsonResponse
    {
        try {
            $results = Result::with(['sport', 'gameMatch.homeTeam.school', 'gameMatch.awayTeam.school', 'gameMatch.venue'])
                ->whereHas('gameMatch', function($query) use ($teamId) {
                    $query->where('home_team_id', $teamId)
                          ->orWhere('away_team_id', $teamId);
                })
                ->whereHas('gameMatch', function($q) {
                    $q->orderBy('match_date', 'desc');
                })
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Team results retrieved successfully',
                'data' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve team results',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get results by sport.
     */
    public function getBySport(int $sportId): JsonResponse
    {
        try {
            $results = Result::with(['gameMatch.homeTeam.school', 'gameMatch.awayTeam.school', 'gameMatch.venue'])
                ->where('sport_id', $sportId)
                ->whereHas('gameMatch', function($q) {
                    $q->orderBy('match_date', 'desc');
                })
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Sport results retrieved successfully',
                'data' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sport results',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent results.
     */
    public function getRecent(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 10);
            $days = $request->input('days', 7);
            
            $results = Result::with(['sport', 'gameMatch.homeTeam.school', 'gameMatch.awayTeam.school', 'gameMatch.venue'])
                ->where('is_official', true)
                ->whereHas('gameMatch', function($query) use ($days) {
                    $query->where('match_date', '>=', now()->subDays($days))
                          ->orderBy('match_date', 'desc');
                })
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Recent results retrieved successfully',
                'data' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve recent results',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get match statistics.
     */
    public function getMatchStatistics(int $gameMatchId): JsonResponse
    {
        try {
            $result = Result::with(['sport', 'gameMatch.homeTeam.school', 'gameMatch.awayTeam.school'])
                ->where('game_match_id', $gameMatchId)
                ->first();

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Result not found for this match'
                ], 404);
            }

            $statistics = [
                'match_info' => [
                    'home_team' => $result->gameMatch->homeTeam->team_name,
                    'away_team' => $result->gameMatch->awayTeam->team_name,
                    'final_score' => $result->final_score_home . ' - ' . $result->final_score_away,
                    'result_type' => $result->result_type,
                    'match_date' => $result->gameMatch->match_date,
                    'sport' => $result->sport->name
                ],
                'detailed_scores' => $result->detailed_scores,
                'performance_stats' => $result->performance_stats,
                'key_events' => $result->key_events,
                'player_stats' => $result->player_stats,
                'attendance' => $result->attendance_count,
                'weather' => $result->weather_conditions
            ];

            return response()->json([
                'success' => true,
                'message' => 'Match statistics retrieved successfully',
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve match statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify result.
     */
    public function verifyResult(Request $request, Result $result): JsonResponse
    {
        try {
            $validated = $request->validate([
                'verified_by' => 'required|string|max:255',
                'is_official' => 'required|boolean'
            ]);

            $validated['verification_date'] = now();

            $result->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Result verified successfully',
                'data' => $result
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
                'message' => 'Failed to verify result',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get team performance summary.
     */
    public function getTeamPerformance(int $teamId, Request $request): JsonResponse
    {
        try {
            $seasonYear = $request->input('season_year', date('Y'));
            
            $results = Result::with(['gameMatch'])
                ->whereHas('gameMatch', function($query) use ($teamId, $seasonYear) {
                    $query->where(function($q) use ($teamId) {
                        $q->where('home_team_id', $teamId)
                          ->orWhere('away_team_id', $teamId);
                    })
                    ->whereYear('match_date', $seasonYear);
                })
                ->where('is_official', true)
                ->get();

            $performance = [
                'total_matches' => $results->count(),
                'wins' => $results->where('winner_team_id', $teamId)->count(),
                'draws' => $results->where('result_type', 'draw')->count(),
                'losses' => $results->where('winner_team_id', '!=', $teamId)
                    ->where('winner_team_id', '!=', null)->count(),
                'goals_scored' => 0,
                'goals_conceded' => 0
            ];

            // Calculate goals scored and conceded
            foreach ($results as $result) {
                if ($result->gameMatch->home_team_id == $teamId) {
                    $performance['goals_scored'] += $result->final_score_home;
                    $performance['goals_conceded'] += $result->final_score_away;
                } else {
                    $performance['goals_scored'] += $result->final_score_away;
                    $performance['goals_conceded'] += $result->final_score_home;
                }
            }

            $performance['goal_difference'] = $performance['goals_scored'] - $performance['goals_conceded'];
            $performance['win_percentage'] = $performance['total_matches'] > 0 
                ? round($performance['wins'] / $performance['total_matches'] * 100, 2) 
                : 0;

            return response()->json([
                'success' => true,
                'message' => 'Team performance summary retrieved successfully',
                'data' => $performance
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve team performance summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
