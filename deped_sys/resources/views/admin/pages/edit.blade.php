@extends('layouts.admin')

@section('page_title', 'Edit Page: ' . $page->title)

@section('content')
<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h2 class="text-xl font-bold text-gray-800 uppercase">Edit Page</h2>
        <a href="{{ route('admin.pages.index') }}" class="text-gray-500 hover:text-gray-800 font-bold text-sm">← Back to List</a>
    </div>
    
    <form action="{{ route('admin.pages.update', $page->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Page Title</label>
            <input type="text" name="title" value="{{ old('title', $page->title) }}" class="w-full border border-gray-300 px-4 py-2 rounded focus:ring-2 focus:ring-[#a52a2a] outline-none" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Parent Category (Optional)</label>
            <select name="parent_id" class="w-full border border-gray-300 px-4 py-2 rounded focus:ring-2 focus:ring-[#a52a2a] outline-none">
                <option value="">-- No Parent (Make this a Main Menu Item) --</option>
                @if(isset($allPages))
                    @foreach($allPages as $parentPage)
                        <option value="{{ $parentPage->id }}" {{ (old('parent_id', $page->parent_id) == $parentPage->id) ? 'selected' : '' }}>
                            {{ $parentPage->title }}
                        </option>
                    @endforeach
                @endif
            </select>
            <p class="text-xs text-gray-500 mt-1">If selected, this page will appear as a dropdown under the parent.</p>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Frontend Layout Style</label>
            <select name="layout_template" class="w-full border border-gray-300 px-4 py-2 rounded focus:ring-2 focus:ring-[#a52a2a] outline-none">
                <option value="default" {{ old('layout_template', $page->layout_template) == 'default' ? 'selected' : '' }}>Default View (Standard Width)</option>
                <option value="full_width" {{ old('layout_template', $page->layout_template) == 'full_width' ? 'selected' : '' }}>Full Width (No Margins)</option>
                <option value="boxed_shadow" {{ old('layout_template', $page->layout_template) == 'boxed_shadow' ? 'selected' : '' }}>Boxed with Shadow</option>
            </select>
        </div>

        {{-- SMART CONTENT BOX: Hide if it's a parent category --}}
        @if($page->children->isNotEmpty())
            <div class="mb-4 p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
                <div class="flex">
                    <svg class="h-6 w-6 text-blue-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-blue-800">
                        <strong class="font-bold">Category Heading:</strong> Because you added sub-categories to this page, it now acts strictly as a dropdown menu navigation button. It cannot have its own content.
                    </p>
                </div>
            </div>
            <input type="hidden" name="content" value="">
        @else
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Page Content</label>
                <textarea name="content" id="rich-editor" class="w-full">{{ old('content', $page->content) }}</textarea>
            </div>
        @endif

        <div class="mb-6 flex items-center">
            <input type="checkbox" name="show_in_nav" id="showNav" value="1" class="w-4 h-4 text-[#a52a2a] border-gray-300 rounded focus:ring-[#a52a2a]" {{ $page->show_in_nav ? 'checked' : '' }}>
            <label class="ml-2 text-gray-700 font-bold" for="showNav">Show in Public Navigation Menu</label>
        </div>

        <button type="submit" class="bg-[#a52a2a] hover:bg-red-800 text-white font-bold py-2 px-6 rounded transition-colors shadow-md">
            Update Page
        </button>
    </form>
</div>

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    class MyUploadAdapter {
        constructor(loader) {
            this.loader = loader;
        }

        upload() {
            return this.loader.file.then(file => new Promise((resolve, reject) => {
                this._initRequest();
                this._initListeners(resolve, reject, file);
                this._sendRequest(file);
            }));
        }

        abort() {
            if (this.xhr) { this.xhr.abort(); }
        }

        _initRequest() {
            const xhr = this.xhr = new XMLHttpRequest();
            
            // POINT TO THE NEW UNRESTRICTED ROUTE
            xhr.open('POST', '{{ route('editor.upload') }}', true);
            
            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
            xhr.responseType = 'json';
        }

        _initListeners(resolve, reject, file) {
            const xhr = this.xhr;
            const loader = this.loader;
            const genericErrorText = `Couldn't upload file: ${file.name}.`;

            xhr.addEventListener('error', () => reject(genericErrorText));
            xhr.addEventListener('abort', () => reject());
            xhr.addEventListener('load', () => {
                const response = xhr.response;

                // If Laravel throws a 500 error, response will be null/HTML
                if (!response || response.error) {
                    return reject(response && response.error ? response.error.message : genericErrorText);
                }

                // Success! Pass the URL back to the editor
                resolve({
                    default: response.url
                });
            });

            // Adds a loading bar to the image upload
            if (xhr.upload) {
                xhr.upload.addEventListener('progress', evt => {
                    if (evt.lengthComputable) {
                        loader.uploadTotal = evt.total;
                        loader.uploaded = evt.loaded;
                    }
                });
            }
        }

        _sendRequest(file) {
            const data = new FormData();
            data.append('upload', file);
            data.append('_token', '{{ csrf_token() }}'); // <-- ADD THIS LINE
            this.xhr.send(data);
        }
    }

    function MyCustomUploadAdapterPlugin(editor) {
        editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
            return new MyUploadAdapter(loader);
        };
    }

    if (document.querySelector('#rich-editor')) {
        ClassicEditor
            .create(document.querySelector('#rich-editor'), {
                extraPlugins: [MyCustomUploadAdapterPlugin]
            })
            .catch(error => {
                console.error(error);
            });
    }
</script>
<style>
    .ck-editor__editable_inline { min-height: 400px; }

    /* TABLE FIX: Make grid lines visible in the editor */
    .ck-content table {
        border-collapse: collapse !important;
        width: 100% !important;
        margin-bottom: 1.5rem !important;
    }
    .ck-content table, 
    .ck-content th, 
    .ck-content td {
        border: 1px solid #d1d5db !important; /* Light gray border */
        padding: 12px !important;
    }
    .ck-content th {
        background-color: #f3f4f6 !important; /* Light gray header */
        font-weight: bold !important;
    }

    /* Keep your existing heading fixes here too */
    .ck-content h1 { font-size: 2.5rem !important; font-weight: 800 !important; }
    .ck-content h2 { font-size: 2rem !important; font-weight: 700 !important; }
</style>
@endpush
@endsection