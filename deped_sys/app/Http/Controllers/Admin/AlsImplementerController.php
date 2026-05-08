<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlsImplementer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlsImplementerController extends Controller
{
    public function index(Request $request)
    {
        $query = AlsImplementer::query();

        // 1. Search Filter (Title/Month & Description)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // 2. Sort Filter
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $query->oldest('created_at')->oldest('id');
                    break;
                case 'a_z':
                    $query->orderBy('title', 'asc');
                    break;
                case 'z_a':
                    $query->orderBy('title', 'desc');
                    break;
                case 'newest':
                default:
                    $query->latest('created_at')->latest('id');
                    break;
            }
        } else {
            $query->latest('created_at')->latest('id'); // Default Sort
        }

        // withQueryString() keeps search active on page 2, 3, etc.
        $implementers = $query->paginate(10)->withQueryString();

        return view('admin.als_implementers.index', compact('implementers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:als_implementers,title',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'file' => 'nullable|mimes:pdf,xlsx,xls,csv,doc,docx|max:10240',
        ], [
            'title.unique' => 'This Name / Month already exists. Please provide a unique entry.'
        ]);

        $data = $request->only(['title', 'content']);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $data['image_path'] = $file->storeAs('als_implementers/images', $filename, 'public');
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $data['file_path'] = $file->storeAs('als_implementers/files', $filename, 'public');
        }

        AlsImplementer::create($data);

        return back()->with('success', 'ALS Implementer created successfully.');
    }

    public function update(Request $request, $id)
    {
        $alsImplementer = AlsImplementer::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255|unique:als_implementers,title,' . $id,
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'file' => 'nullable|mimes:pdf,xlsx,xls,csv,doc,docx|max:10240',
        ], [
            'title.unique' => 'This Name / Month already exists. Please provide a unique entry.'
        ]);

        $data = $request->only(['title', 'content']);

        // Check explicit file removals
        if ($request->remove_image == '1') {
            if ($alsImplementer->image_path) Storage::disk('public')->delete($alsImplementer->image_path);
            $data['image_path'] = null;
        }

        if ($request->remove_file == '1') {
            if ($alsImplementer->file_path) Storage::disk('public')->delete($alsImplementer->file_path);
            $data['file_path'] = null;
        }

        // Check new file uploads
        if ($request->hasFile('image')) {
            if ($alsImplementer->image_path) Storage::disk('public')->delete($alsImplementer->image_path);
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $data['image_path'] = $file->storeAs('als_implementers/images', $filename, 'public');
        }

        if ($request->hasFile('file')) {
            if ($alsImplementer->file_path) Storage::disk('public')->delete($alsImplementer->file_path);
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
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