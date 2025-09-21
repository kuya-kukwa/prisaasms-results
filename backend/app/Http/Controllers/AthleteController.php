<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AthleteController extends Controller
{
    /**
     * Generate the next athlete number in format ATH001, ATH002, etc.
     */
    private function generateAthleteNumber(): string
    {
        // Get the latest athlete number
        $latestAthlete = Athlete::orderBy('id', 'desc')->first();
        
        if (!$latestAthlete || !$latestAthlete->athlete_number) {
            return 'ATH001';
        }
        
        // Extract the number part from the athlete number (e.g., "ATH001" -> 1)
        $numberPart = (int) substr($latestAthlete->athlete_number, 3);
        
        // Increment the number
        $nextNumber = $numberPart + 1;
        
        // Format with leading zeros to maintain 3-digit format
        return 'ATH' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Display a listing of the resource.
     */
public function index(Request $request): JsonResponse
{
    try {
        $user = $request->user(); // logged-in user

        $query = Athlete::with(['school', 'sport']);

        // ✅ Restrict coach to their school only
        if ($user->role === 'coach') {
            $query->where('school_id', $user->school_id);
        } else {
            // Admin / Tournament Manager can still filter by school_id
            if ($request->has('school_id')) {
                $query->where('school_id', $request->school_id);
            }
        }

        // Other filters
        if ($request->has('sport_id')) {
            $query->where('sport_id', $request->sport_id);
        }
        if ($request->has('gender')) {
            $query->where('gender', $request->gender);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('athlete_number', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 15);
        $athletes = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Athletes retrieved successfully',
            'data' => $athletes
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to retrieve athletes',
            'error' => $e->getMessage()
        ], 500);
    }
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:athletes,email',
                'gender' => 'required|in:male,female',
                'birthdate' => 'nullable|date',
                'avatar' => 'nullable|file|image|max:2048', // FIXED
                'school_id' => 'required|exists:schools,id',
                'sport_id' => 'required|exists:sports,id',
                'status' => 'required|in:active,inactive,injured,suspended'
            ]);

            // Auto-generate athlete number
            $validated['athlete_number'] = $this->generateAthleteNumber();

            $athlete = Athlete::create($validated);
            $athlete->load(['school', 'sport']);

            return response()->json([
                'success' => true,
                'message' => 'Athlete created successfully',
                'data' => $athlete
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create athlete',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Athlete $athlete): JsonResponse
    {
        try {
            $athlete->load(['school', 'sport', 'teams']);

            return response()->json([
                'success' => true,
                'message' => 'Athlete retrieved successfully',
                'data' => $athlete
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve athlete',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Athlete $athlete): JsonResponse
    {
        try {
            $validated = $request->validate([
                'first_name' => 'sometimes|required|string|max:255',
                'last_name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|max:255|unique:athletes,email,' . $athlete->id,
                'gender' => 'sometimes|required|in:male,female',
                'birthdate' => 'sometimes|nullable|date',
                'avatar' => 'sometimes|nullable|file|image|max:2048', // FIXED
                'school_id' => 'sometimes|required|exists:schools,id',
                'sport_id' => 'sometimes|nullable|exists:sports,id',
                'athlete_number' => 'sometimes|required|string|max:255|unique:athletes,athlete_number,' . $athlete->id,
                'status' => 'sometimes|required|in:active,inactive,injured,suspended'
            ]);

            if ($request->hasFile('avatar')) {
                $path = $request->file('avatar')->store('avatars', 'public');
                $validated['avatar'] = $path;
            }

            $athlete->update($validated);
            $athlete->load(['school', 'sport']);

            return response()->json([
                'success' => true,
                'message' => 'Athlete updated successfully',
                'data' => $athlete
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update athlete',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Athlete $athlete): JsonResponse
    {
        try {
            if ($athlete->teams()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete athlete: Athlete is coaching teams'
                ], 409);
            }

            $athlete->delete();

            return response()->json([
                'success' => true,
                'message' => 'Athlete deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete athlete',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
