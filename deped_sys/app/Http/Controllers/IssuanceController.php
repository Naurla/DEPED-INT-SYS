<?php

namespace App\Http\Controllers;

use App\Models\Issuance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IssuanceController extends Controller
{
    // --- PUBLIC VIEWS (Kept exactly as you wrote them) ---
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

    public function show(Issuance $issuance)
    {
        // Fetch recent advisories (excluding the current one)
        $recentAdvisories = Issuance::where('type', 'advisory')
                                    ->where('id', '!=', $issuance->id)
                                    ->latest()
                                    ->take(5)
                                    ->get();

        // Fetch recent memoranda (excluding the current one)
        $recentMemoranda = Issuance::where('type', 'memorandum')
                                   ->where('id', '!=', $issuance->id)
                                   ->latest()
                                   ->take(5)
                                   ->get();

        return view('issuances.show', compact('issuance', 'recentAdvisories', 'recentMemoranda'));
    }

    // --- ADMIN CRUD METHODS ---
    public function adminIndex(Request $request)
    {
        // Get the type from the URL (e.g., ?type=memorandum), default to advisory
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
            'pdf_file' => 'required|mimes:pdf|max:10240', // Max 10MB PDF
        ]);

        // Upload the file to local public storage
        $path = $request->file('pdf_file')->store('issuances/' . $validated['type'], 'public');
        
        // Save to database
        Issuance::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'pdf_path' => $path,
        ]);

        return back()->with('success', ucfirst($validated['type']) . ' uploaded successfully!');
    }

    public function update(Request $request, Issuance $issuance)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string', 
            'pdf_file' => 'nullable|mimes:pdf|max:10240',
        ]);

        $dataToUpdate = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
        ];

        // Replace PDF if a new one is uploaded
        if ($request->hasFile('pdf_file')) {
            
            // Delete old file from local storage
            if ($issuance->pdf_path) {
                Storage::disk('public')->delete($issuance->pdf_path);
            }
            
            // Upload the new file to local public storage
            $dataToUpdate['pdf_path'] = $request->file('pdf_file')->store('issuances/' . $issuance->type, 'public');
        }

        $issuance->update($dataToUpdate);

        return back()->with('success', 'Issuance updated successfully!');
    }

    public function destroy(Issuance $issuance)
    {
        // Delete PDF file from local storage
        if ($issuance->pdf_path) {
            Storage::disk('public')->delete($issuance->pdf_path);
        }
        
        // Delete database record
        $issuance->delete();

        return back()->with('success', 'Issuance deleted successfully!');
    }
    public function globalSearch(Request $request)
    {
        $query = $request->input('q');
        
        // Search across title and description of all issuances
        $results = \App\Models\Issuance::where('title', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        // Append the query string to pagination links so they don't break when navigating pages
        $results->appends(['q' => $query]);

        return view('frontend.search_results', compact('results', 'query'));
    }
}