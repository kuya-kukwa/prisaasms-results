<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Layer2\TeamAthleteController;
Route::prefix('teams/{team}')->group(function () {
    Route::get('athletes', [TeamAthleteController::class, 'index']);
    Route::post('athletes', [TeamAthleteController::class, 'store']);
    Route::delete('athletes/{athlete}', [TeamAthleteController::class, 'destroy']);
});


Route::get('/', function () {
    return response()->json([
        'message' => 'PRISAA Sports Management API Server',
        'status' => 'running',
        'version' => '1.0.0',
        'timestamp' => now(),
        'api_docs' => [
            'json_metadata' => url('/api/documentation'),
            'html_preview' => url('/api/documentation/preview'),
            'postman_collection' => url('/docs/collection.json'),
            'openapi_spec' => url('/docs/openapi.yaml'),
            'documentation_home' => url('/docs')
        ],
        'health' => 'ok'
    ]);
});

// API Documentation JSON (for frontend consumption)
Route::get('/api/documentation', function () {
    // Return structured API documentation as JSON
    return response()->json([
        'title' => 'PRISAA Management System API',
        'version' => '3.0.0',
        'description' => 'Complete API for Private Schools Athletic Association Games Management',
        'base_url' => url('/api'),
        'authentication' => [
            'type' => 'Bearer Token',
            'header' => 'Authorization: Bearer {token}',
            'login_endpoint' => '/api/auth/login'
        ],
        'endpoints' => [
            'total_endpoints' => 163,
            'public_endpoints' => 27,
            'protected_endpoints' => 136
        ],
        'features' => [
            'multi_level_tournaments' => 'Provincial, Regional, National',
            'historical_tracking' => '2017-2025 PRISAA Games records',
            'role_based_access' => 'Admin, Manager, User roles',
            'comprehensive_filtering' => 'Advanced query parameters',
            'real_time_results' => 'Live match results and medal tallies'
        ],
        'main_endpoints' => [
            'authentication' => [
                'POST /api/auth/login' => 'User authentication',
                'POST /api/auth/register' => 'User registration',
                'POST /api/auth/logout' => 'User logout',
                'GET /api/auth/me' => 'Get current user'
            ],
            'prisaa_management' => [
                'GET /api/prisaa-years' => 'List PRISAA years (2017-2025)',
                'GET /api/overall-champions' => 'Multi-level champions',
                'GET /api/tournaments' => 'Tournament management',
                'GET /api/schools' => 'School management'
            ],
            'sports_management' => [
                'GET /api/sports' => 'Sports management',
                'GET /api/athletes' => 'Athlete management', 
                'GET /api/teams' => 'Team management',
                'GET /api/matches' => 'Match management'
            ],
            'results_tracking' => [
                'GET /api/results' => 'Competition results',
                'GET /api/rankings' => 'Performance rankings',
                'GET /api/medals' => 'Medal tallies',
                'GET /api/schedules' => 'Event scheduling'
            ]
        ],
        'documentation_formats' => [
            'postman_collection' => url('/docs/collection.json'),
            'openapi_spec' => url('/docs/openapi.yaml'),
            'documentation_home' => url('/docs')
        ]
    ]);
});

// Custom HTML Documentation Preview (for development/quick reference)
Route::get('/api/documentation/preview', function () {
    return view('api-documentation');
});

// Serve Scribe-generated documentation files
Route::get('/docs/collection.json', function () {
    $path = storage_path('app/private/scribe/collection.json');
    if (!file_exists($path)) {
        return response()->json([
            'error' => 'Postman collection not found',
            'message' => 'Run: php artisan scribe:generate to generate documentation files',
            'alternative' => 'Use the JSON API documentation at /api/documentation'
        ], 404);
    }
    return response()->file($path, [
        'Content-Type' => 'application/json',
        'Content-Disposition' => 'attachment; filename="PRISAA-API-Collection.json"'
    ]);
});

Route::get('/docs/openapi.yaml', function () {
    $path = storage_path('app/private/scribe/openapi.yaml');
    if (!file_exists($path)) {
        return response()->json([
            'error' => 'OpenAPI spec not found', 
            'message' => 'Run: php artisan scribe:generate to generate documentation files',
            'alternative' => 'Use the JSON API documentation at /api/documentation'
        ], 404);
    }
    return response()->file($path, [
        'Content-Type' => 'application/x-yaml',
        'Content-Disposition' => 'attachment; filename="PRISAA-API-OpenAPI.yaml"'
    ]);
});

// Fallback documentation route
Route::get('/docs', function () {
    return response()->json([
        'message' => 'PRISAA API Documentation',
        'description' => 'API-only backend - Use JSON endpoints for documentation',
        'available_formats' => [
            'json_metadata' => url('/api/documentation'),
            'html_preview' => url('/api/documentation/preview'),
            'postman_collection' => url('/docs/collection.json'),
            'openapi_spec' => url('/docs/openapi.yaml')
        ],
        'note' => 'For interactive HTML documentation, use Scribe in development environment'
    ]);
});

// Minimal named login route (development stub)
Route::get('/login', function () {
    return response()->json([
        'message' => 'Login route (stub) - use /api/auth/login for API authentication',
    ]);
})->name('login');
