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
            'content' => 'nullable|string',
            // UPDATED: Added support for PDF, Word, and Excel
            'csv_file' => 'nullable|file|mimes:csv,txt,pdf,doc,docx,xls,xlsx|max:10240',
        ]);

        $path = null;
        if ($request->hasFile('csv_file')) {
            $file = $request->file('csv_file');
            $filename = $file->getClientOriginalName();
            $path = $file->storeAs('senior_high/documents', $filename, 'public');
        }

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
            'content' => 'nullable|string',
            'csv_file' => 'nullable|file|mimes:csv,txt,pdf,doc,docx,xls,xlsx|max:10240',
        ]);

        $seniorHigh = SeniorHighContent::findOrFail($id);

        if ($request->hasFile('csv_file')) {
            if ($seniorHigh->csv_path) {
                Storage::disk('public')->delete($seniorHigh->csv_path);
            }
            $file = $request->file('csv_file');
            $filename = $file->getClientOriginalName();
            $seniorHigh->csv_path = $file->storeAs('senior_high/documents', $filename, 'public');
        }

        $seniorHigh->title = $request->title;
        $seniorHigh->content = $request->content;
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