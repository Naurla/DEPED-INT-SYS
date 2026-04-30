<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlsStory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlsStoryController extends Controller
{
    public function index()
    {
        $stories = AlsStory::latest()->paginate(7);
        return view('admin.als_stories.index', compact('stories'));
    }

    public function store(Request $request)
    {
        // Added unique validation rule and custom error messages
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
            $filename = $file->getClientOriginalName();
            $data['image_path'] = $file->storeAs('als_stories/images', $filename, 'public');
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $data['file_path'] = $file->storeAs('als_stories/files', $filename, 'public');
        }

        AlsStory::create($data);

        return back()->with('success', 'ALS Story created successfully.');
    }

    public function update(Request $request, $id)
    {
        $alsStory = AlsStory::findOrFail($id);

        // Added unique validation rule that ignores the current record
        $request->validate([
            'title' => 'required|string|max:255|unique:als_stories,title,' . $id,
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'file' => 'nullable|mimes:pdf,xlsx,xls,csv,doc,docx|max:10240',
        ], [
            'title.unique' => 'This Story Title already exists. Please provide a unique entry.'
        ]);

        $data = $request->only(['title', 'content']);

        if ($request->hasFile('image')) {
            if ($alsStory->image_path) Storage::disk('public')->delete($alsStory->image_path);
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $data['image_path'] = $file->storeAs('als_stories/images', $filename, 'public');
        }

        if ($request->hasFile('file')) {
            if ($alsStory->file_path) Storage::disk('public')->delete($alsStory->file_path);
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
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