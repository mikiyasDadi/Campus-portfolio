<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    public function index()
    {
        $deptId = Auth::user()->department_id;
        $rooms = Room::where('department_id', $deptId)->orderBy('order_weight')->get();
        return view('department.rooms.index', compact('rooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:normal,hall',
            'order_weight' => 'required|integer'
        ]);

        Room::create([
            'name' => $request->name,
            'type' => $request->type,
            'department_id' => Auth::user()->department_id,
            'order_weight' => $request->order_weight
        ]);

        return back()->with('success', 'Room registered successfully.');
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:normal,hall',
            'order_weight' => 'required|integer'
        ]);

        $room->update($request->only('name', 'type', 'order_weight'));

        return back()->with('success', 'Room updated successfully.');
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return back()->with('success', 'Room deleted successfully.');
    }
}
