<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebAuthController;
use Illuminate\Support\Facades\Route;

// ... (top comments)

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('register', [WebAuthController::class, 'showRegister'])->name('register');
    Route::post('register', [WebAuthController::class, 'register']);
    Route::get('login', [WebAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [WebAuthController::class, 'login']);
});

Route::post('logout', [WebAuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/tasks', function () { return view('tasks'); })->name('tasks');
    Route::get('/availabilities', function () { return view('availabilities'); })->name('availabilities');
    Route::get('/calendar', function () { return view('calendar'); })->name('calendar');
    Route::get('/categories', function () { return view('categories'); })->name('categories');

    // ---- Routes web (session auth) pour les appels fetch() des vues Blade ----
    Route::prefix('web-api')->group(function () {
        // Tasks
        Route::get('/tasks', [\App\Http\Controllers\Api\TaskController::class, 'index']);
        Route::post('/tasks', [\App\Http\Controllers\Api\TaskController::class, 'store']);
        Route::put('/tasks/{task}', [\App\Http\Controllers\Api\TaskController::class, 'update']);
        Route::delete('/tasks/{task}', [\App\Http\Controllers\Api\TaskController::class, 'destroy']);

        // Availabilities
        Route::get('/availabilities', [\App\Http\Controllers\Api\AvailabilityController::class, 'index']);
        Route::post('/availabilities', [\App\Http\Controllers\Api\AvailabilityController::class, 'store']);
        Route::put('/availabilities/{availability}', [\App\Http\Controllers\Api\AvailabilityController::class, 'update']);
        Route::delete('/availabilities/{availability}', [\App\Http\Controllers\Api\AvailabilityController::class, 'destroy']);

        // Categories
        Route::get('/categories', [\App\Http\Controllers\Api\CategoryController::class, 'index']);
        Route::post('/categories', [\App\Http\Controllers\Api\CategoryController::class, 'store']);
        Route::put('/categories/{category}', [\App\Http\Controllers\Api\CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [\App\Http\Controllers\Api\CategoryController::class, 'destroy']);

        // Schedule
        Route::get('/schedule', [\App\Http\Controllers\Api\ScheduleController::class, 'index']);
        Route::post('/schedule/generate', [\App\Http\Controllers\Api\ScheduleController::class, 'generate']);
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

