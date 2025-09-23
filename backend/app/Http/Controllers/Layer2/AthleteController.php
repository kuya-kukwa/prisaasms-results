<?php

namespace App\Http\Controllers\Layer2controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Layer2\Athlete;
use App\Models\Layer1\Division;
use App\Services\AthleteService;
use App\Services\AthleteEligibilityService;

class AthleteController extends Controller
{
    protected $athleteService;
    protected $eligibilityService;

    public function __construct(AthleteService $athleteService, AthleteEligibilityService $eligibilityService)
    {
        $this->athleteService = $athleteService;
        $this->eligibilityService = $eligibilityService;
    }

    public function index(Request $request)
    {
        $athletes = $this->athleteService->listAthletes(
            $request->all(),
            $request->get('per_page', 15),
            $request->get('sort_by', 'last_name'),
            $request->get('sort_dir', 'asc')
        );

        return response()->json($athletes);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'gender'         => 'required|in:male,female',
            'birthdate'      => 'nullable|date',
            'school_id'      => 'required|exists:schools,id',
            'division_id'    => 'required|exists:divisions,id',
            'weight_class_id'=> 'nullable|exists:weight_classes,id',
        ]);

        $athlete = new Athlete($data);
        $division = Division::findOrFail($data['division_id']);

        if (! $this->eligibilityService->checkDivisionEligibility($athlete, $division)) {
            return response()->json([
                'error' => "Athlete not eligible for division {$division->name}"
            ], 422);
        }

        $created = $this->athleteService->createAthlete($data);
        return response()->json($created, 201);
    }

    public function show(Athlete $athlete)
    {
        return response()->json($athlete->load(['school', 'division', 'sports', 'teams']));
    }

    public function update(Request $request, Athlete $athlete)
    {
        $data = $request->validate([
            'first_name'     => 'sometimes|string|max:255',
            'last_name'      => 'sometimes|string|max:255',
            'gender'         => 'sometimes|in:male,female',
            'birthdate'      => 'nullable|date',
            'school_id'      => 'sometimes|exists:schools,id',
            'division_id'    => 'sometimes|exists:divisions,id',
            'weight_class_id'=> 'nullable|exists:weight_classes,id',
        ]);

        if (isset($data['division_id'])) {
            $division = Division::findOrFail($data['division_id']);
            $athlete->fill($data); // para makuha updated birthdate
            if (! $this->eligibilityService->checkDivisionEligibility($athlete, $division)) {
                return response()->json([
                    'error' => "Athlete not eligible for division {$division->name}"
                ], 422);
            }
        }

        $updated = $this->athleteService->updateAthlete($athlete, $data);
        return response()->json($updated);
    }

    public function destroy(Athlete $athlete)
    {
        $this->athleteService->deleteAthlete($athlete);
        return response()->json(['message' => 'Athlete deleted successfully']);
    }
}
