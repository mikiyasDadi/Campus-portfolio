<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OfficialRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration form.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle the registration request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:191'], // This is the ID Number
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:191'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // 1. Verify against Official Records (The Whitelist)
        $record = OfficialRecord::where('id_number', $request->username)
                                ->where('email', $request->email)
                                ->first();

        if (!$record) {
            return back()->withErrors([
                'username' => 'Access Denied. Your ID number and email do not match our official records.'
            ])->withInput();
        }

        // 2. Prevent duplicate registrations
        if (User::where('username', $request->username)->exists()) {
            return back()->withErrors([
                'username' => 'This University ID is already registered.'
            ])->withInput();
        }

        // 3. Create the User
        // We pull the first_name and last_name from the official record we found
        User::create([
            'first_name'    => $record->first_name,
            'last_name'     => $record->last_name,
            'username'      => $record->id_number,
            'email'         => $record->email,
            'password'      => Hash::make($request->password),
            'role_id'       => $record->role_id,      
            'department_id' => $record->department_id, 
            'status'        => 'active',
        ]);

        return redirect()->route('login')->with('success', 'Registration successful! Please log in to access your portal.');
    }
}