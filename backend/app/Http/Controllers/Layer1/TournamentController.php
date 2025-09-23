<?php

namespace App\Http\Controllers\Layer1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Layer1\TournamentService;

class TournamentController extends Controller
{
    protected $tournamentService;

    public function __construct(TournamentService $tournamentService)
    {
        $this->tournamentService = $tournamentService;
    }

    public function index(Request $request)
    {
        $filters = $request->only([
            'name',
            'level',
            'season_year_id',
            'host_school_id',
            'host_province_id',
            'host_region_id'
        ]);

        $perPage = $request->input('per_page', 10);
        $sortBy  = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');

        return response()->json(
            $this->tournamentService->getAll($filters, $perPage, $sortBy, $sortDir)
        );
    }

    public function show($id)
    {
        return response()->json($this->tournamentService->getById($id));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'level'            => 'required|in:school,provincial,regional,national',
            'season_year_id'   => 'required|exists:season_years,id',
            'host_school_id'   => 'nullable|exists:schools,id',
            'host_province_id' => 'nullable|exists:provinces,id',
            'host_region_id'   => 'nullable|exists:regions,id',
            'school_ids'       => 'array|nullable',
            'school_ids.*'     => 'exists:schools,id'
        ]);

        $tournament = $this->tournamentService->create($validated);

        return response()->json($tournament, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'             => 'sometimes|string|max:255',
            'level'            => 'sometimes|in:school,provincial,regional,national',
            'season_year_id'   => 'sometimes|exists:season_years,id',
            'host_school_id'   => 'nullable|exists:schools,id',
            'host_province_id' => 'nullable|exists:provinces,id',
            'host_region_id'   => 'nullable|exists:regions,id',
            'school_ids'       => 'array|nullable',
            'school_ids.*'     => 'exists:schools,id'
        ]);

        $tournament = $this->tournamentService->update($id, $validated);

        return response()->json($tournament);
    }

    public function destroy($id)
    {
        $this->tournamentService->delete($id);
        return response()->json(['message' => 'Tournament deleted successfully.']);
    }
}
