<?php

namespace App\Http\Controllers\Layer3Controllers;

use App\Http\Controllers\Controller;
use App\Models\Layer3\GameMatch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GameMatchController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = GameMatch::with(['schedule', 'teamA', 'teamB', 'winner', 'results']);

        if ($request->has('status')) $query->where('status', $request->status);
        if ($request->has('schedule_id')) $query->where('schedule_id', $request->schedule_id);

        $matches = $query->orderBy('scheduled_at')->paginate($request->input('per_page', 15));
        return $this->successResponse($matches, 'Matches retrieved successfully');
    }

    public function show(GameMatch $gameMatch): JsonResponse
    {
        $gameMatch->load(['schedule', 'teamA', 'teamB', 'winner', 'results.metrics']);
        return $this->successResponse($gameMatch, 'Match retrieved successfully');
    }

    public function updateStatus(Request $request, GameMatch $gameMatch): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,ongoing,completed,cancelled'
        ]);

        $gameMatch->update($validated);
        return $this->successResponse($gameMatch, 'Match status updated successfully');
    }

    public function getUpcoming(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 10);
        $matches = GameMatch::with(['schedule', 'teamA', 'teamB'])
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->limit($limit)
            ->get();

        return $this->successResponse($matches, 'Upcoming matches retrieved successfully');
    }
}
