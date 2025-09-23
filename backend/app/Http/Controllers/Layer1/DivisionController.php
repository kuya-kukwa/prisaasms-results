<?php

namespace App\Http\Controllers\Layer1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Layer1\DivisionService;

class DivisionController extends Controller
{
    protected $divisionService;

    public function __construct(DivisionService $divisionService)
    {
        $this->divisionService = $divisionService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['name']);
        $perPage = $request->input('per_page', 10);
        $sortBy  = $request->input('sort_by', 'name');
        $sortDir = $request->input('sort_dir', 'asc');

        return response()->json(
            $this->divisionService->getAll($filters, $perPage, $sortBy, $sortDir)
        );
    }

    public function show($id)
    {
        return response()->json($this->divisionService->getById($id));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:divisions,name',
        ]);

        $division = $this->divisionService->create($validated);

        return response()->json($division, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:divisions,name,' . $id,
        ]);

        $division = $this->divisionService->update($id, $validated);

        return response()->json($division);
    }

    public function destroy($id)
    {
        $this->divisionService->delete($id);
        return response()->json(['message' => 'Division soft deleted successfully.']);
    }

    public function restore($id)
    {
        $this->divisionService->restore($id);
        return response()->json(['message' => 'Division restored successfully.']);
    }

    public function forceDelete($id)
    {
        $this->divisionService->forceDelete($id);
        return response()->json(['message' => 'Division permanently deleted successfully.']);
    }
}
