<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sgod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SgodController extends Controller
{
    public function index()
    {
        $sgods = Sgod::latest()->get();
        return view('admin.sgod.index', compact('sgods'));
    }

    public function store(Request $request)
    {
        $messages = [
            'title.unique' => 'This Title already exists. Please provide a unique entry.',
        ];

        $request->validate([
            'title' => 'required|string|max:255|unique:sgods,title',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], $messages);

        $data = $request->only(['title', 'description']);
        
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('sgod/images', 'public');
        }

        Sgod::create($data);

        return back()->with('success', 'SGOD Chart uploaded successfully.');
    }

    public function update(Request $request, Sgod $sgod)
    {
        $messages = [
            'title.unique' => 'This Title already exists. Please provide a unique entry.',
        ];

        $request->validate([
            // Ignore the current record's ID to allow updating without triggering the unique error on itself
            'title' => 'required|string|max:255|unique:sgods,title,' . $sgod->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], $messages);

        $data = $request->only(['title', 'description']);

        if ($request->hasFile('image')) {
            if ($sgod->image_path) Storage::disk('public')->delete($sgod->image_path);
            $data['image_path'] = $request->file('image')->store('sgod/images', 'public');
        }

        $sgod->update($data);

        return back()->with('success', 'SGOD Chart updated successfully.');
    }

    public function destroy(Sgod $sgod)
    {
        if ($sgod->image_path) Storage::disk('public')->delete($sgod->image_path);
        $sgod->delete();

        return back()->with('success', 'SGOD Chart deleted successfully.');
    }
}