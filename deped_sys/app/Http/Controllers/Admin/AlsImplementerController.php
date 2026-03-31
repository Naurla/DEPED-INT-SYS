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
        $implementers = AlsImplementer::latest()->paginate(5);
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
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $data['image_path'] = $file->storeAs('als_implementers/images', $filename, 'public');
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $data['file_path'] = $file->storeAs('als_implementers/files', $filename, 'public');
        }

        AlsImplementer::create($data);

        return back()->with('success', 'ALS Implementer created successfully.');
    }

    public function update(Request $request, $id)
    {
        $alsImplementer = AlsImplementer::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'file' => 'nullable|mimes:pdf,xlsx,xls,csv,doc,docx|max:10240',
        ]);

        $data = $request->only(['title', 'content']);

        if ($request->hasFile('image')) {
            if ($alsImplementer->image_path) Storage::disk('public')->delete($alsImplementer->image_path);
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $data['image_path'] = $file->storeAs('als_implementers/images', $filename, 'public');
        }

        if ($request->hasFile('file')) {
            if ($alsImplementer->file_path) Storage::disk('public')->delete($alsImplementer->file_path);
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $data['file_path'] = $file->storeAs('als_implementers/files', $filename, 'public');
        }

        $alsImplementer->update($data);

        return back()->with('success', 'ALS Implementer updated successfully.');
    }

    public function destroy($id)
    {
        $alsImplementer = AlsImplementer::findOrFail($id);

        if ($alsImplementer->image_path) Storage::disk('public')->delete($alsImplementer->image_path);
        if ($alsImplementer->file_path) Storage::disk('public')->delete($alsImplementer->file_path);
        
        $alsImplementer->delete();

        return back()->with('success', 'ALS Implementer deleted successfully.');
    }
}