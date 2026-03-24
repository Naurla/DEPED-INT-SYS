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
            'csv_file' => 'nullable|file|mimes:csv,txt|max:5000',
        ]);

        $path = $request->hasFile('csv_file') ? $request->file('csv_file')->store('junior_high/csv', 'public') : null;

        JuniorHighContent::create([
            'title' => $request->title,
            'content' => $request->content,
            'csv_path' => $path,
        ]);

        return back()->with('success', 'Added successfully.');
    }

    public function update(Request $request, JuniorHighContent $juniorHigh) {
        if ($request->hasFile('csv_file')) {
            if ($juniorHigh->csv_path) Storage::disk('public')->delete($juniorHigh->csv_path);
            $juniorHigh->csv_path = $request->file('csv_file')->store('junior_high/csv', 'public');
        }
        $juniorHigh->update($request->only('title', 'content'));
        return back()->with('success', 'Updated successfully.');
    }

    public function destroy(JuniorHighContent $juniorHigh) {
        if ($juniorHigh->csv_path) Storage::disk('public')->delete($juniorHigh->csv_path);
        $juniorHigh->delete();
        return back()->with('success', 'Deleted successfully.');
    }
}