<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ExamSchedulingEngine
{
    private $workload;
    private $settings;
    private $exclusionGroups = [];
    private $instructorBusyMap = [];
    private $externalConstraints = [];
    private $schedule = [];
    private $occupiedSlotsCount = []; // [day][period] => count of exams
    private $roomUsageMap = []; // [day][period][room_name] => true
    private $globalRoomOccupancy = []; // Room occupancy from other sections
    private $globalExamsPerSlot = []; // Exam counts from other sections
    private $lockedRooms = null; // Store the room(s) assigned to this section
    private $courseToGroupMap = [];
    private $failureCounts = []; // Track why slots are being rejected

    private $externalGroupMap = [];
    private $preferredDays = [];
    private $examDates = [];

    /**
     * @param array $workload List of courses with invigilators and duration
     * @param array $constraints ['external' => [...], 'instructor_locks' => [...], 'exclusion_groups' => [...], 'exam_dates' => [...]]
     * @param array $settings ['total_sections' => X, 'rooms' => [...], 'total_days' => 10]
     */
    public function __construct($workload, $constraints, $settings)
    {
        $this->workload = $workload;
        $this->settings = $settings; // 'rooms' now contains objects/arrays with 'name' and 'type'
        $this->externalConstraints = $constraints['external'] ?? [];
        $this->exclusionGroups = $constraints['exclusion_groups'] ?? [];
        $this->examDates = $constraints['exam_dates'] ?? [];
        $this->globalRoomOccupancy = $constraints['global_room_occupancy'] ?? [];
        $this->globalExamsPerSlot = $constraints['exams_per_slot'] ?? [];

        // Map courses to their exclusion groups for fast lookup
        foreach ($this->exclusionGroups as $group) {
            foreach ($group['course_ids'] as $courseId) {
                $this->courseToGroupMap[(int)$courseId][] = (int)$group['id'];
            }
        }

        // Pre-process instructor busy periods
        foreach ($constraints['instructor_locks'] ?? [] as $lock) {
            $this->instructorBusyMap[(int)$lock['instructor_id']][$lock['day_number']][$lock['period']] = true;
        }

        // Pre-process external constraints groups for optimization
        if (!empty($this->externalConstraints)) {
            $extCourseIds = collect($this->externalConstraints)->pluck('course_id')->unique()->toArray();
            $extGroupData = DB::table('exam_exclusion_group_courses')
                ->whereIn('course_id', $extCourseIds)
                ->get(['course_id', 'group_id']);
            
            foreach ($extGroupData as $row) {
                $this->externalGroupMap[(int)$row->course_id][] = (int)$row->group_id;
            }
        }

        // Calculate preferred days based on gap logic
        $this->calculatePreferredDays();
    }

    private function calculatePreferredDays()
    {
        $count = count($this->workload);
        
        if ($count <= 6) {
            // Strategy A: 3 in Week 1, 3 in Week 2 with Jumps
            // Prioritize: 1, 3, 5 (Week 1) then 6, 8, 10 (Week 2)
            $this->preferredDays = [1, 3, 5, 6, 8, 10, 2, 4, 7, 9];
        } else {
            // Strategy B: More than 6 courses - Consecutive days
            // Prioritize: 1, 2, 3, 4, 5, 6, 7, 8, 9, 10
            $this->preferredDays = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        }
    }

    public function run()
    {
        set_time_limit(300);

        // Sort workload: Hardest to place first (those with most exclusion rules)
        usort($this->workload, function($a, $b) {
            $aCount = count($this->courseToGroupMap[$a['course_id']] ?? []);
            $bCount = count($this->courseToGroupMap[$b['course_id']] ?? []);
            return $bCount <=> $aCount;
        });

        $this->schedule = [];
        
        // Initialize roomUsageMap and occupiedSlotsCount with global occupancy from other sections
        $this->roomUsageMap = $this->globalRoomOccupancy;
        $this->occupiedSlotsCount = $this->globalExamsPerSlot;

        // Diagnostic: Check if we even have enough rooms registered
        if (count($this->settings['rooms']) == 0) {
            session(['exam_scheduler_failure_reason' => "No rooms are registered for your department."]);
            return false;
        }

        $result = $this->backtrack(0, 0);
        
        if (!$result) {
            $reason = "The engine could not find a valid schedule.";
            
            if (!empty($this->failureCounts)) {
                arsort($this->failureCounts);
                $topFailure = array_key_first($this->failureCounts);
                
                switch($topFailure) {
                    case 'rooms': $reason = "All registered rooms are booked in the available slots. Try registering more rooms."; break;
                    case 'instructor_busy': $reason = "Instructors are marked as 'Busy' in too many slots. Check instructor availability."; break;
                    case 'daily_overlap': $reason = "Too many exams for this section to fit into the 10-day window without overlaps."; break;
                    case 'gap_rule': $reason = "Conflict-free schedule found, but it violates the 1-day gap rule for overlapping student groups."; break;
                    case 'external_overlap': $reason = "Conflicts found with exams already scheduled in other departments/sections."; break;
                }
            }
            
            session(['exam_scheduler_failure_reason' => $reason]);
        }
        
        return $result ? $this->schedule : false;
    }

    private function backtrack($index, $startDayOffset)
    {
        if ($index >= count($this->workload)) {
            return true;
        }

        $currentTask = $this->workload[$index];
        $totalDays = (int)$this->settings['total_days'];
        $hasTwoInvigilators = !empty($currentTask['inv2_id']) && $currentTask['inv2_name'] !== 'MISSING';

        // Iterate through all 20 slots (10 days * 2 periods), starting from an offset to spread exams
        for ($d = 0; $d < $totalDays; $d++) {
            $day = (($startDayOffset + $d) % $totalDays) + 1;
            for ($period = 1; $period <= 2; $period++) {
                $periodName = $period == 1 ? 'morning' : 'afternoon';

                // A) If rooms are already locked for this branch, we MUST use them
                if ($this->lockedRooms !== null) {
                    $roomsNeeded = count($this->lockedRooms);
                    if ($this->isSafe($currentTask, $day, $periodName, $roomsNeeded)) {
                        $canUseLocked = true;
                        foreach ($this->lockedRooms as $lr) {
                            if (isset($this->roomUsageMap[$day][$periodName][$lr['name']])) {
                                $canUseLocked = false;
                                break;
                            }
                        }

                        if ($canUseLocked) {
                            $allocatedRooms = [];
                            if (count($this->lockedRooms) == 1) { // Hall or Single Normal
                                $allocatedRooms[] = [
                                    'name' => $this->lockedRooms[0]['name'],
                                    'type' => $this->lockedRooms[0]['type'],
                                    'inv1' => $currentTask['inv1_name'],
                                    'inv2' => $hasTwoInvigilators ? $currentTask['inv2_name'] : null
                                ];
                            } else { // Normal (Split)
                                $allocatedRooms[] = [
                                    'name' => $this->lockedRooms[0]['name'],
                                    'type' => $this->lockedRooms[0]['type'],
                                    'inv1' => $currentTask['inv1_name'],
                                    'inv2' => null
                                ];
                                $allocatedRooms[] = [
                                    'name' => $this->lockedRooms[1]['name'],
                                    'type' => $this->lockedRooms[1]['type'],
                                    'inv1' => null,
                                    'inv2' => $currentTask['inv2_name']
                                ];
                            }

                            if ($this->tryPlacement($index, $day, $periodName, $allocatedRooms, $startDayOffset)) {
                                return true;
                            }
                        }
                    }
                } 
                // B) Otherwise, we need to pick a room combination and lock it for this branch
                else {
                    $halls = array_filter($this->settings['rooms'], fn($r) => $r['type'] == 'hall');
                    $normals = array_filter($this->settings['rooms'], fn($r) => $r['type'] == 'normal');

                    // 1. Try Halls
                    if ($this->isSafe($currentTask, $day, $periodName, 1)) {
                        foreach ($halls as $hall) {
                            if (isset($this->roomUsageMap[$day][$periodName][$hall['name']])) continue;

                            $currentAllocated = [[
                                'name' => $hall['name'], 'type' => 'hall',
                                'inv1' => $currentTask['inv1_name'], 'inv2' => $hasTwoInvigilators ? $currentTask['inv2_name'] : null
                            ]];
                            $newLocked = [['name' => $hall['name'], 'type' => 'hall']];

                            $this->lockedRooms = $newLocked;
                            if ($this->tryPlacement($index, $day, $periodName, $currentAllocated, $startDayOffset)) {
                                return true;
                            }
                            $this->lockedRooms = null;
                        }
                    }

                    // 2. Try Normal Rooms
                    $normalRooms = array_values($normals);
                    $countNormals = count($normalRooms);

                    if ($hasTwoInvigilators) {
                        // Split Exam (2 rooms)
                        if ($this->isSafe($currentTask, $day, $periodName, 2)) {
                            for ($i_n = 0; $i_n < $countNormals; $i_n++) {
                                for ($j_n = $i_n + 1; $j_n < $countNormals; $j_n++) {
                                    $r1 = $normalRooms[$i_n];
                                    $r2 = $normalRooms[$j_n];

                                    if (isset($this->roomUsageMap[$day][$periodName][$r1['name']])) continue;
                                    if (isset($this->roomUsageMap[$day][$periodName][$r2['name']])) continue;

                                    $currentAllocated = [
                                        ['name' => $r1['name'], 'type' => 'normal', 'inv1' => $currentTask['inv1_name'], 'inv2' => null],
                                        ['name' => $r2['name'], 'type' => 'normal', 'inv1' => null, 'inv2' => $currentTask['inv2_name']]
                                    ];
                                    $newLocked = [
                                        ['name' => $r1['name'], 'type' => 'normal'],
                                        ['name' => $r2['name'], 'type' => 'normal']
                                    ];

                                    $this->lockedRooms = $newLocked;
                                    if ($this->tryPlacement($index, $day, $periodName, $currentAllocated, $startDayOffset)) {
                                        return true;
                                    }
                                    $this->lockedRooms = null;
                                }
                            }
                        }
                    } else {
                        // Single Normal Room
                        if ($this->isSafe($currentTask, $day, $periodName, 1)) {
                            foreach ($normalRooms as $r1) {
                                if (isset($this->roomUsageMap[$day][$periodName][$r1['name']])) continue;

                                $currentAllocated = [
                                    ['name' => $r1['name'], 'type' => 'normal', 'inv1' => $currentTask['inv1_name'], 'inv2' => null]
                                ];
                                $newLocked = [
                                    ['name' => $r1['name'], 'type' => 'normal']
                                ];

                                $this->lockedRooms = $newLocked;
                                if ($this->tryPlacement($index, $day, $periodName, $currentAllocated, $startDayOffset)) {
                                    return true;
                                }
                                $this->lockedRooms = null;
                            }
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Helper to handle the actual placement, recursion, and backtracking of a task
     */
    private function tryPlacement($index, $day, $period, $allocatedRooms, $startDayOffset)
    {
        $currentTask = $this->workload[$index];
        $combinedRoomNames = collect($allocatedRooms)->pluck('name')->implode(', ');

        $this->schedule[] = [
            'course_id' => $currentTask['course_id'],
            'course_code' => $currentTask['course_code'],
            'course_name' => $currentTask['course_name'],
            'inv1_id' => $currentTask['inv1_id'],
            'inv1_name' => $currentTask['inv1_name'] ?? 'Inv1',
            'inv2_id' => $currentTask['inv2_id'],
            'inv2_name' => $currentTask['inv2_name'] ?? 'Inv2',
            'day_number' => $day,
            'period' => $period,
            'room_name' => $combinedRoomNames,
            'is_split' => count($allocatedRooms) > 1,
            'rooms_detail' => $allocatedRooms,
            'duration' => ($currentTask['hours'] ?? 2) . 'h ' . ($currentTask['mins'] ?? '00') . 'm'
        ];

        // Mark resources as busy
        foreach ($allocatedRooms as $ar) {
            $this->roomUsageMap[$day][$period][$ar['name']] = true;
        }
        $this->occupiedSlotsCount[$day][$period] = ($this->occupiedSlotsCount[$day][$period] ?? 0) + count($allocatedRooms);

        // Recurse (Increment day offset to spread exams)
        if ($this->backtrack($index + 1, ($startDayOffset + 2) % (int)$this->settings['total_days'])) {
            return true;
        }

        // Backtrack resources
        array_pop($this->schedule);
        foreach ($allocatedRooms as $ar) {
            unset($this->roomUsageMap[$day][$period][$ar['name']]);
        }
        $this->occupiedSlotsCount[$day][$period] -= count($allocatedRooms);

        return false;
    }

    private function isSafe($task, $day, $period, $roomsNeeded = 1)
    {
        $courseId = (int)$task['course_id'];
        $inv1Id = (int)($task['inv1_id'] ?? 0);
        $inv2Id = (int)($task['inv2_id'] ?? 0);
        $workloadCount = count($this->workload);

        // 1. Check Room Capacity
        $currentCount = $this->occupiedSlotsCount[$day][$period] ?? 0;
        if (($currentCount + $roomsNeeded) > $this->settings['total_sections']) {
            $this->failureCounts['rooms'] = ($this->failureCounts['rooms'] ?? 0) + 1;
            return false;
        }

        // 2. Check Instructor Availability (Global Locks from DB)
        if ($inv1Id > 0 && isset($this->instructorBusyMap[$inv1Id][$day][$period])) {
            $this->failureCounts['instructor_busy'] = ($this->failureCounts['instructor_busy'] ?? 0) + 1;
            return false;
        }
        if ($inv2Id > 0 && isset($this->instructorBusyMap[$inv2Id][$day][$period])) {
            $this->failureCounts['instructor_busy'] = ($this->failureCounts['instructor_busy'] ?? 0) + 1;
            return false;
        }

        // 3. Check Instructor Conflict (Internal - already teaching this period)
        foreach ($this->schedule as $scheduled) {
            if ($scheduled['day_number'] == $day && $scheduled['period'] == $period) {
                if ($inv1Id > 0 && ($scheduled['inv1_id'] == $inv1Id || $scheduled['inv2_id'] == $inv1Id)) return false;
                if ($inv2Id > 0 && ($scheduled['inv1_id'] == $inv2Id || $scheduled['inv2_id'] == $inv2Id)) return false;
            }
        }

        // 4. Check Exclusion Groups (Overlap Prevention)
        $myGroups = $this->courseToGroupMap[$courseId] ?? [];

        foreach ($this->schedule as $scheduled) {
            $otherGroups = $this->courseToGroupMap[(int)$scheduled['course_id']] ?? [];
            
            // Same section = No two exams on the same day
            if ($scheduled['day_number'] == $day) {
                $this->failureCounts['daily_overlap'] = ($this->failureCounts['daily_overlap'] ?? 0) + 1;
                return false;
            }
            
            // Gap Rule: Only force a gap if they share a specific exclusion group
            $sharesExclusion = !empty(array_intersect($myGroups, $otherGroups));
            if ($sharesExclusion) {
                $isWeekTransition = ($day == 6 && $scheduled['day_number'] == 5) || ($day == 5 && $scheduled['day_number'] == 6);
                if (!$isWeekTransition && abs($scheduled['day_number'] - $day) < 2) {
                    $this->failureCounts['gap_rule'] = ($this->failureCounts['gap_rule'] ?? 0) + 1;
                    return false;
                }
            }
        }

        // 5. Check External Constraints (Exams in other sections/departments)
        foreach ($this->externalConstraints as $ext) {
            $extGroups = $this->externalGroupMap[(int)$ext['course_id']] ?? [];
            $hasSharedStudents = !empty(array_intersect($myGroups, $extGroups));

            if ($hasSharedStudents) {
                if ($ext['day_number'] == $day) {
                    $this->failureCounts['external_overlap'] = ($this->failureCounts['external_overlap'] ?? 0) + 1;
                    return false;
                }
                
                $isWeekTransition = ($day == 6 && $ext['day_number'] == 5) || ($day == 5 && $ext['day_number'] == 6);
                if (!$isWeekTransition && abs($ext['day_number'] - $day) < 2) {
                    $this->failureCounts['gap_rule'] = ($this->failureCounts['gap_rule'] ?? 0) + 1;
                    return false;
                }
            }
        }

        return true;
    }
}
