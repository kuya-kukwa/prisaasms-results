<?php

namespace App\Http\Controllers;

use App\Models\MedalTally;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MedalTallyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = MedalTally::with(['school', 'sport']);
            
            // Filter by school if provided
            if ($request->has('school_id')) {
                $query->where('school_id', $request->school_id);
            }
            
            // Filter by sport if provided
            if ($request->has('sport_id')) {
                $query->where('sport_id', $request->sport_id);
            }
            
            // Filter by event year if provided
            if ($request->has('event_year')) {
                $query->where('event_year', $request->event_year);
            }
            
            // Filter by medal type if provided
            if ($request->has('medal_type')) {
                $query->where('medal_type', $request->medal_type);
            }
            
            // Filter by category if provided
            if ($request->has('category')) {
                $query->where('category', $request->category);
            }
            
            // Search by event name or athlete name
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('event_name', 'like', "%{$search}%")
                      ->orWhere('athlete_name', 'like', "%{$search}%")
                      ->orWhere('team_name', 'like', "%{$search}%");
                });
            }
            
            // Order by event year and medal type
            $query->orderBy('event_year', 'desc')
                  ->orderByRaw("FIELD(medal_type, 'gold', 'silver', 'bronze')");
            
            // Pagination
            $perPage = $request->input('per_page', 15);
            $medalTallies = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'Medal tallies retrieved successfully',
                'data' => $medalTallies
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve medal tallies',
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
                'school_id' => 'required|exists:schools,id',
                'sport_id' => 'required|exists:sports,id',
                'event_name' => 'required|string|max:255',
                'event_year' => 'required|integer',
                'medal_type' => 'required|in:gold,silver,bronze',
                'category' => 'required|string|max:255',
                'athlete_name' => 'nullable|string|max:255',
                'team_name' => 'nullable|string|max:255',
                'performance_record' => 'nullable|string|max:255',
                'event_date' => 'required|date',
                'venue_location' => 'nullable|string|max:255',
                'competition_level' => 'required|in:school,regional,national,international',
                'medal_notes' => 'nullable|string'
            ]);

            $medalTally = MedalTally::create($validated);

            $medalTally->load(['school', 'sport']);

            return response()->json([
                'success' => true,
                'message' => 'Medal tally created successfully',
                'data' => $medalTally
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
                'message' => 'Failed to create medal tally',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MedalTally $medalTally): JsonResponse
    {
        try {
            $medalTally->load(['school', 'sport']);

            return response()->json([
                'success' => true,
                'message' => 'Medal tally retrieved successfully',
                'data' => $medalTally
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve medal tally',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MedalTally $medalTally): JsonResponse
    {
        try {
            $validated = $request->validate([
                'school_id' => 'sometimes|required|exists:schools,id',
                'sport_id' => 'sometimes|required|exists:sports,id',
                'event_name' => 'sometimes|required|string|max:255',
                'event_year' => 'sometimes|required|integer',
                'medal_type' => 'sometimes|required|in:gold,silver,bronze',
                'category' => 'sometimes|required|string|max:255',
                'athlete_name' => 'sometimes|nullable|string|max:255',
                'team_name' => 'sometimes|nullable|string|max:255',
                'performance_record' => 'sometimes|nullable|string|max:255',
                'event_date' => 'sometimes|required|date',
                'venue_location' => 'sometimes|nullable|string|max:255',
                'competition_level' => 'sometimes|required|in:school,regional,national,international',
                'medal_notes' => 'sometimes|nullable|string'
            ]);

            $medalTally->update($validated);

            $medalTally->load(['school', 'sport']);

            return response()->json([
                'success' => true,
                'message' => 'Medal tally updated successfully',
                'data' => $medalTally
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
                'message' => 'Failed to update medal tally',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedalTally $medalTally): JsonResponse
    {
        try {
            $medalTally->delete();

            return response()->json([
                'success' => true,
                'message' => 'Medal tally deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete medal tally',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get medal tally by school.
     */
    public function getBySchool(int $schoolId, Request $request): JsonResponse
    {
        try {
            $eventYear = $request->input('event_year');
            
            $query = MedalTally::with(['sport'])
                ->where('school_id', $schoolId);
            
            if ($eventYear) {
                $query->where('event_year', $eventYear);
            }
            
            $medals = $query->orderBy('event_year', 'desc')
                ->orderByRaw("FIELD(medal_type, 'gold', 'silver', 'bronze')")
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'School medal tally retrieved successfully',
                'data' => $medals
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve school medal tally',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get medal tally by sport.
     */
    public function getBySport(int $sportId, Request $request): JsonResponse
    {
        try {
            $eventYear = $request->input('event_year');
            
            $query = MedalTally::with(['school'])
                ->where('sport_id', $sportId);
            
            if ($eventYear) {
                $query->where('event_year', $eventYear);
            }
            
            $medals = $query->orderBy('event_year', 'desc')
                ->orderByRaw("FIELD(medal_type, 'gold', 'silver', 'bronze')")
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Sport medal tally retrieved successfully',
                'data' => $medals
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sport medal tally',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get overall medal statistics.
     */
    public function getOverallStatistics(Request $request): JsonResponse
    {
        try {
            $eventYear = $request->input('event_year', date('Y'));
            
            $query = MedalTally::where('event_year', $eventYear);
            
            $totalGold = $query->clone()->where('medal_type', 'gold')->count();
            $totalSilver = $query->clone()->where('medal_type', 'silver')->count();
            $totalBronze = $query->clone()->where('medal_type', 'bronze')->count();
            
            // Get medal count by school
            $schoolMedals = MedalTally::selectRaw('school_id, medal_type, COUNT(*) as count')
                ->with('school:id,name,short_name')
                ->where('event_year', $eventYear)
                ->groupBy('school_id', 'medal_type')
                ->get()
                ->groupBy('school_id');
            
            // Get medal count by sport
            $sportMedals = MedalTally::selectRaw('sport_id, medal_type, COUNT(*) as count')
                ->with('sport:id,name')
                ->where('event_year', $eventYear)
                ->groupBy('sport_id', 'medal_type')
                ->get()
                ->groupBy('sport_id');
            
            $statistics = [
                'event_year' => $eventYear,
                'total_medals' => $totalGold + $totalSilver + $totalBronze,
                'gold_medals' => $totalGold,
                'silver_medals' => $totalSilver,
                'bronze_medals' => $totalBronze,
                'medals_by_school' => $schoolMedals,
                'medals_by_sport' => $sportMedals
            ];

            return response()->json([
                'success' => true,
                'message' => 'Overall medal statistics retrieved successfully',
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve overall medal statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get school medal ranking.
     */
    public function getSchoolRanking(Request $request): JsonResponse
    {
        try {
            $eventYear = $request->input('event_year', date('Y'));
            
            $schoolRankings = MedalTally::selectRaw('
                    school_id,
                    SUM(CASE WHEN medal_type = "gold" THEN 1 ELSE 0 END) as gold_count,
                    SUM(CASE WHEN medal_type = "silver" THEN 1 ELSE 0 END) as silver_count,
                    SUM(CASE WHEN medal_type = "bronze" THEN 1 ELSE 0 END) as bronze_count,
                    COUNT(*) as total_medals,
                    (SUM(CASE WHEN medal_type = "gold" THEN 3 ELSE 0 END) +
                     SUM(CASE WHEN medal_type = "silver" THEN 2 ELSE 0 END) +
                     SUM(CASE WHEN medal_type = "bronze" THEN 1 ELSE 0 END)) as total_points
                ')
                ->with('school:id,name,short_name,region')
                ->where('event_year', $eventYear)
                ->groupBy('school_id')
                ->orderByDesc('total_points')
                ->orderByDesc('gold_count')
                ->orderByDesc('silver_count')
                ->orderByDesc('bronze_count')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'School medal ranking retrieved successfully',
                'data' => $schoolRankings
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve school medal ranking',
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
            $eventYear = $request->input('event_year', date('Y'));
            $medalType = $request->input('medal_type');
            $limit = $request->input('limit', 10);
            
            $query = MedalTally::with(['school', 'sport'])
                ->where('event_year', $eventYear);
            
            if ($medalType) {
                $query->where('medal_type', $medalType);
            }
            
            $topPerformers = $query->orderByRaw("FIELD(medal_type, 'gold', 'silver', 'bronze')")
                ->orderBy('event_date', 'desc')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Top performers retrieved successfully',
                'data' => $topPerformers
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve top performers',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
