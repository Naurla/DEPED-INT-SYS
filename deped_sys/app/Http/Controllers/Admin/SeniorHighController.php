<?php

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

    public function update(Request $request, $id) {
        $request->validate([
            'title' => 'required|string|max:255',
            'csv_file' => 'nullable|file|mimes:csv,txt|max:5000',
        ]);

        $seniorHigh = SeniorHighContent::findOrFail($id);

        // Handle new file upload overriding the old one
        if ($request->hasFile('csv_file')) {
            if ($seniorHigh->csv_path) {
                Storage::disk('public')->delete($seniorHigh->csv_path);
            }
            $seniorHigh->csv_path = $request->file('csv_file')->store('senior_high/csv', 'public');
        }

        // Update the text fields
        $seniorHigh->title = $request->title;
        $seniorHigh->content = $request->content;
        
        // Save all changes at once
        $seniorHigh->save();

        return back()->with('success', 'Updated successfully.');
    }

    public function destroy($id) {
        $seniorHigh = SeniorHighContent::findOrFail($id);
        
        if ($seniorHigh->csv_path) {
            Storage::disk('public')->delete($seniorHigh->csv_path);
        }
        
        $seniorHigh->delete();
        return back()->with('success', 'Deleted successfully.');
    }
}