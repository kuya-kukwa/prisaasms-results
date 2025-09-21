<?php

namespace App\Http\Controllers;

use App\Models\OverallChampion;
use App\Models\PrisaaYear;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class OverallChampionController extends Controller
{
    /**
     * Display a listing of overall champions.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = OverallChampion::with(['prisaaYear', 'school']);
            
            // Filter by PRISAA year
            if ($request->has('prisaa_year_id')) {
                $query->where('prisaa_year_id', $request->prisaa_year_id);
            }
            
            // Filter by level
            if ($request->has('level')) {
                $query->where('level', $request->level);
            }
            
            // Filter by category
            if ($request->has('category')) {
                $query->where('category', $request->category);
            }
            
            // Filter by region/province
            if ($request->has('region')) {
                $query->where('region', $request->region);
            }
            
            if ($request->has('province')) {
                $query->where('province', $request->province);
            }
            
            // Search
            if ($request->has('search')) {
                $search = $request->search;
                $query->whereHas('school', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            }
            
            $perPage = $request->input('per_page', 15);
            $champions = $query->orderBy('rank', 'asc')->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'Overall champions retrieved successfully',
                'data' => $champions
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve overall champions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created overall champion.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'prisaa_year_id' => 'required|exists:prisaa_years,id',
                'level' => 'required|in:provincial,regional,national',
                'category' => 'nullable|string|max:100',
                'school_id' => 'required|exists:schools,id',
                'points' => 'required|numeric|min:0',
                'gold_medals' => 'required|integer|min:0',
                'silver_medals' => 'required|integer|min:0',
                'bronze_medals' => 'required|integer|min:0',
                'rank' => 'required|integer|min:1',
                'region' => 'nullable|string|max:100',
                'province' => 'nullable|string|max:100'
            ]);

            // Calculate total medals
            $validated['total_medals'] = $validated['gold_medals'] + 
                                       $validated['silver_medals'] + 
                                       $validated['bronze_medals'];

            $champion = OverallChampion::create($validated);
            $champion->load(['prisaaYear', 'school']);

            return response()->json([
                'success' => true,
                'message' => 'Overall champion record created successfully',
                'data' => $champion
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
                'message' => 'Failed to create overall champion record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified overall champion.
     */
    public function show(OverallChampion $overallChampion): JsonResponse
    {
        try {
            $overallChampion->load(['prisaaYear', 'school']);

            return response()->json([
                'success' => true,
                'message' => 'Overall champion retrieved successfully',
                'data' => $overallChampion
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve overall champion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified overall champion.
     */
    public function update(Request $request, OverallChampion $overallChampion): JsonResponse
    {
        try {
            $validated = $request->validate([
                'prisaa_year_id' => 'sometimes|exists:prisaa_years,id',
                'level' => 'sometimes|in:provincial,regional,national',
                'category' => 'sometimes|nullable|string|max:100',
                'school_id' => 'sometimes|exists:schools,id',
                'points' => 'sometimes|numeric|min:0',
                'gold_medals' => 'sometimes|integer|min:0',
                'silver_medals' => 'sometimes|integer|min:0',
                'bronze_medals' => 'sometimes|integer|min:0',
                'rank' => 'sometimes|integer|min:1',
                'region' => 'sometimes|nullable|string|max:100',
                'province' => 'sometimes|nullable|string|max:100'
            ]);

            // Recalculate total medals if any medal count is updated
            if (isset($validated['gold_medals']) || 
                isset($validated['silver_medals']) || 
                isset($validated['bronze_medals'])) {
                
                $goldMedals = $validated['gold_medals'] ?? $overallChampion->gold_medals;
                $silverMedals = $validated['silver_medals'] ?? $overallChampion->silver_medals;
                $bronzeMedals = $validated['bronze_medals'] ?? $overallChampion->bronze_medals;
                
                $validated['total_medals'] = $goldMedals + $silverMedals + $bronzeMedals;
            }

            $overallChampion->update($validated);
            $overallChampion->load(['prisaaYear', 'school']);

            return response()->json([
                'success' => true,
                'message' => 'Overall champion updated successfully',
                'data' => $overallChampion
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
                'message' => 'Failed to update overall champion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified overall champion.
     */
    public function destroy(OverallChampion $overallChampion): JsonResponse
    {
        try {
            $overallChampion->delete();

            return response()->json([
                'success' => true,
                'message' => 'Overall champion deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete overall champion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get champions by year.
     */
    public function getByYear(int $year): JsonResponse
    {
        try {
            $prisaaYear = PrisaaYear::where('year', $year)->first();
            
            if (!$prisaaYear) {
                return response()->json([
                    'success' => false,
                    'message' => 'PRISAA year not found'
                ], 404);
            }

            $champions = OverallChampion::where('prisaa_year_id', $prisaaYear->id)
                ->with(['school'])
                ->orderBy('level')
                ->orderBy('rank')
                ->get()
                ->groupBy('level');

            return response()->json([
                'success' => true,
                'message' => 'Champions by year retrieved successfully',
                'data' => [
                    'year' => $year,
                    'prisaa_year' => $prisaaYear,
                    'champions_by_level' => $champions
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve champions by year',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get champions by level.
     */
    public function getByLevel(string $level): JsonResponse
    {
        try {
            $champions = OverallChampion::where('level', $level)
                ->with(['prisaaYear', 'school'])
                ->orderBy('prisaa_year_id', 'desc')
                ->orderBy('rank', 'asc')
                ->get()
                ->groupBy('prisaa_year.year');

            return response()->json([
                'success' => true,
                'message' => 'Champions by level retrieved successfully',
                'data' => [
                    'level' => $level,
                    'champions_by_year' => $champions
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve champions by level',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
