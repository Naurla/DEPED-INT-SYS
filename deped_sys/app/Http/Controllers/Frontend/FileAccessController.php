<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BidOpportunity;
use Illuminate\Support\Facades\Storage;

class FileAccessController extends Controller
{
    public function show($id, $type)
    {
        // 1. STRICT DOMAIN CHECK
        // Check if the currently logged in user's email ends with @deped.gov.ph
        if (!str_ends_with(auth()->user()->email, '@wmsu.edu.ph')) {
            // If they are logged in with @gmail.com or anything else, block them completely.
            abort(403, 'Access Blocked: This document is strictly restricted to @deped.gov.ph accounts only.');
        }

        // 2. Find the opportunity
        $opportunity = BidOpportunity::findOrFail($id);

        // 3. Determine the private path based on the type
        $path = ($type === 'jpeg') ? $opportunity->jpeg_path : $opportunity->pdf_path;

        // 4. Verify the file exists in private storage
        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'File not found in private storage.');
        }

        // 5. Return the file as a dynamic secure response
        return response()->file(Storage::disk('local')->path($path));
    }
}