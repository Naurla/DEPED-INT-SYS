@extends('layouts.admin')

@section('page_title', 'SGOD Organizational Charts')

@section('content')
<div x-data="{ 
    uploadModal: false, 
    deleteModal: false,
    editMode: false,
    itemId: null,
    formData: { title: '', description: '' },
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
    confirmDelete(id) {
        this.itemId = id;
        this.deleteModal = true;
    }
}">

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">School Governance and Operations Division</h2>
            <p class="text-gray-500 text-sm mt-1">Manage organizational charts for SGOD.</p>
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
                        {{-- Added Description Column --}}
                        <th class="p-4 border-b">Description</th>
                        <th class="p-4 border-b text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sgods as $item)
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
                                    <button type="button" @click="confirmDelete({{ $item->id }})" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline">Delete</button>
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

    {{-- Add / Edit Modal --}}
    <div x-show="uploadModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-2xl shadow-2xl overflow-hidden" @click.away="uploadModal = false">
            <div class="bg-red-700 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg" x-text="editMode ? 'Edit Chart' : 'Upload New Chart'"></h3>
                <button type="button" @click="uploadModal = false" class="hover:text-gray-200 text-2xl font-bold">&times;</button>
            </div>
            
            <form :action="editMode ? '/admin/sgod/' + itemId : '{{ route('admin.sgod.store') }}'" 
                  method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                @csrf
                <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>
                
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-1">Chart Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" x-model="formData.title" placeholder="e.g., SGOD Organizational Chart 2024" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-1">Description <span class="font-normal text-gray-500 text-xs">(Optional)</span></label>
                    <textarea name="description" x-model="formData.description" rows="3" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none"></textarea>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mt-2">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1">Chart Image <span x-show="!editMode" class="text-red-500">*</span></label>
                        <input type="file" name="image" accept="image/*" :required="!editMode" class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white">
                        <span x-show="editMode" class="text-[10px] text-gray-500 mt-2 block font-medium italic">Leave blank to keep the current image.</span>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 items-center border-t border-gray-100 -mx-6 -mb-6 mt-6">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-colors text-sm" x-text="editMode ? 'Save Changes' : 'Upload Chart'"></button>
                    <button type="button" @click="uploadModal = false" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-2xl p-8 shadow-2xl z-50 w-full max-w-sm transform transition-all relative" @click.away="deleteModal = false">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            
            <h3 class="text-xl font-bold text-gray-800 mb-2 text-center">Confirm Deletion</h3>
            <p class="text-gray-500 text-sm mb-6 text-center">Are you sure you want to delete this chart? <br>This action cannot be undone.</p>
            
            <div class="flex space-x-3 border-t border-gray-100 pt-4">
                <button type="button" @click="deleteModal = false" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                
                <form :action="'/admin/sgod/' + itemId" method="POST" class="flex-1 m-0 p-0 flex">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2.5 bg-red-700 text-white rounded-xl font-bold text-sm hover:bg-red-800 shadow-sm transition-colors">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush