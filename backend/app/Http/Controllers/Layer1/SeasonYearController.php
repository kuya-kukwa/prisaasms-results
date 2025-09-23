<?php

namespace App\Http\Controllers\Layer1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Layer1\SeasonYearService;

class SeasonYearController extends Controller
{
    protected $seasonYearService;

    public function __construct(SeasonYearService $seasonYearService)
    {
        $this->seasonYearService = $seasonYearService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['year', 'active']);

        $perPage = $request->input('per_page', 10);
        $sortBy  = $request->input('sort_by', 'year');
        $sortDir = $request->input('sort_dir', 'desc');

        return response()->json(
            $this->seasonYearService->getAll($filters, $perPage, $sortBy, $sortDir)
        );
    }

    public function show($id)
    {
        return response()->json($this->seasonYearService->getById($id));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year'   => 'required|integer|digits:4|unique:season_years,year',
            'active' => 'boolean'
        ]);

        $seasonYear = $this->seasonYearService->create($validated);

        return response()->json($seasonYear, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'year'   => 'sometimes|integer|digits:4|unique:season_years,year,' . $id,
            'active' => 'boolean'
        ]);

        $seasonYear = $this->seasonYearService->update($id, $validated);

        return response()->json($seasonYear);
    }

    public function destroy($id)
    {
        $this->seasonYearService->delete($id);
        return response()->json(['message' => 'Season Year deleted successfully.']);
    }
}
    