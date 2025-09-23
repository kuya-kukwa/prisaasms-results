<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Layer1\RegionController;
use App\Http\Controllers\Layer1\ProvinceController;
use App\Http\Controllers\Layer1\SchoolController;
use App\Http\Controllers\Layer1\DivisionController;
use App\Http\Controllers\Layer1\TournamentController;
use App\Http\Controllers\Layer1\VenueController;

// Public read-only endpoints
Route::get('regions', [RegionController::class, 'index']);
Route::get('regions/{id}', [RegionController::class, 'show']);

Route::get('provinces', [ProvinceController::class, 'index']);
Route::get('provinces/{id}', [ProvinceController::class, 'show']);

Route::get('schools', [SchoolController::class, 'index']);
Route::get('schools/{id}', [SchoolController::class, 'show']);

Route::get('divisions', [DivisionController::class, 'index']);
Route::get('divisions/{id}', [DivisionController::class, 'show']);

Route::get('tournaments', [TournamentController::class, 'index']);
Route::get('tournaments/{id}', [TournamentController::class, 'show']);

Route::get('venues', [VenueController::class, 'index']);
Route::get('venues/{id}', [VenueController::class, 'show']);
