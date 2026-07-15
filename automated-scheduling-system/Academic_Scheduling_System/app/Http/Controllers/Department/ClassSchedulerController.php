<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\User;
use App\Models\InstructorProfile;
use App\Models\InstructorAvailability;
use Illuminate\Support\Facades\Auth;
use App\Services\SchedulingEngine;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClassSchedulerController extends Controller
{
    public function index()
    {
        return view('department.scheduler.index');
    }

   public function preview(Request $request)
{
    // 1. Initial Upload (POST)
    if ($request->isMethod('post') && $request->hasFile('csv_file')) {
        $request->validate([
            'year' => 'required|integer|between:1,5',
            'semester' => 'required|integer|between:1,2',
            'section' => 'required|string|max:10',
            'csv_file' => 'required|mimes:csv,txt|max:2048'
        ]);

        $department = Auth::user()->department;
        $errors = [];

        if (!$department || $department->total_periods <= 0) {
            $errors[] = "Missing Department Settings: Please set 'Total Periods' before generating.";
        }

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        fgetcsv($handle); // Skip header

        $previewData = [];

        while (($row = fgetcsv($handle)) !== FALSE) {
            if (empty($row) || !isset($row[1])) continue;

            $courseCode = trim($row[0]);
            $lecInstId = trim($row[1]); 
            $labInstId = isset($row[2]) ? trim($row[2]) : ''; 

            if (empty($courseCode)) continue;

            $course = Course::where('course_code', $courseCode)->first();
            $lecUser = User::where('username', $lecInstId)->first();
            
            $status = 'Ready';
            $parsedHours = ['lec' => 0, 'tut' => 0, 'lab' => 0];

            if (!$course) {
                $status = 'Error';
                $errors[] = "Course Code '$courseCode' not found.";
            } else {
                $hoursParts = explode('/', $course->hours);
                $parsedHours = [
                    'lec' => (int)($hoursParts[0] ?? 0),
                    'tut' => (int)($hoursParts[1] ?? 0),
                    'lab' => (int)($hoursParts[2] ?? 0)
                ];

                if ($course->year != $request->year || $course->semester != $request->semester) {
                    $status = 'Mismatch';
                    $errors[] = "Course $courseCode mismatch: Target Year $request->year vs Database Year $course->year.";
                }
            }

            $lecName = null; $lecUserId = null;
            if ($lecUser) {
                $lecUserId = $lecUser->id;
                $profile = InstructorProfile::where('user_id', $lecUser->id)->first();
                $lecName = $profile ? $profile->first_name . ' ' . $profile->last_name : $lecUser->name;
            } else {
                $status = 'Error';
                $errors[] = "Lecture Instructor '$lecInstId' not found for course $courseCode.";
            }

            $labName = null; $labUserId = null;
            if ($parsedHours['lab'] > 0) {
                if (empty($labInstId)) {
                    $status = 'Error';
                    $errors[] = "Course $courseCode requires a Lab Instructor.";
                } else {
                    $labUser = User::where('username', $labInstId)->first();
                    if ($labUser) {
                        $labUserId = $labUser->id;
                        $profile = InstructorProfile::where('user_id', $labUser->id)->first();
                        $labName = $profile ? $profile->first_name . ' ' . $profile->last_name : $labUser->name;
                    } else {
                        $status = 'Error';
                        $errors[] = "Lab Instructor '$labInstId' not found for course $courseCode.";
                    }
                }
            } else {
                $labName = !empty($labInstId) ? "No Lab Required (ID Ignored)" : null;
            }

            $previewData[] = [
                'course_id'     => $course->id ?? null,
                'csv_course_id' => $courseCode,
                'course_name'   => $course->course_name ?? 'Unknown Course',
                'lec_user_id'   => $lecUserId,
                'lab_user_id'   => $labUserId,
                'lec_name'      => $lecName,
                'lab_name'      => $labName,
                'csv_lec_inst'  => $lecInstId,
                'csv_lab_inst'  => $labInstId,
                'hours'         => $parsedHours,
                'status'        => $status
            ];
        }
        fclose($handle);

        // Store result in session so it survives a refresh
        session([
            'last_preview_data' => $previewData,
            'last_preview_errors' => $errors,
            'last_preview_params' => $request->only(['year', 'semester', 'section'])
        ]);

    } else {
        // 2. Handle Refresh (GET)
        if (!session()->has('last_preview_data')) {
            return redirect()->route('department.scheduler.index')
                ->with('error', 'Session expired or invalid request. Please upload CSV again.');
        }

        $previewData = session('last_preview_data');
        $errors = session('last_preview_errors');
        
        // Re-inject the year/semester into the request so the view doesn't break
        $request->merge(session('last_preview_params'));
    }

    return view('department.scheduler.preview', [
        'previewData' => $previewData,
        'previewErrors' => $errors,
        'request' => $request
    ]);
}




public function generate(Request $request)
{
    // 1. Decode the background payload sent from the preview page
    $payload = json_decode($request->input('schedule_payload'), true);
    $dept = Auth::user()->department;

    if (!$payload) {
        return redirect()->route('department.scheduler.preview')
                         ->with('error', 'No schedule data found to process. Please try uploading the CSV again.');
    }

    if (!$dept || $dept->total_periods <= 0) {
        return redirect()->route('department.scheduler.index')
                         ->with('error', 'Department settings are incomplete. Please set Total Periods in Department Settings.');
    }

    // 2. Map Day Names to Database Integers (based on your screenshot showing 3 and 5)
    $dayMap = [
        'Monday'    => 1,
        'Tuesday'   => 2,
        'Wednesday' => 3,
        'Thursday'  => 4,
        'Friday'    => 5
    ];

    // 3. Extract all instructor User IDs from the payload
    $userIds = collect($payload)
        ->pluck('lec_user_id')
        ->merge(collect($payload)->pluck('lab_user_id'))
        ->filter()
        ->unique()
        ->toArray();

    // 4. Fetch Profile IDs
    $profileIds = InstructorProfile::whereIn('user_id', $userIds)
        ->pluck('user_id') 
        ->toArray();

    // 5. Fetch and Transform Availabilities
    $reverseDayMap = array_flip($dayMap);

    $constraints = InstructorAvailability::whereIn('instructor_profile_id', $profileIds)
        ->get()
        ->map(function(InstructorAvailability $c) use ($reverseDayMap) {
            return (object)[
                'instructor_profile_id' => $c->instructor_profile_id,
                'day'    => $reverseDayMap[$c->day_of_week] ?? 'Monday',
                'period' => $c->time_slot_id
            ];
        })->toArray(); 

    // 5.5 Fetch Global Occupancy (Already scheduled groups for the same time)
    $globalOccupancy = Schedule::where('year', $request->year)
        ->where('semester', $request->semester)
        ->where('section', '!=', $request->section) 
        ->get();
    
    foreach ($globalOccupancy as $go) {
        /** @var Schedule $go */
        $constraints[] = (object)[
            'instructor_profile_id' => $go->instructor_id,
            'day' => $go->day,
            'period' => $go->period
        ];
    }

    // 6. Initialize the Optimized Engine with transformed constraints
    $engine = new SchedulingEngine($payload, $constraints, [
        'total_periods'  => $dept->total_periods,
        'class_duration' => $dept->class_duration,
        'lab_duration'   => $dept->lab_duration,
    ]);

    // 7. Run the algorithm
    $finalSchedule = $engine->run();

    // 8. Handle the Engine Result
    if ($finalSchedule) {
        session()->forget('scheduler_failure_reason');
        return view('department.scheduler.results', [
            'schedule'      => $finalSchedule,
            'days'          => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            'total_periods' => $dept->total_periods,
            'meta'          => [
                'year'      => $request->input('year'),
                'semester'  => $request->input('semester'),
                'section'   => $request->input('section'),
                'dept_name' => $dept->name
            ]
        ]);
    } else {
        $reason = session('scheduler_failure_reason', 'The Scheduling Engine could not find a solution. Tip: Check if instructors have too many "Busy" constraints in the database.');
        return redirect()->route('department.scheduler.preview')
            ->with('error', $reason);
    }
}





public function store(Request $request)
{
    // 1. Basic Validation
    $request->validate([
        'schedule_data' => 'required',
        'year'          => 'required|integer',
        'semester'      => 'required|integer',
        'section'       => 'required|string',
    ]);

    $data = json_decode($request->input('schedule_data'), true);
    $deptId = Auth::user()->department_id;
    $dayMap = ['Monday' => 1, 'Tuesday' => 2, 'Wednesday' => 3, 'Thursday' => 4, 'Friday' => 5];

    if (!$data || !is_array($data)) {
        return redirect()->route('department.scheduler.preview')
                         ->with('error', 'Invalid schedule data. Please try regenerating.');
    }

    DB::beginTransaction();

    try {
        // 2. Identify and Clear existing "System Held" availabilities for this group first
        $oldSchedules = Schedule::where('department_id', $deptId)
                                ->where('year', $request->year)
                                ->where('semester', $request->semester)
                                ->where('section', $request->section)
                                ->get();

        foreach ($oldSchedules as $old) {
            $oldDayInt = $dayMap[$old->day] ?? 1;
            InstructorAvailability::where('instructor_profile_id', $old->instructor_id)
                ->where('day_of_week', $oldDayInt)
                ->where('time_slot_id', $old->period)
                ->where('type', 'system_held')
                ->delete();
        }

        // 3. Clear existing schedule records
        Schedule::where('department_id', $deptId)
                ->where('year', $request->year)
                ->where('semester', $request->semester)
                ->where('section', $request->section)
                ->delete();

        // 4. Iterate and Save New Schedule + Lock Availabilities
        foreach ($data as $slotKey => $item) {
            $parts = explode('_', $slotKey); // "Monday_1"
            if (count($parts) < 2) continue;

            $dayName = $parts[0];
            $dayInt = $dayMap[$dayName] ?? 1;
            $period = (int)$parts[1];
            $instructorId = $item['user_id'] ?? null;

            // Create Schedule Record
            Schedule::create([
                'department_id' => $deptId,
                'course_id'     => $item['course_id'] ?? null,
                'course_code'   => $item['course_code'],
                'instructor_id' => $instructorId,
                'type'          => $item['type'],
                'day'           => $dayName,
                'period'        => $period,
                'year'          => $request->year,
                'semester'      => $request->semester,
                'section'       => $request->section,
                'status'        => 'published',
            ]);

            // Create "System Held" Availability entry
            if ($instructorId) {
                InstructorAvailability::create([
                    'instructor_profile_id' => $instructorId,
                    'day_of_week'           => $dayInt,
                    'time_slot_id'          => $period,
                    'type'                  => 'system_held',
                    'department_id'         => $deptId
                ]);
            }
        }

        DB::commit();
        // Redirecting to the locked page as planned
        return redirect()->route('department.scheduler.locked')
                         ->with('success', "Schedule for Year {$request->year} published and instructors locked.");

    } catch (\Exception $e) {
        DB::rollback();
        \Log::error("Scheduler Store Error: " . $e->getMessage());
        return redirect()->route('department.scheduler.preview')
                         ->with('error', 'Failed to save the schedule: ' . $e->getMessage());
    }
}




// NEW: The page that lists all finalized/locked groups
 public function lockedIndex()
{
    $deptId = Auth::user()->department_id;
    
    // FETCH THE DATA
    $groups = Schedule::where('department_id', $deptId)
        ->select('year', 'semester', 'section', DB::raw('MAX(updated_at) as latest_update'), DB::raw('count(*) as total_classes'))
        ->groupBy('year', 'semester', 'section')
        ->get();

    // PASS IT AS 'groups'
    return view('department.scheduler.locked', compact('groups'));
}


    // NEW: Method to show the specific grid when "View Grid" is clicked
public function show($year, $semester, $section)
{
    $departmentId = Auth::user()->department_id;

    // Fetch schedules with instructor names joined
    $savedSchedules = DB::table('schedules')
        ->join('instructor_profiles', 'schedules.instructor_id', '=', 'instructor_profiles.user_id')
        ->join('courses', 'schedules.course_code', '=', 'courses.course_code')
        ->where([
            'schedules.department_id' => $departmentId,
            'schedules.year' => $year,
            'schedules.semester' => $semester,
            'schedules.section' => $section
        ])
        ->select(
            'schedules.*', 
            'courses.course_name', 
            'instructor_profiles.first_name', 
            'instructor_profiles.last_name'
        )
        ->get();

    $schedule = [];
    foreach ($savedSchedules as $s) {
        $key = "{$s->day}_{$s->period}";
        $schedule[$key] = [
            'course_code' => $s->course_code,
            'course_name' => $s->course_name,
            'type'        => $s->type,
            'instructor'  => $s->first_name . ' ' . $s->last_name, // Combined Name
        ];
    }

    $meta = ['year' => $year, 'semester' => $semester, 'section' => $section];
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
    $total_periods = Auth::user()->department->total_periods ?? 8;

    return view('department.scheduler.locked', compact('schedule', 'meta', 'days', 'total_periods'));
}




public function destroyGroup(Request $request)
{
    $deptId = Auth::user()->department_id;
    $year = $request->year;
    $semester = $request->semester;
    $section = $request->section;

    DB::beginTransaction();
    try {
        // 1. Find all schedules in this group to identify which instructors to release
        $schedules = Schedule::where([
            'department_id' => $deptId,
            'year' => $year,
            'semester' => $semester,
            'section' => $section
        ])->get();

        $dayMap = ['Monday' => 1, 'Tuesday' => 2, 'Wednesday' => 3, 'Thursday' => 4, 'Friday' => 5];

        // 2. Release instructors from Availability table
        foreach ($schedules as $item) {
            $dayInt = $dayMap[$item->day] ?? 1;
            
            // Only delete the specific 'system_held' slot created by this schedule
            InstructorAvailability::where('instructor_profile_id', $item->instructor_id)
                ->where('day_of_week', $dayInt)
                ->where('time_slot_id', $item->period)
                ->where('type', 'system_held')
                ->delete();
        }

        // 3. Delete the actual schedule records
        Schedule::where([
            'department_id' => $deptId,
            'year' => $year,
            'semester' => $semester,
            'section' => $section
        ])->delete();

        DB::commit();
        return back()->with('success', "Schedule for Year $year, Sem $semester, Section $section deleted and instructors released.");
    } catch (\Exception $e) {
        DB::rollback();
        \Log::error("Schedule Release Error: " . $e->getMessage());
        return back()->with('error', 'Error releasing schedule: ' . $e->getMessage());
    }
}






}