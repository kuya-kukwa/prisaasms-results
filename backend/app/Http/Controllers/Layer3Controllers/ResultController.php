<?php

namespace App\Http\Controllers\Layer3Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Layer3\Result;
use App\Services\Layer4\ResultMetricsService;
use Illuminate\Http\JsonResponse;

class ResultController extends Controller
{
    protected ResultMetricsService $metricsService;

    public function __construct(ResultMetricsService $metricsService)
    {
        $this->metricsService = $metricsService;
    }

    public function index(): JsonResponse
    {
        $results = Result::with(['match', 'metrics', 'match.teamA', 'match.teamB'])->get();
        return $this->successResponse($results, 'Results retrieved successfully');
    }

    public function show(Result $result): JsonResponse
    {
        $result->load(['match', 'metrics']);
        return $this->successResponse($result, 'Result retrieved with metrics');
    }
}
