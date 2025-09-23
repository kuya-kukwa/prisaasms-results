<?php

namespace App\Services\Layer2;

use App\Models\Layer2\Team;
use App\Models\Layer2\Athlete;
use Illuminate\Validation\ValidationException;

class TeamAthleteService
{
    /**
     * Assign an athlete to a team with division rules.
     */
    public function assignAthleteToTeam(Team $team, Athlete $athlete)
    {
        // ✅ Validate athlete based on division
        $this->validateDivision($team, $athlete);

        // ✅ Check if already assigned
        if ($team->athletes()->where('athlete_id', $athlete->id)->exists()) {
            throw ValidationException::withMessages([
                'athlete_id' => "Athlete {$athlete->full_name} is already assigned to this team.",
            ]);
        }

        // ✅ Attach athlete to pivot
        $team->athletes()->attach($athlete->id);

        return $athlete;
    }

    /**
     * Remove an athlete from a team.
     */
    public function removeAthleteFromTeam(Team $team, Athlete $athlete)
    {
        $team->athletes()->detach($athlete->id);
        return true;
    }

    /**
     * List athletes in a team.
     */
    public function listTeamAthletes(Team $team)
    {
        return $team->athletes()->with(['school', 'division'])->get();
    }

    /**
     * Validate athlete age against division rules.
     */
    private function validateDivision(Team $team, Athlete $athlete): void
    {
        $division = strtolower($team->division->name);
        $age = $athlete->age;

        // ✅ Division rules
        $rules = [
            'boys'         => ['min' => 0,  'max' => 17],
            'girls'        => ['min' => 0,  'max' => 17],
            'mixed youth'  => ['min' => 0,  'max' => 17],
            'men'          => ['min' => 18, 'max' => 40],
            'women'        => ['min' => 18, 'max' => 40],
            'mixed'        => ['min' => 18, 'max' => 40],
            'mixed senior' => ['min' => 25, 'max' => 100],
        ];

        if (!isset($rules[$division])) {
            throw ValidationException::withMessages([
                'division' => "No age rules defined for division: {$division}.",
            ]);
        }

        $rule = $rules[$division];

        if ($age < $rule['min'] || $age > $rule['max']) {
            throw ValidationException::withMessages([
                'athlete_id' => "Athlete {$athlete->full_name} (Age {$age}) is not eligible for {$team->division->name}. Required age: {$rule['min']}–{$rule['max']}.",
            ]);
        }
    }
}
