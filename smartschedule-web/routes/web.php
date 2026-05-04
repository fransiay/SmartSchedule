<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes Web — SmartSchedule
|--------------------------------------------------------------------------
|
| Ce fichier définit toutes les routes accessibles via le navigateur web.
| Les routes sont protégées par deux systèmes d'authentification :
|   - 'guest'  : accessible uniquement aux utilisateurs NON connectés
|   - 'auth'   : accessible uniquement aux utilisateurs CONNECTÉS (session)
|
*/

// Page d'accueil (publique)
Route::get('/', function () {
    return view('welcome');
});

// ────────────────────────────────────────────────
// Routes d'authentification (réservées aux invités non connectés)
// ────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('register',  [WebAuthController::class, 'showRegister'])->name('register');
    Route::post('register', [WebAuthController::class, 'register']);
    Route::get('login',     [WebAuthController::class, 'showLogin'])->name('login');
    Route::post('login',    [WebAuthController::class, 'login']);
});

// Déconnexion (accessible aux utilisateurs connectés)
Route::post('logout', [WebAuthController::class, 'logout'])->name('logout');

// ────────────────────────────────────────────────
// Tableau de bord (accès restreint aux utilisateurs connectés)
// ────────────────────────────────────────────────
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// ────────────────────────────────────────────────
// Routes principales de l'application (authentification requise)
// ────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/tasks',          function () { return view('tasks'); })->name('tasks');
    Route::get('/availabilities', function () { return view('availabilities'); })->name('availabilities');
    Route::get('/calendar',       function () { return view('calendar'); })->name('calendar');
    Route::get('/categories',     function () { return view('categories'); })->name('categories');

    // ────────────────────────────────────────────────
    // Routes "web-api" : utilisées par le JavaScript des vues Blade
    // Ces routes réutilisent les contrôleurs API mais avec l'auth par session (pas par token).
    // Cela évite de dupliquer la logique entre le web et l'API mobile.
    // ────────────────────────────────────────────────
    Route::prefix('web-api')->group(function () {

        // ---- Tâches ----
        Route::get('/tasks',           [\App\Http\Controllers\Api\TaskController::class, 'index']);
        Route::post('/tasks',          [\App\Http\Controllers\Api\TaskController::class, 'store']);
        Route::put('/tasks/{task}',    [\App\Http\Controllers\Api\TaskController::class, 'update']);
        Route::delete('/tasks/{task}', [\App\Http\Controllers\Api\TaskController::class, 'destroy']);
        

        // ---- Disponibilités ----
        Route::get('/availabilities',                    [\App\Http\Controllers\Api\AvailabilityController::class, 'index']);
        Route::post('/availabilities',                   [\App\Http\Controllers\Api\AvailabilityController::class, 'store']);
        Route::put('/availabilities/{availability}',     [\App\Http\Controllers\Api\AvailabilityController::class, 'update']);
        Route::delete('/availabilities/{availability}',  [\App\Http\Controllers\Api\AvailabilityController::class, 'destroy']);

        // ---- Catégories ----
        Route::get('/categories',              [\App\Http\Controllers\Api\CategoryController::class, 'index']);
        Route::post('/categories',             [\App\Http\Controllers\Api\CategoryController::class, 'store']);
        Route::put('/categories/{category}',   [\App\Http\Controllers\Api\CategoryController::class, 'update']);
        Route::delete('/categories/{category}',[\App\Http\Controllers\Api\CategoryController::class, 'destroy']);

        // ---- Planning ----
        Route::get('/schedule',           [\App\Http\Controllers\Api\ScheduleController::class, 'index']);
        Route::post('/schedule/generate', [\App\Http\Controllers\Api\ScheduleController::class, 'generate']);

        // ---- Notifications ----
        Route::get('/notifications', function (Request $request) {
            return $request->user()->notifications()->take(10)->get();
        });
        Route::post('/notifications/mark-read', function (Request $request) {
            $request->user()->unreadNotifications->markAsRead();
            return response()->json(['success' => true]);
        });
    });
});

// ────────────────────────────────────────────────
// Gestion du profil utilisateur
// ────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
