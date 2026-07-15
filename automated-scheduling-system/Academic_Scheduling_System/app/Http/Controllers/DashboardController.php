<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\OfficialRecord;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    /**
     * Redirect to respective dashboard based on role
     */
   public function index()
    {
        $user = auth()->user();

        // Route the Admin (Role 1)
        if ($user->role_id == 1) {
            return view('admin.dashboard', [
                'totalUsers' => User::count(),
                'totalDepartments' => Department::count(),
                'totalFaculties' => Faculty::count(),
            ]);
        }// Route the Faculty Head (Role 2)
        if ($user->role_id == 2) {
            return redirect()->route('faculty.dashboard');
        }

        // Route the Department Head (Role 3)
        if ($user->role_id == 3) {
            return redirect()->route('department.dashboard');
        }

        // Route the Instructor (Role 4)
        if ($user->role_id == 4) {
            return redirect()->route('instructor.dashboard');
        }

        // Route the Student (Role 5)
        if ($user->role_id == 5) {
            return redirect()->route('student.dashboard');
        }

        return view('dashboard');
    }

    /**
     * UC03: User Management with Role Filtering
     */
    public function users(Request $request)
    {
        $roleFilter = $request->query('role');

        // Exclude the currently logged-in admin
        $query = User::where('id', '!=', auth()->id());

        // Apply the role filter if one is selected
        if ($roleFilter && $roleFilter !== 'all') {
            $query->where('role_id', $roleFilter);
        }

        $users = $query->orderBy('first_name', 'asc')->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Dedicated Page: Faculty Head Assignment
     */
    public function facultyAssignments()
    {
        $facultyHeads = User::where('role_id', 2)->with('faculty')->get();
        $faculties = Faculty::all();

        return view('admin.users.faculty_assignments', compact('facultyHeads', 'faculties'));
    }

    /**
     * Assign a specific Faculty to a Faculty Head User
     */
 public function assignFaculty(Request $request, $id) // Ensure this is $id to match the route
{
    $request->validate([
        'faculty_id' => 'required|exists:faculties,id'
    ]);

    $user = \App\Models\User::findOrFail($id);
    
    $user->update([
        'faculty_id' => $request->faculty_id
    ]);

    return back()->with('success', "Faculty assigned to {$user->first_name} successfully!");
}

    /**
     * UC03: Import Official Records from CSV
     */
    public function importOfficialRecords(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        
        fgetcsv($handle); // Skip header

        $importCount = 0;
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (isset($data[0], $data[1], $data[2], $data[3], $data[4])) {
                
                $idNumber     = trim($data[0]);
                $email        = trim($data[1]);
                $firstName    = trim($data[2]);
                $lastName     = trim($data[3]);
                $roleId       = trim($data[4]);
                $departmentId = isset($data[5]) && $data[5] !== '' ? trim($data[5]) : null;

                // 1. Update or Create the Official Record
                OfficialRecord::updateOrCreate(
                    ['id_number' => $idNumber], 
                    [
                        'email'         => $email,
                        'first_name'    => $firstName,
                        'last_name'     => $lastName,
                        'role_id'       => $roleId,
                        'department_id' => $departmentId,
                    ]
                );

                // 2. Sync with the Users table
                $user = User::where('username', $idNumber)->first();
                if ($user) {
                    $user->update([
                        'first_name'    => $firstName,
                        'last_name'     => $lastName,
                        'email'         => $email,
                        'role_id'       => $roleId,
                        'department_id' => $departmentId,
                    ]);
                }

                $importCount++;
            }
        }

        fclose($handle);
        return back()->with('success', "$importCount records imported and synchronized successfully!");
    }

    /**
     * UC03: Update User Details (Role update)
     */
    public function updateUserDetails(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|integer|in:1,2,3,4,5',
        ]);

        $user->role_id = $request->role_id;
        $user->save();

        // Fixed the full_name reference to the new columns
        return back()->with('success', "Role for {$user->first_name} {$user->last_name} has been updated.");
    }

    /**
     * UC03: Suspend/Activate User
     */
    public function toggleUserStatus(User $user)
    {
        $newStatus = ($user->status === 'active') ? 'suspended' : 'active';
        $user->update(['status' => $newStatus]);
        
        return back()->with('success', "User account has been {$newStatus}.");
    }

    /**
     * UC04: Department Management List (Advisor Comment Applied)
     */
   public function departments()
{
    // We combine the 'head' relationship with dynamic counts for users and courses.
    // This creates 'users_count' and 'courses_count' attributes automatically.
    $departments = Department::with(['head', 'faculty'])
        ->withCount(['users', 'courses'])
        ->get();

    // Fetch users who are Dept Heads (role_id = 3) for the dropdown
    $deptHeads = User::where('role_id', 3)->get();
    
    // Fetch all faculties for the dropdown
    $faculties = Faculty::all();

    return view('admin.departments.index', compact('departments', 'deptHeads', 'faculties'));
}

    /**
     * UC04: Store Department
     */
    public function storeDepartment(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:departments|max:10',
            'name' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id', 
            'faculty_id' => 'required|exists:faculties,id',
        ]);

        Department::create($validated);

        return redirect()->back()->with('success', 'Department created successfully!');
    }

    /**
     * UC04: Update Department
     */
    public function updateDepartment(Request $request, Department $department)
    {
        $validated = $request->validate([
            'code' => 'required|max:10|unique:departments,code,' . $department->id,
            'name' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id', 
            'faculty_id' => 'required|exists:faculties,id',
        ]);

        $department->update($validated);

        return redirect()->back()->with('success', 'Department updated successfully!');
    }

    /**
     * UC04: Delete Department
     */
    public function destroyDepartment(Department $department)
    {
        $department->delete();
        return redirect()->back()->with('success', 'Department deleted successfully!');
    }

    /**
     * Department Head Dashboard View (Role 3)
     */
    public function departmentDashboard()
    {
        $user = auth()->user();
        
        if (!$user->department_id) {
            return view('department.dashboard', [
                'data' => [
                    'courses_count' => 0,
                    'instructors_count' => 0,
                    'draft_schedules' => 0
                ]
            ])->with('error', 'Warning: No department assigned to your account.');
        }

        $deptId = $user->department_id;

        $data = [
            'courses_count'     => \App\Models\Course::where('department_id', $deptId)->count(),
            'instructors_count' => User::where('department_id', $deptId)->where('role_id', 4)->count(),
            'draft_schedules'   => \App\Models\Schedule::where('department_id', $deptId)->where('status', 'draft')->count(),
            'unread_comments'   => \App\Models\ScheduleComment::where('department_id', $deptId)->where('is_read', false)->with('user')->orderBy('created_at', 'desc')->get(),
        ];

        return view('department.dashboard', compact('data'));
    }
}