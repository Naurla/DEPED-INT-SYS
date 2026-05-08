<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeniorHighContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SeniorHighController extends Controller
{
    public function index(Request $request) {
        $query = SeniorHighContent::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        // NEW: Month Filter
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

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
            $query->latest('created_at')->latest('id');
        }

        $contents = $query->paginate(10)->withQueryString();

        $years = SeniorHighContent::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('admin.senior_high.index', compact('contents', 'years'));
    }

    public function store(Request $request) {
        $messages = [
            'title.unique' => 'This Title already exists. Please provide a unique entry.',
            'csv_file.max' => 'The document must not be larger than 10MB.',
            'csv_file.mimes' => 'The document must be a file of type: csv, txt, pdf, doc, docx, xls, xlsx.',
        ];

        $request->validate([
            'title' => 'required|string|max:255|unique:senior_high_contents,title',
            'content' => 'nullable|string',
            'csv_file' => 'nullable|file|mimes:csv,txt,pdf,doc,docx,xls,xlsx|max:10240',
        ], $messages);

        $path = null;
        if ($request->hasFile('csv_file')) {
            $file = $request->file('csv_file');
            $filename = time() . '_' . $file->getClientOriginalName(); 
            $path = $file->storeAs('senior_high/documents', $filename, 'public');
        }

        SeniorHighContent::create([
            'title' => $request->title,
            'school_type' => 'public', 
            'content' => $request->content,
            'csv_path' => $path,
        ]);

        return back()->with('success', 'Senior High entry added successfully.');
    }

    public function update(Request $request, $id) {
        $messages = [
            'title.unique' => 'This Title already exists. Please provide a unique entry.',
            'csv_file.max' => 'The document must not be larger than 10MB.',
            'csv_file.mimes' => 'The document must be a file of type: csv, txt, pdf, doc, docx, xls, xlsx.',
        ];

        $request->validate([
            'title' => 'required|string|max:255|unique:senior_high_contents,title,' . $id,
            'content' => 'nullable|string',
            'csv_file' => 'nullable|file|mimes:csv,txt,pdf,doc,docx,xls,xlsx|max:10240',
        ], $messages);

        $seniorHigh = SeniorHighContent::findOrFail($id);

        if ($request->remove_file == '1') {
            if ($seniorHigh->csv_path) {
                Storage::disk('public')->delete($seniorHigh->csv_path);
            }
            $seniorHigh->csv_path = null;
        }

        if ($request->hasFile('csv_file')) {
            if ($seniorHigh->csv_path) {
                Storage::disk('public')->delete($seniorHigh->csv_path);
            }
            $file = $request->file('csv_file');
            $filename = time() . '_' . $file->getClientOriginalName(); 
            $seniorHigh->csv_path = $file->storeAs('senior_high/documents', $filename, 'public');
        }

        $seniorHigh->title = $request->title;
        $seniorHigh->school_type = 'public'; 
        $seniorHigh->content = $request->content;
        $seniorHigh->save();

        return back()->with('success', 'Senior High entry updated successfully.');
    }

    public function destroy($id) {
        $seniorHigh = SeniorHighContent::findOrFail($id);
        if ($seniorHigh->csv_path) {
            Storage::disk('public')->delete($seniorHigh->csv_path);
        }
        $seniorHigh->delete();
        return back()->with('success', 'Senior High entry deleted successfully.');
    }
}