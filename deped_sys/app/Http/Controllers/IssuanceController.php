<?php

namespace App\Http\Controllers;

use App\Models\Issuance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IssuanceController extends Controller
{
    public function advisories()
    {
        $items = Issuance::where('type', 'advisory')->latest()->paginate(5);
        $recentAdvisories = Issuance::where('type', 'advisory')->latest()->take(5)->get();
        $recentMemoranda = Issuance::where('type', 'memorandum')->latest()->take(5)->get();
        
        return view('issuances.category', [
            'items' => $items,
            'title' => 'Division Advisories',
            'color' => 'red',
            'recentAdvisories' => $recentAdvisories,
            'recentMemoranda' => $recentMemoranda,
        ]);
    }

    public function memoranda()
    {
        $items = Issuance::where('type', 'memorandum')->latest()->paginate(5);
        $recentAdvisories = Issuance::where('type', 'advisory')->latest()->take(5)->get();
        $recentMemoranda = Issuance::where('type', 'memorandum')->latest()->take(5)->get();
        
        return view('issuances.category', [
            'items' => $items,
            'title' => 'Division Memoranda',
            'color' => 'blue',
            'recentAdvisories' => $recentAdvisories,
            'recentMemoranda' => $recentMemoranda,
        ]);
    }

    public function hrmpsb()
    {
        $items = Issuance::where('type', 'hrmpsb')->latest()->paginate(5);
        $recentAdvisories = Issuance::where('type', 'advisory')->latest()->take(5)->get();
        $recentMemoranda = Issuance::where('type', 'memorandum')->latest()->take(5)->get();
        
        return view('issuances.category', [
            'items' => $items,
            'title' => 'HRMPSB Assessment Results',
            'color' => 'yellow',
            'recentAdvisories' => $recentAdvisories,
            'recentMemoranda' => $recentMemoranda,
        ]);
    }

    public function show($id)
    {
        $issuance = Issuance::findOrFail($id);

        $recentAdvisories = Issuance::where('type', 'advisory')
                                    ->where('id', '!=', $issuance->id)
                                    ->latest()
                                    ->take(5)
                                    ->get();

        $recentMemoranda = Issuance::where('type', 'memorandum')
                                   ->where('id', '!=', $issuance->id)
                                   ->latest()
                                   ->take(5)
                                   ->get();

        return view('issuances.show', compact('issuance', 'recentAdvisories', 'recentMemoranda'));
    }

    public function adminIndex(Request $request)
    {
        $type = $request->query('type', 'advisory'); 
        $issuances = Issuance::where('type', $type)->latest()->paginate(10);
        return view('admin.issuances.index', compact('issuances', 'type'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string', 
            'type' => 'required|in:advisory,memorandum,hrmpsb',
            'pdf_file' => 'nullable|mimes:pdf|max:10240', // Changed to nullable
            'link' => 'nullable|url|max:2000', // Added link validation
            'date' => 'nullable|date',
        ]);

        $path = null;
        
        // Only attempt to store the file if one was actually uploaded
        if ($request->hasFile('pdf_file')) {
            $pdfFile = $request->file('pdf_file');
            $pdfFilename = $pdfFile->getClientOriginalName();
            $path = $pdfFile->storeAs('issuances/' . $validated['type'], $pdfFilename, 'public');
        }
        
        Issuance::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'pdf_path' => $path,
            'link' => $validated['link'] ?? null, // Save the link
            'date' => $validated['date'] ?: now()->toDateString(), // Fallback to current date
        ]);

        return back()->with('success', ucfirst($validated['type']) . ' uploaded successfully!');
    }

    public function update(Request $request, $id)
    {
        $issuance = Issuance::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string', 
            'pdf_file' => 'nullable|mimes:pdf|max:10240',
            'link' => 'nullable|url|max:2000', // Added link validation
            'date' => 'nullable|date',
        ]);

        $dataToUpdate = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'link' => $validated['link'] ?? null, // Update the link
            'date' => $validated['date'] ?: now()->toDateString(), // Fallback to current date
        ];

        if ($request->hasFile('pdf_file')) {
            // Delete old PDF if it exists
            if ($issuance->pdf_path) {
                Storage::disk('public')->delete($issuance->pdf_path);
            }
            
            $pdfFile = $request->file('pdf_file');
            $pdfFilename = $pdfFile->getClientOriginalName();
            $dataToUpdate['pdf_path'] = $pdfFile->storeAs('issuances/' . $issuance->type, $pdfFilename, 'public');
        }

        // If the user checked "Remove PDF" in the frontend (if you add that logic later) 
        // or if you need to clear it when they provide a link instead, you can handle that here.
        if ($request->input('remove_pdf') == '1' && $issuance->pdf_path) {
            Storage::disk('public')->delete($issuance->pdf_path);
            $dataToUpdate['pdf_path'] = null;
        }

        $issuance->update($dataToUpdate);

        return back()->with('success', 'Issuance updated successfully!');
    }

    public function destroy($id)
    {
        $issuance = Issuance::findOrFail($id);

        if ($issuance->pdf_path) {
            Storage::disk('public')->delete($issuance->pdf_path);
        }
        
        $issuance->delete();

        return back()->with('success', 'Issuance deleted successfully!');
    }

    public function globalSearch(Request $request)
    {
        $query = $request->input('q');
        
        $results = Issuance::where('title', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        $results->appends(['q' => $query]);

        return view('frontend.search_results', compact('results', 'query'));
    }
}