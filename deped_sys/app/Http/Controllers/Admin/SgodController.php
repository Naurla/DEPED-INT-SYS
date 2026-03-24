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
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
           'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $request->only(['title', 'description']);
        $data['image_path'] = $request->file('image')->store('sgod/images', 'public');

        Sgod::create($data);

        return back()->with('success', 'SGOD Chart uploaded successfully.');
    }

    public function update(Request $request, Sgod $sgod)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

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