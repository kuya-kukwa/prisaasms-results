<?php

namespace App\Http\Controllers;

use App\Models\Layer1\User;
use App\Models\Layer2\SportSubcategory;
use App\Services\SportSubcategoryOfficialService;
use Illuminate\Http\Request;

class SportSubcategoryOfficialController extends Controller
{
    protected SportSubcategoryOfficialService $service;

    public function __construct(SportSubcategoryOfficialService $service)
    {
        $this->service = $service;
    }

    public function index(SportSubcategory $subcategory)
    {
        return response()->json([
            'data' => $this->service->listOfficials($subcategory)
        ]);
    }

    public function store(Request $request, SportSubcategory $subcategory)
    {
        $validated = $request->validate([
            'official_id' => 'required|exists:users,id'
        ]);

        $official = User::findOrFail($validated['official_id']);
        $this->service->assignOfficial($subcategory, $official);

        return response()->json([
            'message' => 'Official assigned successfully',
            'data' => $official
        ]);
    }

    public function destroy(SportSubcategory $subcategory, User $official)
    {
        $this->service->removeOfficial($subcategory, $official);

        return response()->json([
            'message' => 'Official removed successfully'
        ]);
    }
}
