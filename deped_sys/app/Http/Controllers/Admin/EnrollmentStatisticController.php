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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'file' => 'nullable|mimes:pdf,xlsx,xls,csv|max:10240',
        ]);

        $data = $request->only(['title', 'school_year', 'content']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('enrollment_statistics/images', 'public');
        }

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('enrollment_statistics/files', 'public');
        }

        EnrollmentStatistic::create($data);

        return back()->with('success', 'Enrollment Statistic created successfully.');
    }

    public function update(Request $request, EnrollmentStatistic $enrollmentStatistic)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'school_year' => 'nullable|string|max:50',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'file' => 'nullable|mimes:pdf,xlsx,xls,csv|max:10240',
        ]);

        $data = $request->only(['title', 'school_year', 'content']);

        if ($request->hasFile('image')) {
            if ($enrollmentStatistic->image_path) Storage::disk('public')->delete($enrollmentStatistic->image_path);
            $data['image_path'] = $request->file('image')->store('enrollment_statistics/images', 'public');
        }

        if ($request->hasFile('file')) {
            if ($enrollmentStatistic->file_path) Storage::disk('public')->delete($enrollmentStatistic->file_path);
            $data['file_path'] = $request->file('file')->store('enrollment_statistics/files', 'public');
        }

        $enrollmentStatistic->update($data);

        return back()->with('success', 'Enrollment Statistic updated successfully.');
    }

    public function destroy(EnrollmentStatistic $enrollmentStatistic)
    {
        if ($enrollmentStatistic->image_path) Storage::disk('public')->delete($enrollmentStatistic->image_path);
        if ($enrollmentStatistic->file_path) Storage::disk('public')->delete($enrollmentStatistic->file_path);
        
        $enrollmentStatistic->delete();

        return back()->with('success', 'Enrollment Statistic deleted successfully.');
    }
}
