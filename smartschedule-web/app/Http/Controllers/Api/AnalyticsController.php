<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Contrôleur Analytics — Statistiques de l'utilisateur.
 *
 * Fournit des données agrégées sur :
 *   - La répartition des tâches par statut
 *   - Le temps (en minutes) par catégorie
 *   - Le taux de complétion sur les 30 derniers jours
 *   - L'évolution hebdomadaire (tâches complétées chaque jour)
 */
class AnalyticsController extends Controller
{
    /**
     * Retourne toutes les statistiques consolidées.
     */
    public function index(Request $request)
    {
        $user  = $request->user();
        $tasks = $user->tasks()->with('category')->get();

        return response()->json([
            'overview'          => $this->overview($tasks),
            'by_category'       => $this->byCategory($tasks),
            'completion_trend'  => $this->completionTrend($user),
            'priority_breakdown'=> $this->priorityBreakdown($tasks),
        ]);
    }

    // ── Vue d'ensemble ──────────────────────────────

    private function overview($tasks): array
    {
        $total       = $tasks->count();
        $done        = $tasks->where('status', 'done')->count();
        $inProgress  = $tasks->where('status', 'in_progress')->count();
        $todo        = $tasks->where('status', 'todo')->count();
        $overdue     = $tasks->filter(fn($t) =>
            $t->status !== 'done' && $t->deadline && $t->deadline->isPast()
        )->count();

        $totalMinutes = $tasks->sum('duration_minutes');
        $doneMinutes  = $tasks->where('status', 'done')->sum('duration_minutes');

        return [
            'total'            => $total,
            'done'             => $done,
            'in_progress'      => $inProgress,
            'todo'             => $todo,
            'overdue'          => $overdue,
            'completion_rate'  => $total > 0 ? round(($done / $total) * 100, 1) : 0,
            'total_minutes'    => $totalMinutes,
            'done_minutes'     => $doneMinutes,
        ];
    }

    // ── Temps par catégorie ──────────────────────────

    private function byCategory($tasks): array
    {
        $grouped = $tasks->groupBy('category_id');
        $result  = [];

        foreach ($grouped as $catId => $group) {
            $cat = $group->first()->category;
            $result[] = [
                'category_id'   => $catId,
                'category_name' => $cat ? $cat->name  : 'Sans catégorie',
                'category_color'=> $cat ? $cat->color : '#6c6c70',
                'total_tasks'   => $group->count(),
                'done_tasks'    => $group->where('status', 'done')->count(),
                'total_minutes' => $group->sum('duration_minutes'),
                'done_minutes'  => $group->where('status', 'done')->sum('duration_minutes'),
                'completion_rate' => $group->count() > 0
                    ? round(($group->where('status', 'done')->count() / $group->count()) * 100, 1)
                    : 0,
            ];
        }

        // Trie par temps total décroissant
        usort($result, fn($a, $b) => $b['total_minutes'] <=> $a['total_minutes']);
        return $result;
    }

    // ── Tendance de complétion (30 derniers jours) ──

    private function completionTrend($user): array
    {
        $days   = 30;
        $result = [];
        $start  = Carbon::now()->subDays($days - 1)->startOfDay();

        // Tâches complétées groupées par date de mise à jour
        $doneTasks = $user->tasks()
            ->where('status', 'done')
            ->where('updated_at', '>=', $start)
            ->get()
            ->groupBy(fn($t) => $t->updated_at->toDateString());

        for ($i = 0; $i < $days; $i++) {
            $date = Carbon::now()->subDays($days - 1 - $i)->toDateString();
            $result[] = [
                'date'  => $date,
                'label' => Carbon::parse($date)->locale('fr')->isoFormat('D MMM'),
                'count' => isset($doneTasks[$date]) ? $doneTasks[$date]->count() : 0,
            ];
        }

        return $result;
    }

    // ── Répartition par priorité ─────────────────────

    private function priorityBreakdown($tasks): array
    {
        $labels = [
            1 => 'Urgent',
            2 => 'Élevé',
            3 => 'Moyen',
            4 => 'Normal',
            5 => 'Bas',
        ];
        $result = [];
        for ($p = 1; $p <= 5; $p++) {
            $group = $tasks->where('priority', $p);
            $result[] = [
                'priority'       => $p,
                'label'          => $labels[$p],
                'total'          => $group->count(),
                'done'           => $group->where('status', 'done')->count(),
                'completion_rate'=> $group->count() > 0
                    ? round(($group->where('status', 'done')->count() / $group->count()) * 100, 1)
                    : 0,
            ];
        }
        return $result;
    }
}
