<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TournamentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Tournament::query();
            
            // Filter by sport if provided
            if ($request->has('sport_id')) {
                $query->where('sport_id', $request->sport_id);
            }
            
            // Filter by tournament type if provided
            if ($request->has('tournament_type')) {
                $query->where('tournament_type', $request->tournament_type);
            }
            
            // Filter by status if provided
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            // Filter by season year if provided
            if ($request->has('season_year')) {
                $query->where('season_year', $request->season_year);
            }
            
            // Filter by date range
            if ($request->has('start_date')) {
                $query->whereDate('start_date', '>=', $request->start_date);
            }
            
            if ($request->has('end_date')) {
                $query->whereDate('end_date', '<=', $request->end_date);
            }
            
            // Search by tournament name or description
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('tournament_name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('organizer', 'like', "%{$search}%");
                });
            }
            
            // Order by start date
            $query->orderBy('start_date', 'desc');
            
            // Pagination
            $perPage = $request->input('per_page', 15);
            $tournaments = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'Tournaments retrieved successfully',
                'data' => $tournaments
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tournaments',
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
                'tournament_name' => 'required|string|max:255',
                'short_name' => 'nullable|string|max:255',
                'tournament_code' => 'nullable|string|max:255|unique:tournaments',
                'description' => 'nullable|string',
                'type' => 'nullable|in:championship,invitational,league',
                'level' => 'nullable|in:national,regional,provincial,local',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'registration_end' => 'nullable|date|before_or_equal:start_date',
                'host_location' => 'nullable|string|max:255',
                'host_school_id' => 'nullable|exists:schools,id',
                'tournament_manager_id' => 'nullable|exists:users,id',
                'has_medal_tally' => 'nullable|boolean',
                'sports_included' => 'nullable|array',
                'status' => 'required|in:planning,registration_open,ongoing,completed,cancelled',
                'is_public' => 'nullable|boolean',
                'champion_school_id' => 'nullable|exists:schools,id'
            ]);

            // Map tournament_name to name field for database
            $validated['name'] = $validated['tournament_name'];
            unset($validated['tournament_name']);

            // Generate tournament code if not provided
            if (!isset($validated['tournament_code'])) {
                $validated['tournament_code'] = strtoupper(substr($validated['name'], 0, 3)) . date('Y') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            }

            // Set default host_location if not provided
            if (!isset($validated['host_location'])) {
                $validated['host_location'] = 'TBD';
            }

            // Set defaults
            if (!isset($validated['type'])) {
                $validated['type'] = 'championship';
            }
            if (!isset($validated['level'])) {
                $validated['level'] = 'regional';
            }

            $tournament = Tournament::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Tournament created successfully',
                'data' => $tournament
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
                'message' => 'Failed to create tournament',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Tournament $tournament): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Tournament retrieved successfully',
                'data' => $tournament
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tournament',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tournament $tournament): JsonResponse
    {
        try {
            $validated = $request->validate([
                'tournament_name' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|nullable|string',
                'sport_id' => 'sometimes|required|exists:sports,id',
                'tournament_type' => 'sometimes|required|in:knockout,round_robin,league,mixed,elimination,championship',
                'start_date' => 'sometimes|required|date',
                'end_date' => 'sometimes|required|date|after_or_equal:start_date',
                'registration_start' => 'sometimes|required|date',
                'registration_end' => 'sometimes|required|date|after_or_equal:registration_start',
                'max_participants' => 'sometimes|required|integer|min:2',
                'current_participants' => 'sometimes|nullable|integer|min:0',
                'entry_fee' => 'sometimes|nullable|numeric|min:0',
                'prize_pool' => 'sometimes|nullable|numeric|min:0',
                'venue_details' => 'sometimes|nullable|string',
                'rules_regulations' => 'sometimes|nullable|string',
                'organizer' => 'sometimes|required|string|max:255',
                'contact_person' => 'sometimes|nullable|string|max:255',
                'contact_email' => 'sometimes|nullable|email|max:255',
                'contact_phone' => 'sometimes|nullable|string|max:255',
                'season_year' => 'sometimes|required|integer',
                'age_category' => 'sometimes|nullable|string|max:255',
                'gender_category' => 'sometimes|required|in:male,female,mixed',
                'status' => 'sometimes|required|in:planning,registration_open,registration_closed,ongoing,completed,cancelled',
                'tournament_logo' => 'sometimes|nullable|string|max:255',
                'sponsors' => 'sometimes|nullable|array',
                'awards' => 'sometimes|nullable|array'
            ]);

            $tournament->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Tournament updated successfully',
                'data' => $tournament
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
                'message' => 'Failed to update tournament',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tournament $tournament): JsonResponse
    {
        try {
            $tournament->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tournament deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tournament',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get tournaments by sport.
     */
    public function getBySport(int $sportId): JsonResponse
    {
        try {
            $tournaments = Tournament::where('sport_id', $sportId)
                ->where('status', '!=', 'cancelled')
                ->orderBy('start_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Sport tournaments retrieved successfully',
                'data' => $tournaments
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sport tournaments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get upcoming tournaments.
     */
    public function getUpcoming(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 10);
            
            $tournaments = Tournament::query()
                ->where('start_date', '>=', now())
                ->whereIn('status', ['planning', 'registration_open', 'registration_closed'])
                ->orderBy('start_date')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Upcoming tournaments retrieved successfully',
                'data' => $tournaments
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve upcoming tournaments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get ongoing tournaments.
     */
    public function getOngoing(): JsonResponse
    {
        try {
            $tournaments = Tournament::query()
                ->where('status', 'ongoing')
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->orderBy('start_date')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Ongoing tournaments retrieved successfully',
                'data' => $tournaments
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve ongoing tournaments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get completed tournaments.
     */
    public function getCompleted(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 10);
            $seasonYear = $request->input('season_year');
            
            $query = Tournament::query()
                ->where('status', 'completed');
            
            if ($seasonYear) {
                $query->where('season_year', $seasonYear);
            }
            
            $tournaments = $query->orderBy('end_date', 'desc')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Completed tournaments retrieved successfully',
                'data' => $tournaments
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve completed tournaments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin listing for tournaments (paginated)
     */
    public function adminIndex(Request $request): JsonResponse
    {
        try {
            $query = Tournament::query();

            // optional admin filters
            if ($request->has('season_year')) {
                $query->where('season_year', $request->season_year);
            }

            // default ordering
            $query->orderBy('start_date', 'desc');

            $perPage = (int) $request->input('per_page', 15);
            $tournaments = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Tournaments retrieved successfully',
                'data' => $tournaments
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tournaments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Register participant for tournament.
     */
    public function registerParticipant(Request $request, Tournament $tournament): JsonResponse
    {
        try {
            // Check if registration is open
            if ($tournament->status !== 'registration_open') {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration is not open for this tournament'
                ], 422);
            }

            // Check if tournament is full
            if ($tournament->current_participants >= $tournament->max_participants) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tournament is full'
                ], 422);
            }

            // Check if registration deadline has passed
            if (now() > $tournament->registration_end) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration deadline has passed'
                ], 422);
            }

            // Increment participant count
            $tournament->increment('current_participants');

            return response()->json([
                'success' => true,
                'message' => 'Participant registered successfully',
                'data' => [
                    'tournament' => $tournament,
                    'current_participants' => $tournament->current_participants,
                    'remaining_slots' => $tournament->max_participants - $tournament->current_participants
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to register participant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update tournament status.
     */
    public function updateStatus(Request $request, Tournament $tournament): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:planning,registration_open,registration_closed,ongoing,completed,cancelled'
            ]);

            $tournament->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Tournament status updated successfully',
                'data' => $tournament
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
                'message' => 'Failed to update tournament status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get tournament statistics.
     */
    public function getStatistics(Tournament $tournament): JsonResponse
    {
        try {
            $statistics = [
                'total_participants' => $tournament->current_participants,
                'max_participants' => $tournament->max_participants,
                'registration_progress' => round(($tournament->current_participants / $tournament->max_participants) * 100, 2),
                'days_until_start' => now()->diffInDays($tournament->start_date, false),
                'days_until_registration_end' => now()->diffInDays($tournament->registration_end, false),
                'tournament_duration' => $tournament->start_date->diffInDays($tournament->end_date) + 1,
                'entry_fee' => $tournament->entry_fee,
                'prize_pool' => $tournament->prize_pool,
                'total_revenue' => $tournament->current_participants * ($tournament->entry_fee ?? 0)
            ];

            return response()->json([
                'success' => true,
                'message' => 'Tournament statistics retrieved successfully',
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tournament statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
