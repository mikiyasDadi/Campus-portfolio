<?php
namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ExamInstructorAvailability;
use Illuminate\Http\Request;

class ExamInstructorAvailabilityController extends Controller
{
    public function index()
    {
        $deptId = auth()->user()->department_id;
        $totalDays = 10; 

        // Fetch instructors linked to your department
        $instructors = User::where('department_id', $deptId)
            ->with(['examAvailabilities' => function($query) use ($deptId) {
                $query->where('department_id', $deptId);
            }])
            ->get();

        return view('department.exams.instructor_availability', compact('instructors', 'totalDays', 'deptId'));
    }

    public function update(Request $request)
    {
        $currentDeptId = auth()->user()->department_id;

        if (!$request->has('availability')) {
            return back()->with('info', 'No changes detected.');
        }

        foreach ($request->availability as $insId => $days) {
            foreach ($days as $dayNum => $periods) {
                foreach ($periods as $period => $value) {
                    // This ensures you only update/create your own department's preferences
                    ExamInstructorAvailability::updateOrCreate(
    [
        'instructor_id' => $insId, 
        'day_number' => $dayNum, 
        'period' => $period,
        'department_id' => $currentDeptId // This must be in the first array
    ],
    ['is_available' => $value]
);
                }
            }
        }

        return back()->with('success', 'Availability settings updated successfully.');
    }
}