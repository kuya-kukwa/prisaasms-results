<?php

namespace App\Http\Controllers\Layer2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Layer2\SportService;

class SportController extends Controller
{
    protected SportService $sportService;

    public function __construct(SportService $sportService)
    {
        $this->sportService = $sportService;
    }

    public function index(Request $request)
    {
        $sports = $this->sportService->getAll($request->get('per_page', 10));
        return response()->json($sports);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'type'          => 'required|in:individual,team',
            'result_format' => 'required|in:score,time,distance,points,set_based',
            'description'   => 'nullable|string',
        ]);

        $sport = $this->sportService->create($validated);

        return response()->json($sport, 201);
    }

    public function show(int $id)
    {
        $sport = $this->sportService->findById($id);

        if (!$sport) {
            return response()->json(['message' => 'Sport not found'], 404);
        }

        return response()->json($sport);
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'name'          => 'sometimes|string|max:255',
            'type'          => 'sometimes|in:individual,team',
            'result_format' => 'sometimes|in:score,time,distance,points,set_based',
            'description'   => 'nullable|string',
        ]);

        $sport = $this->sportService->update($id, $validated);

        if (!$sport) {
            return response()->json(['message' => 'Sport not found'], 404);
        }

        return response()->json($sport);
    }

    public function destroy(int $id)
    {
        if (!$this->sportService->delete($id)) {
            return response()->json(['message' => 'Sport not found'], 404);
        }

        return response()->json(['message' => 'Sport soft deleted']);
    }

    public function restore(int $id)
    {
        if (!$this->sportService->restore($id)) {
            return response()->json(['message' => 'Sport not found or not deleted'], 404);
        }

        return response()->json(['message' => 'Sport restored']);
    }

    public function forceDelete(int $id)
    {
        if (!$this->sportService->forceDelete($id)) {
            return response()->json(['message' => 'Sport not found'], 404);
        }

        return response()->json(['message' => 'Sport permanently deleted']);
    }
}
