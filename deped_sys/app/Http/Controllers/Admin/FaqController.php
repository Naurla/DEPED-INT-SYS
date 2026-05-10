<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $query = Faq::query();

        // 1. Search Filter (Question & Answer)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                  ->orWhere('answer', 'like', "%{$search}%");
            });
        }

        // 2. Status Filter (Active / Inactive)
        if ($request->filled('status')) {
            $isActive = $request->status === 'active' ? 1 : 0;
            $query->where('is_active', $isActive);
        }

        // 3. Sort Filter
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $query->oldest('created_at')->oldest('id');
                    break;
                case 'a_z':
                    $query->orderBy('question', 'asc');
                    break;
                case 'z_a':
                    $query->orderBy('question', 'desc');
                    break;
                case 'newest':
                default:
                    $query->latest('created_at')->latest('id');
                    break;
            }
        } else {
            $query->latest('created_at')->latest('id'); // Default Sort
        }

        // withQueryString() ensures filters stay active when paginating
        $faqs = $query->paginate(10)->withQueryString();

        return view('admin.faq.index', compact('faqs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255|unique:faqs,question',
            'answer' => 'required|array', 
            'answer.*' => 'nullable|string', 
            'is_active' => 'boolean'
        ], [
            'question.required' => 'Please provide a question.',
            'question.unique' => 'This question already exists. Please provide a unique entry.',
            'answer.required' => 'Please provide at least one answer point.',
        ]);

        // Filter out any empty bullet points and implode with a newline
        $formattedAnswer = implode("\n", array_filter($request->answer, fn($val) => !is_null($val) && trim($val) !== ''));

        Faq::create([
            'question' => $request->question,
            'answer' => $formattedAnswer,
            // $request->boolean() accurately translates "1", "0", "true", "false", or missing to a strict boolean
            'is_active' => $request->boolean('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.faq.index')->with('success', 'FAQ added successfully.');
    }

    public function update(Request $request, Faq $faq)
    {
        $request->validate([
            'question' => 'required|string|max:255|unique:faqs,question,' . $faq->id,
            'answer' => 'required|array', 
            'answer.*' => 'nullable|string',
            'is_active' => 'boolean'
        ], [
            'question.required' => 'Please provide a question.',
            'question.unique' => 'This question already exists. Please provide a unique entry.',
            'answer.required' => 'Please provide at least one answer point.',
        ]);

        // Filter out any empty bullet points and implode with a newline
        $formattedAnswer = implode("\n", array_filter($request->answer, fn($val) => !is_null($val) && trim($val) !== ''));

        $faq->update([
            'question' => $request->question,
            'answer' => $formattedAnswer,
            // Uses robust boolean checking instead of just check for presence
            'is_active' => $request->boolean('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.faq.index')->with('success', 'FAQ updated successfully.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        return redirect()->route('admin.faq.index')->with('success', 'FAQ deleted successfully.');
    }
}