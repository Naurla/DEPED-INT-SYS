@extends('layouts.admin')

@section('page_title', 'ALS Stories')

@section('content')
<style>
    [x-cloak] { display: none !important; }
    
    /* Subtle scrollbar for the delete modal target box and forms */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent; 
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #fca5a5; 
        border-radius: 10px;
    }
</style>

<div x-data="{ 
    addModal: {{ (old('form_type') === 'add' && $errors->any()) ? 'true' : 'false' }}, 
    editModal: {{ (old('form_type') === 'edit' && $errors->any()) ? 'true' : 'false' }}, 
    deleteModal: false,
    successModal: {{ session('success') ? 'true' : 'false' }},
    
    editItem: {{ (old('form_type') === 'edit' && $errors->any()) ? Js::from([
        'id' => old('id'),
        'title' => old('title'),
        'content' => old('content'),
        'image_path' => old('existing_image'),
        'file_path' => old('existing_file')
    ]) : 'null' }}, 
    
    removeImage: {{ old('remove_image') == '1' ? 'true' : 'false' }}, 
    removeFile: {{ old('remove_file') == '1' ? 'true' : 'false' }},
    deleteId: null,
    deleteTitle: '',
    
    openEdit(story) {
        this.editItem = story;
        this.removeImage = false;
        this.removeFile = false;
        this.editModal = true;
    },
    confirmDelete(id, title) {
        this.deleteId = id;
        this.deleteTitle = title;
        this.deleteModal = true;
    }
}">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">ALS Stories</h2>
            <p class="text-gray-500 text-sm mt-1">Manage and share success stories from the Alternative Learning System.</p>
        </div>
        <button @click="addModal = true" class="bg-red-700 hover:bg-red-800 text-white text-sm font-bold px-4 py-2.5 rounded-lg shadow transition-colors flex items-center shrink-0 uppercase tracking-wider">
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
                        <th class="p-4 border-b whitespace-nowrap w-16 text-center">#</th>
                        <th class="p-4 border-b">Title</th>
                        <th class="p-4 border-b">Image</th>
                        <th class="p-4 border-b">Document</th>
                        <th class="p-4 border-b text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($stories as $index => $story)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 text-sm text-gray-600 font-medium text-center align-middle">{{ $stories->firstItem() + $index }}</td>
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
                        <td colspan="5" class="p-10 text-center text-gray-500 italic">No stories found. Click "New Story" to create one.</td>
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

    {{-- Add Modal (Extra Large size, clearer text, scrollable content) --}}
    <div x-show="addModal" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-5xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]" @click.away="addModal = false">
            
            <!-- Fixed Header -->
            <div class="bg-red-700 px-8 py-5 flex justify-between items-center text-white flex-shrink-0">
                <h3 class="font-bold text-2xl">Upload New Story</h3>
                <button type="button" @click="addModal = false" class="hover:text-gray-200 text-4xl font-bold">&times;</button>
            </div>

            <!-- Flex Form -->
            <form action="{{ route('admin.als-stories.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col overflow-hidden min-h-0">
                @csrf
                <input type="hidden" name="form_type" value="add">
                
                <!-- Scrollable Content Area -->
                <div class="p-8 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Story Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('form_type') === 'add' ? old('title') : '' }}" required class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                        @if(old('form_type') === 'add') @error('title') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror @endif
                    </div>

                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Story Content <span class="text-red-500">*</span></label>
                        <textarea name="content" rows="6" required class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none resize-none">{{ old('form_type') === 'add' ? old('content') : '' }}</textarea>
                        @if(old('form_type') === 'add') @error('content') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror @endif
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-lg border border-gray-200">
                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2">Featured Image</label>
                            <input type="file" name="image" accept="image/*" class="w-full border border-gray-300 p-3.5 rounded-lg text-lg text-gray-600 file:mr-5 file:py-3 file:px-6 file:rounded-md file:border-0 file:text-base file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white">
                            @if(old('form_type') === 'add') @error('image') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror @endif
                        </div>
                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2">Attached Document</label>
                            <input type="file" name="file" accept=".pdf,.xlsx,.xls,.csv,.doc,.docx" class="w-full border border-gray-300 p-3.5 rounded-lg text-lg text-gray-600 file:mr-5 file:py-3 file:px-6 file:rounded-md file:border-0 file:text-base file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white">
                            @if(old('form_type') === 'add') @error('file') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror @endif
                        </div>
                    </div>
                </div>

                <!-- Fixed Footer -->
                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-4 items-center border-t border-gray-200 flex-shrink-0">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-3.5 px-10 rounded-lg shadow-md transition-colors text-lg">Upload Story</button>
                    <button type="button" @click="addModal = false" class="px-8 py-3.5 text-lg font-bold text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modal (Extra Large size, clearer text, scrollable content) --}}
    <div x-show="editModal" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-5xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]" @click.away="editModal = false">
            
            <!-- Fixed Header -->
            <div class="bg-red-700 px-8 py-5 flex justify-between items-center text-white flex-shrink-0">
                <h3 class="font-bold text-2xl">Edit Story</h3>
                <button type="button" @click="editModal = false" class="hover:text-gray-200 text-4xl font-bold">&times;</button>
            </div>
            
            <!-- Flex Form -->
            <form :action="`/admin/als-stories/${editItem?.id}`" method="POST" enctype="multipart/form-data" class="flex flex-col overflow-hidden min-h-0">
                @csrf @method('PUT')
                <input type="hidden" name="form_type" value="edit">
                <input type="hidden" name="id" :value="editItem?.id">
                <input type="hidden" name="existing_image" :value="editItem?.image_path">
                <input type="hidden" name="existing_file" :value="editItem?.file_path">
                <input type="hidden" name="remove_image" :value="removeImage ? '1' : '0'">
                <input type="hidden" name="remove_file" :value="removeFile ? '1' : '0'">

                <!-- Scrollable Content Area -->
                <div class="p-8 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Story Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" x-model="editItem.title" required class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                        @if(old('form_type') === 'edit') @error('title') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror @endif
                    </div>

                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Story Content <span class="text-red-500">*</span></label>
                        <textarea name="content" x-model="editItem.content" required rows="6" class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none resize-none"></textarea>
                        @if(old('form_type') === 'edit') @error('content') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-lg border border-gray-200">
                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2">Replace Photo</label>
                            <input type="file" name="image" accept="image/*" class="w-full border border-gray-300 p-3.5 rounded-lg text-lg text-gray-600 file:mr-5 file:py-3 file:px-6 file:rounded-md file:border-0 file:text-base file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white">
                            @if(old('form_type') === 'edit') @error('image') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror @endif
                            <template x-if="editItem && editItem.image_path && !removeImage">
                                <div class="mt-3 flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <span class="text-base text-blue-800 font-bold truncate max-w-[200px]" x-text="'Current: ' + editItem.image_path.split('/').pop()"></span>
                                    <button type="button" @click="removeImage = true" class="text-red-500 hover:bg-red-100 p-1.5 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                </div>
                            </template>
                        </div>

                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2">Replace Document</label>
                            <input type="file" name="file" accept=".pdf,.xlsx,.xls,.csv,.doc,.docx" class="w-full border border-gray-300 p-3.5 rounded-lg text-lg text-gray-600 file:mr-5 file:py-3 file:px-6 file:rounded-md file:border-0 file:text-base file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white">
                            @if(old('form_type') === 'edit') @error('file') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror @endif
                            <template x-if="editItem && editItem.file_path && !removeFile">
                                <div class="mt-3 flex items-center justify-between p-3 bg-red-50 border border-red-200 rounded-lg">
                                    <span class="text-base text-red-800 font-bold truncate max-w-[200px]" x-text="'Current: ' + editItem.file_path.split('/').pop()"></span>
                                    <button type="button" @click="removeFile = true" class="text-red-500 hover:bg-red-100 p-1.5 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Fixed Footer -->
                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-4 items-center border-t border-gray-200 flex-shrink-0">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-3.5 px-10 rounded-lg shadow-md transition-colors text-lg">Save Changes</button>
                    <button type="button" @click="editModal = false" class="px-8 py-3.5 text-lg font-bold text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODERNIZED GLOBAL MODAL: Delete Confirmation --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="deleteModal = false">
            
            <!-- Soft Double-Ring Icon -->
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            
            <!-- Text Content -->
            <div class="text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Delete Als Story?</h3>
                <p class="text-gray-500 text-sm mb-5">
                    You are about to permanently delete this Als Story:
                </p>
                
                <!-- Target Highlight (Scrollable, no background, bold dark text) -->
                <div class="mb-8 max-h-32 overflow-y-auto custom-scrollbar">
                    <span class="font-bold text-gray-900 break-all text-lg block" x-text="deleteTitle"></span>
                </div>
                
                <p class="text-gray-400 text-sm italic mb-8">
                    This action cannot be undone.
                </p>
            </div>
            
            <!-- Action Buttons (Perfectly Centered & Balanced) -->
            <div class="flex gap-3">
                <button type="button" @click="deleteModal = false" class="flex-1 inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:ring-offset-1 transition-all">
                    Cancel
                </button>
                
                <form :action="`/admin/als-stories/${deleteId}`" method="POST" class="flex-1 m-0 p-0">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-all">
                        Yes, Delete it
                    </button>
                </form>
            </div>

        </div>
    </div>

    {{-- MODERNIZED GLOBAL MODAL: Success Message --}}
    <div x-show="successModal" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="successModal = false">
            
            <!-- Soft Double-Ring Icon -->
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            
            <!-- Text Content -->
            <div class="text-center mb-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Success!</h3>
                <p class="text-gray-500 text-base">
                    @if(session('success'))
                        {{ session('success') }}
                    @else
                        Operation completed successfully.
                    @endif
                </p>
            </div>
            
            <!-- Action Button -->
            <div class="flex">
                <button type="button" @click="successModal = false" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-6 py-3 text-base font-bold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-all">
                    Continue
                </button>
            </div>

        </div>
    </div>

</div>
@endsection