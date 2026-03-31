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
            'pdf_file' => 'required|mimes:pdf|max:10240', 
        ]);

        $pdfFile = $request->file('pdf_file');
        $pdfFilename = $pdfFile->getClientOriginalName();
        $path = $pdfFile->storeAs('issuances/' . $validated['type'], $pdfFilename, 'public');
        
        Issuance::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'pdf_path' => $path,
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
        ]);

        $dataToUpdate = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
        ];

        if ($request->hasFile('pdf_file')) {
            if ($issuance->pdf_path) {
                Storage::disk('public')->delete($issuance->pdf_path);
            }
            
            $pdfFile = $request->file('pdf_file');
            $pdfFilename = $pdfFile->getClientOriginalName();
            $dataToUpdate['pdf_path'] = $pdfFile->storeAs('issuances/' . $issuance->type, $pdfFilename, 'public');
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