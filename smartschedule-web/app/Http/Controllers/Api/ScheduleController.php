<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SmartScheduleService;
use App\Models\Schedule;
use Illuminate\Http\Request;

/**
 * Contrôleur du planning (Schedule) pour l'API REST.
 * 
 * Permet de récupérer le planning existant de l'utilisateur
 * et de déclencher la génération automatique via l'algorithme SmartSchedule.
 */
class ScheduleController extends Controller
{
    /**
     * Retourne tous les créneaux planifiés de l'utilisateur connecté.
     * 
     * Inclut la relation 'task' pour afficher le titre, la priorité, etc.
     * Résultats triés par heure de début.
     */
    public function index(Request $request)
    {
        $schedules = $request->user()
            ->schedules()
            ->with('task')          // Chargement eager de la tâche liée
            ->orderBy('start_time')
            ->get()
            ->map(function ($s) {
                return [
                    'id'         => $s->id,
                    'start_time' => $s->start_time->toIso8601String(), // Format ISO 8601 pour le calendrier
                    'end_time'   => $s->end_time->toIso8601String(),
                    'task'       => $s->task,
                ];
            });

        return response()->json($schedules);
    }

    /**
     * Génère automatiquement un nouveau planning pour l'utilisateur.
     * 
     * Prérequis :
     *  - L'utilisateur doit avoir au moins une disponibilité définie.
     *  - L'utilisateur doit avoir au moins une tâche non terminée.
     * 
     * Le planning existant est entièrement supprimé avant chaque génération.
     */
    public function generate(Request $request, SmartScheduleService $scheduler)
    {
        // Vérification : des disponibilités existent-elles ?
        $hasAvailabilities = $request->user()->availabilities()->exists();
        if (!$hasAvailabilities) {
            return response()->json([
                'message' => 'Vous n\'avez défini aucune disponibilité. Allez dans "Disponibilités" pour configurer vos horaires.'
            ], 422);
        }

        // Vérification : des tâches à planifier existent-elles ?
        $hasTasks = $request->user()->tasks()->where('status', '!=', 'done')->exists();
        if (!$hasTasks) {
            return response()->json([
                'message' => 'Vous n\'avez aucune tâche en attente à planifier. Ajoutez de nouvelles tâches !'
            ], 422);
        }

        // Suppression du planning existant (remise à zéro)
        $request->user()->schedules()->delete();

        // Lancement de l'algorithme de génération
        $raw = $scheduler->generateForUser($request->user());

        // Re-fetch avec la relation 'task' et format de date correct
        $schedules = $request->user()
            ->schedules()
            ->with('task')
            ->orderBy('start_time')
            ->get()
            ->map(function ($s) {
                return [
                    'id'         => $s->id,
                    'start_time' => $s->start_time->toIso8601String(),
                    'end_time'   => $s->end_time->toIso8601String(),
                    'task'       => $s->task,
                ];
            });

        return response()->json([
            'message'   => 'Schedule generated successfully',
            'schedules' => $schedules,
        ]);
    }
}
