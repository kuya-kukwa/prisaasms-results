<?php

namespace App\Http\Controllers;

use App\Models\Official;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OfficialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Official::query();
            
            // Filter by certification level if provided
            if ($request->has('certification_level')) {
                $query->where('certification_level', $request->certification_level);
            }
            
            // Filter by official type if provided
            if ($request->has('official_type')) {
                $query->where('official_type', $request->official_type);
            }
            
            // Filter by status if provided
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            // Filter by gender if provided
            if ($request->has('gender')) {
                $query->where('gender', $request->gender);
            }
            
            // Search by name or email
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                });
            }
            
            // Pagination
            $perPage = $request->input('per_page', 15);
            $officials = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'Officials retrieved successfully',
                'data' => $officials
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve officials',
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
                'gender' => 'required|in:male,female',
                'birthdate' => 'nullable|date|before:today',
                'contact_number' => 'nullable|string|max:255',
                'email' => 'nullable|email|unique:officials,email|max:255',
                'avatar' => 'nullable|string|max:255',
                'certification_level' => 'required|in:national,regional,local,trainee',
                'official_type' => 'required|in:referee,umpire,judge,timekeeper,scorer,technical_official,line_judge,table_official,starter,field_judge,track_judge,swimming_judge,diving_judge,gymnastics_judge,athletics_official,team_manager,match_commissioner,protest_jury_member',
                'sports_certified' => 'nullable|array',
                'years_experience' => 'nullable|integer|min:0',
                'status' => 'required|in:active,inactive,suspended,retired',
                'available_for_assignment' => 'nullable|boolean',
                'availability_schedule' => 'nullable|array'
            ]);

            $official = Official::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Official created successfully',
                'data' => $official
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
                'message' => 'Failed to create official',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Official $official): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Official retrieved successfully',
                'data' => $official
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve official',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Official $official): JsonResponse
    {
        try {
            $validated = $request->validate([
                'first_name' => 'sometimes|required|string|max:255',
                'last_name' => 'sometimes|required|string|max:255',
                'gender' => 'sometimes|required|in:male,female',
                'birthdate' => 'sometimes|nullable|date|before:today',
                'contact_number' => 'sometimes|nullable|string|max:255',
                'email' => 'sometimes|nullable|email|unique:officials,email,' . $official->id . '|max:255',
                'avatar' => 'sometimes|nullable|string|max:255',
                'certification_level' => 'sometimes|required|in:national,regional,local,trainee',
                'official_type' => 'sometimes|required|in:referee,umpire,judge,timekeeper,scorer,technical_official,line_judge,table_official,starter,field_judge,track_judge,swimming_judge,diving_judge,gymnastics_judge,athletics_official,team_manager,match_commissioner,protest_jury_member',
                'sports_certified' => 'sometimes|nullable|array',
                'years_experience' => 'sometimes|nullable|integer|min:0',
                'status' => 'sometimes|required|in:active,inactive,suspended,retired',
                'available_for_assignment' => 'sometimes|nullable|boolean',
                'availability_schedule' => 'sometimes|nullable|array'
            ]);

            $official->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Official updated successfully',
                'data' => $official
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
                'message' => 'Failed to update official',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Official $official): JsonResponse
    {
        try {
            $official->delete();

            return response()->json([
                'success' => true,
                'message' => 'Official deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete official',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get officials by certification level.
     */
    public function getByCertification(string $level): JsonResponse
    {
        try {
            $officials = Official::where('certification_level', $level)
                ->where('status', 'active')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Officials retrieved successfully',
                'data' => $officials
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve officials by certification level',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get officials by type.
     */
    public function getByType(string $type): JsonResponse
    {
        try {
            $officials = Official::where('official_type', $type)
                ->where('status', 'active')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Officials retrieved successfully',
                'data' => $officials
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve officials by type',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available officials for assignment.
     */
    public function getAvailable(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'sport_id' => 'required|integer',
                'date' => 'required|date',
                'time' => 'required|date_format:H:i:s',
                'official_type' => 'required|in:referee,umpire,judge,timekeeper,scorer,technical_official,line_judge,table_official,starter,field_judge,track_judge,swimming_judge,diving_judge,gymnastics_judge,athletics_official,team_manager,match_commissioner,protest_jury_member'
            ]);

            $officials = Official::whereJsonContains('sports_certified', $validated['sport_id'])
                ->where('official_type', $validated['official_type'])
                ->where('status', 'active')
                ->where('available_for_assignment', true)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Available officials retrieved successfully',
                'data' => $officials
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
                'message' => 'Failed to retrieve available officials',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get officials by sport ID (checking sports_certified JSON field).
     */
    public function getBySport(int $sportId): JsonResponse
    {
        try {
            $officials = Official::whereJsonContains('sports_certified', $sportId)
                ->where('status', 'active')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Officials retrieved successfully',
                'data' => $officials
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve officials by sport',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
