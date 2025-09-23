<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Layer0Controllers\AuthController;
use App\Http\Controllers\Layer3Controllers\ScheduleController;

Route::middleware('auth:sanctum')->group(function () {

    // Authenticated user info
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('logout-all', [AuthController::class, 'logoutAll']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    // Schedules for all authenticated users
    Route::get('schedules/upcoming', [ScheduleController::class, 'getUpcoming']);
});
