<?php

use Illuminate\Support\Facades\Route;


// Admin middleware
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

    // Layer1: CRUD + Soft Delete
    Route::apiResource('regions', App\Http\Controllers\Layer1\RegionController::class);
    Route::post('regions/{id}/restore', [App\Http\Controllers\Layer1\RegionController::class, 'restore']);
    Route::delete('regions/{id}/force', [App\Http\Controllers\Layer1\RegionController::class, 'forceDelete']);

    Route::apiResource('provinces', App\Http\Controllers\Layer1\ProvinceController::class);
    Route::post('provinces/{id}/restore', [App\Http\Controllers\Layer1\ProvinceController::class, 'restore']);
    Route::delete('provinces/{id}/force', [App\Http\Controllers\Layer1\ProvinceController::class, 'forceDelete']);

    Route::apiResource('schools', App\Http\Controllers\Layer1\SchoolController::class);
    Route::post('schools/{id}/restore', [App\Http\Controllers\Layer1\SchoolController::class, 'restore']);
    Route::delete('schools/{id}/force', [App\Http\Controllers\Layer1\SchoolController::class, 'forceDelete']);

    Route::apiResource('divisions', App\Http\Controllers\Layer1\DivisionController::class);
    Route::post('divisions/{id}/restore', [App\Http\Controllers\Layer1\DivisionController::class, 'restore']);
    Route::delete('divisions/{id}/force', [App\Http\Controllers\Layer1\DivisionController::class, 'forceDelete']);

    Route::apiResource('season-years', App\Http\Controllers\Layer1\SeasonYearController::class);
    Route::apiResource('tournaments', App\Http\Controllers\Layer1\TournamentController::class);
    Route::apiResource('users', App\Http\Controllers\Layer1\UserController::class);
    Route::apiResource('venues', App\Http\Controllers\Layer1\VenueController::class);

    // Layer2
    Route::apiResource('athletes', App\Http\Controllers\Layer2controllers\AthleteController::class);
    Route::apiResource('teams', App\Http\Controllers\Layer2\TeamController::class);

    // Layer3
    Route::apiResource('schedules', App\Http\Controllers\Layer3Controllers\ScheduleController::class);
    Route::get('schedules/upcoming', [App\Http\Controllers\Layer3Controllers\ScheduleController::class, 'getUpcoming']);
    Route::apiResource('matches', App\Http\Controllers\Layer3Controllers\GameMatchController::class);
    Route::apiResource('results', App\Http\Controllers\Layer3Controllers\ResultController::class);

    // Layer4
    Route::apiResource('champions', App\Http\Controllers\Layer4Controllers\ChampionController::class);
    Route::apiResource('medal-tallies', App\Http\Controllers\Layer4Controllers\MedalTallyController::class);
    Route::apiResource('qualifications', App\Http\Controllers\Layer4Controllers\QualificationController::class);
});
