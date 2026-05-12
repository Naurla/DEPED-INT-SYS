<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Qms;
use Illuminate\Http\Request;

class QmsController extends Controller
{
    public function index()
    {
        $qms = Qms::firstOrCreate([]);
        return view('admin.qms.index', compact('qms'));
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

        $qms = Qms::first();
        
        // Ensure array keys are sequential if a section was removed
        $sections = $request->has('sections') ? array_values($request->input('sections')) : [];

        $qms->update([
            'sections' => $sections
        ]);

        return redirect()->route('admin.qms.index')
                         ->with('success', 'QMS information updated successfully.');
    }
}