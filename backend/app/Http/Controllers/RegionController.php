<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Region::query();

            // Filter by status if provided
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Search by name or code
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            }

            // Order by name
            $query->orderBy('name');

            // Pagination
            $perPage = $request->input('per_page', 15);
            $regions = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Regions retrieved successfully',
                'data' => $regions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve regions',
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
                'code' => 'required|string|max:10|unique:regions,code',
                'status' => 'required|in:active,inactive'
            ]);

            $region = Region::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Region created successfully',
                'data' => $region
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
                'message' => 'Failed to create region',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Region $region): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Region retrieved successfully',
                'data' => $region->load(['schools', 'venues'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve region',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Region $region): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'code' => 'sometimes|required|string|max:10|unique:regions,code,' . $region->id,
                'status' => 'sometimes|required|in:active,inactive'
            ]);

            $region->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Region updated successfully',
                'data' => $region
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
                'message' => 'Failed to update region',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Region $region): JsonResponse
    {
        try {
            // Check if region has related records
            $schoolsCount = $region->schools()->count();
            $venuesCount = $region->venues()->count();

            if ($schoolsCount > 0 || $venuesCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete region with associated schools or venues',
                    'data' => [
                        'schools_count' => $schoolsCount,
                        'venues_count' => $venuesCount
                    ]
                ], 422);
            }

            $region->delete();

            return response()->json([
                'success' => true,
                'message' => 'Region deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete region',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get regions by status.
     */
    public function getByStatus(string $status): JsonResponse
    {
        try {
            $regions = Region::where('status', $status)
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Regions retrieved successfully',
                'data' => $regions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve regions by status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get region statistics.
     */
    public function getStatistics(Region $region): JsonResponse
    {
        try {
            $statistics = [
                'total_schools' => $region->schools()->count(),
                'total_venues' => $region->venues()->count(),
                'active_schools' => $region->schools()->where('status', 'active')->count(),
                'active_venues' => $region->venues()->where('status', 'active')->count()
            ];

            return response()->json([
                'success' => true,
                'message' => 'Region statistics retrieved successfully',
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve region statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}