<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlsImplementer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlsImplementerController extends Controller
{
    public function index()
    {
        $implementers = AlsImplementer::latest()->get();
        return view('admin.als_implementers.index', compact('implementers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'file' => 'nullable|mimes:pdf,xlsx,xls,csv,doc,docx|max:10240',
        ]);

        $data = $request->only(['title', 'content']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('als_implementers/images', 'public');
        }

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('als_implementers/files', 'public');
        }

        AlsImplementer::create($data);

        return back()->with('success', 'ALS Implementer created successfully.');
    }

    public function update(Request $request, AlsImplementer $alsImplementer)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'file' => 'nullable|mimes:pdf,xlsx,xls,csv,doc,docx|max:10240',
        ]);

        $data = $request->only(['title', 'content']);

        if ($request->hasFile('image')) {
            if ($alsImplementer->image_path) Storage::disk('public')->delete($alsImplementer->image_path);
            $data['image_path'] = $request->file('image')->store('als_implementers/images', 'public');
        }

        if ($request->hasFile('file')) {
            if ($alsImplementer->file_path) Storage::disk('public')->delete($alsImplementer->file_path);
            $data['file_path'] = $request->file('file')->store('als_implementers/files', 'public');
        }

        $alsImplementer->update($data);

        return back()->with('success', 'ALS Implementer updated successfully.');
    }

    public function destroy(AlsImplementer $alsImplementer)
    {
        if ($alsImplementer->image_path) Storage::disk('public')->delete($alsImplementer->image_path);
        if ($alsImplementer->file_path) Storage::disk('public')->delete($alsImplementer->file_path);
        
        $alsImplementer->delete();

        return back()->with('success', 'ALS Implementer deleted successfully.');
    }
}