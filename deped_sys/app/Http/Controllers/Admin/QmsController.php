<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Qms;
use Illuminate\Http\Request;

class QmsController extends Controller
{
    public function index()
    {
        // Get the first record, or create an empty one if it doesn't exist
        $qms = Qms::firstOrCreate([]);
        
        return view('admin.qms.index', compact('qms'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'scope' => 'nullable|string',
            'policy' => 'nullable|string',
            'objective' => 'nullable|string',
        ]);

        $qms = Qms::first();
        $qms->update($request->all());

        return redirect()->route('admin.qms.index')->with('success', 'QMS information updated successfully.');
    }
}