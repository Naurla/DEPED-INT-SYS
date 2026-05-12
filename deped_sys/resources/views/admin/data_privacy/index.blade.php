@extends('layouts.admin') 

@section('page_title', 'Manage Data Privacy')

@section('content')
<div class="w-full pb-10">

    {{-- Standard Success Alert --}}
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm w-full">
            <span class="block sm:inline font-bold text-left">{{ session('success') }}</span>
        </div>
    @endif

    <div class="flex justify-between items-center mb-6 w-full">
        <div class="text-left">
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Manage DepEd Data Privacy</h2>
            <p class="text-gray-500 text-sm mt-1">Review the current policy above, and make edits below.</p>
        </div>
    </div>

    <div class="space-y-6 w-full">
        
        {{-- TOP SECTION: Display Current Content --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden w-full">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-800">Current Data Privacy Notice</h3>
            </div>
            
            <div class="p-6">
                <div class="w-full border border-gray-200 p-6 text-sm rounded-lg min-h-[200px] text-gray-800 leading-relaxed !text-left whitespace-normal break-words space-y-6">
                    @if(!empty($data->sections) && count($data->sections) > 0)
                        @foreach($data->sections as $section)
                            <div>
                                @if(!empty($section['title']))
                                    <h4 class="font-bold text-lg mb-2 text-gray-900">{{ $section['title'] }}</h4>
                                @endif
                                
                                {{-- ADDED preview-content CLASS HERE SO THE CSS TARGETS IT --}}
                                <div class="preview-content prose prose-sm max-w-none text-gray-700">
                                    {!! $section['content'] !!}
                                </div>
                            </div>
                        @endforeach
                    @else
                        <span class="text-gray-400 italic">No data privacy notice has been published yet.</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- BOTTOM SECTION: Edit Form --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden w-full">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-800 text-left">Edit Data Privacy Sections</h3>
            </div>
            
            <form action="{{ route('admin.data_privacy.update') }}" method="POST">
                @csrf
                
                <div id="sections-container" class="p-6 space-y-6 bg-gray-50/50">
                    @php
                        // Initialize with at least one empty section if none exist
                        $sections = old('sections', $data->sections ?? [['title' => '', 'content' => '']]);
                    @endphp

                    @foreach($sections as $index => $section)
                        <div class="section-item relative bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                            <button type="button" class="remove-section absolute top-4 right-4 text-red-500 hover:text-red-700 font-bold text-xs uppercase tracking-wider transition-colors z-10">
                                ✕ Remove
                            </button>
                            
                            <div class="mb-4 pr-20">
                                <label class="block text-gray-700 text-sm font-bold mb-2 text-left">Section Title (Optional)</label>
                                <input type="text" name="sections[{{ $index }}][title]" value="{{ $section['title'] ?? '' }}" 
                                       class="w-full border border-gray-300 p-3 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" 
                                       placeholder="e.g. Information We Collect">
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2 text-left">Content</label>
                                <textarea name="sections[{{ $index }}][content]" 
                                          class="rich-text-editor w-full border border-gray-300 p-4 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none !text-left" 
                                          placeholder="Type the section content here...">{{ $section['content'] ?? '' }}</textarea>
                                @error("sections.{$index}.content")
                                    <p class="text-red-500 text-xs mt-2 font-medium text-left">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="px-6 pb-6 pt-2 text-left bg-gray-50/50">
                    <button type="button" id="add-section" class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-6 rounded-lg shadow-sm transition-colors text-sm">
                        + Add Another Section
                    </button>
                </div>

                <div class="bg-gray-50 p-4 border-t border-gray-200 flex justify-end">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-10 rounded-lg shadow-sm transition-colors text-sm uppercase tracking-wide">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

{{-- Add styling so the editor has a decent height AND restores Tailwind's stripped formatting --}}
<style>
    /* Set minimum height for the text area */
    .ck-editor__editable_inline {
        min-height: 200px;
    }

    /* Restore list styling inside the editor AND the top preview section */
    .ck-content ul, .preview-content ul {
        list-style-type: disc !important;
        padding-left: 2rem !important;
        margin-bottom: 1rem !important;
    }
    
    .ck-content ol, .preview-content ol {
        list-style-type: decimal !important;
        padding-left: 2rem !important;
        margin-bottom: 1rem !important;
    }

    .ck-content li, .preview-content li {
        margin-bottom: 0.25rem !important;
    }

    /* Restore heading styling */
    .ck-content h1, .ck-content h2, .ck-content h3, .ck-content h4,
    .preview-content h1, .preview-content h2, .preview-content h3, .preview-content h4 {
        font-weight: 700 !important;
        margin-top: 1rem !important;
        margin-bottom: 0.5rem !important;
        color: #111827 !important; /* Tailwind gray-900 */
    }
    
    .ck-content h1, .preview-content h1 { font-size: 1.875rem !important; }
    .ck-content h2, .preview-content h2 { font-size: 1.5rem !important; }
    .ck-content h3, .preview-content h3 { font-size: 1.25rem !important; }
    
    /* Restore link styling */
    .ck-content a, .preview-content a {
        color: #2563eb !important; /* Tailwind blue-600 */
        text-decoration: underline !important;
    }
</style>

{{-- Include CKEditor Script --}}
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('sections-container');
    const addButton = document.getElementById('add-section');
    
    // Function to initialize the rich text editor
    function initEditor(element) {
        ClassicEditor
            .create(element, {
                toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo' ]
            })
            .catch(error => {
                console.error(error);
            });
    }

    // 1. Initialize editors for existing textareas on page load
    document.querySelectorAll('.rich-text-editor').forEach(textarea => {
        initEditor(textarea);
    });

    // Track the current index to ensure unique names for dynamically added inputs
    let sectionIndex = {{ count($sections) }};

    // 2. Add new section dynamically
    addButton.addEventListener('click', function() {
        const template = `
            <div class="section-item relative bg-white border border-gray-200 rounded-lg p-5 shadow-sm mt-6">
                <button type="button" class="remove-section absolute top-4 right-4 text-red-500 hover:text-red-700 font-bold text-xs uppercase tracking-wider transition-colors z-10">
                    ✕ Remove
                </button>
                
                <div class="mb-4 pr-20">
                    <label class="block text-gray-700 text-sm font-bold mb-2 text-left">Section Title (Optional)</label>
                    <input type="text" name="sections[${sectionIndex}][title]" 
                           class="w-full border border-gray-300 p-3 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" 
                           placeholder="e.g. Information We Collect">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2 text-left">Content</label>
                    <textarea name="sections[${sectionIndex}][content]" 
                              class="rich-text-editor w-full border border-gray-300 p-4 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none !text-left" 
                              placeholder="Type the section content here..."></textarea>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', template);
        
        // Initialize the newly added textarea with CKEditor
        const newTextarea = container.querySelector(`textarea[name="sections[${sectionIndex}][content]"]`);
        initEditor(newTextarea);

        sectionIndex++;
    });

    // 3. Remove section via event delegation
    container.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-section')) {
            const sectionItem = e.target.closest('.section-item');
            if(container.querySelectorAll('.section-item').length > 1) {
                sectionItem.remove();
            } else {
                alert('You must have at least one section.');
            }
        }
    });
});
</script>
@endsection