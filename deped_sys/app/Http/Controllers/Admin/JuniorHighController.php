<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JuniorHighContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JuniorHighController extends Controller
{
    public function index(Request $request) {
        $query = JuniorHighContent::query();

        // 1. Search Filter (Title & Content)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // 2. Year Filter (Filtering based on created_at)
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        // 3. Month Filter
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        // 4. Sort Filter
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $query->oldest('created_at')->oldest('id');
                    break;
                case 'a_z':
                    $query->orderBy('title', 'asc');
                    break;
                case 'z_a':
                    $query->orderBy('title', 'desc');
                    break;
                case 'newest':
                default:
                    $query->latest('created_at')->latest('id');
                    break;
            }
        } else {
            $query->latest('created_at')->latest('id'); // Default Sort
        }

        // withQueryString() ensures filters stay active when paginating
        $contents = $query->paginate(10)->withQueryString();

        // Get dynamic available years for the dropdown filter
        $years = JuniorHighContent::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('admin.junior_high.index', compact('contents', 'years'));
    }

    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255|unique:junior_high_contents,title',
            'content' => 'nullable|string',
            'csv_file' => 'nullable|file|mimes:csv,txt,pdf,doc,docx,xls,xlsx|max:10240',
        ], [
            'title.required' => 'Please provide a title.',
            'title.unique' => 'This title already exists. Please provide a unique entry.',
            'csv_file.mimes' => 'The document must be a file of type: csv, txt, pdf, doc, docx, xls, xlsx.',
            'csv_file.max' => 'The document must not exceed 10MB in size.'
        ]);

        $path = null;
        if ($request->hasFile('csv_file')) {
            $file = $request->file('csv_file');
            // Adding time() to prevent file overwrites
            $filename = time() . '_' . $file->getClientOriginalName();
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
            'title' => 'required|string|max:255|unique:junior_high_contents,title,' . $id,
            'content' => 'nullable|string',
            'csv_file' => 'nullable|file|mimes:csv,txt,pdf,doc,docx,xls,xlsx|max:10240',
        ], [
            'title.required' => 'Please provide a title.',
            'title.unique' => 'This title already exists. Please provide a unique entry.',
            'csv_file.mimes' => 'The document must be a file of type: csv, txt, pdf, doc, docx, xls, xlsx.',
            'csv_file.max' => 'The document must not exceed 10MB in size.'
        ]);

        $juniorHigh = JuniorHighContent::findOrFail($id);

        // Handle explicitly removing the document file via UI flag
        if ($request->remove_file == '1') {
            if ($juniorHigh->csv_path) {
                Storage::disk('public')->delete($juniorHigh->csv_path);
            }
            $juniorHigh->csv_path = null;
        }

        if ($request->hasFile('csv_file')) {
            // Delete old file if it exists
            if ($juniorHigh->csv_path) {
                Storage::disk('public')->delete($juniorHigh->csv_path);
            }
            $file = $request->file('csv_file');
            $filename = time() . '_' . $file->getClientOriginalName();
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