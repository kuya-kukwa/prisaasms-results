<?php

namespace App\Http\Controllers;

use App\Models\Layer1\User;
use App\Models\Layer2\WeightClass;
use App\Services\WeightClassOfficialService;
use Illuminate\Http\Request;

class WeightClassOfficialController extends Controller
{
    protected WeightClassOfficialService $service;

    public function __construct(WeightClassOfficialService $service)
    {
        $this->service = $service;
    }

    public function index(WeightClass $weightClass)
    {
        return response()->json([
            'data' => $this->service->listOfficials($weightClass)
        ]);
    }

    public function store(Request $request, WeightClass $weightClass)
    {
        $validated = $request->validate([
            'official_id' => 'required|exists:users,id'
        ]);

        $official = User::findOrFail($validated['official_id']);
        $this->service->assignOfficial($weightClass, $official);

        return response()->json([
            'message' => 'Official assigned successfully',
            'data' => $official
        ]);
    }

    public function destroy(WeightClass $weightClass, User $official)
    {
        $this->service->removeOfficial($weightClass, $official);

        return response()->json([
            'message' => 'Official removed successfully'
        ]);
    }
}
