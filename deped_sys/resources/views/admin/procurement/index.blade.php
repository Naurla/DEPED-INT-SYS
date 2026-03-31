@extends('layouts.admin')

@section('page_title', 'Manage ' . $categoryTitle)

@section('content')
<div class="container mx-auto p-4" x-data="{ addModal: false, editModal: false, deleteModal: false, editItem: null, deleteItem: null, removeImage: false, removePdf: false }">
    
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative shadow-sm">
            <strong class="font-bold">Oops! Please check your inputs:</strong>
            <ul class="list-disc pl-5 mt-1 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Manage {{ $categoryTitle }}</h2>
            <p class="text-gray-500 text-sm mt-1">
                Provide details and attach the necessary files for {{ strtolower($categoryTitle) }}. 
                <span class="font-semibold text-red-600">You must upload at least an Image OR a PDF (or both).</span>
            </p>
        </div>
        <button @click="addModal = true" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors">
            + Upload New
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                        <th class="p-4 border-b">Title</th>
                        <th class="p-4 border-b">Description</th> 
                        <th class="p-4 border-b w-32">Cover Image</th>
                        <th class="p-4 border-b w-32">Document (PDF)</th>
                        <th class="p-4 border-b w-32">Date Uploaded</th>
                        <th class="p-4 border-b text-right w-32">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($opportunities as $item)
                        <tr class="hover:bg-gray-50 border-b transition-colors">
                            <td class="p-4 font-semibold text-gray-800 max-w-[200px] break-words whitespace-normal">
                                {{ $item->title }}
                            </td>
                            <td class="p-4 text-sm text-gray-600 max-w-xs break-words whitespace-normal">
                                {{ Str::limit($item->description, 100) }}
                            </td>
                            <td class="p-4">
                                @if($item->jpeg_path)
                                    <img src="{{ asset('storage/' . $item->jpeg_path) }}" alt="Image" class="w-24 h-auto rounded shadow-sm border object-cover">
                                @else
                                    <span class="text-xs font-semibold text-gray-400 bg-gray-100 px-2 py-1 rounded">No Image</span>
                                @endif
                            </td>
                            <td class="p-4">
                                @if($item->pdf_path)
                                    <a href="{{ asset('storage/' . $item->pdf_path) }}" target="_blank" title="{{ basename($item->pdf_path) }}" class="text-red-600 font-bold hover:text-red-800 hover:underline flex items-center text-sm whitespace-nowrap">
                                        <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span class="max-w-[150px] truncate">{{ basename($item->pdf_path) }}</span>
                                    </a>
                                @else
                                    <span class="text-xs font-semibold text-gray-400 bg-gray-100 px-2 py-1 rounded">No PDF</span>
                                @endif
                            </td>
                            <td class="p-4 text-sm text-gray-500 whitespace-nowrap">{{ $item->created_at->format('M d, Y') }}</td>
                            <td class="p-4 flex justify-end gap-3 items-center">
                                <button @click="editModal = true; editItem = {{ $item->toJson() }}; removeImage = false; removePdf = false;" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase hover:underline">Edit</button>
                                <button @click="deleteModal = true; deleteItem = {{ $item->toJson() }}" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-gray-500">No {{ strtolower($categoryTitle) }} uploaded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $opportunities->links() }}
    </div>

    {{-- Add Modal --}}
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-2xl shadow-2xl overflow-hidden" @click.away="addModal = false">
            <div class="bg-red-700 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg">Upload New {{ \Illuminate\Support\Str::singular($categoryTitle) }}</h3>
                <button type="button" @click="addModal = false" class="hover:text-gray-200 text-2xl font-bold">&times;</button>
            </div>
            <form action="{{ route('admin.procurement.store', $category) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-1">Title</label>
                    <input type="text" name="title" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" required value="{{ old('title') }}">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">{{ old('description') }}</textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200 mt-2">
                    <div class="col-span-full mb-1">
                        <p class="text-sm font-semibold text-gray-700">Attachments <span class="text-xs font-normal text-gray-500">(Please provide at least one)</span></p>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1">Cover Image (Max 5MB)</label>
                        <input type="file" name="jpeg_file" accept="image/*" class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1">PDF Document (Max 10MB)</label>
                        <input type="file" name="pdf_file" accept=".pdf" class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer">
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                    <button type="button" @click="addModal = false" class="px-5 py-2 text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-sm bg-red-700 hover:bg-red-800 text-white font-bold rounded-lg shadow-sm transition-colors">Upload</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-2xl shadow-2xl overflow-hidden" @click.away="editModal = false">
            <div class="bg-[#a52a2a] px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg">Edit {{ \Illuminate\Support\Str::singular($categoryTitle) }}</h3>
                <button type="button" @click="editModal = false" class="hover:text-gray-200 text-2xl font-bold">&times;</button>
            </div>
            <form :action="`/admin/procurement/{{ $category }}/${editItem?.id}`" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf @method('PUT')
                <input type="hidden" name="remove_image" :value="removeImage ? '1' : '0'">
                <input type="hidden" name="remove_pdf" :value="removePdf ? '1' : '0'">

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-1">Title</label>
                    <input type="text" name="title" x-model="editItem.title" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-1">Description</label>
                    <textarea name="description" x-model="editItem.description" rows="3" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200 mt-2">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1">Replace Image</label>
                        <input type="file" name="jpeg_file" accept="image/*" class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                        <template x-if="editItem && editItem.jpeg_path && !removeImage">
                            <div class="mt-2 flex items-center justify-between p-2 bg-blue-50/50 border border-blue-100 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <img :src="'/storage/' + editItem.jpeg_path" class="h-10 w-12 object-cover rounded shadow-sm border border-gray-200">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] text-gray-500 uppercase font-bold">Current Image</span>
                                        <a :href="'/storage/' + editItem.jpeg_path" target="_blank" :title="editItem.jpeg_path.split('/').pop()" class="text-xs text-blue-600 hover:underline max-w-[120px] truncate block" x-text="editItem.jpeg_path.split('/').pop()"></a>
                                    </div>
                                </div>
                                <button type="button" @click="removeImage = true" class="p-1 text-red-500 hover:bg-red-100 rounded-md transition-colors" title="Remove"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                            </div>
                        </template>
                        <template x-if="removeImage"><span class="text-xs text-red-500 mt-2 block font-medium">Image will be removed.</span></template>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1">Replace PDF</label>
                        <input type="file" name="pdf_file" accept=".pdf" class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer">
                        <template x-if="editItem && editItem.pdf_path && !removePdf">
                            <div class="mt-2 flex items-center justify-between p-2 bg-red-50/50 border border-red-100 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="p-1 bg-white rounded shadow-sm border border-gray-200 text-red-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[10px] text-gray-500 uppercase font-bold">Current PDF</span>
                                        <a :href="'/storage/' + editItem.pdf_path" target="_blank" :title="editItem.pdf_path.split('/').pop()" class="text-xs text-red-600 hover:text-red-800 hover:underline max-w-[120px] truncate block" x-text="editItem.pdf_path.split('/').pop()"></a>
                                    </div>
                                </div>
                                <button type="button" @click="removePdf = true" class="p-1 text-red-500 hover:bg-red-100 rounded-md transition-colors" title="Remove"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                            </div>
                        </template>
                        <template x-if="removePdf"><span class="text-xs text-red-500 mt-2 block font-medium">PDF will be removed.</span></template>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                    <button type="button" @click="editModal = false" class="px-5 py-2 text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-sm bg-[#a52a2a] hover:bg-red-800 text-white font-bold rounded-lg shadow-sm transition-colors">Update Details</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-[60] overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="deleteModal = false"></div>
            <div x-show="deleteModal" x-transition class="bg-white rounded-2xl p-8 shadow-2xl z-[70] w-full max-w-sm transform transition-all relative">
                <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Confirm Deletion</h3>
                <p class="text-gray-500 text-sm mb-6 break-words whitespace-normal">
                    Are you sure you want to delete <span class="font-bold text-gray-900" x-text="deleteItem?.title"></span>?<br><br>This action cannot be undone.
                </p>
                <div class="flex space-x-3">
                    <button type="button" @click="deleteModal = false" class="flex-1 px-4 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition">Cancel</button>
                    <form :action="`/admin/procurement/{{ $category }}/${deleteItem?.id}`" method="POST" class="flex-1">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 shadow-lg shadow-red-200 transition">Yes, Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection