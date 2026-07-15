<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'], // This is the ID Number
            'password' => ['required', 'string'],
        ]);

        // UC01: System validates credentials 
        // We also check if the user is suspended (UC03) [cite: 4]
        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password'], 'status' => 'active'])) {
            $request->session()->regenerate();

            // UC01: Redirect to respective dashboard 
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records or the account is suspended.',
        ])->onlyInput('username');
    }
}