@extends('layouts.admin')

@section('page_title', 'Manage ' . $categoryTitle)

@section('content')
<div class="container mx-auto p-4" x-data="{ addModal: false, editModal: false, deleteModal: false, editItem: null, deleteItem: null }">
    
    {{-- Success Message --}}
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Validation Errors Display --}}
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
                        <th class="p-4 border-b">Cover Image</th>
                        <th class="p-4 border-b">Document (PDF)</th>
                        <th class="p-4 border-b">Date Uploaded</th>
                        <th class="p-4 border-b text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($opportunities as $item)
                        <tr class="hover:bg-gray-50 border-b transition-colors">
                            <td class="p-4 font-semibold text-gray-800">{{ $item->title }}</td>
                            
                            <td class="p-4 text-sm text-gray-600 max-w-xs">
                                {{ Str::limit($item->description, 100) }}
                            </td>

                            <td class="p-4">
                                @if($item->jpeg_path)
                                    <img src="{{ asset('storage/' . $item->jpeg_path) }}" alt="Image" class="w-24 h-auto rounded shadow-sm border">
                                @else
                                    <span class="text-xs font-semibold text-gray-400 bg-gray-100 px-2 py-1 rounded">No Image</span>
                                @endif
                            </td>
                            
                            <td class="p-4">
                                @if($item->pdf_path)
                                    <a href="{{ asset('storage/' . $item->pdf_path) }}" target="_blank" class="text-red-600 font-bold hover:underline flex items-center text-sm">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        View PDF
                                    </a>
                                @else
                                    <span class="text-xs font-semibold text-gray-400 bg-gray-100 px-2 py-1 rounded">No PDF</span>
                                @endif
                            </td>
                            
                            <td class="p-4 text-sm text-gray-500 whitespace-nowrap">{{ $item->created_at->format('M d, Y') }}</td>
                            
                            <td class="p-4 flex justify-end gap-3 items-center">
                                <button @click="editModal = true; editItem = {{ $item->toJson() }}" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase hover:underline">Edit</button>
                                
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
                    <textarea name="description" rows="3" 
                        placeholder="Brief details..." 
                        class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200 mt-2">
                    <div class="col-span-full mb-1">
                        <p class="text-sm font-semibold text-gray-700">Attachments <span class="text-xs font-normal text-gray-500">(Please provide at least one)</span></p>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1">Cover Image (Max 5MB)</label>
                        <input type="file" name="jpeg_file" accept="image/*" class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                        <p class="text-xs text-gray-400 mt-1">Optional if you provide a PDF document.</p>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1">PDF Document (Max 10MB)</label>
                        <input type="file" name="pdf_file" accept=".pdf" class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer">
                        <p class="text-xs text-gray-400 mt-1">Optional if you provide a Cover image.</p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                    <button type="button" @click="addModal = false" class="px-5 py-2 text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-sm bg-red-700 hover:bg-red-800 text-white font-bold rounded-lg shadow-sm transition-colors">
                        Upload {{ \Illuminate\Support\Str::singular($categoryTitle) }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-2xl shadow-2xl overflow-hidden" @click.away="editModal = false">
            <div class="bg-[#a52a2a] px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg">Edit {{ \Illuminate\Support\Str::singular($categoryTitle) }}</h3>
                <button type="button" @click="editModal = false" class="hover:text-gray-200 text-2xl font-bold">&times;</button>
            </div>
            
            <form :action="`/admin/procurement/{{ $category }}/${editItem?.id}`" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf @method('PUT')
                
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-1">Title</label>
                    <input type="text" name="title" x-model="editItem.title" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" required>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-1">Description</label>
                    <textarea name="description" x-model="editItem.description" rows="3" 
                        class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200 mt-2">
                    <div class="col-span-full mb-1">
                        <p class="text-sm font-semibold text-gray-700">Update Attachments <span class="text-xs font-normal text-gray-500">(Leave blank to keep current files)</span></p>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1">Replace Image (Max 5MB)</label>
                        <input type="file" name="jpeg_file" accept="image/*" class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1">Replace PDF (Max 10MB)</label>
                        <input type="file" name="pdf_file" accept=".pdf" class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer">
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                    <button type="button" @click="editModal = false" class="px-5 py-2 text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-sm bg-[#a52a2a] hover:bg-red-800 text-white font-bold rounded-lg shadow-sm transition-colors">
                        Update Details
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-md shadow-2xl overflow-hidden" @click.away="deleteModal = false">
            <div class="bg-red-700 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Confirm Delete
                </h3>
                <button type="button" @click="deleteModal = false" class="hover:text-gray-200 text-2xl font-bold">&times;</button>
            </div>
            
            <div class="p-6">
                <p class="text-gray-700 mb-2">Are you sure you want to delete <span class="font-bold text-gray-900" x-text="deleteItem?.title"></span>?</p>
                <p class="text-sm text-gray-500 mb-6">This action cannot be undone. Associated files will also be permanently deleted from the server.</p>
                
                <form :action="`/admin/procurement/{{ $category }}/${deleteItem?.id}`" method="POST" class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    @csrf @method('DELETE')
                    <button type="button" @click="deleteModal = false" class="px-5 py-2 text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-sm bg-red-600 hover:bg-red-800 text-white font-bold rounded-lg shadow-sm transition-colors">
                        Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection