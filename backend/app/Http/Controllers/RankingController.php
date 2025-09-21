<?php

namespace App\Http\Controllers;

use App\Models\Ranking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RankingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Ranking::with(['sport', 'team.school', 'athlete.school']);
            
            // Filter by sport if provided
            if ($request->has('sport_id')) {
                $query->where('sport_id', $request->sport_id);
            }
            
            // Filter by ranking type if provided
            if ($request->has('ranking_type')) {
                $query->where('ranking_type', $request->ranking_type);
            }
            
            // Filter by season year if provided
            if ($request->has('season_year')) {
                $query->where('season_year', $request->season_year);
            }
            
            // Filter by team if provided
            if ($request->has('team_id')) {
                $query->where('team_id', $request->team_id);
            }
            
            // Filter by athlete if provided
            if ($request->has('athlete_id')) {
                $query->where('athlete_id', $request->athlete_id);
            }
            
            // Filter by rank range
            if ($request->has('min_rank')) {
                $query->where('rank', '>=', $request->min_rank);
            }
            
            if ($request->has('max_rank')) {
                $query->where('rank', '<=', $request->max_rank);
            }
            
            // Search by category or notes
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('category', 'like', "%{$search}%")
                      ->orWhere('ranking_notes', 'like', "%{$search}%");
                });
            }
            
            // Order by rank
            $query->orderBy('rank', 'asc');
            
            // Pagination
            $perPage = $request->input('per_page', 15);
            $rankings = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'Rankings retrieved successfully',
                'data' => $rankings
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve rankings',
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
                'ranking_type' => 'required|in:team,individual',
                'team_id' => 'required_if:ranking_type,team|nullable|exists:teams,id',
                'athlete_id' => 'required_if:ranking_type,individual|nullable|exists:athletes,id',
                'rank' => 'required|integer|min:1',
                'points' => 'required|integer|min:0',
                'wins' => 'nullable|integer|min:0',
                'losses' => 'nullable|integer|min:0',
                'draws' => 'nullable|integer|min:0',
                'matches_played' => 'nullable|integer|min:0',
                'goals_for' => 'nullable|integer|min:0',
                'goals_against' => 'nullable|integer|min:0',
                'category' => 'required|string|max:255',
                'season_year' => 'required|integer',
                'last_updated' => 'required|date',
                'ranking_notes' => 'nullable|string'
            ]);

            // Validate that either team_id or athlete_id is provided based on ranking_type
            if ($validated['ranking_type'] === 'team' && empty($validated['team_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Team ID is required for team rankings'
                ], 422);
            }

            if ($validated['ranking_type'] === 'individual' && empty($validated['athlete_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Athlete ID is required for individual rankings'
                ], 422);
            }

            $ranking = Ranking::create($validated);

            $ranking->load(['sport', 'team.school', 'athlete.school']);

            return response()->json([
                'success' => true,
                'message' => 'Ranking created successfully',
                'data' => $ranking
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
                'message' => 'Failed to create ranking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Ranking $ranking): JsonResponse
    {
        try {
            $ranking->load(['sport', 'team.school', 'athlete.school']);

            return response()->json([
                'success' => true,
                'message' => 'Ranking retrieved successfully',
                'data' => $ranking
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve ranking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ranking $ranking): JsonResponse
    {
        try {
            $validated = $request->validate([
                'sport_id' => 'sometimes|required|exists:sports,id',
                'ranking_type' => 'sometimes|required|in:team,individual',
                'team_id' => 'sometimes|required_if:ranking_type,team|nullable|exists:teams,id',
                'athlete_id' => 'sometimes|required_if:ranking_type,individual|nullable|exists:athletes,id',
                'rank' => 'sometimes|required|integer|min:1',
                'points' => 'sometimes|required|integer|min:0',
                'wins' => 'sometimes|nullable|integer|min:0',
                'losses' => 'sometimes|nullable|integer|min:0',
                'draws' => 'sometimes|nullable|integer|min:0',
                'matches_played' => 'sometimes|nullable|integer|min:0',
                'goals_for' => 'sometimes|nullable|integer|min:0',
                'goals_against' => 'sometimes|nullable|integer|min:0',
                'category' => 'sometimes|required|string|max:255',
                'season_year' => 'sometimes|required|integer',
                'last_updated' => 'sometimes|required|date',
                'ranking_notes' => 'sometimes|nullable|string'
            ]);

            $ranking->update($validated);

            $ranking->load(['sport', 'team.school', 'athlete.school']);

            return response()->json([
                'success' => true,
                'message' => 'Ranking updated successfully',
                'data' => $ranking
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
                'message' => 'Failed to update ranking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ranking $ranking): JsonResponse
    {
        try {
            $ranking->delete();

            return response()->json([
                'success' => true,
                'message' => 'Ranking deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete ranking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get team rankings by sport and season.
     */
    public function getTeamRankings(Request $request, int $sportId): JsonResponse
    {
        try {
            $seasonYear = $request->input('season_year', date('Y'));
            
            $rankings = Ranking::with(['team.school', 'sport'])
                ->where('sport_id', $sportId)
                ->where('ranking_type', 'team')
                ->where('season_year', $seasonYear)
                ->orderBy('rank')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Team rankings retrieved successfully',
                'data' => $rankings
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve team rankings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get individual rankings by sport and season.
     */
    public function getIndividualRankings(Request $request, int $sportId): JsonResponse
    {
        try {
            $seasonYear = $request->input('season_year', date('Y'));
            $category = $request->input('category');
            
            $query = Ranking::with(['athlete.school', 'sport'])
                ->where('sport_id', $sportId)
                ->where('ranking_type', 'individual')
                ->where('season_year', $seasonYear);
            
            if ($category) {
                $query->where('category', $category);
            }
            
            $rankings = $query->orderBy('rank')->get();

            return response()->json([
                'success' => true,
                'message' => 'Individual rankings retrieved successfully',
                'data' => $rankings
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve individual rankings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get top performers.
     */
    public function getTopPerformers(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 10);
            $rankingType = $request->input('ranking_type', 'both');
            $seasonYear = $request->input('season_year', date('Y'));
            
            $query = Ranking::with(['sport', 'team.school', 'athlete.school'])
                ->where('season_year', $seasonYear)
                ->where('rank', '<=', $limit);
            
            if ($rankingType !== 'both') {
                $query->where('ranking_type', $rankingType);
            }
            
            $rankings = $query->orderBy('sport_id')
                ->orderBy('ranking_type')
                ->orderBy('rank')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Top performers retrieved successfully',
                'data' => $rankings
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve top performers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update rankings based on latest results.
     */
    public function updateRankings(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'sport_id' => 'required|exists:sports,id',
                'season_year' => 'required|integer',
                'ranking_type' => 'required|in:team,individual'
            ]);

            // This is a placeholder for the ranking calculation logic
            // In a real implementation, you would calculate rankings based on:
            // - Match results
            // - Points system
            // - Win/loss records
            // - Goal differences
            // - Performance metrics

            return response()->json([
                'success' => true,
                'message' => 'Rankings updated successfully',
                'note' => 'This endpoint would implement ranking calculation logic based on latest results'
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
                'message' => 'Failed to update rankings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get ranking statistics.
     */
    public function getRankingStatistics(int $sportId, Request $request): JsonResponse
    {
        try {
            $seasonYear = $request->input('season_year', date('Y'));
            
            $teamRankings = Ranking::where('sport_id', $sportId)
                ->where('ranking_type', 'team')
                ->where('season_year', $seasonYear)
                ->count();
            
            $individualRankings = Ranking::where('sport_id', $sportId)
                ->where('ranking_type', 'individual')
                ->where('season_year', $seasonYear)
                ->count();
            
            $totalMatches = Ranking::where('sport_id', $sportId)
                ->where('season_year', $seasonYear)
                ->sum('matches_played');
            
            $statistics = [
                'total_team_rankings' => $teamRankings,
                'total_individual_rankings' => $individualRankings,
                'total_matches_played' => $totalMatches,
                'season_year' => $seasonYear
            ];

            return response()->json([
                'success' => true,
                'message' => 'Ranking statistics retrieved successfully',
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve ranking statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
