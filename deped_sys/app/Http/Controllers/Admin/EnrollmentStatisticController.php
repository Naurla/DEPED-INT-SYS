<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EnrollmentStatistic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EnrollmentStatisticController extends Controller
{
    public function index()
    {
        $statistics = EnrollmentStatistic::latest()->get();
        return view('admin.enrollment_statistics.index', compact('statistics'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'school_year' => 'nullable|string|max:50',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'file' => 'nullable|mimes:pdf,xlsx,xls,csv|max:10240',
        ]);

        $data = $request->only(['title', 'school_year', 'content']);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $data['image_path'] = $file->storeAs('enrollment_statistics/images', $filename, 'public');
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $data['file_path'] = $file->storeAs('enrollment_statistics/files', $filename, 'public');
        }

        EnrollmentStatistic::create($data);

        return back()->with('success', 'Enrollment Statistic created successfully.');
    }

    public function update(Request $request, $id)
    {
        $enrollmentStatistic = EnrollmentStatistic::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'school_year' => 'nullable|string|max:50',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'file' => 'nullable|mimes:pdf,xlsx,xls,csv|max:10240',
        ]);

        $data = $request->only(['title', 'school_year', 'content']);

        if ($request->hasFile('image')) {
            if ($enrollmentStatistic->image_path) Storage::disk('public')->delete($enrollmentStatistic->image_path);
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $data['image_path'] = $file->storeAs('enrollment_statistics/images', $filename, 'public');
        }

        if ($request->hasFile('file')) {
            if ($enrollmentStatistic->file_path) Storage::disk('public')->delete($enrollmentStatistic->file_path);
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $data['file_path'] = $file->storeAs('enrollment_statistics/files', $filename, 'public');
        }

        $enrollmentStatistic->update($data);

        return back()->with('success', 'Enrollment Statistic updated successfully.');
    }

    public function destroy($id)
    {
        $enrollmentStatistic = EnrollmentStatistic::findOrFail($id);

        if ($enrollmentStatistic->image_path) Storage::disk('public')->delete($enrollmentStatistic->image_path);
        if ($enrollmentStatistic->file_path) Storage::disk('public')->delete($enrollmentStatistic->file_path);
        
        $enrollmentStatistic->delete();

        return back()->with('success', 'Enrollment Statistic deleted successfully.');
    }
}