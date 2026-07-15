<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use App\Models\InstructorProfile;
use App\Models\InstructorAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class InstructorAvailabilityController extends Controller
{
    public function index(InstructorProfile $instructor)
    {
        $dept = Auth::user()->department; 

        // Dynamic time slot generation logic
        $totalPeriods = $dept->total_periods ?? 8; 
        $duration = $dept->class_duration ?? 60;   
        
        $dynamicSlots = [];
        $currentTime = Carbon::createFromTime(8, 0, 0); 

        for ($i = 1; $i <= $totalPeriods; $i++) {
            // Break for lunch logic after 4th period
            if ($i == 5) {
                $currentTime = Carbon::createFromTime(14, 0, 0); 
            }

            $slotStart = $currentTime->copy();
            $slotEnd = $currentTime->addMinutes($duration)->copy();
            
            $dynamicSlots[] = (object)[
                'id' => $i, 
                'start_time' => $slotStart->format('H:i:s'),
                'end_time' => $slotEnd->format('H:i:s'),
                'label' => "Period $i"
            ];
        }

        $days = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday'];
        
        // Retrieve availability using the custom foreign key instructor_profile_id
        $availabilities = InstructorAvailability::where('instructor_profile_id', $instructor->user_id)->get();

        return view('department.instructors.availability', compact('instructor', 'dynamicSlots', 'days', 'availabilities'));
    }

    public function toggle(Request $request, InstructorProfile $instructor)
    {
        $data = $request->validate([
            'day_of_week' => 'required|integer|between:1,5',
            'time_slot_id' => 'required|integer',
        ]);

        $deptId = Auth::user()->department_id;

        // Check if a slot already exists for this instructor
        $existing = InstructorAvailability::where('instructor_profile_id', $instructor->user_id)
            ->where('day_of_week', $data['day_of_week'])
            ->where('time_slot_id', $data['time_slot_id'])
            ->first();

        if ($existing) {
            // Only allow the department that created the slot to remove it
            if ($existing->department_id == $deptId) {
                $existing->delete();
                return response()->json(['status' => 'removed']);
            }
            return response()->json(['status' => 'error', 'message' => 'Locked by another department.'], 403);
        }

        // Create new availability entry
        InstructorAvailability::create([
            'instructor_profile_id' => $instructor->user_id,
            'day_of_week'           => $data['day_of_week'],
            'time_slot_id'          => $data['time_slot_id'],
            'type'                  => 'manual',
            'department_id'         => $deptId,
        ]);

        return response()->json(['status' => 'added']);
    }

    public function reset(InstructorProfile $instructor)
    {
        // Resets only for the current department head's scope
        InstructorAvailability::where('instructor_profile_id', $instructor->user_id)
            ->where('department_id', Auth::user()->department_id)
            ->delete();

        return back()->with('success', 'Availability reset for this instructor.');
    }

    public function resetAll()
    {
        $deptId = Auth::user()->department_id;
        InstructorAvailability::where('department_id', $deptId)->delete();

        return back()->with('success', 'All instructor schedules have been reset for this department.');
    }
}