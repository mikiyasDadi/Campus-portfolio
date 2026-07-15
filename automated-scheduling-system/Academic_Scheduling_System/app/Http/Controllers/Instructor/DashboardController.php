<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\TimeSlot;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $schedules = Schedule::where('instructor_id', $user->id)
            ->where('status', 'published') // Changed from 'locked' to match ClassSchedulerController
            ->with('course') // Eager load course
            ->get();

        $timeSlots = TimeSlot::orderBy('start_time')->get();
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        // Fallback: If no time slots exist, generate defaults based on department settings
        if ($timeSlots->isEmpty()) {
            $totalPeriods = $user->department->total_periods ?? 8;
            for ($i = 1; $i <= $totalPeriods; $i++) {
                $startHour = 8 + $i - 1;
                $timeSlots->push(new TimeSlot([
                    'start_time' => sprintf('%02d:00:00', $startHour),
                    'end_time'   => sprintf('%02d:00:00', $startHour + 1),
                ]));
            }
        }

        // Personalized Timetable
        $timetable = [];
        foreach ($days as $day) {
            foreach ($timeSlots as $index => $slot) {
                $period = $index + 1;
                $timetable[$day][$period] = $schedules->where('day', $day)->where('period', $period)->first();
            }
        }

        // Real-Time "Next Class" Card
        $nextClass = $this->getNextClass($schedules, $timeSlots);

        // Faculty Overview (grouped by department)
        $facultyOverview = Department::with(['users' => function($query) {
            $query->where('role_id', 4); // Instructors only
        }])->get();

        return view('instructor.dashboard', compact('user', 'timetable', 'days', 'timeSlots', 'nextClass', 'facultyOverview'));
    }

    public function downloadSchedule()
    {
        $user = Auth::user();
        $schedules = Schedule::where('instructor_id', $user->id)
            ->where('status', 'published')
            ->with('course') // Eager load course
            ->get();

        $timeSlots = TimeSlot::orderBy('start_time')->get();
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        // Fallback: If no time slots exist, generate defaults based on department settings
        if ($timeSlots->isEmpty()) {
            $totalPeriods = $user->department->total_periods ?? 8;
            for ($i = 1; $i <= $totalPeriods; $i++) {
                $startHour = 8 + $i - 1;
                $timeSlots->push(new TimeSlot([
                    'start_time' => sprintf('%02d:00:00', $startHour),
                    'end_time'   => sprintf('%02d:00:00', $startHour + 1),
                ]));
            }
        }

        $timetable = [];
        foreach ($days as $day) {
            foreach ($timeSlots as $index => $slot) {
                $period = $index + 1;
                $timetable[$day][$period] = $schedules->where('day', $day)->where('period', $period)->first();
            }
        }

        return view('instructor.print_schedule', compact('user', 'timetable', 'days', 'timeSlots'));
    }

    private function getNextClass($schedules, $timeSlots)
    {
        $now = Carbon::now();
        $currentDay = $now->format('l');
        $currentTime = $now->format('H:i:s');

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        
        // Find today's next class
        if (in_array($currentDay, $days)) {
            $todayClasses = $schedules->where('day', $currentDay)->sortBy('period');
            foreach ($todayClasses as $class) {
                $slot = $timeSlots->get($class->period - 1);
                if ($slot && $slot->start_time > $currentTime) {
                    return [
                        'class' => $class,
                        'day' => 'Today',
                        'time' => Carbon::parse($slot->start_time)->format('h:i A')
                    ];
                }
            }
        }

        // Find first class tomorrow (or next available day)
        $dayIndex = array_search($currentDay, $days);
        if ($dayIndex === false) {
            $dayIndex = -1; // It's weekend, start from Monday
        }

        for ($i = 1; $i <= 7; $i++) {
            $nextIndex = ($dayIndex + $i) % 5;
            $nextDay = $days[$nextIndex];
            
            $nextDayClasses = $schedules->where('day', $nextDay)->sortBy('period');
            if ($nextDayClasses->isNotEmpty()) {
                $firstClass = $nextDayClasses->first();
                $slot = $timeSlots->get($firstClass->period - 1);
                
                $dayLabel = ($i == 1) ? 'Tomorrow' : $nextDay;
                
                return [
                    'class' => $firstClass,
                    'day' => $dayLabel,
                    'time' => $slot ? Carbon::parse($slot->start_time)->format('h:i A') : 'N/A'
                ];
            }
            
            // If we've looped through all 5 days and found nothing, break
            if ($i >= 5) break;
        }

        return null;
    }

    public function facultySchedule($departmentId)
    {
        $department = Department::findOrFail($departmentId);
        $schedules = Schedule::where('department_id', $departmentId)
            ->where('status', 'published')
            ->with(['instructor', 'course']) // Eager load course
            ->get()
            ->groupBy('instructor_id');

        $timeSlots = TimeSlot::orderBy('start_time')->get();
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        // Fallback: If no time slots exist, generate defaults based on department settings
        if ($timeSlots->isEmpty()) {
            $totalPeriods = $department->total_periods ?? 8;
            for ($i = 1; $i <= $totalPeriods; $i++) {
                $startHour = 8 + $i - 1;
                $timeSlots->push(new TimeSlot([
                    'start_time' => sprintf('%02d:00:00', $startHour),
                    'end_time'   => sprintf('%02d:00:00', $startHour + 1),
                ]));
            }
        }

        return view('instructor.faculty_schedule', compact('department', 'schedules', 'timeSlots', 'days'));
    }
}
