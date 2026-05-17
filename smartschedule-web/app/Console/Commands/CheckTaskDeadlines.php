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
    protected $description = 'Vérifie les échéances et envoie des rappels uniquement à J-1';

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

                // Anti-spam : vérifier qu'on n'a pas déjà notifié pour cette tâche dans les dernières 24h
                $alreadySent = $user->notifications()
                    ->where('created_at', '>=', Carbon::now()->subHours(24))
                    ->get()
                    ->contains(function ($n) use ($task) {
                        $data = is_array($n->data) ? $n->data : json_decode($n->data, true);
                        return ($data['task_id'] ?? null) == $task->id
                            && ($data['reminder_type'] ?? null) === 'tomorrow';
                    });

                if ($alreadySent) continue;

                $user->notify(new TaskNotification(
                    $task,
                    $notification['message'],
                    $notification['type'],
                    'tomorrow'
                ));
                $totalNotifs++;
            }
        }

        $this->info("✅ {$totalNotifs} rappel(s) envoyé(s).");
        return self::SUCCESS;
    }

    /**
     * Détermine le message de rappel (Uniquement J-1).
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

        // On ne garde QUE le rappel pour demain (J-1)
        if ($diff === 1) {
            return [
                'message'       => "🔔 Rappel : La tâche « {$task->title} » expire demain. Statut : {$statusText}.",
                'type'          => 'warning',
            ];
        }

        return null;
    }
}
