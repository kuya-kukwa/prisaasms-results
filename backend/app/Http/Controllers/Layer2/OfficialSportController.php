<?php

namespace App\Http\Controllers\Layer2;

use App\Http\Controllers\Controller;
use App\Models\Layer2\OfficialSport;
use App\Services\Layer2\OfficialSportService;
use Illuminate\Http\Request;

class OfficialSportController extends Controller
{
    protected OfficialSportService $service;

    public function __construct(OfficialSportService $service)
    {
        $this->service = $service;
    }

    /**
     * List assignments with pagination, filtering & sorting
     */
    public function index(Request $request)
    {
        $filters = $request->only(['official_id', 'sport_id']);
        $perPage = $request->get('per_page', 10);
        $sortBy  = $request->get('sort_by', 'id');
        $sortDir = $request->get('sort_dir', 'asc');

        $data = $this->service->list($filters, $perPage, $sortBy, $sortDir);

        return response()->json($data);
    }

    /**
     * Store new assignment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'official_id' => 'required|exists:users,id',
            'sport_id'    => 'required|exists:sports,id',
        ]);

        $assignment = $this->service->assign($validated);

        return response()->json($assignment, 201);
    }

    /**
     * Update assignment
     */
    public function update(Request $request, OfficialSport $officialSport)
    {
        $validated = $request->validate([
            'official_id' => 'required|exists:users,id',
            'sport_id'    => 'required|exists:sports,id',
        ]);

        $assignment = $this->service->update($officialSport, $validated);

        return response()->json($assignment);
    }

    /**
     * Delete assignment
     */
    public function destroy(OfficialSport $officialSport)
    {
        $this->service->delete($officialSport);

        return response()->json(['message' => 'Assignment deleted successfully']);
    }
}
