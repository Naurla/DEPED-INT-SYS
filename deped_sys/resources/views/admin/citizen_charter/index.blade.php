@extends('layouts.admin') 

@section('page_title', 'Manage Citizen\'s Charter')

@section('content')
<style>
    [x-cloak] { display: none !important; }
</style>

<div class="w-full pb-10" x-data="{
    links: {{ json_encode(old('links', $data->links ?? [])) }} || [],
    showDeleteModal: false,
    deleteIndex: null,
    deleteTitle: '',
    
    // Open modal to confirm link removal
    confirmDelete(index, name) {
        this.deleteIndex = index;
        this.deleteTitle = name || 'this link';
        this.showDeleteModal = true;
    },
    
    // Execute the client-side removal
    executeDelete() {
        if(this.deleteIndex !== null) {
            this.links.splice(this.deleteIndex, 1);
        }
        this.showDeleteModal = false;
    }
}">

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative shadow-sm">
            <span class="block sm:inline font-bold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="flex justify-between items-center mb-6 w-full">
        <div class="text-left">
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Manage Citizen's Charter</h2>
            <p class="text-gray-500 text-sm mt-1">Review the current charter above, and update the content, PDF, or links below.</p>
        </div>
    </div>

    <div class="space-y-6 w-full">
        
        {{-- TOP SECTION: Live Preview --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden w-full">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-800">Current Citizen's Charter Preview</h3>
            </div>
            
            <div class="p-6 md:p-8">
                <div class="w-full border border-gray-200 p-6 rounded-lg min-h-[200px] bg-gray-50/30">
                    
                    {{-- Title Preview --}}
                    @if(!empty($data->title))
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 pb-2 border-b border-gray-200">{{ $data->title }}</h2>
                    @endif

                    {{-- Rich Text Content Preview --}}
                    @if(!empty($data->content))
                        <div class="preview-content text-gray-700 text-sm leading-relaxed mb-8">
                            {!! $data->content !!}
                        </div>
                    @else
                        <span class="text-gray-400 italic block mb-8">No content has been published yet.</span>
                    @endif

                    {{-- PDF & Links Preview Box --}}
                    @if($data->file_path || !empty($data->links))
                        <div class="bg-white p-5 border border-gray-200 rounded-lg shadow-sm">
                            <h4 class="font-bold text-gray-800 mb-3 text-sm uppercase tracking-wider">Attached Resources</h4>
                            <div class="flex flex-wrap gap-4 items-center">
                                
                                @if($data->file_path)
                                    <a href="{{ asset('storage/' . $data->file_path) }}" target="_blank" class="inline-flex items-center gap-2 bg-red-50 text-red-700 border border-red-200 px-4 py-2 rounded-lg font-bold text-sm hover:bg-red-100 transition-colors">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M11.363 2c4.156 0 2.637 6 2.637 6s6-1.65 6 2.457v11.543h-16v-20h7.363zm.827-2h-10.19v24h20v-14.386c0-2.391-6.648-9.614-9.81-9.614z"/></svg>
                                        {{ $data->file_name ?? 'Download PDF' }}
                                    </a>
                                @endif

                                @if(!empty($data->links))
                                    @foreach($data->links as $link)
                                        <a href="{{ $link['url'] }}" target="_blank" class="inline-flex items-center gap-2 bg-gray-50 text-gray-700 border border-gray-300 px-4 py-2 rounded-lg font-bold text-sm hover:bg-gray-100 transition-colors">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                            {{ $link['name'] }}
                                        </a>
                                    @endforeach
                                @endif

                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- BOTTOM SECTION: Edit Form --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden w-full mb-6">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-800 text-left">Edit Citizen's Charter</h3>
            </div>

            <form action="{{ route('admin.citizen_charter.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="p-6 md:p-8 space-y-8">
                    
                    {{-- Optional Title & Charter Content (Rich Text) --}}
                    <div>
                        <div class="mb-4">
                            <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Title (Optional)</label>
                            <input type="text" id="title" name="title" value="{{ old('title', $data->title ?? '') }}" 
                                   class="w-full border border-gray-300 p-3 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" 
                                   placeholder="e.g. Citizen's Charter 2024">
                        </div>

                        <div>
                            <label for="content" class="block text-gray-700 text-sm font-bold mb-2">Main Content</label>
                            <textarea class="rich-text-editor w-full border border-gray-300 p-3 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" 
                                      id="content" name="content" placeholder="Enter charter content here...">{{ old('content', $data->content ?? '') }}</textarea>
                        </div>
                    </div>

                    {{-- Downloadable PDF Document --}}
                    <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2">Downloadable PDF Document</h3>
                        
                        @if(isset($data) && $data->file_path)
                            <div class="mb-6">
                                <label class="block text-gray-700 text-xs font-bold mb-3 uppercase tracking-wider">Current Uploaded File</label>
                                
                                <div class="relative inline-flex flex-col items-center bg-white border border-gray-200 shadow-sm rounded-xl p-5 w-48 group hover:shadow-md transition-shadow">
                                    
                                    {{-- Remove PDF Checkbox --}}
                                    <label class="absolute -top-3 -right-3 bg-white text-gray-400 border border-gray-200 shadow-sm rounded-full p-1.5 cursor-pointer hover:bg-red-50 hover:text-red-600 transition-colors z-10" title="Check to mark for removal">
                                        <input type="checkbox" name="remove_pdf" value="1" class="hidden peer">
                                        <svg class="w-4 h-4 peer-checked:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        <svg class="w-4 h-4 hidden peer-checked:block text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                    </label>

                                    <a href="{{ asset('storage/' . $data->file_path) }}" target="_blank" class="flex flex-col items-center peer-checked:opacity-40 peer-checked:grayscale transition-all duration-200">
                                        <svg class="w-16 h-16 text-red-600 mb-4 group-hover:scale-105 transition-transform" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M11.363 2c4.156 0 2.637 6 2.637 6s6-1.65 6 2.457v11.543h-16v-20h7.363zm.827-2h-10.19v24h20v-14.386c0-2.391-6.648-9.614-9.81-9.614zm4.711 13.009c-1.815-.395-3.32-.42-4.145-.333-.872-.888-1.594-2.149-1.89-3.21-.29-1.042-.266-2.031-.132-2.392.21-.568.868-.696 1.157-.318.175.231.229.627.143 1.137-.161.947-1.144 2.476-1.144 2.476s-.682 1.621-1.385 2.92c-1.288 1.076-2.928 2.059-3.414 2.29-.85.405-1.012.981-.663 1.353.255.27.755.27 1.488-.23 1.332-.907 2.434-2.671 2.434-2.671s2.174-.636 3.864-.817c1.552.924 3.016 1.341 3.791 1.053.477-.179.625-.658.468-1.019-.175-.405-.729-.465-1.572-.239zm-7.662 2.766c-.328.29-.636.438-.85.438-.178 0-.256-.073-.243-.2.02-.191.312-.533 1.093-.822l-.001.584zm2.146-2.115c.616-1.066 1.054-2.106 1.253-2.659-.395 1.154-1.253 2.659-1.253 2.659zm1.318.599c.925.101 1.956.19 2.932.327-.478.291-1.206.452-1.928.324-.306-.055-.635-.152-.989-.283.003-.131-.005-.246-.015-.368zm3.014-1.26c-.463-.099-.958-.112-1.428-.088.374-.183.74-.239 1.04-.15.25.074.348.167.388.238z"/>
                                        </svg>
                                        <span class="text-sm font-bold text-gray-800 text-center line-clamp-2 leading-tight w-40" title="{{ $data->file_name }}">
                                            {{ $data->file_name ?? 'CITIZENS_CHARTER.pdf' }}
                                        </span>
                                    </a>
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-1">Display Name for PDF</label>
                                <input type="text" name="pdf_name" value="{{ old('pdf_name', $data->file_name ?? '') }}" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" placeholder="e.g. Citizen's Charter 2024 PDF">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-1">Upload New PDF <span class="text-xs font-normal text-gray-500">(Replaces current)</span></label>
                                <input type="file" name="pdf_file" accept=".pdf" class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white">
                            </div>
                        </div>
                    </div>

                    {{-- External Links / Forms --}}
                    <div>
                        <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-2">
                            <h3 class="text-lg font-bold text-gray-800">External Links / Forms</h3>
                            <button type="button" @click="links.push({name: '', url: ''})" class="text-xs font-bold uppercase text-blue-600 hover:text-blue-800 hover:underline flex items-center">
                                + Add Link
                            </button>
                        </div>

                        <div class="space-y-4">
                            <template x-for="(link, index) in links" :key="index">
                                <div class="flex flex-col md:flex-row gap-4 items-end bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm hover:border-red-300 transition-colors">
                                    <div class="w-full md:w-5/12">
                                        <label class="block text-gray-700 text-xs font-bold mb-1 uppercase">Link Name</label>
                                        <input type="text" x-model="link.name" :name="`links[${index}][name]`" class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-red-500 outline-none text-sm bg-white" placeholder="e.g. Feedback Form" required>
                                    </div>
                                    <div class="w-full md:w-6/12">
                                        <label class="block text-gray-700 text-xs font-bold mb-1 uppercase">URL <span class="font-normal normal-case text-gray-500">(Include https://)</span></label>
                                        <input type="url" x-model="link.url" :name="`links[${index}][url]`" class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-red-500 outline-none text-sm bg-white" placeholder="https://..." required>
                                    </div>
                                    <div class="w-full md:w-1/12 pb-3 flex justify-center">
                                        <button type="button" @click="confirmDelete(index, link.name)" class="text-xs font-bold uppercase text-red-600 hover:text-red-800 hover:underline" title="Remove Link">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </template>
                            <div x-show="links.length === 0" class="text-gray-500 text-sm italic py-6 text-center border-2 border-dashed border-gray-300 rounded-lg bg-gray-50">
                                No additional links added. Click "+ Add Link" above.
                            </div>
                        </div>
                    </div>

                </div>

                <div class="bg-gray-50 p-6 border-t border-gray-200 flex justify-end">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-8 rounded-lg shadow-sm transition-colors text-sm uppercase tracking-wide">
                        Save All Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- GLOBAL MODAL: Client-Side Remove Confirmation --}}
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity" style="display: none;">
        <div class="bg-white rounded-2xl p-8 shadow-2xl z-50 w-full max-w-sm transform transition-all relative" @click.away="showDeleteModal = false">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            
            <h3 class="text-xl font-bold text-gray-800 mb-2 text-center">Confirm Removal</h3>
            <p class="text-gray-500 text-sm mb-6 text-center">Are you sure you want to remove <br><span class="font-bold text-gray-800 break-words" x-text="deleteTitle"></span>?<br><br><span class="text-xs italic">You will still need to click "Save All Changes" to make this permanent.</span></p>
            
            <div class="flex space-x-3 border-t border-gray-100 pt-4">
                <button type="button" @click="showDeleteModal = false" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                
                <button type="button" @click="executeDelete()" class="flex-1 px-4 py-2.5 bg-red-700 text-white rounded-xl font-bold text-sm hover:bg-red-800 shadow-sm transition-colors">
                    Remove
                </button>
            </div>
        </div>
    </div>

</div>

{{-- Styling for Editor and Preview (Restoring Tailwind's stripped elements) --}}
<style>
    .ck-editor__editable_inline {
        min-height: 250px;
    }

    /* Restore list styling inside the editor AND the preview section */
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
    // Initialize editors for textareas on page load
    document.querySelectorAll('.rich-text-editor').forEach(textarea => {
        ClassicEditor
            .create(textarea, {
                toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo' ]
            })
            .catch(error => {
                console.error(error);
            });
    });
});
</script>
@endsection