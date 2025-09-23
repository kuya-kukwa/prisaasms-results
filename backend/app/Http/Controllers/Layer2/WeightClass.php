<?php

namespace App\Http\Controllers\Layer2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Layer2\WeightClass;
use Illuminate\Http\JsonResponse;

class WeightClassController extends Controller
{
    public function index(): JsonResponse
    {
        return $this->successResponse(WeightClass::with('sport')->get());
    }

    public function show(WeightClass $weightClass): JsonResponse
    {
        $weightClass->load('sport');
        return $this->successResponse($weightClass);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'sport_id' => 'required|exists:sports,id',
            'name' => 'required|string',
            'min_weight' => 'nullable|numeric',
            'max_weight' => 'nullable|numeric',
        ]);

        $weightClass = WeightClass::create($data);
        return $this->successResponse($weightClass, 'Weight class created', 201);
    }

    public function update(Request $request, WeightClass $weightClass): JsonResponse
    {
        $data = $request->validate([
            'sport_id' => 'sometimes|exists:sports,id',
            'name' => 'sometimes|string',
            'min_weight' => 'nullable|numeric',
            'max_weight' => 'nullable|numeric',
        ]);

        $weightClass->update($data);
        return $this->successResponse($weightClass, 'Weight class updated');
    }

    public function destroy(WeightClass $weightClass): JsonResponse
    {
        $weightClass->delete();
        return $this->successResponse(null, 'Weight class deleted');
    }
}
