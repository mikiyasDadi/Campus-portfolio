<?php
namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
{
    $deptId = auth()->user()->department_id;

    // Start the query filtered by the DH's department
    $query = Course::where('department_id', $deptId);

    // Filter by Year if selected
    if ($request->filled('year')) {
        $query->where('year', $request->year);
    }

    // Filter by Semester if selected
    if ($request->filled('semester')) {
        $query->where('semester', $request->semester);
    }

    // Search by Name or Code if text is entered
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('course_code', 'like', "%{$search}%")
              ->orWhere('course_name', 'like', "%{$search}%");
        });
    }

    // Get the filtered results, ordered by Year then Semester
    $courses = $query->orderBy('year')->orderBy('semester')->get();

    return view('department.courses.index', compact('courses'));
}

 public function store(Request $request)
    {
        $request->validate([
            'course_code' => 'required|unique:courses,course_code',
            'course_name' => 'required',
            'ects' => 'required|integer',
            'hours' => 'required',
            'year' => 'required|integer|min:1|max:5',
        'semester' => 'required|integer|min:1|max:2',
        ], [
        // Custom error message for the user
        'course_code.unique' => 'This course code is already in use. Please provide a unique code.', 
        ]);

        // Wrap in a transaction to ensure both the course and the count are updated together
        DB::transaction(function () use ($request) {
            // 1. Assign the created course to the $course variable
            $course = Course::create([
                'course_code' => $request->course_code,
                'course_name' => $request->course_name,
                'ects' => $request->ects,
                'hours' => $request->hours,
                'year' => $request->year,
        'semester' => $request->semester,
                'department_id' => auth()->user()->department_id,
            ]);

            // 2. Now $course is defined, so we can access the department relationship
            if ($course->department) {
                $course->department->increment('course_count');
            }
        });

        return back()->with('success', 'Course added and department count updated!');
    }
    // ... existing store and index methods ...

public function update(Request $request, Course $course)
{
    // Security: Prevent editing courses from other departments
    if ($course->department_id !== auth()->user()->department_id) {
        abort(403, 'Unauthorized action.');
    }

    $request->validate([
        'course_code' => 'required|unique:courses,course_code,' . $course->id,
         'course_name' => 'required',
        'ects'        => 'required|numeric',
        'hours'       => 'required',
        'year' => 'required|integer',
        'semester' => 'required|integer',
    ], [
        'course_code.unique' => 'The course code provided is already assigned to another course.',
    ]);

    $course->update($request->all());

    return back()->with('success', 'Course updated successfully!');
}

public function destroy(Course $course)
    {
        // Security check
        if ($course->department_id !== auth()->user()->department_id) {
            abort(403, 'Unauthorized action.');
        }

        DB::transaction(function () use ($course) {
            // 1. Decrement the count BEFORE deleting the course
            if ($course->department) {
                $course->department->decrement('course_count');
            }

            // 2. Delete the record
            $course->delete();
        });

        return back()->with('success', 'Course deleted and department count updated!');
    }
}

