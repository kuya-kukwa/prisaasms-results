<?php

namespace App\Services\Layer4;

use App\Models\Layer3\Result;
use App\Models\Layer2\Athlete;
use App\Models\Layer2\Team;
use App\Models\Layer1\Tournament;
use App\Models\Layer4\Medal;

class QualificationService
{
    /**
     * Determine eligible athletes/teams for next level tournament.
     */
    public function getQualifiedParticipants(Tournament $tournament): array
    {
        $goldMedals = Medal::where('tournament_id', $tournament->id)
            ->where('medal_type', 'gold')
            ->get();

        $athletes = $goldMedals->pluck('athlete')->filter();
        $teams = $goldMedals->pluck('team')->filter();

        return [
            'athletes' => $athletes,
            'teams' => $teams,
        ];
    }
}
