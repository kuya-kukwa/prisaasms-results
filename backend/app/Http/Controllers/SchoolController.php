<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class SchoolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = School::select([
                'id', 'name', 'short_name', 'address', 'region_id', 'logo', 'status'
            ])->with('region:id,name');
            
            // Filter by region if provided
            if ($request->has('region')) {
                $query->where('region_id', $request->region);
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
                      ->orWhere('short_name', 'like', "%{$search}%");
                });
            }
            
            // Pagination
            $perPage = $request->input('per_page', 15);
            $schools = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'Schools retrieved successfully',
                'data' => $schools
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve schools',
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
                'short_name' => 'required|string|max:255',
                'address' => 'required|string',
                'region_id' => 'required|exists:regions,id',
                'logo' => 'nullable|string|max:255',
                'status' => 'required|in:active,inactive'
            ]);

            $school = School::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'School created successfully',
                'data' => $school
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
                'message' => 'Failed to create school',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(School $school): JsonResponse
    {
        try {
            $school->load([
                'athletes',
                'teams',
                'users',
                'venues',
                'hostedTournaments',
                'wonTournaments'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'School retrieved successfully',
                'data' => $school
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve school',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, School $school): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'short_name' => 'sometimes|nullable|string|max:255',
                'address' => 'sometimes|nullable|string',
                'region_id' => 'sometimes|nullable|exists:regions,id',
                'logo' => 'sometimes|nullable|string|max:255',
                'status' => 'sometimes|required|in:active,inactive,suspended'
            ]);

            $school->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'School updated successfully',
                'data' => $school
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
                'message' => 'Failed to update school',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(School $school): JsonResponse
    {
        try {
            // Check if school has dependent records
            if ($school->athletes()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete school: School has registered athletes'
                ], 409);
            }

            if ($school->teams()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete school: School has registered teams'
                ], 409);
            }

            $school->delete();

            return response()->json([
                'success' => true,
                'message' => 'School deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete school',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get schools by region.
     */
    public function getByRegion(int $regionId): JsonResponse
    {
        try {
            $schools = School::where('region_id', $regionId)
                ->where('status', 'active')
                ->with('region:id,name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Schools retrieved successfully',
                'data' => $schools
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve schools by region',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get overall system statistics.
     */
    public function getOverallStatistics(): JsonResponse
    {
        try {
            // Start with basic statistics that are less likely to fail
            $statistics = [
                'total_schools' => School::where('status', 'active')->count(),
            ];

            // Add more statistics one by one to isolate any issues
            try {
                $statistics['total_athletes'] = \App\Models\Athlete::where('status', 'active')->count();
            } catch (\Exception $e) {
                $statistics['total_athletes'] = 0;
            }

            try {
                $statistics['total_sports'] = \App\Models\Sport::count();
            } catch (\Exception $e) {
                $statistics['total_sports'] = 0;
            }

            try {
                $statistics['total_matches'] = \App\Models\GameMatch::count();
            } catch (\Exception $e) {
                $statistics['total_matches'] = 0;
            }

            try {
                $statistics['upcoming_matches'] = \App\Models\GameMatch::where('scheduled_start', '>', now())->count();
            } catch (\Exception $e) {
                $statistics['upcoming_matches'] = 0;
            }

            try {
                $statistics['completed_matches'] = \App\Models\GameMatch::where('status', 'completed')->count();
            } catch (\Exception $e) {
                $statistics['completed_matches'] = 0;
            }

            try {
                $statistics['total_tournaments'] = \App\Models\Tournament::count();
            } catch (\Exception $e) {
                $statistics['total_tournaments'] = 0;
            }

            try {
                $statistics['active_tournaments'] = \App\Models\Tournament::where('status', 'active')->count();
            } catch (\Exception $e) {
                $statistics['active_tournaments'] = 0;
            }

            try {
                $statistics['years_of_history'] = \App\Models\PrisaaYear::count();
            } catch (\Exception $e) {
                $statistics['years_of_history'] = 0;
            }

            try {
                $statistics['total_regions'] = School::where('status', 'active')->whereNotNull('region_id')->distinct('region_id')->count('region_id');
            } catch (\Exception $e) {
                $statistics['total_regions'] = 0;
            }

            try {
                $statistics['total_medal_tallies'] = \App\Models\MedalTally::sum('gold') + \App\Models\MedalTally::sum('silver') + \App\Models\MedalTally::sum('bronze');
            } catch (\Exception $e) {
                $statistics['total_medal_tallies'] = 0;
            }

            return response()->json([
                'success' => true,
                'message' => 'Overall statistics retrieved successfully',
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve overall statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get school statistics.
     */
    public function getStatistics(School $school): JsonResponse
    {
        try {
            $statistics = [
                'total_athletes' => $school->athletes()->count(),
                'total_teams' => $school->teams()->count(),
                'total_users' => $school->users()->count(),
                'total_venues' => $school->venues()->count(),
                'tournaments_hosted' => $school->hostedTournaments()->count(),
                'tournaments_won' => $school->wonTournaments()->count(),
                'active_athletes' => $school->athletes()->where('status', 'active')->count(),
                'active_teams' => $school->teams()->where('status', 'active')->count()
            ];

            return response()->json([
                'success' => true,
                'message' => 'School statistics retrieved successfully',
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve school statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
