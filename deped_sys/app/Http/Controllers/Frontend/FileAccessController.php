<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BidOpportunity;
use Illuminate\Support\Facades\Storage;

class FileAccessController extends Controller
{
    public function show($id, $type)
    {
        // 1. CHECK IF LOGGED IN
        if (!auth()->check()) {
            abort(403, 'Access Blocked: You must be logged in to view this document.');
        }

        // 2. STRICT DOMAIN CHECK (Uncomment if needed)
        // if (!str_ends_with(auth()->user()->email, '@wmsu.edu.ph')) {
        //     abort(403, 'Access Blocked: This document is strictly restricted to @wmsu.edu.ph accounts only.');
        // }

        // 3. Find the opportunity
        $opportunity = BidOpportunity::findOrFail($id);

        // 4. Get the local file path based on the type requested
        $filePath = ($type === 'jpeg') ? $opportunity->jpeg_path : $opportunity->pdf_path;

        if (!$filePath) {
            abort(404, 'File path not found in the database.');
        }

        // 5. Check if the file actually exists on your local server disk
        // Assuming you are saving files to the 'public' disk (storage/app/public/...)
        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File does not exist on the server.');
        }

        // 6. Return the file directly to the browser with explicit headers
        // This guarantees the browser knows it's a PDF and renders it inside the iframe natively!
        $absolutePath = Storage::disk('public')->path($filePath);
        $mimeType = Storage::disk('public')->mimeType($filePath);
        
        return response()->file($absolutePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
        ]);
    }
}