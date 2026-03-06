<?php

namespace App\Http\Controllers;

use App\Models\Issuance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IssuanceController extends Controller
{
    // --- PUBLIC VIEWS ---
    public function advisories()
    {
        $items = Issuance::where('type', 'advisory')->latest()->paginate(10);
        
        return view('issuances.category', [
            'items' => $items,
            'title' => 'Division Advisories',
            'color' => 'red',
        ]);
    }

    public function memoranda()
    {
        $items = Issuance::where('type', 'memorandum')->latest()->paginate(10);
        
        return view('issuances.category', [
            'items' => $items,
            'title' => 'Division Memoranda',
            'color' => 'blue',
        ]);
    }

    public function hrmpsb()
    {
        $items = Issuance::where('type', 'hrmpsb')->latest()->paginate(10);
        
        return view('issuances.category', [
            'items' => $items,
            'title' => 'HRMPSB Assessment Results',
            'color' => 'yellow',
        ]);
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
            'description' => 'nullable|string', // Validating description
            'type' => 'required|in:advisory,memorandum,hrmpsb',
            'pdf_file' => 'required|mimes:pdf|max:10240', // Max 10MB PDF
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' // Optional thumbnail
        ]);

        $pdfPath = $request->file('pdf_file')->store('issuances/pdfs', 'public');
        
        $imagePath = null;
        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('issuances/thumbnails', 'public');
        }

        Issuance::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null, // SAVING description to database
            'type' => $validated['type'],
            'pdf_path' => $pdfPath,
            'image_path' => $imagePath,
        ]);

        return back()->with('success', ucfirst($validated['type']) . ' uploaded successfully!');
    }

    public function update(Request $request, Issuance $issuance)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string', // Added description validation here
            'pdf_file' => 'nullable|mimes:pdf|max:10240',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Added description to the fields being updated
        $dataToUpdate = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
        ];

        // Replace PDF if a new one is uploaded
        if ($request->hasFile('pdf_file')) {
            if ($issuance->pdf_path) Storage::disk('public')->delete($issuance->pdf_path);
            $dataToUpdate['pdf_path'] = $request->file('pdf_file')->store('issuances/pdfs', 'public');
        }

        // Replace Thumbnail if a new one is uploaded
        if ($request->hasFile('image_file')) {
            if ($issuance->image_path) Storage::disk('public')->delete($issuance->image_path);
            $dataToUpdate['image_path'] = $request->file('image_file')->store('issuances/thumbnails', 'public');
        }

        $issuance->update($dataToUpdate);

        return back()->with('success', 'Issuance updated successfully!');
    }

    public function destroy(Issuance $issuance)
    {
        // Delete files from storage
        if ($issuance->pdf_path) Storage::disk('public')->delete($issuance->pdf_path);
        if ($issuance->image_path) Storage::disk('public')->delete($issuance->image_path);
        
        // Delete database record
        $issuance->delete();

        return back()->with('success', 'Issuance deleted successfully!');
    }

    public function show(Issuance $issuance)
    {
        // Fetch recent advisories (excluding the current one if it's an advisory)
        $recentAdvisories = Issuance::where('type', 'advisory')
                                    ->where('id', '!=', $issuance->id)
                                    ->latest()
                                    ->take(5)
                                    ->get();

        // Fetch recent memoranda (excluding the current one if it's a memo)
        $recentMemoranda = Issuance::where('type', 'memorandum')
                                   ->where('id', '!=', $issuance->id)
                                   ->latest()
                                   ->take(5)
                                   ->get();

        return view('issuances.show', compact('issuance', 'recentAdvisories', 'recentMemoranda'));
    }
}