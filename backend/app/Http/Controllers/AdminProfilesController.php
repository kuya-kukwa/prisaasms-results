<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Layer0\ProfileService;

class AdminProfilesController extends Controller
{
    protected ProfileService $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * List profiles with optional filters and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Permission check
        if (! $user->hasAnyPermission(['view-users','view-athletes','view-officials','view-schools'])) {
            return $this->errorResponse('Unauthorized to view profiles', [], 403);
        }

        $type   = $request->query('type', 'all');
        $search = $request->query('search', null);
        $page   = (int) $request->query('page', 1);
        $limit  = (int) $request->query('limit', 20);

        try {
            $result = $this->profileService->getProfiles($type, $search, $page, $limit);

            return $this->successResponse([
                'data'         => $result['data'],
                'current_page' => $result['current_page'],
                'last_page'    => $result['last_page'],
                'per_page'     => $limit,
                'total'        => $result['total']
            ], 'Profiles retrieved successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch profiles', [$e->getMessage()], 500);
        }
    }
}
