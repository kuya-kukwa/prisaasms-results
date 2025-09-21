<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VenueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Venue::with(['school']);
            
            // Filter by school if provided
            if ($request->has('school_id')) {
                $query->where('school_id', $request->school_id);
            }
            
            // Filter by venue type if provided
            if ($request->has('venue_type')) {
                $query->where('venue_type', $request->venue_type);
            }
            
            // Filter by status if provided
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            // Filter by capacity range
            if ($request->has('min_capacity')) {
                $query->where('capacity', '>=', $request->min_capacity);
            }
            
            if ($request->has('max_capacity')) {
                $query->where('capacity', '<=', $request->max_capacity);
            }
            
            // Search by venue name or location
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('venue_name', 'like', "%{$search}%")
                      ->orWhere('location', 'like', "%{$search}%")
                      ->orWhere('address', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }
            
            // Pagination
            $perPage = $request->input('per_page', 15);
            $venues = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'Venues retrieved successfully',
                'data' => $venues
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve venues',
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
                'venue_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'location' => 'required|string|max:255',
                'address' => 'required|string',
                'school_id' => 'required|exists:schools,id',
                'venue_type' => 'required|in:indoor,outdoor,mixed',
                'capacity' => 'required|integer|min:1',
                'facilities' => 'nullable|array',
                'contact_person' => 'nullable|string|max:255',
                'contact_number' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'images' => 'nullable|array',
                'amenities' => 'nullable|array',
                'rules_regulations' => 'nullable|string',
                'hourly_rate' => 'nullable|numeric|min:0',
                'availability_schedule' => 'nullable|array',
                'status' => 'required|in:active,inactive,maintenance'
            ]);

            $venue = Venue::create($validated);

            $venue->load(['school']);

            return response()->json([
                'success' => true,
                'message' => 'Venue created successfully',
                'data' => $venue
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
                'message' => 'Failed to create venue',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Venue $venue): JsonResponse
    {
        try {
            $venue->load(['school', 'schedules', 'gameMatches']);

            return response()->json([
                'success' => true,
                'message' => 'Venue retrieved successfully',
                'data' => $venue
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve venue',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Venue $venue): JsonResponse
    {
        try {
            $validated = $request->validate([
                'venue_name' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|nullable|string',
                'location' => 'sometimes|required|string|max:255',
                'address' => 'sometimes|required|string',
                'school_id' => 'sometimes|required|exists:schools,id',
                'venue_type' => 'sometimes|required|in:indoor,outdoor,mixed',
                'capacity' => 'sometimes|required|integer|min:1',
                'facilities' => 'sometimes|nullable|array',
                'contact_person' => 'sometimes|nullable|string|max:255',
                'contact_number' => 'sometimes|nullable|string|max:255',
                'email' => 'sometimes|nullable|email|max:255',
                'latitude' => 'sometimes|nullable|numeric|between:-90,90',
                'longitude' => 'sometimes|nullable|numeric|between:-180,180',
                'images' => 'sometimes|nullable|array',
                'amenities' => 'sometimes|nullable|array',
                'rules_regulations' => 'sometimes|nullable|string',
                'hourly_rate' => 'sometimes|nullable|numeric|min:0',
                'availability_schedule' => 'sometimes|nullable|array',
                'status' => 'sometimes|required|in:active,inactive,maintenance'
            ]);

            $venue->update($validated);

            $venue->load(['school']);

            return response()->json([
                'success' => true,
                'message' => 'Venue updated successfully',
                'data' => $venue
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
                'message' => 'Failed to update venue',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venue $venue): JsonResponse
    {
        try {
            // Check if venue has dependent records
            if ($venue->schedules()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete venue: Venue has scheduled events'
                ], 409);
            }

            if ($venue->gameMatches()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete venue: Venue has game match records'
                ], 409);
            }

            $venue->delete();

            return response()->json([
                'success' => true,
                'message' => 'Venue deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete venue',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get venues by school.
     */
    public function getBySchool(int $schoolId): JsonResponse
    {
        try {
            $venues = Venue::where('host_school_id', $schoolId)
                ->where('status', 'active')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Venues retrieved successfully',
                'data' => $venues
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve venues by school',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get venues by type.
     */
    public function getByType(string $type): JsonResponse
    {
        try {
            $venues = Venue::with(['school'])
                ->where('venue_type', $type)
                ->where('status', 'active')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Venues retrieved successfully',
                'data' => $venues
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve venues by type',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get venue availability.
     */
    public function getAvailability(Venue $venue, Request $request): JsonResponse
    {
        try {
            $date = $request->input('date', now()->format('Y-m-d'));
            
            // Get scheduled events for the venue on the specific date
            $scheduledEvents = $venue->schedules()
                ->whereDate('event_date', $date)
                ->get(['start_time', 'end_time', 'event_name']);

            $gameMatches = $venue->gameMatches()
                ->whereDate('match_date', $date)
                ->get(['match_time', 'home_team_id', 'away_team_id']);

            return response()->json([
                'success' => true,
                'message' => 'Venue availability retrieved successfully',
                'data' => [
                    'venue' => $venue,
                    'date' => $date,
                    'scheduled_events' => $scheduledEvents,
                    'game_matches' => $gameMatches,
                    'availability_schedule' => $venue->availability_schedule
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve venue availability',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get venue statistics.
     */
    public function getStatistics(Venue $venue): JsonResponse
    {
        try {
            $statistics = [
                'total_events' => $venue->schedules()->count(),
                'total_matches' => $venue->gameMatches()->count(),
                'upcoming_events' => $venue->schedules()->where('event_date', '>=', now())->count(),
                'upcoming_matches' => $venue->gameMatches()->where('match_date', '>=', now())->count(),
                'capacity_utilization' => $venue->capacity,
                'monthly_bookings' => $venue->schedules()
                    ->whereMonth('event_date', now()->month)
                    ->whereYear('event_date', now()->year)
                    ->count()
            ];

            return response()->json([
                'success' => true,
                'message' => 'Venue statistics retrieved successfully',
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve venue statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all venues for admin management
     */
    public function adminIndex(Request $request)
    {
        try {
            $query = Venue::query();

            // Apply filters
            if ($request->has('country') && $request->country !== '') {
                $query->where('country', $request->country);
            }

            if ($request->has('state') && $request->state !== '') {
                $query->where('state', $request->state);
            }

            if ($request->has('city') && $request->city !== '') {
                $query->where('city', $request->city);
            }

            if ($request->has('is_active') && $request->is_active !== '') {
                $query->where('is_active', $request->is_active === 'true');
            }

            if ($request->has('search') && $request->search !== '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('address', 'like', "%{$search}%")
                      ->orWhere('city', 'like', "%{$search}%")
                      ->orWhere('contact_person', 'like', "%{$search}%");
                });
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortBy, $sortDirection);

            // Paginate results
            $perPage = $request->get('per_page', 15);
            $venues = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Venues retrieved successfully',
                'data' => $venues->items(),
                'meta' => [
                    'current_page' => $venues->currentPage(),
                    'last_page' => $venues->lastPage(),
                    'per_page' => $venues->perPage(),
                    'total' => $venues->total(),
                    'from' => $venues->firstItem(),
                    'to' => $venues->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve venues',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get venue statistics for admin dashboard
     */
    public function getAdminStats()
    {
        try {
            $totalVenues = Venue::count();
            $activeVenues = Venue::where('is_active', true)->count();
            $inactiveVenues = $totalVenues - $activeVenues;

            $venuesByCountry = Venue::selectRaw('country, COUNT(*) as count')
                ->groupBy('country')
                ->get()
                ->pluck('count', 'country');

            $venuesByState = Venue::selectRaw('state, COUNT(*) as count')
                ->groupBy('state')
                ->get()
                ->pluck('count', 'state');

            $totalCapacity = Venue::whereNotNull('capacity')->sum('capacity');
            $averageCapacity = Venue::whereNotNull('capacity')->avg('capacity');

            $venuesWithFacilities = Venue::whereNotNull('facilities')
                ->where('facilities', '!=', '[]')
                ->count();

            $venuesWithContact = Venue::whereNotNull('contact_person')
                ->orWhereNotNull('contact_phone')
                ->orWhereNotNull('contact_email')
                ->count();

            return response()->json([
                'success' => true,
                'message' => 'Venue statistics retrieved successfully',
                'data' => [
                    'total_venues' => $totalVenues,
                    'active_venues' => $activeVenues,
                    'inactive_venues' => $inactiveVenues,
                    'venues_by_country' => $venuesByCountry,
                    'venues_by_state' => $venuesByState,
                    'total_capacity' => $totalCapacity,
                    'average_capacity' => round($averageCapacity ?? 0, 2),
                    'venues_with_facilities' => $venuesWithFacilities,
                    'venues_with_contact' => $venuesWithContact,
                    'facility_coverage_rate' => $totalVenues > 0 ? round(($venuesWithFacilities / $totalVenues) * 100, 2) : 0,
                    'contact_coverage_rate' => $totalVenues > 0 ? round(($venuesWithContact / $totalVenues) * 100, 2) : 0,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve venue statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get venues by region.
     */
    public function getByRegion(string $region): JsonResponse
    {
        try {
            $venues = Venue::where('region_id', $region)
                ->where('status', 'active')
                ->with(['hostSchool', 'region'])
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Venues retrieved successfully',
                'data' => $venues
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve venues by region',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
