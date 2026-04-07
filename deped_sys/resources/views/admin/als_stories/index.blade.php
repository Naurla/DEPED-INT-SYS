@extends('layouts.admin')

@section('page_title', 'ALS Stories')

@section('content')
<style>
    [x-cloak] { display: none !important; }
</style>

<div x-data="{ 
    uploadModal: false, 
    deleteModal: false,
    editMode: false,
    storyId: null,
    editItem: null,
    removeFile: false,
    removeImage: false,
    formData: { title: '', content: '' },
    openEdit(story) {
        this.editMode = true;
        this.storyId = story.id;
        this.editItem = story;
        this.formData.title = story.title;
        this.formData.content = story.content || '';
        this.removeFile = false;
        this.removeImage = false;
        this.uploadModal = true;
    },
    openCreate() {
        this.editMode = false;
        this.storyId = null;
        this.editItem = null;
        this.formData.title = '';
        this.formData.content = '';
        this.removeFile = false;
        this.removeImage = false;
        this.uploadModal = true;
    },
    confirmDelete(id, title) {
        this.storyId = id;
        this.deleteTitle = title;
        this.deleteModal = true;
    },
    deleteTitle: ''
}">

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative shadow-sm">
            <p class="font-bold text-sm">{{ session('success') }}</p>
        </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">ALS Stories</h2>
            <p class="text-gray-500 text-sm mt-1">Manage and share success stories from the Alternative Learning System.</p>
        </div>
        <button @click="openCreate()" class="bg-red-700 hover:bg-red-800 text-white text-sm font-bold px-4 py-2.5 rounded-lg shadow transition-colors flex items-center shrink-0 uppercase tracking-wider">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            New Story
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                        <th class="p-4 border-b whitespace-nowrap w-16">#</th>
                        <th class="p-4 border-b">Title</th>
                        <th class="p-4 border-b">Image</th>
                        <th class="p-4 border-b">Document</th>
                        <th class="p-4 border-b text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($stories as $index => $story)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 text-sm text-gray-600 font-medium align-middle">{{ $stories->firstItem() + $index }}</td>
                        <td class="p-4 font-bold text-gray-900 align-middle">{{ $story->title }}</td>
                        
                        <td class="p-4 align-middle">
                            @if($story->image_path)
                                <a href="{{ asset('storage/' . $story->image_path) }}" target="_blank" class="text-blue-600 font-bold hover:text-blue-800 hover:underline inline-flex items-center text-xs whitespace-nowrap">
                                    <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ Str::limit(basename($story->image_path), 15) }}
                                </a>
                            @else
                                <span class="text-gray-400 italic text-[10px]">N/A</span>
                            @endif
                        </td>

                        <td class="p-4 align-middle">
                            @if($story->file_path)
                                @php
                                    $extension = pathinfo($story->file_path, PATHINFO_EXTENSION);
                                    $isWord = in_array(strtolower($extension), ['doc', 'docx']);
                                    $isExcel = in_array(strtolower($extension), ['xls', 'xlsx', 'csv']);
                                    $isPdf = strtolower($extension) === 'pdf';
                                    
                                    $docColor = 'text-gray-600 hover:text-gray-800';
                                    if ($isWord) $docColor = 'text-blue-600 hover:text-blue-800';
                                    if ($isExcel) $docColor = 'text-green-600 hover:text-green-800';
                                    if ($isPdf) $docColor = 'text-red-600 hover:text-red-800';
                                @endphp
                                <a href="{{ asset('storage/' . $story->file_path) }}" target="_blank" class="{{ $docColor }} font-bold hover:underline inline-flex items-center text-xs whitespace-nowrap transition-colors">
                                    <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    {{ Str::limit(basename($story->file_path), 20) }}
                                </a>
                            @else
                                <span class="text-gray-400 italic text-[10px]">N/A</span>
                            @endif
                        </td>

                        <td class="p-4 align-middle">
                            <div class="flex justify-end gap-3 items-center">
                                <button @click="openEdit({{ collect($story)->toJson() }})" class="text-blue-600 font-bold uppercase text-xs hover:underline">Edit</button>
                                <button @click="confirmDelete({{ $story->id }}, '{{ addslashes($story->title) }}')" class="text-red-600 font-bold uppercase text-xs hover:underline">Delete</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-gray-500 italic">No stories found. Click "New Story" to create one.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($stories->hasPages())
        <div class="mt-4">
            {{ $stories->links() }}
        </div>
    @endif

    {{-- MODAL: ADD/EDIT STORY --}}
    <div x-show="uploadModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-2xl shadow-2xl overflow-hidden" @click.away="uploadModal = false">
            <div class="bg-red-700 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg" x-text="editMode ? 'Edit Story' : 'Upload New Story'"></h3>
                <button type="button" @click="uploadModal = false" class="hover:text-gray-200 text-2xl font-bold">&times;</button>
            </div>
            
            <form :action="editMode ? '/admin/als-stories/' + storyId : '{{ route('admin.als-stories.store') }}'" 
                  method="POST" enctype="multipart/form-data">
                @csrf
                <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>
                <input type="hidden" name="remove_file" :value="removeFile ? '1' : '0'">
                <input type="hidden" name="remove_image" :value="removeImage ? '1' : '0'">
                
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Story Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" x-model="formData.title" required 
                               class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Story Content <span class="text-red-500">*</span></label>
                        <textarea name="content" x-model="formData.content" required rows="6" 
                                  class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none resize-none"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-1">Featured Image</label>
                            <input type="file" name="image" accept="image/*" 
                                   class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white">
                            
                            <template x-if="editMode && editItem && editItem.image_path && !removeImage">
                                <div class="mt-2 flex items-center justify-between p-2 bg-blue-50 border border-blue-100 rounded-lg">
                                    <span class="text-xs font-bold text-blue-700 truncate max-w-[150px]" x-text="'Current: ' + editItem.image_path.split('/').pop()"></span>
                                    <button type="button" @click="removeImage = true" class="text-red-500 hover:bg-red-50 p-1 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-1">Attached Document</label>
                            <input type="file" name="file" accept=".pdf,.xlsx,.xls,.csv,.doc,.docx" 
                                   class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white">
                            
                            <template x-if="editMode && editItem && editItem.file_path && !removeFile">
                                <div class="mt-2 flex items-center justify-between p-2 bg-red-50 border border-red-100 rounded-lg">
                                    <span class="text-xs font-bold text-red-700 truncate max-w-[150px]" x-text="'Current: ' + editItem.file_path.split('/').pop()"></span>
                                    <button type="button" @click="removeFile = true" class="text-red-500 hover:bg-red-50 p-1 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 items-center border-t border-gray-100">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-colors text-sm" x-text="editMode ? 'Save Changes' : 'Upload Story'"></button>
                    <button type="button" @click="uploadModal = false" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- GLOBAL MODAL: Delete Confirmation --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity" style="display: none;">
        <div class="bg-white rounded-2xl p-8 shadow-2xl z-50 w-full max-w-sm transform transition-all relative" @click.away="deleteModal = false">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            
            <h3 class="text-xl font-bold text-gray-800 mb-2 text-center">Delete Story?</h3>
            <p class="text-gray-500 text-sm mb-6 text-center">Are you sure you want to delete <br><span class="font-bold text-gray-800" x-text="deleteTitle"></span>? <br>This action cannot be undone.</p>
            
            <div class="flex space-x-3 border-t border-gray-100 pt-4">
                <button type="button" @click="deleteModal = false" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                
                <form :action="'/admin/als-stories/' + storyId" method="POST" class="flex-1 m-0 p-0 flex">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2.5 bg-red-700 text-white rounded-xl font-bold text-sm hover:bg-red-800 shadow-sm transition-colors">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection