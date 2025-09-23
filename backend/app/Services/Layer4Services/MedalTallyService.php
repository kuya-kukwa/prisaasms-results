<?php

namespace App\Services\Layer4;

use App\Models\Layer4\Medal;
use App\Models\Layer4\MedalTally;
use App\Models\Layer1\Tournament;
use App\Models\Layer1\School;
use Illuminate\Support\Collection;

class MedalTallyService
{
    /**
     * Calculate the medal tally per school for a tournament.
     */
    public function calculateSchoolTally(Tournament $tournament): Collection
    {
        $medals = Medal::where('tournament_id', $tournament->id)->get();

        $tally = [];

        foreach ($medals as $medal) {
            $schoolId = $medal->athlete?->school_id ?? $medal->team?->school_id;
            if (!$schoolId) continue;

            if (!isset($tally[$schoolId])) {
                $tally[$schoolId] = ['gold' => 0, 'silver' => 0, 'bronze' => 0, 'points' => 0];
            }

            switch ($medal->medal_type) {
                case 'gold':
                    $tally[$schoolId]['gold']++;
                    $tally[$schoolId]['points'] += 5;
                    break;
                case 'silver':
                    $tally[$schoolId]['silver']++;
                    $tally[$schoolId]['points'] += 3;
                    break;
                case 'bronze':
                    $tally[$schoolId]['bronze']++;
                    $tally[$schoolId]['points'] += 1;
                    break;
            }
        }

        return collect($tally)->map(function($data, $schoolId) {
            $school = School::find($schoolId);
            return [
                'school' => $school?->name,
                'gold' => $data['gold'],
                'silver' => $data['silver'],
                'bronze' => $data['bronze'],
                'points' => $data['points']
            ];
        })->sortByDesc('points')->values();
    }

    /**
     * Get overall champion school for a tournament.
     */
    public function getOverallChampion(Tournament $tournament): ?School
    {
        $tally = $this->calculateSchoolTally($tournament);
        return $tally->first()?->school ? School::where('name', $tally->first()['school'])->first() : null;
    }
}
