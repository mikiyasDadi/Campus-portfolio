<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PeriodController extends Controller
{
    public function index()
    {
        // Get the department of the logged-in Head
        $department = auth()->user()->department;
        return view('department.periods.index', compact('department'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'class_duration' => 'required|integer|min:20|max:180',
            'lab_duration'   => 'required|integer|min:20|max:240',
            'total_periods'  => 'required|integer|min:1|max:15',
        ]);

        $department = auth()->user()->department;
        $department->update($request->all());

        return back()->with('success', 'Period settings updated successfully!');
    }
}
