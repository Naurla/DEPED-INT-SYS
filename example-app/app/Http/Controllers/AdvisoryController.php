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
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
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
}