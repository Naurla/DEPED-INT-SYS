@extends('layouts.admin')
@section('page_title', 'Page Content Builder')

@section('content')
<style>
    [x-cloak] { display: none !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #fca5a5; border-radius: 10px; }
    /* Cursor style for draggable items */
    .drag-handle { cursor: grab; }
    .drag-handle:active { cursor: grabbing; }
    .sortable-ghost { opacity: 0.4; background-color: #fef2f2; }
</style>

<div class="w-full" x-data="{
    modalOpen: false,
    editMode: false,
    sectionId: null,
    isSubmitting: false,

    // Delete Modal
    deleteModal: false,
    deleteAction: '',
    deleteTitle: '',

    // General form fields
    formLocation: 'home',
    formType: 'rich_text',
    formTitle: '',
    formContent: '',
    formOrder: 1,
    formActive: '1',

    // Video-specific fields
    formVideoUrl: '',
    formVideoShape: 'landscape',
    formVideoCaption: '',
    videoPreviewSrc: '',
    videoPreviewPortrait: false,

    openAdd() {
        this.editMode = false;
        this.formLocation = 'home';
        this.formType = 'rich_text';
        this.formTitle = '';
        this.formContent = '';
        this.formOrder = 1;
        this.formActive = '1';
        this.formVideoUrl = '';
        this.formVideoShape = 'landscape';
        this.formVideoCaption = '';
        this.videoPreviewSrc = '';
        this.videoPreviewPortrait = false;
        this.modalOpen = true;
    },

    openEdit(section) {
        this.editMode = true;
        this.sectionId = section.id;
        this.formLocation = section.display_location;
        this.formType = section.type;
        this.formTitle = section.title || '';
        this.formContent = section.content || '';
        this.formOrder = section.sort_order;
        this.formActive = (section.is_active == 1 || section.is_active === true || section.is_active == '1') ? '1' : '0';
        this.formVideoUrl = section.video_url || '';
        this.formVideoShape = section.video_shape || 'landscape';
        this.formVideoCaption = section.video_caption || '';
        this.videoPreviewSrc = '';
        this.videoPreviewPortrait = (this.formVideoShape === 'portrait');
        this.modalOpen = true;
        if (section.type === 'video' && section.video_url) {
            this.$nextTick(() => this.buildVideoPreview(section.video_url, section.video_shape));
        }
    },

    confirmDelete(action, title) {
        this.deleteAction = action;
        this.deleteTitle = title;
        this.deleteModal = true;
    },

    isWidget() {
        return this.formType.startsWith('widget_');
    },

    buildVideoPreview(rawUrl, shape) {
        const url = (rawUrl || '').toLowerCase();
        let src = '';
        this.videoPreviewPortrait = (shape === 'portrait');
        if (url.includes('facebook.com') || url.includes('fb.watch') || url.includes('fb.me')) {
            src = 'https://www.facebook.com/plugins/video.php?href=' + encodeURIComponent(rawUrl) + '&show_text=false';
        } else if (url.includes('youtube.com') || url.includes('youtu.be')) {
            let id = '';
            if (url.includes('watch?v='))       id = rawUrl.split('watch?v=')[1].split('&')[0];
            else if (url.includes('youtu.be/')) id = rawUrl.split('youtu.be/')[1].split('?')[0];
            else if (url.includes('/shorts/'))  id = rawUrl.split('/shorts/')[1].split('?')[0];
            if (id) src = 'https://www.youtube.com/embed/' + id;
        } else if (url.includes('tiktok.com')) {
            const m = rawUrl.match(/video\/(\d+)/i);
            const id = m && m[1] ? m[1] : rawUrl.split('/').pop().split('?')[0];
            if (id) src = 'https://www.tiktok.com/embed/v2/' + id;
        }
        this.videoPreviewSrc = src;
    }
}">

    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4 w-full">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Content Builder</h2>
            <p class="text-gray-500 text-sm mt-1">Assign custom text, banners, or dynamic widgets to any page. Drag and drop rows to reorder them.</p>
        </div>
        <button @click="openAdd()" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-5 rounded-lg shadow-sm transition-colors text-sm uppercase tracking-wider flex items-center whitespace-nowrap">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add Content Block
        </button>
    </div>

    {{-- Clean Search & Filter Section --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ url()->current() }}" class="flex flex-col xl:flex-row gap-4 items-center justify-between">
            
            {{-- Search Bar --}}
            <div class="w-full xl:w-1/3 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title or content..." class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none text-sm transition-colors">
            </div>

            {{-- Dropdown Filters --}}
            <div class="w-full xl:w-auto flex flex-col md:flex-row gap-3 items-center">
                
                {{-- Location Filter --}}
                <select name="location" class="w-full md:w-40 py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none text-sm bg-white text-gray-700 cursor-pointer" onchange="this.form.submit()">
                    <option value="">All Locations</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc }}" {{ request('location') == $loc ? 'selected' : '' }}>
                            {{ strtoupper(str_replace('page:', 'Page: ', $loc)) }}
                        </option>
                    @endforeach
                </select>

                {{-- Type Filter --}}
                <select name="type" class="w-full md:w-36 py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none text-sm bg-white text-gray-700 cursor-pointer" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_', ' ', $type)) }}
                        </option>
                    @endforeach
                </select>

                {{-- Sort Filter --}}
                <select name="sort" class="w-full md:w-44 py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none text-sm bg-white text-gray-700 cursor-pointer" onchange="this.form.submit()">
                    <option value="default" {{ request('sort') == 'default' ? 'selected' : '' }}>Page & Order (Default)</option>
                    <option value="order_asc" {{ request('sort') == 'order_asc' ? 'selected' : '' }}>Sort Order (Low-High)</option>
                    <option value="order_desc" {{ request('sort') == 'order_desc' ? 'selected' : '' }}>Sort Order (High-Low)</option>
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest Uploads</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest Uploads</option>
                </select>

                {{-- Clear Filters --}}
                @if(request('search') || request('location') || request('type') || (request('sort') && request('sort') !== 'default'))
                    <a href="{{ url()->current() }}" class="text-sm font-semibold text-gray-500 hover:text-red-600 transition-colors whitespace-nowrap px-2">
                        Clear Filters
                    </a>
                @endif
                
                <button type="submit" class="hidden">Search</button>
            </div>
        </form>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6 w-full relative">
        {{-- Save Notification Toast --}}
        <div id="save-toast" class="absolute top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg text-sm font-bold opacity-0 transition-opacity duration-300 z-50 pointer-events-none">
            Order Saved!
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase font-bold border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 w-16 text-center">#</th>
                        <th class="px-6 py-4 w-1/4">Location</th>
                        <th class="px-6 py-4 w-1/6">Type</th>
                        <th class="px-6 py-4">Preview / Title</th>
                        <th class="px-6 py-4 text-center w-24">Order</th>
                        <th class="px-6 py-4 text-center w-28">Status</th>
                        <th class="px-6 py-4 text-right w-32">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="sortable-sections">
                    @forelse($sections as $index => $sec)
                    <tr class="hover:bg-gray-50/50 transition-colors bg-white" data-id="{{ $sec->id }}">
                        <td class="px-6 py-4 text-sm text-gray-600 font-medium text-center align-middle">{{ $sections->firstItem() + $index }}</td>
                        <td class="px-6 py-4 font-bold text-red-700 align-middle">{{ strtoupper(str_replace('page:', 'Page: ', $sec->display_location)) }}</td>
                        <td class="px-6 py-4 align-middle">
                            <span class="px-3 py-1 bg-gray-200 text-gray-700 rounded-full text-[10px] font-bold uppercase tracking-wider">{{ str_replace('_', ' ', $sec->type) }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 align-middle">
                            @if($sec->type == 'banner')
                                <img src="{{ asset('storage/'.$sec->image_path) }}" class="h-12 w-auto object-cover rounded shadow-sm border border-gray-200">
                            @elseif($sec->type == 'video')
                                @php
                                    $vThumb = '';
                                    $vUrl = strtolower($sec->video_url ?? '');
                                    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $sec->video_url ?? '', $ytMatch);
                                    if (isset($ytMatch[1])) $vThumb = "https://img.youtube.com/vi/{$ytMatch[1]}/mqdefault.jpg";
                                @endphp
                                <div class="flex items-center gap-3">
                                    @if($vThumb)
                                        <img src="{{ $vThumb }}" class="h-10 w-16 object-cover rounded shadow-sm border border-gray-200">
                                    @else
                                        <div class="h-10 w-16 bg-gray-800 rounded flex items-center justify-center">
                                            <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        </div>
                                    @endif
                                    <div>
                                        <strong class="text-gray-900 block text-xs">{{ $sec->title ?? 'Video Block' }}</strong>
                                        <span class="text-xs text-gray-400">{{ Str::limit($sec->video_url, 40) }}</span>
                                    </div>
                                </div>
                            @elseif(str_starts_with($sec->type, 'widget_'))
                                <span class="italic text-gray-400 font-medium">Dynamic Widget Feed</span>
                            @else
                                <strong class="text-gray-900 block mb-1">{{ $sec->title ?? 'No Title' }}</strong>
                                <span class="text-xs text-gray-500">{{ Str::limit(strip_tags($sec->content), 50) }}</span>
                            @endif
                        </td>

                        
                        {{-- DRAG HANDLE COLUMN --}}
                        <td class="px-6 py-4 text-center align-middle">
                            <div class="drag-handle inline-flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg border border-gray-200 transition-colors">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                                <span class="font-bold text-gray-900 sort-number">{{ $sec->sort_order }}</span>
                            </div>
                        </td>

                        {{-- STATUS COLUMN --}}
                        <td class="px-6 py-4 text-center align-middle">
                            @if($sec->is_active == 1)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold bg-green-50 text-green-700 border border-green-200 uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold bg-gray-50 text-gray-600 border border-gray-200 uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Hidden
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-right align-middle">
                            <div class="flex items-center justify-end gap-3">
                                <button type="button" @click="openEdit({{ $sec->toJson() }})" class="text-blue-600 font-bold uppercase text-xs hover:underline transition-all">Edit</button>
                                <button type="button" @click="confirmDelete('{{ route('admin.page-sections.destroy', $sec->id) }}', '{{ addslashes($sec->title ?? 'Content Block') }}')" class="text-red-600 font-bold uppercase text-xs hover:underline transition-all">Delete</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            <p class="text-gray-500 font-medium">No content blocks found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($sections->hasPages())
        <div class="mt-4 mb-6 w-full">
            {{ $sections->links() }}
        </div>
    @endif

    {{-- ADD / EDIT MODAL --}}
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-4xl shadow-2xl flex flex-col max-h-[95vh]" @click.away="if (!isSubmitting) modalOpen = false">
            <div class="bg-red-700 px-6 py-4 text-white flex justify-between items-center flex-shrink-0">
                <h3 class="font-bold text-lg uppercase tracking-wider" x-text="editMode ? 'Edit Content Block' : 'New Content Block'"></h3>
                <button type="button" @click="modalOpen = false" :disabled="isSubmitting" class="text-3xl font-bold hover:text-gray-200 leading-none disabled:opacity-50">&times;</button>
            </div>
            
            <form :action="editMode ? '/admin/page-sections/' + sectionId : '{{ route('admin.page-sections.store') }}'" method="POST" enctype="multipart/form-data" class="flex flex-col overflow-hidden min-h-0" @submit="isSubmitting = true">
                @csrf
                <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>

                <div class="overflow-y-auto custom-scrollbar p-6 space-y-6 flex-1 bg-gray-50/30">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Assign to Page <span class="text-red-500">*</span></label>
                            <select name="display_location" x-model="formLocation" :class="{'pointer-events-none opacity-60': isSubmitting}" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-red-500 outline-none transition-all shadow-sm bg-white text-sm">
                                <option value="home">Home Page</option>
                                <option value="procurement">Procurement Page</option>
                                <optgroup label="Dynamic Pages">
                                    @foreach($dynamicPages as $page)
                                        <option value="page:{{ $page->slug }}">{{ $page->title }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Content Type <span class="text-red-500">*</span></label>
                            <select name="type" x-model="formType" :class="{'pointer-events-none opacity-60': isSubmitting}" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-red-500 outline-none transition-all shadow-sm bg-gray-50 text-sm">
                                <optgroup label="Custom Content">
                                    <option value="rich_text">Text / HTML Box</option>
                                    <option value="banner">Banner Image</option>
                                    <option value="video">&#x1F4F9; Video Embed (YouTube / Facebook / TikTok)</option>
                                </optgroup>
                                <optgroup label="Dynamic Widgets (Auto-updates)">
                                    <option value="widget_advisories">Recent Advisories List</option>
                                    <option value="widget_memoranda">Recent Memoranda List</option>
                                    <option value="widget_faqs">Curriculum FAQ Accordion</option>
                                    <option value="widget_materials">Recent Learning Materials</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>

                    {{-- WIDGET PREVIEW TEXT --}}
                    <div x-show="isWidget()" class="p-6 bg-blue-50 border border-blue-200 rounded-lg text-blue-800 text-center shadow-sm">
                        <svg class="w-10 h-10 mx-auto mb-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="font-bold text-lg">You selected a Dynamic Widget.</p>
                        <p class="text-sm mt-1">This block will automatically fetch and display content from the database. No manual text or image entry is required.</p>
                    </div>

                    {{-- SHOW IF TEXT --}}
                    <div x-show="formType === 'rich_text'" class="space-y-5">
                        <div>
                            <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Block Title (Optional)</label>
                            <input type="text" name="title" x-model="formTitle" :readonly="isSubmitting" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-red-500 outline-none transition-all shadow-sm text-sm" placeholder="Enter an optional heading...">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Text / HTML Content</label>
                            <textarea name="content" x-model="formContent" rows="8" :readonly="isSubmitting" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-red-500 outline-none transition-all shadow-sm text-sm" placeholder="Write your content here..."></textarea>
                        </div>
                    </div>

                    {{-- SHOW IF BANNER --}}
                    <div x-show="formType === 'banner'" class="p-6 border-2 border-dashed border-gray-300 rounded-lg bg-white shadow-sm text-center">
                        <label class="block text-gray-700 text-sm font-bold uppercase tracking-wider mb-4">Upload Banner Image</label>
                        <input type="file" name="image" :class="{'pointer-events-none opacity-60': isSubmitting}" class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer transition-colors">
                        <p class="text-xs text-gray-400 mt-4 italic">Recommended: JPG or PNG under 5MB.</p>
                    </div>

                    {{-- SHOW IF VIDEO --}}
                    <div x-show="formType === 'video'" class="space-y-4">
                        {{-- Panel wrapper — red/maroon themed to match site --}}
                        <div class="rounded-xl overflow-hidden border border-red-200 shadow-md">

                            {{-- Panel header strip --}}
                            <div class="bg-[#a52a2a] px-5 py-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-red-200" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                <span class="text-white font-bold text-sm uppercase tracking-wider">Video Embed Settings</span>
                            </div>

                            {{-- Panel body --}}
                            <div class="bg-red-50 p-5 space-y-4">

                                {{-- Block Title --}}
                                <div>
                                    <label class="block text-[#a52a2a] text-xs font-bold uppercase tracking-wider mb-1.5">Block Title <span class="text-gray-400 font-normal normal-case">(optional)</span></label>
                                    <input
                                        type="text"
                                        name="title"
                                        x-model="formTitle"
                                        :readonly="isSubmitting"
                                        placeholder="e.g. Watch Our Latest Activities"
                                        class="w-full border border-red-200 bg-white text-gray-800 placeholder-gray-400 p-3 rounded-lg focus:ring-2 focus:ring-[#a52a2a] focus:border-[#a52a2a] outline-none text-sm transition-all shadow-sm"
                                    >
                                </div>

                                {{-- Video URL --}}
                                <div>
                                    <label class="block text-[#a52a2a] text-xs font-bold uppercase tracking-wider mb-1.5">Video URL <span class="text-red-600">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                        </div>
                                        <input
                                            type="url"
                                            name="video_url"
                                            x-model="formVideoUrl"
                                            @input="buildVideoPreview(formVideoUrl, formVideoShape)"
                                            :readonly="isSubmitting"
                                            placeholder="Paste YouTube, Facebook, or TikTok link..."
                                            class="w-full pl-10 border border-red-200 bg-white text-gray-800 placeholder-gray-400 p-3 rounded-lg focus:ring-2 focus:ring-[#a52a2a] focus:border-[#a52a2a] outline-none text-sm transition-all shadow-sm"
                                        >
                                    </div>
                                    <p class="text-[10px] text-gray-500 mt-1.5 flex items-center gap-1">
                                        <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 px-1.5 py-0.5 rounded font-bold">YT</span>
                                        <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded font-bold">FB</span>
                                        <span class="inline-flex items-center gap-1 bg-gray-900 text-white px-1.5 py-0.5 rounded font-bold">TikTok</span>
                                        <span class="ml-1">links are supported</span>
                                    </p>
                                </div>

                                {{-- Shape + Caption side-by-side --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[#a52a2a] text-xs font-bold uppercase tracking-wider mb-1.5">Video Shape</label>
                                        <select
                                            name="video_shape"
                                            x-model="formVideoShape"
                                            @change="buildVideoPreview(formVideoUrl, formVideoShape)"
                                            :class="{'pointer-events-none opacity-60': isSubmitting}"
                                            class="w-full border border-red-200 bg-white text-gray-800 p-3 rounded-lg focus:ring-2 focus:ring-[#a52a2a] focus:border-[#a52a2a] outline-none text-sm shadow-sm"
                                        >
                                            <option value="landscape">&#x1F5A5; Landscape (Wide 16:9)</option>
                                            <option value="portrait">&#x1F4F1; Portrait / Reel (9:16)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[#a52a2a] text-xs font-bold uppercase tracking-wider mb-1.5">Caption <span class="text-gray-400 font-normal normal-case">(optional)</span></label>
                                        <input
                                            type="text"
                                            name="video_caption"
                                            x-model="formVideoCaption"
                                            :readonly="isSubmitting"
                                            placeholder="e.g. Brigada Eskwela 2024"
                                            class="w-full border border-red-200 bg-white text-gray-800 placeholder-gray-400 p-3 rounded-lg focus:ring-2 focus:ring-[#a52a2a] focus:border-[#a52a2a] outline-none text-sm transition-all shadow-sm"
                                        >
                                    </div>
                                </div>

                                {{-- Live Preview area --}}
                                <div x-show="videoPreviewSrc" class="mt-1">
                                    <div class="flex items-center gap-2 mb-3">
                                        <div class="flex-1 h-px bg-red-200"></div>
                                        <span class="text-[10px] font-bold text-[#a52a2a] uppercase tracking-widest px-2">Live Preview</span>
                                        <div class="flex-1 h-px bg-red-200"></div>
                                    </div>
                                    <div class="bg-white rounded-xl border border-red-100 p-4 shadow-sm">
                                        <div class="flex justify-center">
                                            <div
                                                :style="videoPreviewPortrait
                                                    ? 'position:relative;width:100%;max-width:240px;aspect-ratio:9/16;border-radius:10px;overflow:hidden;box-shadow:0 4px 20px rgba(165,42,42,0.25);'
                                                    : 'position:relative;width:100%;max-width:560px;aspect-ratio:16/9;border-radius:10px;overflow:hidden;box-shadow:0 4px 20px rgba(165,42,42,0.25);'"
                                            >
                                                <iframe
                                                    :src="videoPreviewSrc"
                                                    style="position:absolute;top:0;left:0;width:100%;height:100%;border:none;"
                                                    allowfullscreen
                                                ></iframe>
                                            </div>
                                        </div>
                                        <p x-show="formVideoCaption" x-text="formVideoCaption" class="text-center text-xs text-[#a52a2a] mt-3 font-medium italic"></p>
                                    </div>
                                </div>

                                {{-- Invalid URL warning --}}
                                <div x-show="!videoPreviewSrc && formVideoUrl" class="flex items-center gap-2 bg-yellow-50 border border-yellow-200 text-yellow-700 text-xs rounded-lg px-4 py-3">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <span>Could not parse this URL. Make sure it is a valid YouTube, Facebook, or TikTok link.</span>
                                </div>

                            </div>
                        </div>
                    </div>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
                        <div>
                            <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Display Order <span class="text-red-500">*</span></label>
                            <input type="number" name="sort_order" x-model="formOrder" required :readonly="isSubmitting" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-red-500 outline-none transition-all shadow-sm text-sm">
                            <p class="text-[10px] text-gray-500 mt-1.5 uppercase font-medium tracking-wide">Lower numbers appear first (e.g., 1 goes above 2).</p>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Visibility Status <span class="text-red-500">*</span></label>
                            <select name="is_active" x-model="formActive" :class="{'pointer-events-none opacity-60': isSubmitting}" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-red-500 outline-none transition-all shadow-sm bg-white text-sm">
                                <option value="1">Active (Visible)</option>
                                <option value="0">Hidden (Draft)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-100 px-6 py-4 flex justify-end gap-3 border-t border-gray-200 flex-shrink-0">
                    <button type="button" @click="modalOpen = false" :disabled="isSubmitting" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:text-gray-800 transition-colors disabled:opacity-50">Cancel</button>
                    <button type="submit" :disabled="isSubmitting" :class="{'opacity-75 cursor-wait': isSubmitting, 'hover:bg-red-800': !isSubmitting}" class="bg-red-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-colors text-sm uppercase tracking-wider flex items-center justify-center min-w-[150px]">
                        <span x-show="!isSubmitting">Save Block</span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Saving...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- DELETE CONFIRMATION MODAL --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="if (!isSubmitting) deleteModal = false">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <div class="text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Delete Content Block?</h3>
                <p class="text-gray-500 text-sm mb-5 px-4 leading-relaxed">Are you sure you want to permanently delete this content block from the page?</p>
                <div class="mb-8 max-h-32 overflow-y-auto custom-scrollbar text-center">
                    <span class="font-bold text-gray-900 break-all text-lg block" x-text="deleteTitle"></span>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="button" @click="deleteModal = false" :disabled="isSubmitting" class="flex-1 inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-all disabled:opacity-50">Cancel</button>
                <form :action="deleteAction" method="POST" class="flex-1 m-0 p-0 flex" @submit="isSubmitting = true">
                    @csrf
                    @method('DELETE')
                    <button type="submit" :disabled="isSubmitting" :class="{'opacity-75 cursor-wait': isSubmitting, 'hover:bg-red-700': !isSubmitting}" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-5 py-3 text-sm font-bold text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 transition-all">
                        <span x-show="!isSubmitting">Yes, Delete</span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Deleting...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- SUCCESS MODAL --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md p-8 overflow-hidden" @click.away="show = false">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
            <div class="text-center mb-8 px-4">
                <h3 class="text-2xl font-bold text-gray-900 mb-2 text-center uppercase tracking-tight">Success!</h3>
                <p class="text-gray-500 text-base leading-relaxed text-center">{{ session('success') }}</p>
            </div>
            <div class="flex">
                <button @click="show = false" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-6 py-3.5 text-base font-bold text-white shadow-sm hover:bg-red-700 transition-all focus:ring-2 focus:ring-red-500">Continue</button>
            </div>
        </div>
    </div>
    @endif

</div>

@push('scripts')
{{-- Drag and Drop Script --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var el = document.getElementById('sortable-sections');
        if (el) {
            var sortable = Sortable.create(el, {
                handle: '.drag-handle', // Only allow dragging from the grip icon
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: function (evt) {
                    let order = [];
                    // Loop through all rows to get the new order
                    document.querySelectorAll('#sortable-sections tr').forEach((row, index) => {
                        let newPosition = index + 1;
                        order.push({
                            id: row.getAttribute('data-id'),
                            position: newPosition
                        });
                        // Update the number visually in the table right away
                        let numberSpan = row.querySelector('.sort-number');
                        if(numberSpan) numberSpan.innerText = newPosition;
                    });

                    // Send AJAX request to save to database
                    fetch('{{ route('admin.page-sections.reorder') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ order: order })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            // Show success toast
                            let toast = document.getElementById('save-toast');
                            toast.classList.remove('opacity-0');
                            setTimeout(() => { toast.classList.add('opacity-0'); }, 2000);
                        }
                    })
                    .catch(error => console.error('Error saving order:', error));
                }
            });
        }
    });
</script>
@endpush
@endsection