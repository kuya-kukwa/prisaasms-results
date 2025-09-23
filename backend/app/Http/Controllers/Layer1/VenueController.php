<?php

namespace App\Http\Controllers\Layer1;

use App\Http\Controllers\Controller;
use App\Models\Layer1\Venue;
use App\Services\Layer1\VenueService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VenueController extends Controller
{
    protected VenueService $venueService;

    public function __construct(VenueService $venueService)
    {
        $this->venueService = $venueService;
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['school_id', 'region_id', 'province_id', 'search']);
        $venues = $this->venueService->list($filters, $request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $venues
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'school_id' => 'nullable|exists:schools,id',
            'province_id' => 'nullable|exists:provinces,id',
            'region_id' => 'nullable|exists:regions,id'
        ]);

        $venue = $this->venueService->create($validated);

        return response()->json([
            'success' => true,
            'data' => $venue
        ], 201);
    }

    public function show(Venue $venue): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $venue
        ]);
    }

    public function update(Request $request, Venue $venue): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'school_id' => 'sometimes|nullable|exists:schools,id',
            'province_id' => 'sometimes|nullable|exists:provinces,id',
            'region_id' => 'sometimes|nullable|exists:regions,id'
        ]);

        $updated = $this->venueService->update($venue, $validated);

        return response()->json([
            'success' => true,
            'data' => $updated
        ]);
    }

    public function destroy(Venue $venue): JsonResponse
    {
        $this->venueService->delete($venue);

        return response()->json([
            'success' => true,
            'message' => 'Venue deleted successfully'
        ]);
    }
}
