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
        $stories = AlsStory::latest()->get();
        return view('admin.als_stories.index', compact('stories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'file' => 'nullable|mimes:pdf,xlsx,xls,csv,doc,docx|max:10240',
        ]);

        $data = $request->only(['title', 'content']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('als_stories/images', 'public');
        }

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('als_stories/files', 'public');
        }

        AlsStory::create($data);

        return back()->with('success', 'ALS Story created successfully.');
    }

    public function update(Request $request, AlsStory $alsStory)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'file' => 'nullable|mimes:pdf,xlsx,xls,csv,doc,docx|max:10240',
        ]);

        $data = $request->only(['title', 'content']);

        if ($request->hasFile('image')) {
            if ($alsStory->image_path) Storage::disk('public')->delete($alsStory->image_path);
            $data['image_path'] = $request->file('image')->store('als_stories/images', 'public');
        }

        if ($request->hasFile('file')) {
            if ($alsStory->file_path) Storage::disk('public')->delete($alsStory->file_path);
            $data['file_path'] = $request->file('file')->store('als_stories/files', 'public');
        }

        $alsStory->update($data);

        return back()->with('success', 'ALS Story updated successfully.');
    }

    public function destroy(AlsStory $alsStory)
    {
        if ($alsStory->image_path) Storage::disk('public')->delete($alsStory->image_path);
        if ($alsStory->file_path) Storage::disk('public')->delete($alsStory->file_path);
        
        $alsStory->delete();

        return back()->with('success', 'ALS Story deleted successfully.');
    }
}