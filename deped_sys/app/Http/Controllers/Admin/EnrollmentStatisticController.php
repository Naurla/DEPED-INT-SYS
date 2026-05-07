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
        // Changed to paginate by 5
        $statistics = EnrollmentStatistic::latest()->paginate(10);
        return view('admin.enrollment_statistics.index', compact('statistics'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:enrollment_statistics,title',
            'school_year' => 'nullable|string|max:50',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'file' => 'nullable|mimes:pdf,xlsx,xls,csv|max:10240',
        ], [
            'title.required' => 'Please provide a title.',
            'title.unique' => 'This title already exists. Please provide a unique entry.',
            'image.image' => 'The attachment must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, webp.',
            'image.max' => 'The image must not be larger than 2MB.',
            'file.mimes' => 'The document must be a file of type: pdf, xlsx, xls, csv.',
            'file.max' => 'The document must not exceed 10MB in size.',
        ]);

        $data = $request->only(['title', 'school_year', 'content']);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $data['image_path'] = $file->storeAs('enrollment_statistics/images', $filename, 'public');
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $data['file_path'] = $file->storeAs('enrollment_statistics/files', $filename, 'public');
        }

        EnrollmentStatistic::create($data);

        return back()->with('success', 'Enrollment Statistic created successfully.');
    }

    public function update(Request $request, $id)
    {
        $enrollmentStatistic = EnrollmentStatistic::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255|unique:enrollment_statistics,title,' . $id,
            'school_year' => 'nullable|string|max:50',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'file' => 'nullable|mimes:pdf,xlsx,xls,csv|max:10240',
        ], [
            'title.required' => 'Please provide a title.',
            'title.unique' => 'This title already exists. Please provide a unique entry.',
            'image.image' => 'The attachment must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, webp.',
            'image.max' => 'The image must not be larger than 2MB.',
            'file.mimes' => 'The document must be a file of type: pdf, xlsx, xls, csv.',
            'file.max' => 'The document must not exceed 10MB in size.',
        ]);

        $data = $request->only(['title', 'school_year', 'content']);

        // Handle explicit image removal
        if ($request->input('remove_image') == '1' && $enrollmentStatistic->image_path) {
            Storage::disk('public')->delete($enrollmentStatistic->image_path);
            $data['image_path'] = null;
        }

        // Handle new image upload
        if ($request->hasFile('image')) {
            if ($enrollmentStatistic->image_path) Storage::disk('public')->delete($enrollmentStatistic->image_path);
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $data['image_path'] = $file->storeAs('enrollment_statistics/images', $filename, 'public');
        }

        // Handle explicit file removal
        if ($request->input('remove_file') == '1' && $enrollmentStatistic->file_path) {
            Storage::disk('public')->delete($enrollmentStatistic->file_path);
            $data['file_path'] = null;
        }

        // Handle new file upload
        if ($request->hasFile('file')) {
            if ($enrollmentStatistic->file_path) Storage::disk('public')->delete($enrollmentStatistic->file_path);
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
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