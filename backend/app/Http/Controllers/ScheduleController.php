<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Schedule::with(['sport', 'venue']);
            
            // Filter by sport if provided
            if ($request->has('sport_id')) {
                $query->where('sport_id', $request->sport_id);
            }
            
            // Filter by venue if provided
            if ($request->has('venue_id')) {
                $query->where('venue_id', $request->venue_id);
            }
            
            // Filter by event type if provided
            if ($request->has('event_type')) {
                $query->where('event_type', $request->event_type);
            }
            
            // Filter by status if provided
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            // Filter by date range
            if ($request->has('start_date')) {
                $query->whereDate('event_date', '>=', $request->start_date);
            }
            
            if ($request->has('end_date')) {
                $query->whereDate('event_date', '<=', $request->end_date);
            }
            
            // Search by event name or description
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('event_name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('organizer', 'like', "%{$search}%");
                });
            }
            
            // Order by event date
            $query->orderBy('event_date', 'asc')->orderBy('start_time', 'asc');
            
            // Pagination
            $perPage = $request->input('per_page', 15);
            $schedules = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'Schedules retrieved successfully',
                'data' => $schedules
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve schedules',
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
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'event_type' => 'required|in:match,practice,training,meeting,ceremony,other',
                'event_date' => 'required|date',
                'start_time' => 'required|date_format:H:i:s',
                'end_time' => 'nullable|date_format:H:i:s|after:start_time',
                'duration_minutes' => 'nullable|integer|min:1',
                'sport_id' => 'required|exists:sports,id',
                'venue_id' => 'nullable|exists:venues,id',
                'tournament_id' => 'nullable|exists:tournaments,id',
                'status' => 'required|in:scheduled,ongoing,completed,cancelled,postponed',
                'priority' => 'nullable|in:low,normal,high,urgent',

                // PRISAA-specific validation
                'competition_level' => 'nullable|in:elementary,high_school,college',
                'age_group' => 'nullable|in:u12,u14,u16,u18,u21,open,masters',
                'gender_category' => 'nullable|in:mens,womens,mixed,co_ed',
                'educational_level' => 'nullable|in:elementary,middle_school,high_school,college,professional',
                'sport_category' => 'nullable|string|max:255',
                'round_type' => 'nullable|in:qualifying,preliminary,quarter_final,semi_final,final,bronze_final,gold_final,consolation',
                'heat_number' => 'nullable|integer|min:1',
                'lane_number' => 'nullable|integer|min:1',
                'court_field_number' => 'nullable|integer|min:1',
                'is_team_event' => 'nullable|boolean',
                'max_teams_per_school' => 'nullable|integer|min:1',
                'qualification_criteria' => 'nullable|array',
                'weather_conditions' => 'nullable|string|max:255',
                'technical_officials_required' => 'nullable|integer|min:0',
                'medical_officials_required' => 'nullable|integer|min:0',
                'spectator_capacity' => 'nullable|integer|min:0',
                'broadcast_info' => 'nullable|string',
                'live_stream_url' => 'nullable|url',
                'result_format' => 'nullable|in:individual,team,relay,combined',
                'scoring_system_used' => 'nullable|string|max:255',
                'protest_deadline_hours' => 'nullable|integer|min:1',
                'appeal_process_info' => 'nullable|string',

                // Organization & Contact
                'organizer' => 'nullable|string|max:255',
                'contact_person' => 'nullable|string|max:255',
                'contact_number' => 'nullable|string|max:20',

                // Participant Management
                'max_participants' => 'nullable|integer|min:1',
                'current_participants' => 'nullable|integer|min:0',
                'registration_deadline' => 'nullable|date|after:event_date',
                'entry_fee' => 'nullable|numeric|min:0',

                // Event Details
                'requirements' => 'nullable|string',
                'prizes' => 'nullable|string',
                'rules' => 'nullable|string',

                // Arrays
                'participants' => 'nullable|array',
                'officials_assigned' => 'nullable|array'
            ]);

            $schedule = Schedule::create($validated);

            $schedule->load(['sport', 'venue']);

            return response()->json([
                'success' => true,
                'message' => 'Schedule created successfully',
                'data' => $schedule
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
                'message' => 'Failed to create schedule',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule): JsonResponse
    {
        try {
            $schedule->load(['sport', 'venue', 'gameMatches']);

            return response()->json([
                'success' => true,
                'message' => 'Schedule retrieved successfully',
                'data' => $schedule
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve schedule',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|nullable|string',
                'event_type' => 'sometimes|required|in:match,practice,training,meeting,ceremony,other',
                'event_date' => 'sometimes|required|date',
                'start_time' => 'sometimes|required|date_format:H:i:s',
                'end_time' => 'sometimes|nullable|date_format:H:i:s|after:start_time',
                'duration_minutes' => 'sometimes|nullable|integer|min:1',
                'sport_id' => 'sometimes|required|exists:sports,id',
                'venue_id' => 'sometimes|nullable|exists:venues,id',
                'tournament_id' => 'sometimes|nullable|exists:tournaments,id',
                'status' => 'sometimes|required|in:scheduled,ongoing,completed,cancelled,postponed',
                'priority' => 'sometimes|nullable|in:low,normal,high,urgent',

                // PRISAA-specific validation
                'competition_level' => 'sometimes|nullable|in:elementary,high_school,college',
                'age_group' => 'sometimes|nullable|in:u12,u14,u16,u18,u21,open,masters',
                'gender_category' => 'sometimes|nullable|in:mens,womens,mixed,co_ed',
                'educational_level' => 'sometimes|nullable|in:elementary,middle_school,high_school,college,professional',
                'sport_category' => 'sometimes|nullable|string|max:255',
                'round_type' => 'sometimes|nullable|in:qualifying,preliminary,quarter_final,semi_final,final,bronze_final,gold_final,consolation',
                'heat_number' => 'sometimes|nullable|integer|min:1',
                'lane_number' => 'sometimes|nullable|integer|min:1',
                'court_field_number' => 'sometimes|nullable|integer|min:1',
                'is_team_event' => 'sometimes|nullable|boolean',
                'max_teams_per_school' => 'sometimes|nullable|integer|min:1',
                'qualification_criteria' => 'sometimes|nullable|array',
                'weather_conditions' => 'sometimes|nullable|string|max:255',
                'technical_officials_required' => 'sometimes|nullable|integer|min:0',
                'medical_officials_required' => 'sometimes|nullable|integer|min:0',
                'spectator_capacity' => 'sometimes|nullable|integer|min:0',
                'broadcast_info' => 'sometimes|nullable|string',
                'live_stream_url' => 'sometimes|nullable|url',
                'result_format' => 'sometimes|nullable|in:individual,team,relay,combined',
                'scoring_system_used' => 'sometimes|nullable|string|max:255',
                'protest_deadline_hours' => 'sometimes|nullable|integer|min:1',
                'appeal_process_info' => 'sometimes|nullable|string',

                // Organization & Contact
                'organizer' => 'sometimes|nullable|string|max:255',
                'contact_person' => 'sometimes|nullable|string|max:255',
                'contact_number' => 'sometimes|nullable|string|max:20',

                // Participant Management
                'max_participants' => 'sometimes|nullable|integer|min:1',
                'current_participants' => 'sometimes|nullable|integer|min:0',
                'registration_deadline' => 'sometimes|nullable|date|after:event_date',
                'entry_fee' => 'sometimes|nullable|numeric|min:0',

                // Event Details
                'requirements' => 'sometimes|nullable|string',
                'prizes' => 'sometimes|nullable|string',
                'rules' => 'sometimes|nullable|string',

                // Arrays
                'participants' => 'sometimes|nullable|array',
                'officials_assigned' => 'sometimes|nullable|array'
            ]);

            $schedule->update($validated);

            $schedule->load(['sport', 'venue']);

            return response()->json([
                'success' => true,
                'message' => 'Schedule updated successfully',
                'data' => $schedule
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
                'message' => 'Failed to update schedule',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule): JsonResponse
    {
        try {
            // Check if schedule has dependent records
            if ($schedule->gameMatches()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete schedule: Schedule has associated game matches'
                ], 409);
            }

            $schedule->delete();

            return response()->json([
                'success' => true,
                'message' => 'Schedule deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete schedule',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get schedules by date range.
     */
    public function getByDateRange(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date'
            ]);

            $schedules = Schedule::with(['sport', 'venue'])
                ->whereBetween('event_date', [$validated['start_date'], $validated['end_date']])
                ->orderBy('event_date')
                ->orderBy('start_time')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Schedules retrieved successfully',
                'data' => $schedules
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
                'message' => 'Failed to retrieve schedules by date range',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get upcoming schedules.
     */
    public function getUpcoming(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 10);
            
            $schedules = Schedule::with(['sport', 'venue'])
                ->where('event_date', '>=', now())
                ->where('status', '!=', 'cancelled')
                ->orderBy('event_date')
                ->orderBy('start_time')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Upcoming schedules retrieved successfully',
                'data' => $schedules
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve upcoming schedules',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get schedules by sport.
     */
    public function getBySport(int $sportId): JsonResponse
    {
        try {
            $schedules = Schedule::with(['venue'])
                ->where('sport_id', $sportId)
                ->where('status', '!=', 'cancelled')
                ->orderBy('event_date')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Schedules retrieved successfully',
                'data' => $schedules
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve schedules by sport',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get schedules by venue.
     */
    public function getByVenue(int $venueId): JsonResponse
    {
        try {
            $schedules = Schedule::with(['sport'])
                ->where('venue_id', $venueId)
                ->where('status', '!=', 'cancelled')
                ->orderBy('event_date')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Schedules retrieved successfully',
                'data' => $schedules
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve schedules by venue',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update schedule status.
     */
    public function updateStatus(Request $request, Schedule $schedule): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:scheduled,ongoing,completed,cancelled,postponed'
            ]);

            $schedule->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Schedule status updated successfully',
                'data' => $schedule
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
                'message' => 'Failed to update schedule status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin index - List all schedules with admin features.
     */
    public function adminIndex(Request $request): JsonResponse
    {
        try {
            $query = Schedule::with(['sport', 'venue']);

            // Filter by sport if provided
            if ($request->has('sport_id')) {
                $query->where('sport_id', $request->sport_id);
            }

            // Filter by venue if provided
            if ($request->has('venue_id')) {
                $query->where('venue_id', $request->venue_id);
            }

            // Filter by event type if provided
            if ($request->has('event_type')) {
                $query->where('event_type', $request->event_type);
            }

            // Filter by status if provided
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by date range
            if ($request->has('start_date')) {
                $query->whereDate('event_date', '>=', $request->start_date);
            }

            if ($request->has('end_date')) {
                $query->whereDate('event_date', '<=', $request->end_date);
            }

            // Search by event name or description
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('event_name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('organizer', 'like', "%{$search}%");
                });
            }

            // Order by event date
            $query->orderBy('event_date', 'asc')->orderBy('start_time', 'asc');

            // Pagination
            $perPage = $request->input('per_page', 20);
            $schedules = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Schedules retrieved successfully',
                'data' => $schedules->items(),
                'meta' => [
                    'current_page' => $schedules->currentPage(),
                    'last_page' => $schedules->lastPage(),
                    'per_page' => $schedules->perPage(),
                    'total' => $schedules->total()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve schedules',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get admin statistics for schedules.
     */
    public function getAdminStats(): JsonResponse
    {
        try {
            $stats = [
                'total_schedules' => Schedule::count(),
                'scheduled_count' => Schedule::where('status', 'scheduled')->count(),
                'ongoing_count' => Schedule::where('status', 'ongoing')->count(),
                'completed_count' => Schedule::where('status', 'completed')->count(),
                'cancelled_count' => Schedule::where('status', 'cancelled')->count(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Schedule statistics retrieved successfully',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve schedule statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
