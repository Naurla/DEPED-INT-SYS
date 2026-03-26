<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CidController extends Controller
{
    public function index()
    {
        $cids = Cid::latest()->paginate(5);;
        return view('admin.cid.index', compact('cids'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', 
        ]);

        $data = $request->only(['title', 'description']);
        $data['image_path'] = $request->file('image')->store('cid/images', 'public');

        Cid::create($data);

        return back()->with('success', 'CID Chart uploaded successfully.');
    }

    public function update(Request $request, Cid $cid)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $data = $request->only(['title', 'description']);

        if ($request->hasFile('image')) {
            if ($cid->image_path) Storage::disk('public')->delete($cid->image_path);
            $data['image_path'] = $request->file('image')->store('cid/images', 'public');
        }

        $cid->update($data);

        return back()->with('success', 'CID Chart updated successfully.');
    }

    public function destroy(Cid $cid)
    {
        if ($cid->image_path) Storage::disk('public')->delete($cid->image_path);
        $cid->delete();

        return back()->with('success', 'CID Chart deleted successfully.');
    }
}