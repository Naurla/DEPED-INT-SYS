<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CitizenCharter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CitizenCharterController extends Controller
{
    public function index()
    {
        $data = CitizenCharter::firstOrCreate([]);
        return view('admin.citizen_charter.index', compact('data'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'content' => 'nullable|string',
            'pdf_file' => 'nullable|mimes:pdf|max:10240', // Max 10MB PDF
            'pdf_name' => 'nullable|string|max:255',
            'links' => 'nullable|array',
            'links.*.name' => 'required_with:links|string',
            'links.*.url' => 'required_with:links|url',
        ]);

        $data = CitizenCharter::first();
        
        // Handle PDF Upload
        if ($request->hasFile('pdf_file')) {
            // Delete old file if exists
            if ($data->file_path && Storage::disk('public')->exists($data->file_path)) {
                Storage::disk('public')->delete($data->file_path);
            }
            
            $file = $request->file('pdf_file');
            $path = $file->store('citizen_charters', 'public');
            $data->file_path = $path;
            
            // If no custom name is provided, use the original file name
            $data->file_name = $request->pdf_name ?? $file->getClientOriginalName();
        } elseif ($request->filled('pdf_name')) {
            // Just update the name if the file wasn't changed
            $data->file_name = $request->pdf_name;
        }

        // Prepare the rest of the data
        $data->content = $request->content;
        
        // Ensure links are re-indexed properly if some were removed
        if ($request->has('links')) {
            $data->links = array_values($request->links);
        } else {
            $data->links = [];
        }

        $data->save();

        return redirect()->route('admin.citizen_charter.index')->with('success', 'Citizen\'s Charter updated successfully.');
    }
}