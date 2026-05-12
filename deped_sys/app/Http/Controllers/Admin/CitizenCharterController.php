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
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'pdf_file' => 'nullable|mimes:pdf|max:10240', // Max 10MB PDF
            'pdf_name' => 'nullable|string|max:255',
            'links' => 'nullable|array',
            'links.*.name' => 'required_with:links|string',
            'links.*.url' => 'required_with:links|url',
        ]);

        $data = CitizenCharter::first();
        
        // 1. Handle explicit PDF removal via checkbox
        if ($request->has('remove_pdf') && $request->remove_pdf == 1) {
            if ($data->file_path && Storage::disk('public')->exists($data->file_path)) {
                Storage::disk('public')->delete($data->file_path);
            }
            $data->file_path = null;
            $data->file_name = null;
        }

        // 2. Handle New PDF Upload (Replaces old if it wasn't removed above)
        if ($request->hasFile('pdf_file')) {
            if ($data->file_path && Storage::disk('public')->exists($data->file_path)) {
                Storage::disk('public')->delete($data->file_path);
            }
            
            $file = $request->file('pdf_file');
            $path = $file->store('citizen_charters', 'public');
            $data->file_path = $path;
            
            $data->file_name = $request->pdf_name ?? $file->getClientOriginalName();
        } elseif ($request->filled('pdf_name') && $data->file_path) {
            // Just update the display name if the file wasn't changed
            $data->file_name = $request->pdf_name;
        }

        // 3. Save Text & Links
        $data->title = $request->title;
        $data->content = $request->content;
        $data->links = $request->has('links') ? array_values($request->links) : [];

        $data->save();

        return redirect()->route('admin.citizen_charter.index')
                         ->with('success', 'Citizen\'s Charter updated successfully.');
    }
}