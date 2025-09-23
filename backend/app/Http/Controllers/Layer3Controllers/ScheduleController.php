<?php

namespace App\Http\Controllers\Layer3Controllers;

use App\Http\Controllers\Controller;
use App\Models\Layer3\Schedule;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use App\Services\FilterService;


class ScheduleController extends Controller
{
    public function index(Request $request, FilterService $filterService): JsonResponse
    {
        $query = Schedule::query()->with(['sport', 'venue', 'tournament']);
        $query = $filterService->applySportFilters($query, $request);
        $query = $filterService->applyTournamentFilters($query, $request);
        $schedules = $filterService->applyPagination($query, $request); 
        return $this->successResponse($schedules, 'Schedules retrieved successfully');
    }


    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'tournament_id' => 'required|exists:tournaments,id',
                'division_id' => 'required|exists:divisions,id',
                'sport_id' => 'required|exists:sports,id',
                'sport_subcategory_id' => 'nullable|exists:sport_subcategories,id',
                'venue_id' => 'nullable|exists:venues,id',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after:start_date',
            ]);

            $schedule = Schedule::create($validated);
            return $this->successResponse($schedule->load(['sport', 'venue']), 'Schedule created successfully', 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function update(Request $request, Schedule $schedule): JsonResponse
{
    try {
        $validated = $request->validate([
            'tournament_id' => 'sometimes|exists:tournaments,id',
            'division_id' => 'sometimes|exists:divisions,id',
            'sport_id' => 'sometimes|exists:sports,id',
            'sport_subcategory_id' => 'sometimes|nullable|exists:sport_subcategories,id',
            'venue_id' => 'sometimes|nullable|exists:venues,id',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|nullable|date|after:start_date',
            'status' => 'sometimes|in:scheduled,ongoing,completed,cancelled',
        ]);

        $schedule->update($validated);
        return $this->successResponse($schedule->load(['sport', 'venue']), 'Schedule updated successfully');

    } catch (ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        return $this->errorResponse('Failed to update schedule', [$e->getMessage()], 500);
    }
}

    public function destroy(Schedule $schedule): JsonResponse
    {
        if ($schedule->matches()->count() > 0) {
            return $this->errorResponse('Cannot delete schedule: has associated matches', [], 409);
        }

        $schedule->delete();
        return $this->successResponse(null, 'Schedule deleted successfully');
    }

    public function getUpcoming(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 10);

        $schedules = Schedule::with(['sport', 'venue'])
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->limit($limit)
            ->get();

        return $this->successResponse($schedules, 'Upcoming schedules retrieved successfully');
    }
}
