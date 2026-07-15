<?php

namespace App\Services;

class SchedulingEngine
{
    private $workload;
    private $settings;
    private $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
    private $schedule = [];
    
    // Performance Optimization Maps
    private $instructorBusyMap = []; 
    private $instructorCurrentSchedule = []; 
    private $occupiedSlots = []; // Prevents two classes for the same year/group at once

    public function __construct($workload, $constraints, $settings)
    {
        $this->workload = $workload;
        $this->settings = $settings;

        // Pre-process constraints into a high-speed lookup map
        foreach ($constraints as $c) {
            $profileId = is_object($c) ? $c->instructor_profile_id : ($c['instructor_profile_id'] ?? $c['user_id'] ?? null);
            $day = is_object($c) ? $c->day : $c['day'];
            $period = is_object($c) ? $c->period : $c['period'];
            
            if ($profileId) {
                $this->instructorBusyMap[$profileId][$day][$period] = true;
            }
        }
    }

    public function run()
    {
        // Prevent script timeout for complex calculations
        set_time_limit(300); 

        // 1. Flatten the workload into individual task units
        $tasks = [];
        foreach ($this->workload as $item) {
            $lec = $item['hours']['lec'] ?? 0;
            $tut = $item['hours']['tut'] ?? 0;
            $lab = $item['hours']['lab'] ?? 0;

            for ($i = 0; $i < $lec; $i++) $tasks[] = ['type' => 'Lec', 'data' => $item];
            for ($i = 0; $i < $tut; $i++) $tasks[] = ['type' => 'Tut', 'data' => $item];
            for ($i = 0; $i < $lab; $i++) $tasks[] = ['type' => 'Lab', 'data' => $item];
        }

        // Heuristic: Place Labs first (they are usually the hardest to fit)
        usort($tasks, function($a, $b) {
            $priority = ['Lab' => 3, 'Tut' => 2, 'Lec' => 1];
            return $priority[$b['type']] <=> $priority[$a['type']];
        });

        // 2. Initial Capacity Check
        if (!$this->validateCapacity($tasks)) {
            return false; 
        }

        // 3. Reset internal state
        $this->schedule = [];
        $this->instructorCurrentSchedule = [];
        $this->occupiedSlots = [];

        // 4. Execute Backtracking
        $result = $this->backtrack($tasks, 0);
        
        if (!$result) {
            session(['scheduler_failure_reason' => "The engine could not find a conflict-free combination. This usually means instructors are too busy or there are too many courses for this group's schedule."]);
        }
        
        return $result ? $this->schedule : false;
    }

    private function backtrack($tasks, $index)
    {
        // Base Case: Success! All tasks have been placed.
        if ($index >= count($tasks)) return true;

        $currentTask = $tasks[$index];
        
        // Pick the correct instructor based on whether this is a Lab or Lecture
        $instructorId = ($currentTask['type'] === 'Lab') 
            ? $currentTask['data']['lab_user_id'] 
            : $currentTask['data']['lec_user_id'];

        foreach ($this->days as $day) {
            for ($period = 1; $period <= $this->settings['total_periods']; $period++) {
                
                if ($this->isSafe($currentTask, $day, $period, $instructorId)) {
                    
                    // SlotKey must match what your Blade view expects (e.g., Monday_1)
                    $slotKey = "{$day}_{$period}"; 
                    
                    // PLACE TASK
                    $this->schedule[$slotKey] = [
                        'course_id'   => $currentTask['data']['course_id'],
                        'course_code' => $currentTask['data']['csv_course_id'],
                        'course_name' => $currentTask['data']['course_name'],
                        'type'        => $currentTask['type'],
                        'day'         => $day,
                        'period'      => $period,
                        'instructor'  => ($currentTask['type'] === 'Lab') ? $currentTask['data']['lab_name'] : $currentTask['data']['lec_name'],
                        'user_id'     => $instructorId
                    ];

                    // Mark resources as occupied for this branch of the search
                    $this->instructorCurrentSchedule[$instructorId][$day][$period] = true;
                    $this->occupiedSlots[$day][$period] = true;

                    // Recurse to next task
                    if ($this->backtrack($tasks, $index + 1)) return true;

                    // BACKTRACK (Remove placement if it leads to a dead end)
                    unset($this->schedule[$slotKey]);
                    unset($this->instructorCurrentSchedule[$instructorId][$day][$period]);
                    unset($this->occupiedSlots[$day][$period]);
                }
            }
        }
        return false;
    }

    private function isSafe($task, $day, $period, $instructorId)
    {
        // 1. Check if this specific time slot is already taken in this schedule
        if (isset($this->occupiedSlots[$day][$period])) return false;

        // 2. Check Instructor Availability (from their 'Busy' settings in DB)
        if (isset($this->instructorBusyMap[$instructorId][$day][$period])) return false;

        // 3. Check Instructor Conflict (already assigned to teach another course at this exact time)
        if (isset($this->instructorCurrentSchedule[$instructorId][$day][$period])) return false;

        // 4. Time-of-Day Rules (Softened for maximum success rate)
        // Note: If you want to strictly force Lec to Morning, uncomment the 'return false' lines below.
        $midPoint = ceil($this->settings['total_periods'] / 2);
        
        if ($task['type'] === 'Lec' && $period > $midPoint) {
            // return false; // Strict: No lectures in afternoon
        }
        
        if ($task['type'] === 'Lab' && $period <= 1) {
            // return false; // Strict: No labs in the first period
        }

        return true;
    }

    private function validateCapacity($tasks)
    {
        $totalRequiredSlots = count($tasks);
        $availableSlots = count($this->days) * $this->settings['total_periods'];

        if ($totalRequiredSlots > $availableSlots) {
            session(['scheduler_failure_reason' => "Too many tasks ($totalRequiredSlots) for the available slots ($availableSlots). Try increasing the number of periods in settings."]);
            return false;
        }

        return true;
    }
}