<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Osds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OsdsController extends Controller
{
    public function index()
    {
        $osds = Osds::latest()->get();
        return view('admin.osds.index', compact('osds'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // webp allowed!
        ]);

        $data = $request->only(['title', 'description']);
        $data['image_path'] = $request->file('image')->store('osds/images', 'public');

        Osds::create($data);

        return back()->with('success', 'OSDS Chart uploaded successfully.');
    }

    public function update(Request $request, Osds $osds)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $data = $request->only(['title', 'description']);

        if ($request->hasFile('image')) {
            if ($osds->image_path) Storage::disk('public')->delete($osds->image_path);
            $data['image_path'] = $request->file('image')->store('osds/images', 'public');
        }

        $osds->update($data);

        return back()->with('success', 'OSDS Chart updated successfully.');
    }

    public function destroy(Osds $osds)
    {
        if ($osds->image_path) Storage::disk('public')->delete($osds->image_path);
        $osds->delete();

        return back()->with('success', 'OSDS Chart deleted successfully.');
    }
}