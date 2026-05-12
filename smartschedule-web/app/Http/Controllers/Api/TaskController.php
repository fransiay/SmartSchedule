<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Notifications\TaskNotification;

/**
 * Contrôleur CRUD des tâches pour l'API REST.
 * 
 * Chaque méthode vérifie que l'utilisateur est bien propriétaire de la tâche
 * avant d'effectuer toute opération (sécurité : isolation des données).
 */
class TaskController extends Controller
{
    /**
     * Retourne toutes les tâches de l'utilisateur connecté.
     * Inclut la catégorie associée via eager loading.
     */
    public function index(Request $request)
    {
        return response()->json(
            $request->user()->tasks()->with(['category', 'attachments'])->whereNull('parent_task_id')->get()
        );
    }

    /**
     * Crée une nouvelle tâche pour l'utilisateur connecté.
     * 
     * Règles de validation :
     * - priority : entier de 1 (Urgent) à 5 (Bas)
     * - status   : todo / in_progress / done
     * - category_id : doit appartenir à l'utilisateur connecté (sécurité)
     */
    public function store(Request $request)
    {
        // Validation des données envoyées
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'priority'         => 'required|integer|min:1|max:5',
            'deadline'         => 'required|date',
            'modifier'         => 'nullable|string',
            'category_id'      => [
                'nullable',
                \Illuminate\Validation\Rule::exists('categories', 'id')->where(function ($query) use ($request) {
                    $query->where('user_id', $request->user()->id);
                }),
            ],
            'status'           => 'required|string|in:todo,in_progress,done',
            // Champs récurrence
            'is_recurring'     => 'boolean',
            'recurrence_type'  => 'nullable|required_if:is_recurring,true|in:daily,weekly,monthly',
            'recurrence_days'  => 'nullable|array',
            'recurrence_days.*'=> 'integer|min:0|max:6',
            'recurrence_end'   => 'nullable|date|after:deadline',
        ]);

        // Création de la tâche liée à l'utilisateur
        $task = $request->user()->tasks()->create($validated);

        // Notification de création
        $request->user()->notify(new TaskNotification($task, "Nouvelle tâche créée : {$task->title}", 'success'));

        return response()->json($task, 201); // 201 Created
    }

    /**
     * Retourne les détails d'une tâche spécifique.
     * Retourne 403 si la tâche n'appartient pas à l'utilisateur.
     */
    public function show(Request $request, Task $task)
    {
        // Vérification de la propriété de la tâche
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return response()->json($task->load('category'));
    }

    /**
     * Met à jour une tâche existante (mise à jour partielle supportée).
     * Tous les champs sont optionnels grâce au préfixe 'sometimes'.
     */
    public function update(Request $request, Task $task)
    {
        // Vérification de la propriété de la tâche
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Validation partielle (seuls les champs envoyés sont validés)
        $validated = $request->validate([
            'title'            => 'sometimes|required|string|max:255',
            'description'      => 'nullable|string',
            'duration_minutes' => 'sometimes|required|integer|min:1',
            'priority'         => 'sometimes|required|integer|min:1|max:5',
            'deadline'         => 'sometimes|required|date',
            'category_id'      => [
                'nullable',
                \Illuminate\Validation\Rule::exists('categories', 'id')->where(function ($query) use ($request) {
                    $query->where('user_id', $request->user()->id);
                }),
            ],
            'status'           => 'sometimes|required|string|in:todo,in_progress,done',
            // Champs récurrence
            'is_recurring'     => 'boolean',
            'recurrence_type'  => 'nullable|in:daily,weekly,monthly',
            'recurrence_days'  => 'nullable|array',
            'recurrence_days.*'=> 'integer|min:0|max:6',
            'recurrence_end'   => 'nullable|date',
        ]);

        $oldStatus = $task->status;
        $task->update($validated);

        // Notification si le statut change
        if (isset($validated['status']) && $validated['status'] !== $oldStatus) {
            $statusText = [
                'todo' => 'À faire',
                'in_progress' => 'En cours',
                'done' => 'Terminée'
            ];
            $request->user()->notify(new TaskNotification(
                $task, 
                "Statut de la tâche '{$task->title}' mis à jour : " . ($statusText[$task->status] ?? $task->status), 
                $task->status === 'done' ? 'success' : 'info'
            ));
        }

        return response()->json($task);
    }

    /**
     * Supprime une tâche.
     * Retourne 204 No Content en cas de succès.
     */
    public function destroy(Request $request, Task $task)
    {
        // Vérification de la propriété de la tâche
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();
        return response()->json(null, 204); // 204 No Content
    }
}
