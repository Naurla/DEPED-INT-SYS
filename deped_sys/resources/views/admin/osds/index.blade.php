@extends('layouts.admin')

@section('page_title', 'OSDS Organizational Charts')

@section('content')
<div x-data="{ 
    uploadModal: {{ (session()->has('error_duplicate') || session()->get('edit_id') || $errors->any()) ? 'true' : 'false' }}, 
    deleteModal: false,
    successModal: {{ session('success') ? 'true' : 'false' }},
    editMode: {{ (session()->has('edit_id') || old('edit_id')) ? 'true' : 'false' }},
    itemId: '{{ session()->get('edit_id') ?? old('edit_id') }}',
    deleteUrl: '', 
    formData: { 
        title: '{!! addslashes(old('title')) !!}', 
        description: '{!! addslashes(old('description')) !!}' 
    },
    openEdit(item) {
        this.editMode = true;
        this.itemId = item.id;
        this.formData.title = item.title;
        this.formData.description = item.description || '';
        this.uploadModal = true;
    },
    openCreate() {
        this.editMode = false;
        this.itemId = null;
        this.formData.title = '';
        this.formData.description = '';
        this.uploadModal = true;
    },
    confirmDelete(url) {
        this.deleteUrl = url;
        this.deleteModal = true;
    }
}">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Office of the Schools Division Superintendent</h2>
            <p class="text-gray-500 text-sm mt-1">Manage organizational charts for OSDS.</p>
        </div>
        <button @click="openCreate()" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-4 rounded-lg shadow-sm transition-colors">
            + Upload Chart
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                        <th class="p-4 border-b whitespace-nowrap">ID</th>
                        <th class="p-4 border-b whitespace-nowrap">Preview</th>
                        <th class="p-4 border-b">Title</th>
                        <th class="p-4 border-b">Description</th>
                        <th class="p-4 border-b text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($osds as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 text-sm text-gray-600 font-medium align-middle">{{ $loop->iteration }}</td>
                            <td class="p-4 text-sm whitespace-nowrap align-middle">
                                <a href="{{ route('serve.image', $item->image_path) }}" target="_blank">
                                    <img src="{{ route('serve.image', $item->image_path) }}" alt="{{ $item->title }}" class="h-12 w-auto object-cover rounded border border-gray-200 hover:opacity-75 transition-opacity">
                                </a>
                            </td>
                            <td class="p-4 font-semibold text-gray-800 align-middle">{{ $item->title }}</td>
                            
                            {{-- Expandable Description Data --}}
                            <td class="p-4 text-sm text-gray-500 max-w-sm align-middle">
                                @if($item->description)
                                    <div x-data="{ expanded: false }">
                                        <p class="cursor-pointer hover:text-gray-800 transition-colors whitespace-normal break-words"
                                           :class="expanded ? '' : 'line-clamp-2'"
                                           @click="expanded = !expanded"
                                           title="Click to show/hide full description">
                                            {{ $item->description }}
                                        </p>
                                    </div>
                                @else
                                    <span class="italic text-gray-400">No description</span>
                                @endif
                            </td>

                            <td class="p-4 align-middle">
                                <div class="flex justify-end gap-3 items-center">
                                    <button type="button" @click="openEdit({{ collect($item)->toJson() }})" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase hover:underline">Edit</button>
                                    <button type="button" @click="confirmDelete('{{ route('admin.osds.destroy', $item->id) }}')" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-500 italic">No organizational charts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add / Edit Modal (Extra Large size, clearer text, scrollable content) --}}
    <div x-show="uploadModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-5xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]" @click.away="uploadModal = false">
            
            <!-- Fixed Header -->
            <div class="bg-red-700 px-8 py-5 flex justify-between items-center text-white flex-shrink-0">
                <h3 class="font-bold text-2xl" x-text="editMode ? 'Edit Chart' : 'Upload New Chart'"></h3>
                <button type="button" @click="uploadModal = false" class="hover:text-gray-200 text-4xl font-bold">&times;</button>
            </div>
            
            <!-- Flex Form -->
            <form id="osdsForm" :action="editMode ? '/admin/osds/' + itemId : '{{ route('admin.osds.store') }}'" 
                  method="POST" enctype="multipart/form-data" class="flex flex-col overflow-hidden min-h-0">
                @csrf
                <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>
                <template x-if="editMode"><input type="hidden" name="edit_id" x-model="itemId"></template>

                <!-- Scrollable Content Area -->
                <div class="p-8 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                    
                    @if(session()->has('error_duplicate') || $errors->any())
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-md">
                            <p class="text-base font-bold text-red-700 mb-1">Please fix the following errors:</p>
                            <ul class="list-disc pl-5 text-base text-red-700 space-y-0.5">
                                @if(session()->has('error_duplicate'))
                                    <li>{{ session('error_duplicate') }}</li>
                                @endif
                                @if ($errors->any())
                                    @foreach ($errors->all() as $error)
                                        @if(session()->has('error_duplicate') && Str::contains($error, 'already exists')) @continue @endif
                                        <li>{{ $error }}</li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    @endif
                    
                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Chart Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" x-model="formData.title" placeholder="e.g., OSDS Organizational Chart 2024" required 
                               class="w-full border {{ ($errors->has('title') || session('error_duplicate')) ? 'border-red-500' : 'border-gray-300' }} p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                        
                        @if(session('error_duplicate'))
                            <p class="text-red-500 text-base mt-1.5 font-medium">{{ session('error_duplicate') }}</p>
                        @endif
                        @error('title')
                            <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Description <span class="font-normal text-gray-500 text-sm">(Optional)</span></label>
                        <textarea name="description" x-model="formData.description" rows="4" 
                                  class="w-full border @error('description') border-red-500 @else border-gray-300 @enderror p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none resize-none"></textarea>
                        @error('description')
                            <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg border @error('image') border-red-500 @else border-gray-200 @enderror mt-2">
                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2">Chart Image <span x-show="!editMode" class="text-red-500">*</span></label>
                            <input type="file" name="image" accept="image/*" :required="!editMode" 
                                   class="w-full border border-gray-300 p-3.5 rounded-lg text-lg text-gray-600 file:mr-5 file:py-3 file:px-6 file:rounded-md file:border-0 file:text-base file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white">
                            <span x-show="editMode" class="text-sm text-gray-500 mt-2 block font-medium italic">Leave blank to keep the current image.</span>
                            @error('image')
                                <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Fixed Footer -->
                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-4 items-center border-t border-gray-200 flex-shrink-0">
                    <button type="submit" form="osdsForm" class="bg-red-700 hover:bg-red-800 text-white font-bold py-3.5 px-10 rounded-lg shadow-md transition-colors text-lg" x-text="editMode ? 'Save Changes' : 'Upload Chart'"></button>
                    <button type="button" @click="uploadModal = false" class="px-8 py-3.5 text-lg font-bold text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Red Success Modal --}}
    <div x-show="successModal" x-cloak class="fixed inset-0 z-[105] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-2xl p-8 shadow-2xl z-50 w-full max-w-sm transform transition-all relative text-center" @click.away="successModal = false">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-50 mb-4">
                <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Success!</h3>
            <div class="mt-2 mb-6">
                <p class="text-sm text-gray-500">{{ session('success') }}</p>
            </div>
            <button type="button" @click="successModal = false" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2.5 bg-red-700 text-base font-bold text-white hover:bg-red-800 transition-colors sm:text-sm">
                Continue
            </button>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-2xl p-8 shadow-2xl z-[110] w-full max-w-sm transform transition-all relative" @click.away="deleteModal = false">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2 text-center">Confirm Deletion</h3>
            <div class="text-gray-500 text-sm mb-6 text-center max-h-40 overflow-y-auto px-1">
                Are you sure you want to delete this chart? <br>This action cannot be undone.
            </div>
            <div class="flex space-x-3 border-t border-gray-100 pt-4">
                <button type="button" @click="deleteModal = false" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-colors">Cancel</button>
                <form :action="deleteUrl" method="POST" class="flex-1 m-0 p-0 flex">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2.5 bg-red-700 text-white rounded-xl font-bold text-sm hover:bg-red-800 shadow-sm transition-colors">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    body.modal-open { overflow: hidden; }
    
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
@endpush