<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataPrivacy;
use Illuminate\Http\Request;

class DataPrivacyController extends Controller
{
    public function index()
    {
        // Get the first record or create an empty one
        $data = DataPrivacy::firstOrCreate([]);
        return view('admin.data_privacy.index', compact('data'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'notice' => 'nullable|string',
        ]);

        $data = DataPrivacy::first();
        $data->update($request->all());

        return redirect()->route('admin.data_privacy.index')->with('success', 'Data Privacy Notice updated successfully.');
    }
}