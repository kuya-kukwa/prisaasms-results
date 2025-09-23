<?php

namespace App\Http\Controllers\Layer1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Layer1\RegionService;

class RegionController extends Controller
{
    protected $regionService;

    public function __construct(RegionService $regionService)
    {
        $this->regionService = $regionService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['name', 'code']);
        $perPage = $request->input('per_page', 10);
        $sortBy  = $request->input('sort_by', 'name');
        $sortDir = $request->input('sort_dir', 'asc');

        return response()->json(
            $this->regionService->getAll($filters, $perPage, $sortBy, $sortDir)
        );
    }

    public function show($id)
    {
        return response()->json($this->regionService->getById($id));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:regions,name',
            'code' => 'nullable|string|max:50'
        ]);

        $region = $this->regionService->create($validated);

        return response()->json($region, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:regions,name,' . $id,
            'code' => 'nullable|string|max:50'
        ]);

        $region = $this->regionService->update($id, $validated);

        return response()->json($region);
    }

    public function destroy($id)
    {
        $this->regionService->delete($id);
        return response()->json(['message' => 'Region soft deleted successfully.']);
    }

    public function restore($id)
    {
        $this->regionService->restore($id);
        return response()->json(['message' => 'Region restored successfully.']);
    }

    public function forceDelete($id)
    {
        $this->regionService->forceDelete($id);
        return response()->json(['message' => 'Region permanently deleted successfully.']);
    }
}
