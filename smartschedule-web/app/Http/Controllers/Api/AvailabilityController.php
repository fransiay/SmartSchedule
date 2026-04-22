<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    public function index(Request $request)
    {
        return response()->json($request->user()->availabilities);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'day_of_week' => 'required|integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'is_active' => 'boolean',
        ]);

        $map = [0 => 'Sun', 1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat'];

        $availability = $request->user()->availabilities()->create([
            'day' => $map[$validated['day_of_week']],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
        ]);
        return response()->json($availability, 201);
    }

    public function show(Request $request, Availability $availability)
    {
        if ($availability->user_id !== $request->user()->id) abort(403);
        return response()->json($availability);
    }

    public function update(Request $request, Availability $availability)
    {
        if ($availability->user_id !== $request->user()->id) abort(403);

        $validated = $request->validate([
            'day_of_week' => 'sometimes|required|integer|between:0,6',
            'start_time' => 'sometimes|required|date_format:H:i',
            'end_time' => 'sometimes|required|date_format:H:i',
            'is_active' => 'boolean',
        ]);

        $updateData = [];
        if (isset($validated['day_of_week'])) {
            $map = [0 => 'Sun', 1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat'];
            $updateData['day'] = $map[$validated['day_of_week']];
        }
        if (isset($validated['start_time'])) $updateData['start_time'] = $validated['start_time'];
        if (isset($validated['end_time'])) $updateData['end_time'] = $validated['end_time'];

        $availability->update($updateData);
        return response()->json($availability);
    }

    public function destroy(Request $request, Availability $availability)
    {
        if ($availability->user_id !== $request->user()->id) abort(403);
        $availability->delete();
        return response()->json(null, 204);
    }
}
