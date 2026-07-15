<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\TimeSlot;
use App\Models\ExamSchedule;
use App\Models\Department;
use App\Models\OfficialRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Find student details from OfficialRecord or use User attributes
        $officialRecord = OfficialRecord::where('id_number', $user->username)->first();
        
        // Priority: Official Record -> User Model -> Default
        $deptId = $user->department_id ?? ($officialRecord->department_id ?? null);
        $year = $user->year ?? ($officialRecord->year ?? 1);
        $section = $user->section ?? ($officialRecord->section ?? 'A');
        
        // Get latest semester from existing schedules for this dept/year
        $latestSchedule = Schedule::where('department_id', $deptId)
            ->where('year', $year)
            ->where('status', 'published')
            ->orderBy('updated_at', 'desc')
            ->first();
            
        $semester = $latestSchedule->semester ?? 1;

        // Automated Weekly Timetable
        $schedules = Schedule::where('department_id', $deptId)
            ->where('year', $year)
            ->where('semester', $semester)
            ->where('section', $section)
            ->where('status', 'published')
            ->with(['course', 'instructor'])
            ->get();

        $timeSlots = TimeSlot::orderBy('start_time')->get();
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        // Fallback: If no time slots exist, generate defaults based on department settings
        if ($timeSlots->isEmpty()) {
            $totalPeriods = Department::find($deptId)->total_periods ?? 8;
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

        // "Happening Now" Indicator
        $currentClass = $this->getCurrentClass($schedules, $timeSlots);

        // Exam Schedule Integration
        $examSchedules = ExamSchedule::where('department_id', $deptId)
            ->where('year', $year)
            ->where('semester', $semester)
            ->where('section', $section)
            ->with('course')
            ->orderBy('exam_date')
            ->get();

        return view('student.dashboard', compact('user', 'timetable', 'days', 'timeSlots', 'currentClass', 'examSchedules', 'year', 'semester', 'section'));
    }

    public function downloadSchedule()
    {
        $user = Auth::user();
        $officialRecord = OfficialRecord::where('id_number', $user->username)->first();
        
        $deptId = $user->department_id ?? ($officialRecord->department_id ?? null);
        $year = $user->year ?? ($officialRecord->year ?? 1);
        $section = $user->section ?? ($officialRecord->section ?? 'A');
        
        $latestSchedule = Schedule::where('department_id', $deptId)
            ->where('year', $year)
            ->where('status', 'published')
            ->orderBy('updated_at', 'desc')
            ->first();
            
        $semester = $latestSchedule->semester ?? 1;

        $schedules = Schedule::where('department_id', $deptId)
            ->where('year', $year)
            ->where('semester', $semester)
            ->where('section', $section)
            ->where('status', 'published')
            ->with(['course', 'instructor'])
            ->get();

        $timeSlots = TimeSlot::orderBy('start_time')->get();
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        // Fallback: If no time slots exist, generate defaults based on department settings
        if ($timeSlots->isEmpty()) {
            $totalPeriods = Department::find($deptId)->total_periods ?? 8;
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

        return view('student.print_schedule', compact('user', 'timetable', 'days', 'timeSlots', 'year', 'semester', 'section'));
    }

    private function getCurrentClass($schedules, $timeSlots)
    {
        $now = Carbon::now();
        $currentDay = $now->format('l');
        $currentTime = $now->format('H:i:s');

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        if (!in_array($currentDay, $days)) return null;

        foreach ($schedules->where('day', $currentDay) as $class) {
            $slot = $timeSlots->get($class->period - 1);
            if ($slot && $currentTime >= $slot->start_time && $currentTime <= $slot->end_time) {
                $endTime = Carbon::parse($slot->end_time);
                $remainingMinutes = $now->diffInMinutes($endTime);
                
                return [
                    'class' => $class,
                    'remaining_minutes' => $remainingMinutes,
                    'end_time' => $endTime->format('h:i A')
                ];
            }
        }

        return null;
    }
}
