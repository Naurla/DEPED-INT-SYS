<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::latest()->paginate(10);
        return view('admin.faq.index', compact('faqs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|array', // Validating as an array now
            'answer.*' => 'nullable|string', 
            'is_active' => 'boolean'
        ]);

        // Filter out any empty bullet points and implode with a newline
        $formattedAnswer = implode("\n", array_filter($request->answer, fn($val) => !is_null($val) && $val !== ''));

        Faq::create([
            'question' => $request->question,
            'answer' => $formattedAnswer,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.faq.index')->with('success', 'FAQ added successfully.');
    }

    public function update(Request $request, Faq $faq)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|array', // Validating as an array now
            'answer.*' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        // Filter out any empty bullet points and implode with a newline
        $formattedAnswer = implode("\n", array_filter($request->answer, fn($val) => !is_null($val) && $val !== ''));

        $faq->update([
            'question' => $request->question,
            'answer' => $formattedAnswer,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.faq.index')->with('success', 'FAQ updated successfully.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        return redirect()->route('admin.faq.index')->with('success', 'FAQ deleted successfully.');
    }
}