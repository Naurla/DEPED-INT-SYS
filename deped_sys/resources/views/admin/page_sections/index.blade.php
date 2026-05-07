@extends('layouts.admin')
@section('page_title', 'Page Content Builder')

@section('content')
<style>
    [x-cloak] { display: none !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #fca5a5; border-radius: 10px; }
</style>

<div class="w-full" x-data="{ 
    modalOpen: false, 
    editMode: false,
    sectionId: null,
    
    // Delete Modal Data
    deleteModal: false,
    deleteAction: '',
    deleteTitle: '',
    
    // Form Data
    formLocation: 'home',
    formType: 'rich_text',
    formTitle: '',
    formContent: '',
    formOrder: 1,
    formActive: '1',

    openAdd() {
        this.editMode = false;
        this.formLocation = 'home';
        this.formType = 'rich_text';
        this.formTitle = '';
        this.formContent = '';
        this.formOrder = 1;
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
        this.formActive = section.is_active ? '1' : '0';
        this.modalOpen = true;
    },

    confirmDelete(action, title) {
        this.deleteAction = action;
        this.deleteTitle = title;
        this.deleteModal = true;
    },

    isWidget() {
        return this.formType.startsWith('widget_');
    }
}">

    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4 w-full">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Content Builder</h2>
            <p class="text-gray-500 text-sm mt-1">Assign custom text, banners, or dynamic widgets to any page.</p>
        </div>
        <button @click="openAdd()" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-5 rounded-lg shadow-sm transition-colors text-sm uppercase tracking-wider flex items-center whitespace-nowrap">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add Content Block
        </button>
    </div>

    {{-- Error Block --}}
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-bold text-red-800 uppercase tracking-wide">Validation Error</h3>
                    <div class="mt-2 text-sm text-red-700 font-medium">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Data Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6 w-full">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase font-bold border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 w-16 text-center">#</th>
                        <th class="px-6 py-4 w-1/4">Location</th>
                        <th class="px-6 py-4 w-1/6">Type</th>
                        <th class="px-6 py-4">Preview / Title</th>
                        <th class="px-6 py-4 text-center w-24">Order</th>
                        <th class="px-6 py-4 text-right w-32">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sections as $index => $sec)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 text-sm text-gray-600 font-medium text-center align-middle">{{ $sections->firstItem() + $index }}</td>
                        <td class="px-6 py-4 font-bold text-red-700 align-middle">{{ strtoupper(str_replace('page:', 'Page: ', $sec->display_location)) }}</td>
                        <td class="px-6 py-4 align-middle">
                            <span class="px-3 py-1 bg-gray-200 text-gray-700 rounded-full text-[10px] font-bold uppercase tracking-wider">{{ str_replace('_', ' ', $sec->type) }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 align-middle">
                            @if($sec->type == 'banner')
                                <img src="{{ asset('storage/'.$sec->image_path) }}" class="h-12 w-auto object-cover rounded shadow-sm border border-gray-200">
                            @elseif(str_starts_with($sec->type, 'widget_'))
                                <span class="italic text-gray-400 font-medium">Dynamic Widget Feed</span>
                            @else
                                <strong class="text-gray-900 block mb-1">{{ $sec->title ?? 'No Title' }}</strong>
                                <span class="text-xs text-gray-500">{{ Str::limit(strip_tags($sec->content), 50) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center font-bold text-gray-900 align-middle">{{ $sec->sort_order }}</td>
                        <td class="px-6 py-4 text-right align-middle">
                            <div class="flex items-center justify-end gap-3">
                                <button type="button" @click="openEdit({{ $sec->toJson() }})" class="text-blue-600 font-bold uppercase text-xs hover:underline transition-all">Edit</button>
                                <button type="button" @click="confirmDelete('{{ route('admin.page-sections.destroy', $sec->id) }}', '{{ addslashes($sec->title ?? 'Content Block') }}')" class="text-red-600 font-bold uppercase text-xs hover:underline transition-all">Delete</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            <p class="text-gray-500 font-medium">No content blocks added yet.</p>
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
        <div class="bg-white rounded-xl w-full max-w-4xl shadow-2xl flex flex-col max-h-[95vh]" @click.away="modalOpen = false">
            <div class="bg-red-700 px-6 py-4 text-white flex justify-between items-center flex-shrink-0">
                <h3 class="font-bold text-lg uppercase tracking-wider" x-text="editMode ? 'Edit Content Block' : 'New Content Block'"></h3>
                <button type="button" @click="modalOpen = false" class="text-3xl font-bold hover:text-gray-200 leading-none">&times;</button>
            </div>
            
            <form :action="editMode ? '/admin/page-sections/' + sectionId : '{{ route('admin.page-sections.store') }}'" method="POST" enctype="multipart/form-data" class="flex flex-col overflow-hidden min-h-0">
                @csrf
                <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>

                <div class="overflow-y-auto custom-scrollbar p-6 space-y-6 flex-1 bg-gray-50/30">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Assign to Page <span class="text-red-500">*</span></label>
                            <select name="display_location" x-model="formLocation" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-red-500 outline-none transition-all shadow-sm bg-white text-sm">
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
                            <select name="type" x-model="formType" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-red-500 outline-none transition-all shadow-sm bg-gray-50 text-sm">
                                <optgroup label="Custom Content">
                                    <option value="rich_text">Text / HTML Box</option>
                                    <option value="banner">Banner Image</option>
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
                            <input type="text" name="title" x-model="formTitle" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-red-500 outline-none transition-all shadow-sm text-sm" placeholder="Enter an optional heading...">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Text / HTML Content</label>
                            <textarea name="content" x-model="formContent" rows="8" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-red-500 outline-none transition-all shadow-sm text-sm" placeholder="Write your content here..."></textarea>
                        </div>
                    </div>

                    {{-- SHOW IF BANNER --}}
                    <div x-show="formType === 'banner'" class="p-6 border-2 border-dashed border-gray-300 rounded-lg bg-white shadow-sm text-center">
                        <label class="block text-gray-700 text-sm font-bold uppercase tracking-wider mb-4">Upload Banner Image</label>
                        <input type="file" name="image" class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer transition-colors">
                        <p class="text-xs text-gray-400 mt-4 italic">Recommended: JPG or PNG under 5MB.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
                        <div>
                            <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Display Order <span class="text-red-500">*</span></label>
                            <input type="number" name="sort_order" x-model="formOrder" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-red-500 outline-none transition-all shadow-sm text-sm">
                            <p class="text-[10px] text-gray-500 mt-1.5 uppercase font-medium tracking-wide">Lower numbers appear first (e.g., 1 goes above 2).</p>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Visibility Status <span class="text-red-500">*</span></label>
                            <select name="is_active" x-model="formActive" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-red-500 outline-none transition-all shadow-sm bg-white text-sm">
                                <option value="1">Active (Visible)</option>
                                <option value="0">Hidden (Draft)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-100 px-6 py-4 flex justify-end gap-3 border-t border-gray-200 flex-shrink-0">
                    <button type="button" @click="modalOpen = false" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-colors text-sm uppercase tracking-wider">Save Block</button>
                </div>
            </form>
        </div>
    </div>

    {{-- DELETE CONFIRMATION MODAL --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="deleteModal = false">
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
                <button type="button" @click="deleteModal = false" class="flex-1 inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-all">Cancel</button>
                <form :action="deleteAction" method="POST" class="flex-1 m-0 p-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-red-700 transition-all">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>

    {{-- SUCCESS MODAL --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="show = false">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
            <div class="text-center mb-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Success!</h3>
                <p class="text-gray-500 text-base leading-relaxed px-4">{{ session('success') }}</p>
            </div>
            <div class="flex">
                <button type="button" @click="show = false" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-6 py-3 text-base font-bold text-white shadow-sm hover:bg-red-700 transition-all">Continue</button>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection