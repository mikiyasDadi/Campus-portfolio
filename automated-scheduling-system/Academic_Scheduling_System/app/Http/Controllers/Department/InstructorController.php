<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\InstructorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class InstructorController extends Controller
{
    /**
     * Show the department roster.
     * We now fetch InstructorProfile models so that the user_id primary key 
     * is correctly passed to the Blade views and Route helpers.
     */
   public function index()
{
    $deptId = auth()->user()->department_id;

    // Fetch instructors belonging to this department
    $instructors = User::where('department_id', $deptId)
        ->orWhereHas('departments', function($query) use ($deptId) {
            $query->where('department_instructor.department_id', $deptId);
        })
        ->withCount('departments') 
        ->get();

    return view('department.instructors.index', compact('instructors'));
}

    /**
     * AJAX Search for existing users
     */
    public function search(Request $request)
    {
        $username = $request->query('username');
        
        $user = User::where('username', $username)
                    ->where('role_id', 4)
                    ->first();

        if ($user) {
            return response()->json([
                'exists' => true,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
            ]);
        }

        return response()->json(['exists' => false]);
    }

    /**
     * Store new instructor and create profile
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email',
            'username'   => 'required|string',
        ]);

        $deptId = auth()->user()->department_id;

        return DB::transaction(function () use ($request, $deptId) {
            $user = User::where('username', $request->username)
                        ->orWhere('email', $request->email)
                        ->first();

            if ($user) {
                if ($user->role_id != 4) {
                    $user->update(['role_id' => 4]);
                }

                // Check if profile exists, if not create it
                if (!$user->profile) {
                    $user->profile()->create([
                        'first_name'    => $request->first_name,
                        'last_name'     => $request->last_name,
                        'department_id' => $deptId,
                        'status'        => 'active',
                    ]);
                }

                if ($user->departments()->where('department_id', $deptId)->exists()) {
                    return redirect()->back()->withErrors(['username' => 'Instructor already in your department.']);
                }

                $user->departments()->attach($deptId);
                return redirect()->back()->with('success', 'Existing instructor linked successfully!');
            }

            // Create Brand New User
            $user = User::create([
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name,
                'username'      => $request->username,
                'email'         => $request->email,
                'password'      => Hash::make('password123'),
                'role_id'       => 4,
                'status'        => 'active',
                'department_id' => $deptId,
            ]);

            // Create Instructor Profile (This is what we use for availability)
            $user->profile()->create([
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name,
                'department_id' => $deptId,
                'status'        => 'active',
            ]);

            $user->departments()->attach($deptId);
            
            if(auth()->user()->department) {
                auth()->user()->department->increment('instructor_count');
            }

            return redirect()->back()->with('success', 'New instructor created!');
        });
    }

    public function update(Request $request, $id)
    {
        $instructor = User::findOrFail($id);
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $instructor->id,
        ]);

        // Update user record
        $instructor->update($request->only('first_name', 'last_name', 'email'));

        // Update profile if it exists
        if ($instructor->profile) {
            $instructor->profile->update($request->only('first_name', 'last_name'));
        }

        return back()->with('success', 'Instructor updated successfully!');
    }

    /**
     * Detach instructor from department
     */
    public function destroy($id)
    {
        $instructor = User::findOrFail($id);
        $deptId = auth()->user()->department_id;

        // Note: Relationship is managed on the User model pivot
        $instructor->departments()->detach($deptId);

        return back()->with('success', 'Instructor removed from roster.');
    }
}