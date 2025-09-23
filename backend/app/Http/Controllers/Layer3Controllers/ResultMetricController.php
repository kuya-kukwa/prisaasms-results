<?php

namespace App\Http\Controllers\Layer3;

use App\Http\Controllers\Controller;
use App\Models\Layer3\Result;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ResultController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $results = Result::with(['match', 'metrics'])->paginate($request->input('per_page', 15));
        return $this->successResponse($results, 'Results retrieved successfully');
    }

    public function show(Result $result): JsonResponse
    {
        $result->load(['match', 'metrics']);
        return $this->successResponse($result, 'Result retrieved successfully');
    }

    public function getRecent(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 10);
        $results = Result::with(['match', 'metrics'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return $this->successResponse($results, 'Recent results retrieved successfully');
    }
}
