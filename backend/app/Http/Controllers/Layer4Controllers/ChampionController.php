<?php

namespace App\Http\Controllers\Layer4Controllers;

use App\Http\Controllers\Controller;
use App\Services\TournamentChampionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChampionController extends Controller
{
    protected TournamentChampionService $service;

    public function __construct(TournamentChampionService $service)
    {
        $this->service = $service;
    }

    /**
     * Get overall champion.
     */
    public function getChampion(Request $request): JsonResponse
    {
        $request->validate([
            'level' => 'required|in:provincial,regional,national',
            'season_year_id' => 'required|integer',
            'province_id' => 'nullable|integer',
            'region_id' => 'nullable|integer',
        ]);

        $champion = $this->service->getOverallChampion(
            $request->level,
            $request->season_year_id,
            $request->province_id,
            $request->region_id
        );

        if (!$champion) {
            return $this->errorResponse('No tournaments found for the given filters', [], 404);
        }

        return $this->successResponse($champion, 'Champion retrieved successfully');
    }

    /**
     * Get full ranking table.
     */
    public function getRanking(Request $request): JsonResponse
    {
        $request->validate([
            'level' => 'required|in:provincial,regional,national',
            'season_year_id' => 'required|integer',
            'province_id' => 'nullable|integer',
            'region_id' => 'nullable|integer',
        ]);

        $ranking = $this->service->getRankingTable(
            $request->level,
            $request->season_year_id,
            $request->province_id,
            $request->region_id
        );

        if ($ranking->isEmpty()) {
            return $this->errorResponse('No tournaments found for the given filters', [], 404);
        }

        return $this->successResponse($ranking, 'Ranking table retrieved successfully');
    }
}
