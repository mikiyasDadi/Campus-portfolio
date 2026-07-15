<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\ExamSchedule;
use App\Models\Schedule;
use App\Models\ScheduleComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FacultyHeadController extends Controller
{
    /**
     * Faculty Head Dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $facultyId = $user->faculty_id;

        // If faculty_id is not set, try to find a faculty this user might be associated with
        if (!$facultyId) {
            // Check if they are a head of any department, and get faculty from there
            $dept = Department::where('user_id', $user->id)->first();
            $facultyId = $dept->faculty_id ?? null;
        }

        $departments = $facultyId ? Department::where('faculty_id', $facultyId)->get() : collect();

        return view('faculty.dashboard', compact('departments', 'user'));
    }

    /**
     * View all published class schedules for a department
     */
    public function classSchedules($departmentId)
    {
        $department = Department::findOrFail($departmentId);
        
        $groups = Schedule::where('department_id', $departmentId)
            ->where('status', 'published')
            ->select('year', 'semester', 'section', DB::raw('MAX(updated_at) as latest_update'), DB::raw('count(*) as total_classes'))
            ->groupBy('year', 'semester', 'section')
            ->get();

        return view('faculty.class_schedules', compact('department', 'groups'));
    }

    /**
     * View all published exam schedules for a department
     */
    public function examSchedules($departmentId)
    {
        $department = Department::findOrFail($departmentId);

        $groups = ExamSchedule::where('department_id', $departmentId)
            ->select('year', 'semester', 'section', DB::raw('MAX(updated_at) as latest_update'), DB::raw('count(*) as total_exams'))
            ->groupBy('year', 'semester', 'section')
            ->get();

        return view('faculty.exam_schedules', compact('department', 'groups'));
    }

    /**
     * Show specific class schedule
     */
    public function showClassSchedule($departmentId, $year, $semester, $section)
    {
        $department = Department::findOrFail($departmentId);
        
        $savedSchedules = Schedule::where([
            'department_id' => $departmentId,
            'year' => $year,
            'semester' => $semester,
            'section' => $section,
            'status' => 'published'
        ])->get();

        if ($savedSchedules->isEmpty()) {
            return back()->with('error', 'Schedule not found.');
        }

        $schedule = [];
        foreach ($savedSchedules as $item) {
            $slotKey = "{$item->day}_{$item->period}";
            $schedule[$slotKey] = [
                'course_id'   => $item->course_id,
                'course_code' => $item->course_code,
                'course_name' => $item->course->course_name ?? 'N/A',
                'type'        => $item->type,
                'instructor'  => $item->instructor->full_name ?? 'N/A',
                'user_id'     => $item->instructor_id
            ];
        }

        return view('department.scheduler.results', [
            'schedule'      => $schedule,
            'days'          => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            'total_periods' => $department->total_periods,
            'meta'          => [
                'year'      => $year,
                'semester'  => $semester,
                'section'   => $section,
                'dept_name' => $department->name,
                'is_locked' => true,
                'is_faculty_view' => true,
                'department_id' => $departmentId,
                'schedule_type' => 'class'
            ]
        ]);
    }

    /**
     * Show specific exam schedule
     */
    public function showExamSchedule($departmentId, $year, $semester, $section)
    {
        $department = Department::findOrFail($departmentId);

        $savedExams = ExamSchedule::with('course')
            ->where([
                'department_id' => $departmentId,
                'year' => $year,
                'semester' => $semester,
                'section' => $section
            ])
            ->get();

        if ($savedExams->isEmpty()) {
            return back()->with('error', 'Schedule not found.');
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
                'duration' => '2h 00m'
            ];
        }

        $examDates = $savedExams->pluck('exam_date', 'day_number')->unique()->toArray();

        return view('department.exams.results', [
            'schedule' => $schedule,
            'total_days' => 10,
            'examDates' => $examDates,
            'meta' => [
                'year' => $year,
                'semester' => $semester,
                'section' => $section,
                'is_locked' => true,
                'is_faculty_view' => true,
                'department_id' => $departmentId,
                'schedule_type' => 'exam'
            ]
        ]);
    }

    /**
     * Store a comment on a schedule
     */
    public function storeComment(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'year'          => 'required|integer',
            'semester'      => 'required|integer',
            'section'       => 'required|string',
            'schedule_type' => 'required|in:class,exam',
            'comment'       => 'required|string|max:1000',
        ]);

        ScheduleComment::create([
            'department_id' => $validated['department_id'],
            'user_id'       => Auth::id(),
            'year'          => $validated['year'],
            'semester'      => $validated['semester'],
            'section'       => $validated['section'],
            'schedule_type' => $validated['schedule_type'],
            'comment'       => $validated['comment'],
            'is_read'       => false,
        ]);

        return back()->with('success', 'Comment sent to Department Head.');
    }

    /**
     * Mark a comment as read (called by Department Head)
     */
    public function markCommentRead($id)
    {
        $comment = ScheduleComment::findOrFail($id);
        
        // Ensure only the department head of the relevant department can mark it as read
        if (Auth::user()->department_id != $comment->department_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
