<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JuniorHighContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JuniorHighController extends Controller
{
    public function index() {
        $contents = JuniorHighContent::latest()->get();
        return view('admin.junior_high.index', compact('contents'));
    }

    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            // Added support for PDF, Word, and Excel
            'csv_file' => 'nullable|file|mimes:csv,txt,pdf,doc,docx,xls,xlsx|max:10240',
        ]);

        $path = null;
        if ($request->hasFile('csv_file')) {
            $file = $request->file('csv_file');
            $filename = time() . '_' . $file->getClientOriginalName(); // Added time() to prevent overwriting
            // Storing in a clean directory
            $path = $file->storeAs('junior_high/documents', $filename, 'public');
        }

        JuniorHighContent::create([
            'title' => $request->title,
            // Hardcoding 'public' to satisfy the strict ENUM rule in the database 
            'school_type' => 'public', 
            'content' => $request->content,
            'csv_path' => $path,
        ]);

        return back()->with('success', 'Content added successfully.');
    }

    public function update(Request $request, $id) {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'csv_file' => 'nullable|file|mimes:csv,txt,pdf,doc,docx,xls,xlsx|max:10240',
        ]);

        $juniorHigh = JuniorHighContent::findOrFail($id);

        if ($request->hasFile('csv_file')) {
            // Delete old file if it exists
            if ($juniorHigh->csv_path) {
                Storage::disk('public')->delete($juniorHigh->csv_path);
            }
            $file = $request->file('csv_file');
            $filename = time() . '_' . $file->getClientOriginalName(); // Added time()
            $juniorHigh->csv_path = $file->storeAs('junior_high/documents', $filename, 'public');
        }

        $juniorHigh->title = $request->title;
        // Keep the database happy during updates
        $juniorHigh->school_type = 'public'; 
        $juniorHigh->content = $request->content;
        $juniorHigh->save();

        return back()->with('success', 'Content updated successfully.');
    }

    public function destroy($id) {
        $juniorHigh = JuniorHighContent::findOrFail($id);
        
        if ($juniorHigh->csv_path) {
            Storage::disk('public')->delete($juniorHigh->csv_path);
        }
        
        $juniorHigh->delete();
        return back()->with('success', 'Content deleted successfully.');
    }
}