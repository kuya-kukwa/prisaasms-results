<?php

namespace App\Http\Controllers\Layer2;

use Illuminate\Http\Request;
use App\Models\Layer2\Team;
use App\Models\Layer2\Athlete;
use App\Services\TeamService;

class TeamController extends Controller
{
    protected TeamService $service;

    public function __construct(TeamService $service)
    {
        $this->service = $service;
    }

    /**
     * GET /api/teams
     * filters: name, school_id, sport_id, division_id, status, search
     * pagination: per_page
     * sorting: sort_by, sort_dir
     */
    public function index(Request $request)
    {
        $filters = $request->only(['name','school_id','sport_id','division_id','status','search']);
        $perPage = (int) $request->get('per_page', 15);
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');

        $teams = $this->service->list($filters, $perPage, $sortBy, $sortDir);
        return response()->json($teams);
    }

    /**
     * POST /api/teams
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'school_id'    => 'required|exists:schools,id',
            'sport_id'     => 'required|exists:sports,id',
            'division_id'  => 'required|exists:divisions,id',
            'name'         => 'nullable|string|max:255',
            'short_name'   => 'nullable|string|max:100',
            'team_code'    => 'nullable|string|max:100|unique:teams,team_code',
            'coach_id'     => 'nullable|exists:users,id',
            'season_year'  => 'nullable|integer',
            'team_logo'    => 'nullable|string',
            'status'       => 'nullable|string',
        ]);

        $team = $this->service->create($data);

        return response()->json([
            'message' => 'Team created',
            'data' => $team
        ], 201);
    }

    /**
     * GET /api/teams/{id}
     */
    public function show($id)
    {
        $team = $this->service->find($id);
        return response()->json($team);
    }

    /**
     * PUT /api/teams/{id}
     */
    public function update(Request $request, $id)
    {
        $team = Team::findOrFail($id);

        $data = $request->validate([
            'school_id'    => 'sometimes|exists:schools,id',
            'sport_id'     => 'sometimes|exists:sports,id',
            'division_id'  => 'sometimes|exists:divisions,id',
            'name'         => 'nullable|string|max:255',
            'short_name'   => 'nullable|string|max:100',
            'team_code'    => "nullable|string|max:100|unique:teams,team_code,{$id}",
            'coach_id'     => 'nullable|exists:users,id',
            'season_year'  => 'nullable|integer',
            'team_logo'    => 'nullable|string',
            'status'       => 'nullable|string',
        ]);

        $updated = $this->service->update($team, $data);

        return response()->json([
            'message' => 'Team updated',
            'data' => $updated
        ]);
    }

    /**
     * DELETE /api/teams/{id}  (soft delete)
     */
    public function destroy($id)
    {
        $team = Team::findOrFail($id);
        $this->service->delete($team);

        return response()->json(['message' => 'Team deleted']);
    }

    /**
     * POST /api/teams/{id}/restore
     */
    public function restore($id)
    {
        $team = $this->service->restore($id);
        return response()->json(['message' => 'Team restored', 'data' => $team]);
    }

    /**
     * DELETE /api/teams/{id}/force
     */
    public function forceDelete($id)
    {
        $this->service->forceDelete($id);
        return response()->json(['message' => 'Team permanently deleted']);
    }

    /**
     * GET /api/teams/{id}/athletes
     */
    public function athletesIndex($id)
    {
        $team = Team::findOrFail($id);
        $data = $this->service->listAthletes($team);
        return response()->json(['data' => $data]);
    }

    /**
     * POST /api/teams/{id}/athletes  (attach single or multiple)
     * body: { "athlete_ids": [1,2,3] } or { "athlete_id": 1 }
     */
    public function attachAthletes(Request $request, $id)
    {
        $team = Team::findOrFail($id);

        $validated = $request->validate([
            'athlete_id'  => 'sometimes|exists:athletes,id',
            'athlete_ids' => 'sometimes|array',
            'athlete_ids.*' => 'exists:athletes,id',
        ]);

        // if athlete_id single provided
        if (!empty($validated['athlete_id'])) {
            $athlete = Athlete::findOrFail($validated['athlete_id']);
            $this->service->attachAthlete($team, $athlete);
        }

        // if list provided
        if (!empty($validated['athlete_ids'])) {
            foreach ($validated['athlete_ids'] as $athleteId) {
                $athlete = Athlete::findOrFail($athleteId);
                $this->service->attachAthlete($team, $athlete);
            }
        }

        return response()->json(['message' => 'Athlete(s) attached to team']);
    }

    /**
     * DELETE /api/teams/{id}/athletes/{athleteId}
     */
    public function detachAthlete($id, $athleteId)
    {
        $team = Team::findOrFail($id);
        $athlete = Athlete::findOrFail($athleteId);
        $this->service->detachAthlete($team, $athlete);

        return response()->json(['message' => 'Athlete detached from team']);
    }

    /**
     * POST /api/teams/{id}/athletes/sync
     * body: { "athlete_ids": [1,2,3] }
     */
    public function syncAthletes(Request $request, $id)
    {
        $team = Team::findOrFail($id);
        $validated = $request->validate([
            'athlete_ids' => 'required|array',
            'athlete_ids.*' => 'exists:athletes,id',
        ]);

        $this->service->syncAthletes($team, $validated['athlete_ids']);

        return response()->json(['message' => 'Team athletes synced']);
    }
}
