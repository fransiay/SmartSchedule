<?php

namespace App\Services;

use App\Models\User;
use App\Models\Schedule;
use Carbon\Carbon;

class SmartScheduleService
{
    public function generateForUser(User $user)
    {
        // 0. Clear existing future schedules to avoid overlapping
        $user->schedules()->delete();

        // 1. Fetch incomplete tasks ordered by Priority (desc) then Deadline (asc)
        $tasks = $user->tasks()
            ->where('status', '!=', 'done')
            ->orderBy('priority', 'desc')
            ->orderBy('deadline', 'asc')
            ->get();

        if ($tasks->isEmpty()) return [];

        // 2. Fetch user's availabilities. Transform into a lookup array by day_of_week
        $availabilities = $user->availabilities()->get()->filter(function ($a) {
            return $a->is_active;
        })->groupBy('day_of_week');

        // 3. Group time blocks for the next 30 days by day
        $startDate = Carbon::today();
        $days = [];
        $breakDuration = (int)($user->break_duration ?? 0);

        for ($i = 0; $i < 30; $i++) {
            $currentDate = $startDate->copy()->addDays($i);
            $dayOfWeek = $currentDate->dayOfWeek; // 0 (Sunday) to 6 (Saturday)
            $dateString = $currentDate->toDateString();

            $days[$dateString] = [
                'date' => $currentDate,
                'used' => 0,
                'blocks' => []
            ];

            if ($availabilities->has($dayOfWeek)) {
                foreach ($availabilities[$dayOfWeek] as $av) {
                    $startBlock = $currentDate->copy()->setTimeFromTimeString($av->start_time);
                    $endBlock = $currentDate->copy()->setTimeFromTimeString($av->end_time);
                    
                    if ($startBlock->isPast() && $currentDate->isToday()) {
                       // if we are generating mid-day, move start block to now (rounded up to 15 mins)
                       $now = Carbon::now();
                       $minutes = ceil($now->minute / 15) * 15;
                       $startBlock = $now->copy()->minute($minutes)->second(0);
                       
                       if ($startBlock->greaterThanOrEqualTo($endBlock)) {
                           continue;
                       }
                    }

                    $days[$dateString]['blocks'][] = [
                        'start' => $startBlock,
                        'end' => $endBlock,
                        'duration' => $startBlock->diffInMinutes($endBlock)
                    ];
                }
            }
            
            // Sort blocks for the day
            usort($days[$dateString]['blocks'], function($a, $b) {
                return $a['start']->timestamp <=> $b['start']->timestamp;
            });
        }

        // 4. Assign tasks using Min-Load Load Balancing with Deadline Enforcement
        $schedulesToInsert = [];
        $maxPerChunk = 120; // Maximum duration of a continuous chunk per task per day
        $now = now();

        foreach ($tasks as $task) {
            $remainingDuration = (int)$task->duration_minutes;
            $deadlineDate = $task->deadline ? $task->deadline->toDateString() : null;

            while ($remainingDuration > 0) {
                $bestDayKey = null;
                $minLoad = PHP_INT_MAX;

                // Optimization: Pre-filter days by deadline to avoid unnecessary checks
                foreach ($days as $dateKey => &$dayData) {
                    // Critical: Ensure task is scheduled before its deadline
                    if ($deadlineDate && $dateKey > $deadlineDate) {
                        continue;
                    }

                    // Check if this day has any valid block
                    $hasCapacity = false;
                    foreach ($dayData['blocks'] as $block) {
                        if ($block['duration'] > 5) {
                            $hasCapacity = true;
                            break;
                        }
                    }

                    if ($hasCapacity && $dayData['used'] < $minLoad) {
                        $minLoad = $dayData['used'];
                        $bestDayKey = $dateKey;
                    }
                }

                if (!$bestDayKey) {
                    // No more capacity available before deadline
                    break;
                }

                // Pick the first available block in this best day
                $assignedBlockIndex = null;
                foreach ($days[$bestDayKey]['blocks'] as $i => $block) {
                    if ($block['duration'] > 5) {
                        $assignedBlockIndex = $i;
                        break;
                    }
                }

                if ($assignedBlockIndex === null) {
                    // Mark as full to prevent infinite loop
                    $days[$bestDayKey]['used'] = PHP_INT_MAX;
                    continue;
                }

                $block = $days[$bestDayKey]['blocks'][$assignedBlockIndex];

                // Assign up to max chunk size or block duration
                $assignedDuration = min($remainingDuration, $block['duration'], $maxPerChunk);

                $scheduleStart = $block['start']->copy();
                $scheduleEnd = $block['start']->copy()->addMinutes($assignedDuration);

                // Prepare schedule segment for Bulk Insert
                $schedulesToInsert[] = [
                    'user_id' => $user->id,
                    'task_id' => $task->id,
                    'start_time' => $scheduleStart->toDateTimeString(),
                    'end_time' => $scheduleEnd->toDateTimeString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                // Update task remaining
                $remainingDuration -= $assignedDuration;
                
                // Add assigned duration + break to day's 'used'
                $days[$bestDayKey]['used'] += $assignedDuration + $breakDuration;

                // Update the block
                $nextStart = $scheduleEnd->copy()->addMinutes($breakDuration);
                if ($nextStart->lessThan($block['end'])) {
                    // Shorten block
                    $days[$bestDayKey]['blocks'][$assignedBlockIndex] = [
                        'start' => $nextStart,
                        'end' => $block['end'],
                        'duration' => $nextStart->diffInMinutes($block['end'])
                    ];
                } else {
                    // Remove block entirely
                    unset($days[$bestDayKey]['blocks'][$assignedBlockIndex]);
                    // Re-index array
                    $days[$bestDayKey]['blocks'] = array_values($days[$bestDayKey]['blocks']);
                }
            }
        }

        if (!empty($schedulesToInsert)) {
            Schedule::insert($schedulesToInsert);
        }

        return $schedulesToInsert;
    }
}
