<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SmartScheduleService;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $schedules = $request->user()
            ->schedules()
            ->with('task')
            ->orderBy('start_time')
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'start_time' => $s->start_time->toIso8601String(),
                    'end_time' => $s->end_time->toIso8601String(),
                    'task' => $s->task,
                ];
            });

        return response()->json($schedules);
    }

    public function generate(Request $request, SmartScheduleService $scheduler)
    {
        $hasAvailabilities = $request->user()->availabilities()->exists();
        if (!$hasAvailabilities) {
            return response()->json([
                'message' => 'Vous n\'avez défini aucune disponibilité. Allez dans "Disponibilités" pour configurer vos horaires.'
            ], 422);
        }

        $hasTasks = $request->user()->tasks()->where('status', '!=', 'done')->exists();
        if (!$hasTasks) {
            return response()->json([
                'message' => 'Vous n\'avez aucune tâche en attente à planifier. Ajoutez de nouvelles tâches !'
            ], 422);
        }

        // Delete all existing schedules
        $request->user()->schedules()->delete();

        // Run algorithm
        $raw = $scheduler->generateForUser($request->user());

        // Re-fetch with task relation and proper date format
        $schedules = $request->user()
            ->schedules()
            ->with('task')
            ->orderBy('start_time')
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'start_time' => $s->start_time->toIso8601String(),
                    'end_time' => $s->end_time->toIso8601String(),
                    'task' => $s->task,
                ];
            });

        return response()->json([
            'message' => 'Schedule generated successfully',
            'schedules' => $schedules,
        ]);
    }
}
