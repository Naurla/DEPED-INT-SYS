<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlsStory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlsStoryController extends Controller
{
    public function index(Request $request)
    {
        $query = AlsStory::query();

        // 1. Search Filter (Title & Content)
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

        // withQueryString() keeps filters active during pagination
        $stories = $query->paginate(10)->withQueryString();

        return view('admin.als_stories.index', compact('stories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:als_stories,title',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'file' => 'nullable|mimes:pdf,xlsx,xls,csv,doc,docx|max:10240',
        ], [
            'title.unique' => 'This Story Title already exists. Please provide a unique entry.'
        ]);

        $data = $request->only(['title', 'content']);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $data['image_path'] = $file->storeAs('als_stories/images', $filename, 'public');
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $data['file_path'] = $file->storeAs('als_stories/files', $filename, 'public');
        }

        AlsStory::create($data);

        return back()->with('success', 'ALS Story created successfully.');
    }

    public function update(Request $request, $id)
    {
        $alsStory = AlsStory::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255|unique:als_stories,title,' . $id,
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'file' => 'nullable|mimes:pdf,xlsx,xls,csv,doc,docx|max:10240',
        ], [
            'title.unique' => 'This Story Title already exists. Please provide a unique entry.'
        ]);

        $data = $request->only(['title', 'content']);

        // Explicitly remove existing files if the user clicked the trash icon
        if ($request->remove_image == '1') {
            if ($alsStory->image_path) Storage::disk('public')->delete($alsStory->image_path);
            $data['image_path'] = null;
        }

        if ($request->remove_file == '1') {
            if ($alsStory->file_path) Storage::disk('public')->delete($alsStory->file_path);
            $data['file_path'] = null;
        }

        // Process new file uploads
        if ($request->hasFile('image')) {
            if ($alsStory->image_path) Storage::disk('public')->delete($alsStory->image_path);
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $data['image_path'] = $file->storeAs('als_stories/images', $filename, 'public');
        }

        if ($request->hasFile('file')) {
            if ($alsStory->file_path) Storage::disk('public')->delete($alsStory->file_path);
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $data['file_path'] = $file->storeAs('als_stories/files', $filename, 'public');
        }

        $alsStory->update($data);

        return back()->with('success', 'ALS Story updated successfully.');
    }

    public function destroy($id)
    {
        $alsStory = AlsStory::findOrFail($id);

        if ($alsStory->image_path) Storage::disk('public')->delete($alsStory->image_path);
        if ($alsStory->file_path) Storage::disk('public')->delete($alsStory->file_path);
        
        $alsStory->delete();

        return back()->with('success', 'ALS Story deleted successfully.');
    }
}