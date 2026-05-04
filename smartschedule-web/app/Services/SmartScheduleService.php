<?php

namespace App\Services;

use App\Models\User;
use App\Models\Schedule;
use Carbon\Carbon;

/**
 * Service principal de génération de planning.
 * 
 * Cet algorithme répartit automatiquement les tâches de l'utilisateur
 * sur ses créneaux de disponibilité, en respectant les priorités et les deadlines.
 * Méthode utilisée : Min-Load Balancing (équilibrage de charge minimale).
 */
class SmartScheduleService
{
    public function generateForUser(User $user)
    {
        // Étape 0 : Suppression de tous les anciens créneaux planifiés
        $user->schedules()->delete();

        // Étape 1 : Récupération des tâches non terminées
        // Triées par priorité décroissante (1=Urgent en premier), puis deadline croissante
        $tasks = $user->tasks()
            ->where('status', '!=', 'done')
            ->orderBy('priority', 'desc')
            ->orderBy('deadline', 'asc')
            ->get();

        // Si aucune tâche, on arrête
        if ($tasks->isEmpty()) return [];

        // Étape 2 : Récupération des disponibilités actives, groupées par jour de la semaine
        $availabilities = $user->availabilities()->get()->filter(function ($a) {
            return $a->is_active;
        })->groupBy('day_of_week');

        // Étape 3 : Construction de la grille de disponibilités pour les 30 prochains jours
        $startDate = Carbon::today();
        $days = [];
        $breakDuration = (int)($user->break_duration ?? 0); // Durée de pause entre les tâches

        for ($i = 0; $i < 30; $i++) {
            $currentDate = $startDate->copy()->addDays($i);
            $dayOfWeek = $currentDate->dayOfWeek; // 0 = Dimanche, 6 = Samedi
            $dateString = $currentDate->toDateString();

            // Initialisation du jour dans la grille
            $days[$dateString] = [
                'date' => $currentDate,
                'used' => 0,      // Charge de travail déjà affectée (en minutes)
                'blocks' => []    // Créneaux disponibles pour ce jour
            ];

            // Si l'utilisateur a des disponibilités ce jour-là
            if ($availabilities->has($dayOfWeek)) {
                foreach ($availabilities[$dayOfWeek] as $av) {
                    $startBlock = $currentDate->copy()->setTimeFromTimeString($av->start_time);
                    $endBlock = $currentDate->copy()->setTimeFromTimeString($av->end_time);
                    
                    // Si on génère en cours de journée, décaler le début au moment actuel
                    if ($startBlock->isPast() && $currentDate->isToday()) {
                       $now = Carbon::now();
                       // Arrondir à la prochaine tranche de 15 minutes
                       $minutes = ceil($now->minute / 15) * 15;
                       $startBlock = $now->copy()->minute($minutes)->second(0);
                       
                       // Si le créneau est déjà terminé, on l'ignore
                       if ($startBlock->greaterThanOrEqualTo($endBlock)) {
                           continue;
                       }
                    }

                    // Ajout du bloc de disponibilité
                    $days[$dateString]['blocks'][] = [
                        'start'    => $startBlock,
                        'end'      => $endBlock,
                        'duration' => $startBlock->diffInMinutes($endBlock)
                    ];
                }
            }
            
            // Tri des blocs de la journée par heure de début
            usort($days[$dateString]['blocks'], function($a, $b) {
                return $a['start']->timestamp <=> $b['start']->timestamp;
            });
        }

        // Étape 4 : Affectation des tâches avec l'algorithme Min-Load Balancing
        $schedulesToInsert = [];
        $maxPerChunk = 120; // Durée maximale d'un bloc continu par tâche (en minutes)
        $now = now();

        foreach ($tasks as $task) {
            $remainingDuration = (int)$task->duration_minutes; // Durée restante à planifier
            $deadlineDate = $task->deadline ? $task->deadline->toDateString() : null;

            // Tant que la tâche n'est pas entièrement planifiée
            while ($remainingDuration > 0) {
                $bestDayKey = null;
                $minLoad = PHP_INT_MAX;

                // Recherche du jour avec le moins de charge (avant la deadline)
                foreach ($days as $dateKey => &$dayData) {
                    // Ne pas planifier après la deadline
                    if ($deadlineDate && $dateKey > $deadlineDate) {
                        continue;
                    }

                    // Vérifier si ce jour a encore de la capacité disponible
                    $hasCapacity = false;
                    foreach ($dayData['blocks'] as $block) {
                        if ($block['duration'] > 5) { // Minimum 5 minutes utiles
                            $hasCapacity = true;
                            break;
                        }
                    }

                    // Choisir le jour le moins chargé
                    if ($hasCapacity && $dayData['used'] < $minLoad) {
                        $minLoad = $dayData['used'];
                        $bestDayKey = $dateKey;
                    }
                }

                // Aucun créneau disponible avant la deadline : on arrête pour cette tâche
                if (!$bestDayKey) {
                    break;
                }

                // Sélection du premier bloc disponible dans le meilleur jour
                $assignedBlockIndex = null;
                foreach ($days[$bestDayKey]['blocks'] as $i => $block) {
                    if ($block['duration'] > 5) {
                        $assignedBlockIndex = $i;
                        break;
                    }
                }

                // Sécurité : marquer le jour comme plein si aucun bloc valide
                if ($assignedBlockIndex === null) {
                    $days[$bestDayKey]['used'] = PHP_INT_MAX;
                    continue;
                }

                $block = $days[$bestDayKey]['blocks'][$assignedBlockIndex];

                // Calcul de la durée affectée : min(durée restante, durée du bloc, max par chunk)
                $assignedDuration = min($remainingDuration, $block['duration'], $maxPerChunk);

                $scheduleStart = $block['start']->copy();
                $scheduleEnd = $block['start']->copy()->addMinutes($assignedDuration);

                // Préparation de l'enregistrement pour insertion en masse
                $schedulesToInsert[] = [
                    'user_id'    => $user->id,
                    'task_id'    => $task->id,
                    'start_time' => $scheduleStart->toDateTimeString(),
                    'end_time'   => $scheduleEnd->toDateTimeString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                // Mise à jour de la durée restante et de la charge du jour
                $remainingDuration -= $assignedDuration;
                $days[$bestDayKey]['used'] += $assignedDuration + $breakDuration;

                // Mise à jour du bloc : réduire ou supprimer selon la durée consommée
                $nextStart = $scheduleEnd->copy()->addMinutes($breakDuration);
                if ($nextStart->lessThan($block['end'])) {
                    // Réduire le bloc
                    $days[$bestDayKey]['blocks'][$assignedBlockIndex] = [
                        'start'    => $nextStart,
                        'end'      => $block['end'],
                        'duration' => $nextStart->diffInMinutes($block['end'])
                    ];
                } else {
                    // Supprimer le bloc entièrement
                    unset($days[$bestDayKey]['blocks'][$assignedBlockIndex]);
                    $days[$bestDayKey]['blocks'] = array_values($days[$bestDayKey]['blocks']);
                }
            }
        }

        // Étape 5 : Insertion en masse en base de données (plus performant qu'un insert par ligne)
        if (!empty($schedulesToInsert)) {
            Schedule::insert($schedulesToInsert);
        }

        return $schedulesToInsert;
    }
}
