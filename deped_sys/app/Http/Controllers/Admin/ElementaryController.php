<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ElementaryContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ElementaryController extends Controller
{
    public function index() {
        $contents = ElementaryContent::latest()->get();
        return view('admin.elementary.index', compact('contents'));
    }

    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'csv_file' => 'nullable|file|mimes:csv,txt,pdf,doc,docx,xls,xlsx|max:10240',
        ]);

        $path = null;
        if ($request->hasFile('csv_file')) {
            $file = $request->file('csv_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('elementary/documents', $filename, 'public');
        }

        ElementaryContent::create([
            'title' => $request->title,
            // Pass 'public' to satisfy the strict ENUM database rule. 
            // (The frontend ignores this now anyway)
            'school_type' => 'public', 
            'content' => $request->content,
            'csv_path' => $path,
        ]);

        return back()->with('success', 'Elementary content added successfully.');
    }

    public function update(Request $request, $id) {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'csv_file' => 'nullable|file|mimes:csv,txt,pdf,doc,docx,xls,xlsx|max:10240',
        ]);

        $elementary = ElementaryContent::findOrFail($id);

        if ($request->hasFile('csv_file')) {
            if ($elementary->csv_path) {
                Storage::disk('public')->delete($elementary->csv_path);
            }
            $file = $request->file('csv_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $elementary->csv_path = $file->storeAs('elementary/documents', $filename, 'public');
        }

        $elementary->title = $request->title;
        $elementary->school_type = 'public'; // Keeps the database happy during updates
        $elementary->content = $request->content;
        $elementary->save();

        return back()->with('success', 'Elementary content updated successfully.');
    }

    public function destroy($id) {
        $elementary = ElementaryContent::findOrFail($id);
        
        if ($elementary->csv_path) {
            Storage::disk('public')->delete($elementary->csv_path);
        }
        
        $elementary->delete();
        return back()->with('success', 'Elementary content deleted successfully.');
    }
}