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

        {{-- FIGURE OUT CURRENT UNIFIED PARENT SELECTION --}}
        @php
            $currentParent = old('parent_selection');
            if (!$currentParent) {
                if ($page->menu_location) {
                    $currentParent = 'menu_' . $page->menu_location;
                } elseif ($page->parent_id) {
                    $currentParent = (string) $page->parent_id;
                }
            }
        @endphp

        {{-- SINGLE UNIFIED PARENT DROPDOWN --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Parent Category (Optional)</label>
            <select name="parent_selection" class="w-full border border-gray-300 px-4 py-2 rounded focus:ring-2 focus:ring-[#a52a2a] outline-none">
                <option value="">-- No Parent (Make this a Standalone Menu Item) --</option>
                
                <optgroup label="Hardcoded Site Menus">
                    <option value="menu_about" {{ $currentParent == 'menu_about' ? 'selected' : '' }}>About Section</option>
                    <option value="menu_issuances" {{ $currentParent == 'menu_issuances' ? 'selected' : '' }}>Issuances Section</option>
                    <option value="menu_k12" {{ $currentParent == 'menu_k12' ? 'selected' : '' }}>K to 12 Section</option>
                    <option value="menu_procurement" {{ $currentParent == 'menu_procurement' ? 'selected' : '' }}>Procurement Section</option>
                </optgroup>

                @if(isset($allPages) && $allPages->isNotEmpty())
                    <optgroup label="Dynamic Custom Pages">
                        @foreach($allPages as $parentPage)
                            <option value="{{ $parentPage->id }}" {{ $currentParent == (string)$parentPage->id ? 'selected' : '' }}>
                                {{ $parentPage->title }}
                            </option>
                        @endforeach
                    </optgroup>
                @endif
            </select>
            <p class="text-xs text-gray-500 mt-1">Select an existing site section or another custom page to nest this under.</p>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Frontend Layout Style</label>
            <select name="layout_template" class="w-full border border-gray-300 px-4 py-2 rounded focus:ring-2 focus:ring-[#a52a2a] outline-none">
                <option value="default" {{ old('layout_template', $page->layout_template) == 'default' ? 'selected' : '' }}>Default View (Standard Width)</option>
                <option value="full_width" {{ old('layout_template', $page->layout_template) == 'full_width' ? 'selected' : '' }}>Full Width (No Margins)</option>
                <option value="boxed_shadow" {{ old('layout_template', $page->layout_template) == 'boxed_shadow' ? 'selected' : '' }}>Boxed with Shadow</option>
            </select>
        </div>

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
            {{-- DYNAMIC MULTI-VIDEO SECTION --}}
            <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-xl">
                <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-200">
                    <div>
                        <label class="block text-gray-800 font-bold text-lg">Featured Videos</label>
                        <p class="text-xs text-gray-500">Add multiple responsive videos. Leave blank if none.</p>
                    </div>
                    <button type="button" id="add-video-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow-sm font-bold text-sm flex items-center transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Add Video
                    </button>
                </div>
                
                <div id="video-container" class="space-y-6"></div>
            </div>

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
        constructor(loader) { this.loader = loader; }
        upload() {
            return this.loader.file.then(file => new Promise((resolve, reject) => {
                this._initRequest(); this._initListeners(resolve, reject, file); this._sendRequest(file);
            }));
        }
        abort() { if (this.xhr) { this.xhr.abort(); } }
        _initRequest() {
            const xhr = this.xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route('editor.upload') }}', true);
            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
            xhr.responseType = 'json';
        }
        _initListeners(resolve, reject, file) {
            const xhr = this.xhr; const loader = this.loader;
            xhr.addEventListener('error', () => reject(`Couldn't upload file: ${file.name}.`));
            xhr.addEventListener('abort', () => reject());
            xhr.addEventListener('load', () => {
                const response = xhr.response;
                if (!response || response.error) return reject(response && response.error ? response.error.message : `Couldn't upload file: ${file.name}.`);
                resolve({ default: response.url });
            });
            if (xhr.upload) {
                xhr.upload.addEventListener('progress', evt => {
                    if (evt.lengthComputable) { loader.uploadTotal = evt.total; loader.uploaded = evt.loaded; }
                });
            }
        }
        _sendRequest(file) {
            const data = new FormData();
            data.append('upload', file); data.append('_token', '{{ csrf_token() }}');
            this.xhr.send(data);
        }
    }

    function MyCustomUploadAdapterPlugin(editor) {
        editor.plugins.get('FileRepository').createUploadAdapter = (loader) => { return new MyUploadAdapter(loader); };
    }

    if (document.querySelector('#rich-editor')) {
        ClassicEditor
            .create(document.querySelector('#rich-editor'), {
                extraPlugins: [MyCustomUploadAdapterPlugin],
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'imageUpload', 'blockQuote', 'insertTable', '|', 'undo', 'redo']
            })
            .catch(error => { console.error(error); });
    }

    // --- DYNAMIC MULTI-VIDEO SCRIPT ---
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('video-container');
        const addBtn = document.getElementById('add-video-btn');
        let videoIndex = 0;

        const existingVideos = @json(old('featured_videos', $page->featured_videos ?? []));

        function renderPreview(urlInput, shapeSelect, previewWrapper, previewContent) {
            const rawUrl = urlInput.value.trim();
            if (!rawUrl) {
                previewWrapper.classList.add('hidden');
                previewContent.innerHTML = '';
                return;
            }

            const url = rawUrl.toLowerCase();
            let iframeSrc = '';
            const isVertical = (shapeSelect.value === 'portrait');

            if (url.includes('facebook.com') || url.includes('fb.watch') || url.includes('fb.me')) {
                iframeSrc = `https://www.facebook.com/plugins/video.php?href=${encodeURIComponent(rawUrl)}&show_text=false`;
            } else if (url.includes('youtube.com') || url.includes('youtu.be')) {
                let videoId = '';
                if (url.includes('watch?v=')) videoId = rawUrl.split('watch?v=')[1].split('&')[0];
                else if (url.includes('youtu.be/')) videoId = rawUrl.split('youtu.be/')[1].split('?')[0];
                else if (url.includes('/shorts/')) videoId = rawUrl.split('/shorts/')[1].split('?')[0];
                if (videoId) iframeSrc = `https://www.youtube.com/embed/${videoId}`;
            } else if (url.includes('tiktok.com')) {
                let matches = rawUrl.match(/video\/(\d+)/i);
                let videoId = matches && matches[1] ? matches[1] : rawUrl.split('/').pop().split('?')[0];
                if (videoId) iframeSrc = `https://www.tiktok.com/embed/v2/${videoId}`;
            }

            if (iframeSrc) {
                previewWrapper.classList.remove('hidden');
                const maxWidth = isVertical ? '350px' : '100%';
                const aspect = isVertical ? '9/16' : '16/9';
                
                previewContent.innerHTML = `
                    <div style="position: relative; width: 100%; max-width: ${maxWidth}; aspect-ratio: ${aspect}; margin: 0 auto; background-color: transparent; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
                        <iframe src="${iframeSrc}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;" scrolling="no" frameborder="0" allowtransparency="true" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="true"></iframe>
                    </div>
                `;
            } else {
                previewWrapper.classList.add('hidden');
                previewContent.innerHTML = '';
            }
        }

        function addVideoRow(url = '', shape = 'landscape') {
            const row = document.createElement('div');
            row.className = "video-row bg-white border border-gray-200 rounded-xl p-5 shadow-sm relative group transition-all";
            
            row.innerHTML = `
                <button type="button" class="remove-btn absolute top-3 right-3 text-red-400 hover:text-red-600 bg-red-50 hover:bg-red-100 p-2 rounded-full transition-colors" title="Remove Video">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-2 pr-10">
                    <div class="md:col-span-2">
                        <label class="block text-gray-600 font-bold mb-1 text-xs uppercase tracking-wider">Video URL</label>
                        <input type="url" name="featured_videos[${videoIndex}][url]" value="${url}" placeholder="Facebook, YouTube, or TikTok link" class="video-input w-full border border-gray-300 px-4 py-2 rounded focus:ring-2 focus:ring-[#a52a2a] outline-none">
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-gray-600 font-bold mb-1 text-xs uppercase tracking-wider">Video Shape</label>
                        <select name="featured_videos[${videoIndex}][shape]" class="shape-select w-full border border-gray-300 px-4 py-2 rounded focus:ring-2 focus:ring-[#a52a2a] outline-none bg-white">
                            <option value="landscape" ${shape === 'landscape' ? 'selected' : ''}>Landscape (Wide)</option>
                            <option value="portrait" ${shape === 'portrait' ? 'selected' : ''}>Portrait (Tall / Reel)</option>
                        </select>
                    </div>
                </div>
                <div class="preview-wrapper mt-4 hidden bg-gray-50 p-4 rounded-lg border border-gray-100">
                    <p class="text-xs font-bold text-gray-400 mb-2 uppercase tracking-widest text-center">Live Preview</p>
                    <div class="preview-content w-full flex justify-center mx-auto"></div>
                </div>
            `;

            container.appendChild(row);
            videoIndex++;

            const input = row.querySelector('.video-input');
            const select = row.querySelector('.shape-select');
            const wrapper = row.querySelector('.preview-wrapper');
            const content = row.querySelector('.preview-content');
            const removeBtn = row.querySelector('.remove-btn');

            const triggerPreview = () => renderPreview(input, select, wrapper, content);

            input.addEventListener('input', triggerPreview);
            select.addEventListener('change', triggerPreview);
            removeBtn.addEventListener('click', () => row.remove());

            triggerPreview();
        }

        addBtn.addEventListener('click', () => addVideoRow());
        
        if (existingVideos && existingVideos.length > 0) {
            existingVideos.forEach(v => addVideoRow(v.url, v.shape));
        } else {
            addVideoRow();
        }
    });
</script>
<style>
    .ck-editor__editable_inline { min-height: 400px; }
    .ck-content table { border-collapse: collapse !important; width: 100% !important; margin-bottom: 1.5rem !important; }
    .ck-content table, .ck-content th, .ck-content td { border: 1px solid #d1d5db !important; padding: 12px !important; }
    .ck-content th { background-color: #f3f4f6 !important; font-weight: bold !important; }
    .ck-content h1 { font-size: 2.5rem !important; font-weight: 800 !important; }
    .ck-content h2 { font-size: 2rem !important; font-weight: 700 !important; }
    .ck-content ul, .ck-content ol { margin-left: 2rem !important; margin-bottom: 1.5rem !important; }
    .ck-content ul { list-style-type: disc !important; }
    .ck-content ol { list-style-type: decimal !important; }
    .ck-content li { margin-bottom: 0.5rem !important; display: list-item !important; }
    .ck-content .image { max-width: 100%; margin: 1.5rem auto !important; display: block !important; }
    .ck-content .image img { max-width: 100%; height: auto; display: block; margin: 0 auto; }
</style>
@endpush
@endsection