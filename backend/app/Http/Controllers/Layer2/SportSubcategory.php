<?php

namespace App\Http\Controllers\Layer2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Layer2\SportSubcategory;
use Illuminate\Http\JsonResponse;

class SportSubcategoryController extends Controller
{
    public function index(): JsonResponse
    {
        return $this->successResponse(SportSubcategory::with('sport', 'weightClasses')->get());
    }

    public function show(SportSubcategory $subcategory): JsonResponse
    {
        $subcategory->load('sport', 'weightClasses');
        return $this->successResponse($subcategory);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'sport_id' => 'required|exists:sports,id',
            'name' => 'required|string',
            'gender' => 'nullable|string',
            'format' => 'nullable|string',
        ]);

        $subcategory = SportSubcategory::create($data);
        return $this->successResponse($subcategory, 'Subcategory created', 201);
    }

    public function update(Request $request, SportSubcategory $subcategory): JsonResponse
    {
        $data = $request->validate([
            'sport_id' => 'sometimes|exists:sports,id',
            'name' => 'sometimes|string',
            'gender' => 'nullable|string',
            'format' => 'nullable|string',
        ]);

        $subcategory->update($data);
        return $this->successResponse($subcategory, 'Subcategory updated');
    }

    public function destroy(SportSubcategory $subcategory): JsonResponse
    {
        $subcategory->delete();
        return $this->successResponse(null, 'Subcategory deleted');
    }
}
