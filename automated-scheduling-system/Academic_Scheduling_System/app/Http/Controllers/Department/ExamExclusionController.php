<?php
namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\ExamExclusionGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamExclusionController extends Controller
{
   public function index()
{
    $deptId = auth()->user()->department_id;
    
    // Grouping logic to match the Blade template expectations
    $courses = Course::with('department')
        ->orderBy('department_id')
        ->orderBy('year')
        ->orderBy('semester')
        ->get()
        ->groupBy(function (Course $item) { 
            $dept = $item->department;
            $deptName = $dept ? $dept->name : 'Unknown Dept';
            return $deptName . ' — Year ' . $item->year; 
        })
        ->map(function ($group) {
            return $group->groupBy('semester');
        });

    $exclusionSets = ExamExclusionGroup::with(['courses.department'])
                    ->where('department_id', $deptId)
                    ->latest()
                    ->get();

    return view('department.exams.exclusions', compact('courses', 'exclusionSets'));
}
    public function store(Request $request)
    {
        $request->validate([
            'course_ids' => 'required|array|min:2',
            'course_ids.*' => 'exists:courses,id',
        ], ['course_ids.min' => 'Please select at least 2 courses to form a set.']);

        $deptId = auth()->user()->department_id;

        DB::transaction(function () use ($request, $deptId) {
            $count = ExamExclusionGroup::where('department_id', $deptId)->count() + 1;
            
            $group = ExamExclusionGroup::create([
                'department_id' => $deptId,
                'set_name' => "Exclusion Set #$count"
            ]);

            $group->courses()->attach($request->course_ids);
        });

        return back()->with('success', 'New exclusion set created successfully.');
    }

    public function destroy($id)
    {
        $group = ExamExclusionGroup::findOrFail($id);

        // Security check: Ensure DH only deletes their own dept's sets
        if ((int)$group->department_id !== (int)auth()->user()->department_id) {
            abort(403, 'Unauthorized action.');
        }

        $group->delete();
        return redirect()->route('department.exclusions.index')
            ->with('success', 'Exclusion set deleted.');
    }
}