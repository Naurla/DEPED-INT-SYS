<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ElementaryContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ElementaryController extends Controller
{
    public function index(Request $request) {
        $query = ElementaryContent::query();

        // 1. Search Filter (Title & Content)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // 2. Sort Filter
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

        return view('admin.elementary.index', compact('contents'));
    }

    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255|unique:elementary_contents,title',
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
            'title' => 'required|string|max:255|unique:elementary_contents,title,' . $id,
            'content' => 'nullable|string',
            'csv_file' => 'nullable|file|mimes:csv,txt,pdf,doc,docx,xls,xlsx|max:10240',
        ], [
            'title.required' => 'Please provide a title.',
            'title.unique' => 'This title already exists. Please provide a unique entry.',
            'csv_file.mimes' => 'The document must be a file of type: csv, txt, pdf, doc, docx, xls, xlsx.',
            'csv_file.max' => 'The document must not exceed 10MB in size.'
        ]);

        $elementary = ElementaryContent::findOrFail($id);

        // Explicitly handling file removal if the user clicks the trash icon
        if ($request->remove_file == '1') {
            if ($elementary->csv_path) {
                Storage::disk('public')->delete($elementary->csv_path);
            }
            $elementary->csv_path = null;
        }

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