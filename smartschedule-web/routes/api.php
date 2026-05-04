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
| Routes API — SmartSchedule (Application Mobile Android)
|--------------------------------------------------------------------------
|
| Ces routes retournent du JSON et sont consommées par l'application mobile
| React Native / Expo via des requêtes HTTP avec le token Bearer Sanctum.
|
| Routes publiques : /api/login et /api/register (pas besoin d'être connecté)
| Routes protégées : toutes les autres (middleware auth:sanctum requis)
|
*/

// ────────────────────────────────────────────────
// Routes publiques (sans authentification)
// ────────────────────────────────────────────────
Route::post('/login',    [AuthController::class, 'login']);    // Connexion → retourne un token Bearer
Route::post('/register', [AuthController::class, 'register']); // Inscription → retourne un token Bearer

// ────────────────────────────────────────────────
// Routes protégées (token Bearer Sanctum requis dans le header)
// Header attendu : Authorization: Bearer {token}
// ────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // ---- Profil utilisateur ----
    Route::get('/user',  [AuthController::class, 'profile']);       // Récupérer son profil
    Route::put('/user',  [AuthController::class, 'updateProfile']); // Modifier nom / durée de pause
    Route::post('/user/push-token', [AuthController::class, 'updatePushToken']); // Enregistrer le token Expo
    Route::post('/logout', [AuthController::class, 'logout']);       // Déconnexion (supprime le token)

    // ---- Tâches (CRUD complet) ----
    // GET    /api/tasks         → liste des tâches
    // POST   /api/tasks         → créer une tâche
    // GET    /api/tasks/{id}    → détail d'une tâche
    // PUT    /api/tasks/{id}    → modifier une tâche
    // DELETE /api/tasks/{id}    → supprimer une tâche
    Route::apiResource('tasks', TaskController::class);

    // ---- Catégories (CRUD complet) ----
    Route::apiResource('categories', CategoryController::class);

    // ---- Disponibilités (CRUD complet) ----
    Route::apiResource('availabilities', AvailabilityController::class);

    // ---- Planning ----
    Route::post('/schedule/generate', [ScheduleController::class, 'generate']); // Générer le planning
    Route::get('/schedule',           [ScheduleController::class, 'index']);     // Récupérer le planning
});
