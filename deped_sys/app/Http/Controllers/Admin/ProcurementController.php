<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BidOpportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProcurementController extends Controller
{
    /**
     * Display a listing of the resource.
     * THIS FIXES YOUR 500 ERROR!
     */
    public function index()
    {
        // Fetch the bid opportunities from the database, newest first
        $bidOpportunities = BidOpportunity::latest()->paginate(10);

        // Return the view and pass the data to it
        return view('admin.bid-opportunities.index', compact('bidOpportunities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string', // Add validation
            'jpeg_file' => 'required|image|mimes:jpeg,jpg|max:5120',
            'pdf_file' => 'required|mimes:pdf|max:10240',
        ]);

        // Create a folder path based on the current month and year
        $folderPath = 'bid_opportunities/' . now()->format('F_Y');

        // Upload Files to local public storage
        $jpegPath = $request->file('jpeg_file')->store($folderPath, 'public');
        $pdfPath = $request->file('pdf_file')->store($folderPath, 'public');

        // Create Local Record (Storing local paths instead of Drive IDs)
        BidOpportunity::create([
            'title' => $request->title,
            'description' => $request->description,
            'jpeg_path' => $jpegPath,
            'pdf_path' => $pdfPath,
        ]);

        return redirect()->route('admin.bid-opportunities.index')
                         ->with('success', 'Bid Opportunity uploaded successfully.');
    }
}   