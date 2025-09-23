<?php

namespace App\Http\Controllers\Layer2;

use App\Http\Controllers\Controller;
use App\Models\Layer2\Team;
use App\Models\Layer2\Athlete;
use App\Services\Layer2\TeamAthleteService;
use Illuminate\Http\Request;

class TeamAthleteController extends Controller
{
    protected TeamAthleteService $service;

    public function __construct(TeamAthleteService $service)
    {
        $this->service = $service;
    }

    /**
     * List all athletes in a team
     */
    public function index(Team $team)
    {
        $athletes = $this->service->listTeamAthletes($team);

        return response()->json([
            'team' => $team->name,
            'division' => $team->division->name,
            'athletes' => $athletes,
        ]);
    }

    /**
     * Assign an athlete to a team
     */
    public function store(Request $request, Team $team)
    {
        $request->validate([
            'athlete_id' => 'required|exists:athletes,id',
        ]);

        $athlete = Athlete::findOrFail($request->athlete_id);

        $this->service->assignAthleteToTeam($team, $athlete);

        return response()->json([
            'message' => "Athlete {$athlete->full_name} assigned to team {$team->name} successfully.",
        ]);
    }

    /**
     * Remove an athlete from a team
     */
    public function destroy(Team $team, Athlete $athlete)
    {
        $this->service->removeAthleteFromTeam($team, $athlete);

        return response()->json([
            'message' => "Athlete {$athlete->full_name} removed from team {$team->name} successfully.",
        ]);
    }
}
