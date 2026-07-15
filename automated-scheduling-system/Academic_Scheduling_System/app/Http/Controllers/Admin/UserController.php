<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // This method handles the 'index' route to show the table
    public function index(Request $request)
    {
        $query = User::query();

        // Handle the role filtering logic (All, Faculty, etc.)
        if ($request->has('role') && $request->role !== 'all') {
            $query->where('role_id', $request->role);
        }

        $users = $query->get();

        return view('admin.users.index', compact('users'));
    }

    // THIS IS THE FIX for your error:
    public function updateRole(Request $request, User $user)
    {
        // We ONLY validate role_id. 
        // Since we don't ask for name/email here, we don't validate them!
        $request->validate([
            'role_id' => 'required|integer|in:1,2,3,4',
        ]);

        $user->update([
            'role_id' => $request->role_id
        ]);

        return back()->with('success', "Role updated for {$user->full_name} successfully.");
    }
}
