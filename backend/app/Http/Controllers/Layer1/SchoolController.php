<?php

namespace App\Http\Controllers\Layer1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Layer1\SchoolService;

class SchoolController extends Controller
{
    protected $schoolService;

    public function __construct(SchoolService $schoolService)
    {
        $this->schoolService = $schoolService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['name', 'province_id']);
        $perPage = $request->input('per_page', 10);
        $sortBy  = $request->input('sort_by', 'name');
        $sortDir = $request->input('sort_dir', 'asc');

        return response()->json(
            $this->schoolService->getAll($filters, $perPage, $sortBy, $sortDir)
        );
    }

    public function show($id)
    {
        return response()->json($this->schoolService->getById($id));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'province_id' => 'required|exists:provinces,id'
        ]);

        $school = $this->schoolService->create($validated);

        return response()->json($school, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'province_id' => 'sometimes|exists:provinces,id'
        ]);

        $school = $this->schoolService->update($id, $validated);

        return response()->json($school);
    }

    public function destroy($id)
    {
        $this->schoolService->delete($id);
        return response()->json(['message' => 'School soft deleted successfully.']);
    }

    public function restore($id)
    {
        $this->schoolService->restore($id);
        return response()->json(['message' => 'School restored successfully.']);
    }

    public function forceDelete($id)
    {
        $this->schoolService->forceDelete($id);
        return response()->json(['message' => 'School permanently deleted successfully.']);
    }
}
