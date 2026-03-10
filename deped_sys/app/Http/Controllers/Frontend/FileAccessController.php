<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BidOpportunity;

class FileAccessController extends Controller
{
    public function show($id, $type)
    {
        // 1. CHECK IF LOGGED IN
        if (!auth()->check()) {
            abort(403, 'Access Blocked: You must be logged in to view this document.');
        }

        // 2. STRICT DOMAIN CHECK
        // Check if the currently logged in user's email ends with @wmsu.edu.ph
        if (!str_ends_with(auth()->user()->email, '@wmsu.edu.ph')) {
            abort(403, 'Access Blocked: This document is strictly restricted to @wmsu.edu.ph accounts only.');
        }

        // 3. Find the opportunity
        $opportunity = BidOpportunity::findOrFail($id);

        // 4. Get the Google Drive ID based on the type requested
        $driveId = ($type === 'jpeg') ? $opportunity->jpeg_path : $opportunity->pdf_path;

        if (!$driveId) {
            abort(404, 'File not found.');
        }

        // 5. Redirect the authorized user to the secure Google Drive viewer/image
        if ($type === 'jpeg') {
            // For images (loaded in the <img> tag)
            return redirect("https://drive.google.com/thumbnail?id={$driveId}&sz=w1000");
        } else {
            // For PDFs (loaded when clicking the download/view button)
            return redirect("https://drive.google.com/file/d/{$driveId}/view");
        }
    }
}