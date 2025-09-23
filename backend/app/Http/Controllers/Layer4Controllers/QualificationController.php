<?php

namespace App\Http\Controllers\Layer4Controllers;

use App\Http\Controllers\Controller;
use App\Models\Layer1\Tournament;
use App\Services\Layer4\QualificationService;
use Illuminate\Http\JsonResponse;

class QualificationController extends Controller
{
    protected QualificationService $qualificationService;

    public function __construct(QualificationService $qualificationService)
    {
        $this->qualificationService = $qualificationService;
    }

    public function nextLevelParticipants(Tournament $tournament): JsonResponse
    {
        $participants = $this->qualificationService->getQualifiedParticipants($tournament);
        return $this->successResponse($participants, 'Qualified participants for next level retrieved');
    }
}
