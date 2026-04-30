@extends('layouts.admin')

@section('page_title', 'CID Organizational Charts')

@section('content')
<style>
    [x-cloak] { display: none !important; }
    
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
        'description' => old('description'),
        'image_path' => old('existing_image')
    ]) : 'null' }}, 
    
    deleteId: null,
    deleteTitle: '',
    
    openEdit(item) {
        this.editItem = item;
        this.editModal = true;
    },
    confirmDelete(id, title) {
        this.deleteId = id;
        this.deleteTitle = title;
        this.deleteModal = true;
    }
}">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Curriculum Implementation Division</h2>
            <p class="text-gray-500 text-sm mt-1">Manage organizational charts for CID.</p>
        </div>
        <button @click="addModal = true" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-4 rounded-lg shadow transition-colors text-sm uppercase tracking-wider">
            + Add New Chart
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                        <th class="p-4 border-b whitespace-nowrap text-center w-16">ID</th>
                        <th class="p-4 border-b whitespace-nowrap">Preview</th>
                        <th class="p-4 border-b">Title</th>
                        <th class="p-4 border-b">Description</th>
                        <th class="p-4 border-b text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($cids as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 text-sm text-gray-600 font-medium text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="p-4 text-sm whitespace-nowrap align-middle">
                                <a href="{{ route('serve.image', $item->image_path) }}" target="_blank">
                                    <img src="{{ route('serve.image', $item->image_path) }}" alt="{{ $item->title }}" class="h-12 w-auto object-cover rounded border border-gray-200 hover:opacity-75 transition-opacity">
                                </a>
                            </td>
                            <td class="p-4 font-bold text-gray-900 align-middle">{{ $item->title }}</td>
                            <td class="p-4 text-sm text-gray-600 max-w-sm align-middle">
                                @if($item->description)
                                    <div x-data="{ expanded: false }">
                                        <p class="cursor-pointer hover:text-gray-900 transition-colors break-words"
                                           :class="expanded ? '' : 'line-clamp-2 italic'"
                                           @click="expanded = !expanded"
                                           title="Click to show/hide">
                                            {{ $item->description }}
                                        </p>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic text-[10px]">No description</span>
                                @endif
                            </td>
                            <td class="p-4 align-middle">
                                <div class="flex justify-end gap-3 items-center">
                                    <button @click="openEdit({{ collect($item)->toJson() }})" class="text-blue-600 font-bold uppercase text-xs hover:underline">Edit</button>
                                    <button @click="confirmDelete({{ $item->id }}, '{{ addslashes($item->title) }}')" class="text-red-600 font-bold uppercase text-xs hover:underline">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-10 text-center text-gray-500 italic">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($cids->hasPages())
        <div class="mt-4">{{ $cids->links() }}</div>
    @endif

    {{-- Add Modal --}}
    <div x-show="addModal" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-5xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]" @click.away="addModal = false">
            
            <!-- Fixed Header -->
            <div class="bg-red-700 px-8 py-5 flex justify-between items-center text-white flex-shrink-0">
                <h3 class="font-bold text-2xl">Upload New Chart</h3>
                <button type="button" @click="addModal = false" class="hover:text-gray-200 text-4xl font-bold">&times;</button>
            </div>

            <!-- Flex Form -->
            <form action="{{ route('admin.cid.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col overflow-hidden min-h-0">
                @csrf
                <input type="hidden" name="form_type" value="add">
                
                <!-- Scrollable Content Area -->
                <div class="p-8 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Chart Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('form_type') === 'add' ? old('title') : '' }}" required class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                        @if(old('form_type') === 'add') @error('title') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror @endif
                    </div>
                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Description</label>
                        <textarea name="description" rows="5" class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none resize-none">{{ old('form_type') === 'add' ? old('description') : '' }}</textarea>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                        <label class="block text-gray-800 text-lg font-bold mb-2">Chart Image <span class="text-red-500">*</span></label>
                        <input type="file" name="image" accept="image/*" required class="w-full border border-gray-300 p-3.5 rounded-lg text-lg text-gray-600 file:mr-5 file:py-3 file:px-6 file:rounded-md file:border-0 file:text-base file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white">
                        @if(old('form_type') === 'add') @error('image') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror @endif
                    </div>
                </div>

                <!-- Fixed Footer -->
                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-4 border-t border-gray-200 flex-shrink-0">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-3.5 px-10 rounded-lg shadow-md text-lg">Upload Chart</button>
                    <button type="button" @click="addModal = false" class="px-8 py-3.5 text-lg font-bold text-gray-600 hover:text-gray-800">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div x-show="editModal" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-5xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]" @click.away="editModal = false">
            
            <!-- Fixed Header -->
            <div class="bg-red-700 px-8 py-5 flex justify-between items-center text-white flex-shrink-0">
                <h3 class="font-bold text-2xl">Edit Chart</h3>
                <button type="button" @click="editModal = false" class="hover:text-gray-200 text-4xl font-bold">&times;</button>
            </div>

            <!-- Flex Form -->
            <form :action="`/admin/cid/${editItem?.id}`" method="POST" enctype="multipart/form-data" class="flex flex-col overflow-hidden min-h-0">
                @csrf @method('PUT')
                <input type="hidden" name="form_type" value="edit">
                <input type="hidden" name="id" :value="editItem?.id">
                <input type="hidden" name="existing_image" :value="editItem?.image_path">
                
                <!-- Scrollable Content Area -->
                <div class="p-8 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Chart Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" x-model="editItem.title" required class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                        @if(old('form_type') === 'edit') @error('title') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror @endif
                    </div>
                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Description</label>
                        <textarea name="description" x-model="editItem.description" rows="5" class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none resize-none"></textarea>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                        <label class="block text-gray-800 text-lg font-bold mb-2">Replace Image</label>
                        <input type="file" name="image" accept="image/*" class="w-full border border-gray-300 p-3.5 rounded-lg text-lg text-gray-600 file:mr-5 file:py-3 file:px-6 file:rounded-md file:border-0 file:text-base file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white">
                        <p class="text-sm text-gray-500 mt-2 italic">Leave blank to keep current chart image.</p>
                    </div>
                </div>

                <!-- Fixed Footer -->
                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-4 border-t border-gray-200 flex-shrink-0">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-3.5 px-10 rounded-lg shadow-md text-lg">Update Chart</button>
                    <button type="button" @click="editModal = false" class="px-8 py-3.5 text-lg font-bold text-gray-600 hover:text-gray-800">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODERNIZED Delete Confirmation --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="deleteModal = false">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <div class="text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Delete Chart?</h3>
                <p class="text-gray-500 text-sm mb-5">You are about to permanently delete this chart:</p>
                <div class="mb-8 max-h-32 overflow-y-auto custom-scrollbar">
                    <span class="font-bold text-gray-900 break-all text-lg block" x-text="deleteTitle"></span>
                </div>
                <p class="text-gray-400 text-sm italic mb-8">This action cannot be undone.</p>
            </div>
            <div class="flex gap-3">
                <button type="button" @click="deleteModal = false" class="flex-1 inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50">Cancel</button>
                <form :action="`/admin/cid/${deleteId}`" method="POST" class="flex-1 m-0 p-0">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-red-700">Yes, Delete it</button>
                </form>
            </div>
        </div>
    </div>

    {{-- MODERNIZED Success Message --}}
    <div x-show="successModal" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="successModal = false">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            <div class="text-center mb-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Success!</h3>
                <p class="text-gray-600 text-base">@if(session('success')) {{ session('success') }} @else Operation completed successfully. @endif</p>
            </div>
            <div class="flex">
                <button type="button" @click="successModal = false" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-6 py-3 text-base font-bold text-white shadow-sm hover:bg-red-700">Continue</button>
            </div>
        </div>
    </div>
</div>
@endsection