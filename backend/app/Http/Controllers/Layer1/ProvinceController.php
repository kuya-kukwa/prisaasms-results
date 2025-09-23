<?php

namespace App\Http\Controllers\Layer1;

use App\Http\Controllers\Controller;
use App\Models\Layer1\Province;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProvinceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Province::query();

        if ($request->has('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', "%" . $request->search . "%");
        }

        $provinces = $query->orderBy('name')->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Provinces retrieved successfully',
            'data' => $provinces,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'region_id' => 'required|exists:regions,id',
            'name' => 'required|string|max:255',
        ]);

        $province = Province::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Province created successfully',
            'data' => $province,
        ], 201);
    }

    public function show(Province $province): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Province retrieved successfully',
            'data' => $province,
        ]);
    }

    public function update(Request $request, Province $province): JsonResponse
    {
        $validated = $request->validate([
            'region_id' => 'sometimes|required|exists:regions,id',
            'name' => 'sometimes|required|string|max:255',
        ]);

        $province->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Province updated successfully',
            'data' => $province,
        ]);
    }

    public function destroy(Province $province): JsonResponse
    {
        $province->delete();

        return response()->json([
            'success' => true,
            'message' => 'Province deleted successfully',
        ]);
    }
}
