<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisionMission;
use Illuminate\Http\Request;

class VisionMissionController extends Controller
{
    public function index()
    {
        $data = VisionMission::firstOrCreate([]);
        return view('admin.vision_mission.index', compact('data'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'sections'   => 'nullable|array',
            'sections.*.title'   => 'nullable|string|max:255',
            'sections.*.content' => 'required|string',
        ], [
            'sections.*.content.required' => 'The content field cannot be empty.'
        ]);

        $data = VisionMission::first();
        
        // Ensure array keys are sequential if a section was removed
        $sections = $request->has('sections') ? array_values($request->input('sections')) : [];

        $data->update([
            'sections' => $sections
        ]);

        return redirect()->route('admin.vision_mission.index')
                         ->with('success', 'Vision, Mission, Core Values, and Mandate updated successfully.');
    }
}