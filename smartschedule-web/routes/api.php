<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AvailabilityController;
use App\Http\Controllers\Api\ScheduleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'profile']);
    Route::put('/user', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Tasks API
    Route::apiResource('tasks', TaskController::class);

    // Categories API
    Route::apiResource('categories', CategoryController::class);

    // Availabilities API
    Route::apiResource('availabilities', AvailabilityController::class);

    // Scheduling APIs
    Route::post('/schedule/generate', [ScheduleController::class, 'generate']);
    Route::get('/schedule', [ScheduleController::class, 'index']);
});
