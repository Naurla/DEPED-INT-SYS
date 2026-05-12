<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataPrivacy;
use Illuminate\Http\Request;

class DataPrivacyController extends Controller
{
    public function index()
    {
        $data = DataPrivacy::firstOrCreate([]);
        return view('admin.data_privacy.index', compact('data'));
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

        $data = DataPrivacy::first();
        
        // array_values ensures the array keys are sequential (0, 1, 2) 
        // in case the user deletes a section in the middle of the form
        $sections = $request->has('sections') ? array_values($request->input('sections')) : [];

        $data->update([
            'sections' => $sections
        ]);

        return redirect()->route('admin.data_privacy.index')
                         ->with('success', 'Data Privacy Notice updated successfully.');
    }
}