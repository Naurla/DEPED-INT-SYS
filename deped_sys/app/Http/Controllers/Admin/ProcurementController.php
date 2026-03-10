<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BidOpportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProcurementController extends Controller
{
    // List all bid opportunities for the admin
    public function index()
    {
        $opportunities = BidOpportunity::latest()->get();
        return view('admin.procurement.index', compact('opportunities'));
    }

    // Show the add form
    public function create()
    {
        return view('admin.procurement.create');
    }

    // Handle form submission and store files securely (NO DESCRIPTION REQUIRED)
    public function store(Request $request)
    {
        // 1. Validate Input (Only title and files now)
        $request->validate([
            'title' => 'required|string|max:255',
            'jpeg_file' => 'required|image|mimes:jpeg,jpg|max:5120', // Max 5MB
            'pdf_file' => 'required|mimes:pdf|max:10240', // Max 10MB
        ]);

        // 2. Handle File Uploads
        $jpegName = time() . '_bid_' . $request->file('jpeg_file')->getClientOriginalName();
        $pdfName = time() . '_bid_' . $request->file('pdf_file')->getClientOriginalName();

        $jpegPath = $request->file('jpeg_file')->storeAs('secure_bids', $jpegName, 'local');
        $pdfPath = $request->file('pdf_file')->storeAs('secure_bids', $pdfName, 'local');

        // 3. Create the Database Record
        BidOpportunity::create([
            'title' => $request->title,
            'jpeg_path' => $jpegPath,
            'pdf_path' => $pdfPath,
        ]);

        return redirect()->route('admin.bid-opportunities.index')
                         ->with('success', 'Bid Opportunity created successfully.');
    }

    public function show(string $id)
    {
        // Not used in admin panel
    }

    public function edit(string $id)
    {
        $opportunity = BidOpportunity::findOrFail($id);
        return view('admin.procurement.edit', compact('opportunity'));
    }

    public function update(Request $request, string $id)
    {
        $opportunity = BidOpportunity::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'jpeg_file' => 'nullable|image|mimes:jpeg,jpg|max:5120',
            'pdf_file' => 'nullable|mimes:pdf|max:10240',
        ]);

        $opportunity->title = $request->title;

        // If a new JPEG is uploaded
        if ($request->hasFile('jpeg_file')) {
            if (Storage::disk('local')->exists($opportunity->jpeg_path)) {
                Storage::disk('local')->delete($opportunity->jpeg_path);
            }
            $jpegName = time() . '_bid_' . $request->file('jpeg_file')->getClientOriginalName();
            $opportunity->jpeg_path = $request->file('jpeg_file')->storeAs('secure_bids', $jpegName, 'local');
        }

        // If a new PDF is uploaded
        if ($request->hasFile('pdf_file')) {
            if (Storage::disk('local')->exists($opportunity->pdf_path)) {
                Storage::disk('local')->delete($opportunity->pdf_path);
            }
            $pdfName = time() . '_bid_' . $request->file('pdf_file')->getClientOriginalName();
            $opportunity->pdf_path = $request->file('pdf_file')->storeAs('secure_bids', $pdfName, 'local');
        }

        $opportunity->save();

        return redirect()->route('admin.bid-opportunities.index')
                         ->with('success', 'Bid Opportunity updated successfully.');
    }

    public function destroy(string $id)
    {
        $opportunity = BidOpportunity::findOrFail($id);

        if ($opportunity->jpeg_path && Storage::disk('local')->exists($opportunity->jpeg_path)) {
            Storage::disk('local')->delete($opportunity->jpeg_path);
        }
        
        if ($opportunity->pdf_path && Storage::disk('local')->exists($opportunity->pdf_path)) {
            Storage::disk('local')->delete($opportunity->pdf_path);
        }

        $opportunity->delete();

        return back()->with('success', 'Bid Opportunity deleted successfully.');
    }
}