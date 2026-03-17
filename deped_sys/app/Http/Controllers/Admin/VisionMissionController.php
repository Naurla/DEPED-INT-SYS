<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisionMission;
use Illuminate\Http\Request;

class VisionMissionController extends Controller
{
    public function index()
    {
        // Get the first record or create an empty one
        $data = VisionMission::firstOrCreate([]);
        return view('admin.vision_mission.index', compact('data'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'core_values' => 'nullable|string',
            'mandate' => 'nullable|string',
        ]);

        $data = VisionMission::first();
        $data->update($request->all());

        return redirect()->route('admin.vision_mission.index')->with('success', 'Vision, Mission, Core Values, and Mandate updated successfully.');
    }
}