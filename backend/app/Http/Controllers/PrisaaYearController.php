<?php

namespace App\Http\Controllers;

use App\Models\PrisaaYear;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class PrisaaYearController extends Controller
{
    /**
     * Display a listing of PRISAA years.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = PrisaaYear::with(['director']);
            
            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            // Filter by year range
            if ($request->has('from_year')) {
                $query->where('year', '>=', $request->from_year);
            }
            
            if ($request->has('to_year')) {
                $query->where('year', '<=', $request->to_year);
            }
            
            // Search
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('year', 'like', "%{$search}%")
                      ->orWhere('host_region', 'like', "%{$search}%")
                      ->orWhere('host_province', 'like', "%{$search}%");
                });
            }
            
            $perPage = $request->input('per_page', 15);
            $prisaaYears = $query->orderBy('year', 'desc')->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'PRISAA years retrieved successfully',
                'data' => $prisaaYears
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve PRISAA years',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created PRISAA year.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'year' => 'required|integer|unique:prisaa_years,year',
                'host_region' => 'nullable|string|max:255',
                'host_province' => 'nullable|string|max:255',
                'host_city' => 'nullable|string|max:255',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'director_id' => 'nullable|exists:users,id',
                'description' => 'nullable|string',
                'status' => 'required|in:planning,ongoing,completed,cancelled'
            ]);

            $prisaaYear = PrisaaYear::create($validated);
            $prisaaYear->load(['director']);

            return response()->json([
                'success' => true,
                'message' => 'PRISAA year created successfully',
                'data' => $prisaaYear
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
                'message' => 'Failed to create PRISAA year',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified PRISAA year.
     */
    public function show(PrisaaYear $prisaaYear): JsonResponse
    {
        try {
            $prisaaYear->load([
                'director',
                'tournaments',
                'medalTallies',
                'overallChampions.school'
            ]);

            $statistics = $prisaaYear->getYearlyStatistics();

            return response()->json([
                'success' => true,
                'message' => 'PRISAA year retrieved successfully',
                'data' => $prisaaYear,
                'statistics' => $statistics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve PRISAA year',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified PRISAA year.
     */
    public function update(Request $request, PrisaaYear $prisaaYear): JsonResponse
    {
        try {
            $validated = $request->validate([
                'year' => 'sometimes|integer|unique:prisaa_years,year,' . $prisaaYear->id,
                'host_region' => 'sometimes|nullable|string|max:255',
                'host_province' => 'sometimes|nullable|string|max:255',
                'host_city' => 'sometimes|nullable|string|max:255',
                'start_date' => 'sometimes|nullable|date',
                'end_date' => 'sometimes|nullable|date|after_or_equal:start_date',
                'total_participants' => 'sometimes|integer|min:0',
                'total_schools' => 'sometimes|integer|min:0',
                'total_sports' => 'sometimes|integer|min:0',
                'total_events' => 'sometimes|integer|min:0',
                'director_id' => 'sometimes|nullable|exists:users,id',
                'description' => 'sometimes|nullable|string',
                'highlights' => 'sometimes|nullable|array',
                'achievements' => 'sometimes|nullable|array',
                'records_broken' => 'sometimes|nullable|array',
                'status' => 'sometimes|in:planning,ongoing,completed,cancelled'
            ]);

            $prisaaYear->update($validated);
            $prisaaYear->load(['director']);

            return response()->json([
                'success' => true,
                'message' => 'PRISAA year updated successfully',
                'data' => $prisaaYear
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
                'message' => 'Failed to update PRISAA year',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified PRISAA year.
     */
    public function destroy(PrisaaYear $prisaaYear): JsonResponse
    {
        try {
            // Check if there are dependent records
            if ($prisaaYear->tournaments()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete PRISAA year: There are tournaments associated with this year'
                ], 409);
            }

            $prisaaYear->delete();

            return response()->json([
                'success' => true,
                'message' => 'PRISAA year deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete PRISAA year',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get yearly statistics and trends.
     */
    public function getStatistics(Request $request): JsonResponse
    {
        try {
            $years = $request->input('years', 5); // Default to last 5 years
            
            $prisaaYears = PrisaaYear::orderBy('year', 'desc')
                ->limit($years)
                ->get();

            $statistics = $prisaaYears->map(function ($year) {
                return $year->getYearlyStatistics();
            });

            return response()->json([
                'success' => true,
                'message' => 'PRISAA statistics retrieved successfully',
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get multi-level breakdown for a specific year.
     */
    public function getMultiLevelBreakdown(PrisaaYear $prisaaYear): JsonResponse
    {
        try {
            $breakdown = [
                'year' => $prisaaYear->year,
                'provincial' => [
                    'tournaments' => $prisaaYear->provincialGames()->count(),
                    'champions' => $prisaaYear->overallChampions()
                        ->where('level', 'provincial')
                        ->with('school')
                        ->get()
                ],
                'regional' => [
                    'tournaments' => $prisaaYear->regionalGames()->count(),
                    'champions' => $prisaaYear->overallChampions()
                        ->where('level', 'regional')
                        ->with('school')
                        ->get()
                ],
                'national' => [
                    'tournaments' => $prisaaYear->nationalGames()->count(),
                    'champions' => $prisaaYear->overallChampions()
                        ->where('level', 'national')
                        ->with('school')
                        ->get()
                ]
            ];

            return response()->json([
                'success' => true,
                'message' => 'Multi-level breakdown retrieved successfully',
                'data' => $breakdown
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve multi-level breakdown',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
