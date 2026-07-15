<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\User;
use App\Models\Room;
use App\Models\ExamSchedule;
use App\Models\ExamInstructorAvailability;
use App\Services\ExamSchedulingEngine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ExamSchedulerController extends Controller
{
    /**
     * BRIDGE: Fixed to point to your existing tabbed portal.
     * This ensures the "Exam Scheduler" tab is active on arrival.
     */
    public function index()
    {
        return view('department.scheduler.index', ['activeTab' => 'exam']); 
    }

    /**
     * Alias for index to match route naming if needed.
     */
    public function examInput()
    {
        return $this->index();
    }

    /**
     * Processes the uploaded CSV and prepares the validation UI
     * Existing functionality preserved.
     */
    public function processCsv(Request $request)
    {
        // 0. Handle Refresh (GET Request)
        if ($request->isMethod('get')) {
            return redirect()->route('department.exam-scheduler.input')
                ->with('error', 'Session expired or invalid request. Please upload your CSV again.');
        }

        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
            'year' => 'required',
            'semester' => 'required',
            'section' => 'required|string'
        ]);

        $fileData = array_map('str_getcsv', file($request->file('csv_file')->getRealPath()));
        array_shift($fileData); 

        $processedData = [];
        $warnings = [];

        foreach ($fileData as $row) {
            if (empty($row[0])) continue;
            $courseCode = strtoupper(trim($row[0]));
            
            $course = Course::where('course_code', $courseCode)
                ->where('year', $request->year)
                ->where('semester', $request->semester)
                ->first();

            if ($course) {
                $hasExclusion = DB::table('exam_exclusion_group_courses')
                    ->where('course_id', $course->id)
                    ->exists();
                if (!$hasExclusion) {
                    $warnings[] = "Missing Constraint: No Overlap Exclusion rules found for [{$courseCode}].";
                }
            } else {
                $warnings[] = "Critical: Course code [{$courseCode}] not found in database for the selected period.";
            }

            $inst1 = User::where('username', trim($row[1] ?? ''))->first();
            $inst2 = User::where('username', trim($row[2] ?? ''))->first();

            $processedData[] = [
                'course_id' => $course->id ?? null,
                'course_code' => $courseCode,
                'course_name' => $course->course_name ?? 'UNKNOWN',
                'inv1_id' => $inst1->id ?? null,
                'inv1_name' => $inst1 ? ($inst1->first_name . ' ' . $inst1->last_name) : 'MISSING',
                'inv2_id' => $inst2->id ?? null,
                'inv2_name' => $inst2 ? ($inst2->first_name . ' ' . $inst2->last_name) : 'MISSING',
                'ready' => ($course && $inst1 && $inst2)
            ];
        }

        return view('department.exams.scheduler_input', [
            'processedData' => $processedData,
            'selection' => $request->only('year', 'semester', 'section'),
            'warnings' => $warnings
        ]);
    }

    /**
     * Initiates the Greedy + Backtracking Engine
     * Optimized to handle exam-specific constraints.
     */
    public function initiateEngine(Request $request)
    {
        // 0. Handle Refresh (GET Request)
        if ($request->isMethod('get')) {
            return redirect()->route('department.exam-scheduler.input')
                ->with('error', 'Session expired or invalid request. Please upload your CSV again.');
        }

        // Validate that we have all required fields for the engine
        $request->validate([
            'year' => 'required',
            'semester' => 'required',
            'section' => 'required',
            'start_date' => 'required|date',
            'courses' => 'required|array'
        ], [
            'start_date.required' => 'Please select a start date for the exams.',
            'courses.required' => 'No courses found to schedule. Please check your CSV.'
        ]);

        // Update session with latest parameters so they persist on refresh/error
        session([
            'last_exam_params' => $request->only('start_date'),
            'last_exam_selection' => $request->only('year', 'semester', 'section')
        ]);

        $courseIds = collect($request->courses)->pluck('course_id')->filter()->toArray();
        $externalConstraints = [];

        // 1. Fetch Cross-Departmental Constraints
        if (Schema::hasTable('exam_schedules')) {
            $groupIds = DB::table('exam_exclusion_group_courses')
                ->whereIn('course_id', $courseIds)
                ->pluck('group_id');

            $partnerCourseIds = DB::table('exam_exclusion_group_courses')
                ->whereIn('group_id', $groupIds)
                ->whereNotIn('course_id', $courseIds)
                ->pluck('course_id');

            $externalConstraints = ExamSchedule::whereIn('course_id', $partnerCourseIds)
                ->where('year', $request->year)
                ->where('semester', $request->semester)
                ->get(['course_id', 'day_number', 'period'])
                ->toArray();
        }

        // 2. Fetch Global Instructor Availability
        $instructorIds = collect($request->courses)
            ->flatMap(fn($c) => [$c['inv1_id'] ?? null, $c['inv2_id'] ?? null])
            ->filter()
            ->unique()
            ->toArray();

        $globalLocks = ExamInstructorAvailability::whereIn('instructor_id', $instructorIds)
            ->where('is_available', 0)
            ->get(['instructor_id', 'day_number', 'period'])
            ->toArray();

        // 5. Generate exam dates first
        $examDates = [];
        $dateToDayMap = []; // Map calendar date string to engine day number
        $currentDate = Carbon::parse($request->start_date);
        $dayCount = 0;
        while ($dayCount < 10) {
            if (!$currentDate->isWeekend()) {
                $dayCount++;
                $dateString = $currentDate->toDateString();
                $examDates[$dayCount] = $dateString;
                $dateToDayMap[$dateString] = $dayCount;
            }
            $currentDate->addDay();
        }

        // 2.5 Fetch Global Room Occupancy
        // We need to know which rooms are already taken by OTHER sections in each slot
        $globalRoomOccupancy = [];
        $examsPerSlot = [];
        if (Schema::hasTable('exam_schedules')) {
            $existingSchedules = ExamSchedule::where('year', $request->year)
                ->where('semester', $request->semester)
                ->get(['exam_date', 'period', 'room_name']);
            
            foreach ($existingSchedules as $es) {
                $dayNum = $dateToDayMap[$es->exam_date] ?? null;
                if (!$dayNum) continue; // Not in our current 10-day window

                // room_name might be a comma-separated string if split (e.g., "Room 101, Room 102")
                $roomsTaken = explode(', ', $es->room_name);
                foreach ($roomsTaken as $rName) {
                    $globalRoomOccupancy[$dayNum][$es->period][$rName] = true;
                }
                $examsPerSlot[$dayNum][$es->period] = ($examsPerSlot[$dayNum][$es->period] ?? 0) + 1;
            }
        }

        // 3. Fetch Local Exclusion Groups for the engine
        $exclusionGroups = [];
        $groupIds = DB::table('exam_exclusion_group_courses')
            ->whereIn('course_id', $courseIds)
            ->pluck('group_id')
            ->unique();
        
        foreach ($groupIds as $gid) {
            $exclusionGroups[] = [
                'id' => $gid,
                'course_ids' => DB::table('exam_exclusion_group_courses')
                    ->where('group_id', $gid)
                    ->pluck('course_id')
                    ->toArray()
            ];
        }

        // 4. Fetch Registered Rooms from Database
        $rooms = Room::where('department_id', auth()->user()->department_id)
            ->orderBy('order_weight')
            ->get(['name', 'type'])
            ->toArray();

        if (empty($rooms)) {
            return redirect()->route('department.exam-scheduler.input')
                ->with('error', 'No rooms registered for your department. Please register rooms first.');
        }

        // 6. Initialize Engine
        $engine = new ExamSchedulingEngine(
            (array)$request->courses, // workload
            [
                'external' => (array)$externalConstraints,
                'instructor_locks' => (array)$globalLocks,
                'exclusion_groups' => (array)$exclusionGroups,
                'exam_dates' => (array)$examDates, // Pass dates to engine
                'global_room_occupancy' => (array)$globalRoomOccupancy,
                'exams_per_slot' => (array)$examsPerSlot
            ],
            [
                'total_sections' => count($rooms), // Auto-cap based on physical rooms
                'rooms' => (array)$rooms,
                'total_days' => 10 // Standard for exams
            ]
        );

        $finalSchedule = $engine->run();

        if ($finalSchedule) {
            session()->forget('exam_scheduler_failure_reason');
            
            return view('department.exams.results', [
                'schedule' => $finalSchedule,
                'total_days' => 10,
                'examDates' => $examDates,
                'meta' => [
                    'year' => $request->year,
                    'semester' => $request->semester,
                    'section' => $request->section,
                    'start_date' => $request->start_date
                ]
            ]);
        } else {
            $reason = session('exam_scheduler_failure_reason', 'The Exam Scheduling Engine could not find a solution. Please check for conflicting constraints (e.g., too many exams for the available rooms or instructor conflicts).');
            return redirect()->route('department.exam-scheduler.input')
                ->with('error', $reason);
        }
    }

    /**
     * Saves the generated schedule to the database.
     */
    public function saveSchedule(Request $request)
    {
        $scheduleData = json_decode($request->schedule_data, true);
        $examDates = json_decode($request->exam_dates, true);

        if (!$scheduleData || !$examDates) {
            return redirect()->route('department.exam-scheduler.input')
                ->with('error', 'Invalid schedule data received. Please regenerate.');
        }

        $deptId = auth()->user()->department_id;

        DB::beginTransaction();
        try {
            // Remove existing schedule for this year/semester/section/department
            ExamSchedule::where('year', $request->year)
                ->where('semester', $request->semester)
                ->where('section', $request->section)
                ->where('department_id', $deptId)
                ->delete();

            foreach ($scheduleData as $row) {
                ExamSchedule::create([
                    'course_id' => $row['course_id'],
                    'day_number' => $row['day_number'],
                    'period' => $row['period'],
                    'exam_date' => $examDates[$row['day_number']] ?? null,
                    'room_name' => $row['room_name'],
                    'year' => $request->year,
                    'semester' => $request->semester,
                    'section' => $request->section,
                    'department_id' => $deptId,
                    'inv1_id' => $row['inv1_id'],
                    'inv2_id' => $row['inv2_id'],
                    'inv1_name' => $row['inv1_name'],
                    'inv2_name' => $row['inv2_name']
                ]);

                // Update instructor availability for both invigilators
                foreach ([$row['inv1_id'], $row['inv2_id']] as $invId) {
                    if ($invId) {
                        ExamInstructorAvailability::updateOrCreate(
                            [
                                'instructor_id' => $invId,
                                'day_number' => $row['day_number'],
                                'period' => $row['period'],
                                'department_id' => $deptId
                            ],
                            ['is_available' => 0]
                        );
                    }
                }
            }

            DB::commit();
            return redirect()->route('department.exam-scheduler.locked')
                ->with('success', 'Exam Schedule has been successfully saved and published.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('department.exam-scheduler.input')
                ->with('error', 'Failed to save schedule: ' . $e->getMessage());
        }
    }

    /**
     * Lists all locked exam schedules.
     */
    public function lockedIndex()
    {
        $deptId = auth()->user()->department_id;
        
        $groups = ExamSchedule::where('department_id', $deptId)
            ->select('year', 'semester', 'section', DB::raw('MAX(updated_at) as latest_update'), DB::raw('count(*) as total_exams'))
            ->groupBy('year', 'semester', 'section')
            ->get();

        return view('department.exams.locked', compact('groups'));
    }

    /**
     * Shows a specific locked exam schedule.
     */
    public function show($year, $semester, $section)
    {
        $deptId = auth()->id() ? auth()->user()->department_id : null;

        $savedExams = ExamSchedule::with('course')
            ->where([
                'department_id' => $deptId,
                'year' => $year,
                'semester' => $semester,
                'section' => $section
            ])
            ->get();

        if ($savedExams->isEmpty()) {
            return redirect()->route('department.exam-scheduler.locked')->with('error', 'Schedule not found.');
        }

        $schedule = [];
        foreach ($savedExams as $exam) {
            $schedule[] = [
                'course_id' => $exam->course_id,
                'course_code' => $exam->course->course_code ?? 'N/A',
                'course_name' => $exam->course->course_name ?? 'DELETED COURSE',
                'day_number' => $exam->day_number,
                'period' => $exam->period,
                'exam_date' => $exam->exam_date,
                'room_name' => $exam->room_name,
                'inv1_id' => $exam->inv1_id,
                'inv2_id' => $exam->inv2_id,
                'inv1_name' => $exam->inv1_name,
                'inv2_name' => $exam->inv2_name,
                'duration' => '2h 00m' // Default duration or retrieve from somewhere if stored
            ];
        }

        // Get exam dates mapping
        $examDates = $savedExams->pluck('exam_date', 'day_number')->unique()->toArray();

        return view('department.exams.results', [
            'schedule' => $schedule,
            'total_days' => 10,
            'examDates' => $examDates,
            'meta' => [
                'year' => $year,
                'semester' => $semester,
                'section' => $section,
                'is_locked' => true
            ]
        ]);
    }

    /**
     * Deletes the exam schedule group and unlocks the instructors.
     */
    public function destroyGroup(Request $request)
    {
        $request->validate([
            'year' => 'required',
            'semester' => 'required',
            'section' => 'required'
        ]);

        $deptId = auth()->user()->department_id;

        DB::beginTransaction();
        try {
            // 1. Identify and unlock invigilators
            $schedules = ExamSchedule::where([
                'department_id' => $deptId,
                'year' => $request->year,
                'semester' => $request->semester,
                'section' => $request->section
            ])->get();

            foreach ($schedules as $s) {
                // Update to "Free" (1) for both invigilators
                foreach ([$s->inv1_id, $s->inv2_id] as $invId) {
                    if ($invId) {
                        ExamInstructorAvailability::updateOrCreate(
                            [
                                'instructor_id' => $invId,
                                'day_number' => $s->day_number,
                                'period' => $s->period,
                                'department_id' => $deptId
                            ],
                            ['is_available' => 1] // Set to FREE
                        );
                    }
                }
            }

            // 2. Delete the schedules
            ExamSchedule::where([
                'department_id' => $deptId,
                'year' => $request->year,
                'semester' => $request->semester,
                'section' => $request->section
            ])->delete();

            DB::commit();
            return back()->with('success', "Exam schedule for Year {$request->year} Sem {$request->semester} has been deleted and invigilators unlocked.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('department.exam-scheduler.locked')
                ->with('error', 'Failed to delete schedule: ' . $e->getMessage());
        }
    }
}