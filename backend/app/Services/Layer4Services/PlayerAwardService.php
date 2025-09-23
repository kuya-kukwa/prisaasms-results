<?php

namespace App\Services\Layer4;

use App\Models\Layer4\Award;
use App\Models\Layer2\Athlete;
use App\Models\Layer3\GameMatch;
use App\Models\Layer1\Tournament;

class PlayerAwardService
{
    public function assignAward(Athlete $athlete, Tournament $tournament, ?GameMatch $match, string $awardType, ?string $remarks = null): Award
    {
        return Award::updateOrCreate(
            [
                'athlete_id' => $athlete->id,
                'tournament_id' => $tournament->id,
                'award_type' => $awardType,
            ],
            [
                'match_id' => $match?->id,
                'remarks' => $remarks,
            ]
        );
    }

    public function getAwardsByTournament(Tournament $tournament)
    {
        return Award::where('tournament_id', $tournament->id)->with(['athlete', 'match'])->get();
    }
}
