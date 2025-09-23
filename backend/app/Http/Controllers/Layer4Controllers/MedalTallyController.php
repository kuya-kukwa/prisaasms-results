<?php

namespace App\Http\Controllers\Layer4Controllers;

use App\Http\Controllers\Controller;
use App\Models\Layer1\Tournament;
use App\Services\Layer4\MedalTallyService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MedalTallyController extends Controller
{
    protected MedalTallyService $medalService;

    public function __construct(MedalTallyService $medalService)
    {
        $this->medalService = $medalService;
    }

    public function schoolTally(Tournament $tournament): JsonResponse
    {
        $tally = $this->medalService->calculateSchoolTally($tournament);
        return $this->successResponse($tally, 'School medal tally retrieved');
    }

    public function overallChampion(Tournament $tournament): JsonResponse
    {
        $champion = $this->medalService->getOverallChampion($tournament);
        return $this->successResponse($champion, 'Overall champion retrieved');
    }
}
