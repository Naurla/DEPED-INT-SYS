<?php
// Location: app/Http/Controllers/Admin/SeniorHighController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeniorHighContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SeniorHighController extends Controller
{
    public function index() {
        $contents = SeniorHighContent::latest()->get();
        return view('admin.senior_high.index', compact('contents'));
    }

    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'csv_file' => 'nullable|file|mimes:csv,txt|max:5000',
        ]);

        // Store file in a dedicated senior_high directory
        $path = $request->hasFile('csv_file') 
            ? $request->file('csv_file')->store('senior_high/csv', 'public') 
            : null;

        SeniorHighContent::create([
            'title' => $request->title,
            'content' => $request->content,
            'csv_path' => $path,
        ]);

        return back()->with('success', 'Senior High entry added successfully.');
    }

    public function update(Request $request, SeniorHighContent $seniorHigh) {
        if ($request->hasFile('csv_file')) {
            // Delete old file if a new one is uploaded
            if ($seniorHigh->csv_path) Storage::disk('public')->delete($seniorHigh->csv_path);
            $seniorHigh->csv_path = $request->file('csv_file')->store('senior_high/csv', 'public');
        }
        $seniorHigh->update($request->only('title', 'content'));
        return back()->with('success', 'Updated successfully.');
    }

    public function destroy(SeniorHighContent $seniorHigh) {
        if ($seniorHigh->csv_path) Storage::disk('public')->delete($seniorHigh->csv_path);
        $seniorHigh->delete();
        return back()->with('success', 'Deleted successfully.');
    }
}