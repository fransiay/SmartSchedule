<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Commande de vérification des échéances de tâches.
 *
 * Analyse toutes les tâches non terminées et envoie une notification
 * de rappel uniquement si la deadline est DEMAIN.
 *
 * Anti-spam : vérifie qu'une notification similaire n'a pas déjà été
 * envoyée dans les dernières 12 heures pour la même tâche.
 */
class CheckTaskDeadlines extends Command
{
    protected $signature = 'tasks:check-deadlines {--user= : ID d\'un utilisateur spécifique (optionnel)}';
    protected $description = 'Vérifie les échéances des tâches et envoie des rappels (J-1 uniquement)';

    public function handle(): int
    {
        $query = User::query();

        // Si un user spécifique est demandé (ex: au login)
        if ($userId = $this->option('user')) {
            $query->where('id', $userId);
        }

        $users = $query->get();
        $totalNotifs = 0;

        foreach ($users as $user) {
            $tasks = $user->tasks()
                ->whereNotNull('deadline')
                ->where('status', '!=', 'done')
                ->get();

            foreach ($tasks as $task) {
                $notification = $this->buildReminder($task);
                if (!$notification) continue;

                // Anti-spam : vérifier qu'on n'a pas déjà notifié pour cette tâche récemment
                $alreadySent = $user->notifications()
                    ->where('created_at', '>=', Carbon::now()->subHours(12))
                    ->get()
                    ->contains(function ($n) use ($task, $notification) {
                        return ($n->data['task_id'] ?? null) == $task->id
                            && ($n->data['reminder_type'] ?? null) === $notification['reminder_type'];
                    });

                if ($alreadySent) continue;

                $user->notify(new TaskNotification(
                    $task,
                    $notification['message'],
                    $notification['type'],
                    $notification['reminder_type']
                ));
                $totalNotifs++;
            }
        }

        $this->info("✅ {$totalNotifs} rappel(s) envoyé(s).");
        return self::SUCCESS;
    }

    /**
     * Détermine le message de rappel (Uniquement pour J-1).
     */
    private function buildReminder(Task $task): ?array
    {
        $now = Carbon::now()->startOfDay();
        $deadline = Carbon::parse($task->deadline)->startOfDay();
        $diff = $now->diffInDays($deadline, false); 

        $statusLabels = [
            'todo'        => 'À faire',
            'in_progress' => 'En cours',
        ];
        $statusText = $statusLabels[$task->status] ?? $task->status;

        // Rappel uniquement pour demain (J-1)
        if ($diff === 1) {
            return [
                'message'       => "🔔 Rappel : La tâche « {$task->title} » expire demain. Statut : {$statusText}.",
                'type'          => 'warning',
                'reminder_type' => 'tomorrow',
            ];
        }

        return null; // Pas de rappel nécessaire pour les autres cas
    }
}
