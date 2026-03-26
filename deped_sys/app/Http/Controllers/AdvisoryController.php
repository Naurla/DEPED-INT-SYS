<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Advisory; // Ensure you have an Advisory model created
use Illuminate\Support\Facades\Storage;

class AdvisoryController extends Controller
{
    /**
     * Store a newly created advisory in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'pdf' => 'required|mimes:pdf|max:10000',
        ]);

        // Save files to the 'public' disk
        $imagePath = $request->file('image')->store('advisories/images', 'public');
        $pdfPath = $request->file('pdf')->store('advisories/pdfs', 'public');

        Advisory::create([
            'title' => $request->title,
            'image_path' => $imagePath,
            'pdf_path' => $pdfPath,
        ]);

        return back()->with('success', 'Advisory uploaded successfully!');
    }

   // app/Http/Controllers/AdvisoryController.php

public function index(Request $request)
    {
        $query = Advisory::query();

        // PostgreSQL Case-Insensitive Search
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            // Use ilike for PostgreSQL to ignore case
            $query->where('title', 'ilike', '%' . $searchTerm . '%');
        }

        // Date Filters
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        // CHANGE: Changed ->get() to ->paginate(10)
        $advisories = $query->latest()->paginate(5);

        return view('admin.advisories.index', compact('advisories'));
    }

public function destroy(Advisory $advisory)
{
    // Delete files first
    \Illuminate\Support\Facades\Storage::disk('public')->delete([$advisory->image_path, $advisory->pdf_path]);
    $advisory->delete();
    return back()->with('success', 'Advisory deleted!');
}

public function update(Request $request, Advisory $advisory)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'pdf' => 'nullable|mimes:pdf|max:10000',
    ]);

    // Update Title
    $advisory->title = $request->title;

    // Optional: Handle new image
    if ($request->hasFile('image')) {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($advisory->image_path);
        $advisory->image_path = $request->file('image')->store('advisories/images', 'public');
    }

    // Optional: Handle new PDF
    if ($request->hasFile('pdf')) {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($advisory->pdf_path);
        $advisory->pdf_path = $request->file('pdf')->store('advisories/pdfs', 'public');
    }

    $advisory->save();

    return back()->with('success', 'Advisory updated successfully!');
}
}