@extends('layouts.admin')

@section('page_title', 'Manage ' . $categoryTitle)

@section('content')
<style>
    [x-cloak] { display: none !important; }
    
    /* Subtle scrollbar for the modal target box and forms */
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

{{-- We use $dispatch listeners on the main container --}}
<div x-data="{ 
        addModal: {{ (!old('edit_id') && !session('edit_id') && ($errors->any() || session('error_duplicate'))) ? 'true' : 'false' }}, 
        editModal: {{ (old('edit_id') || session('edit_id') || (old('_method') == 'PUT' && $errors->any())) ? 'true' : 'false' }}, 
        deleteModal: false, 
        successModal: {{ session('success') ? 'true' : 'false' }},
        editItem: {
            id: '{{ old('edit_id', session('edit_id')) }}',
            title: '{!! addslashes(old('title', '')) !!}',
            description: '{!! addslashes(old('description', '')) !!}',
            date: '{{ old('date', '') }}'
        }, 
        editUrl: '{{ old('edit_url', session('edit_url')) }}',
        deleteTitle: '', 
        removeImage: false, 
        removePdf: false,
        removeExcel: false
    }"
    @open-edit-modal.window="
        editItem = $event.detail.item;
        removeImage = false;
        removePdf = false;
        removeExcel = false;
        editUrl = $event.detail.url;
        editModal = true;
    "
    @open-delete-modal.window="
        deleteTitle = $event.detail.title;
        document.getElementById('deleteForm').action = $event.detail.url;
        deleteModal = true;
    "
>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Manage {{ $categoryTitle }}</h2>
            <p class="text-gray-500 text-sm mt-1">
                Provide details and attach the necessary files for {{ strtolower($categoryTitle) }}. 
                <span class="font-semibold text-red-600">You must upload at least an Image OR a PDF OR a Spreadsheet.</span>
            </p>
        </div>
        <button @click="addModal = true; editItem = {};" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-4 rounded-lg shadow transition-colors flex items-center shrink-0 uppercase tracking-wider text-sm">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Upload New
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                        <th class="p-4 border-b whitespace-nowrap w-16 text-center">ID</th>
                        <th class="p-4 border-b">Title</th>
                        <th class="p-4 border-b">Description</th> 
                        <th class="p-4 border-b w-32">Cover Image</th>
                        <th class="p-4 border-b w-32">Document (PDF)</th>
                        <th class="p-4 border-b w-32">Spreadsheet</th>
                        <th class="p-4 border-b w-32">Date Uploaded</th>
                        <th class="p-4 border-b text-right w-32">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($opportunities as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 text-sm text-gray-600 font-medium text-center align-middle">{{ $opportunities->firstItem() + $loop->index }}</td>
                            <td class="p-4 font-semibold text-gray-800 max-w-[200px] break-words whitespace-normal align-middle">{{ $item->title }}</td>
                            <td class="p-4 text-sm text-gray-600 max-w-xs break-words whitespace-normal align-middle">{{ Str::limit($item->description, 100) }}</td>
                            <td class="p-4 align-middle">
                                @if($item->jpeg_path)
                                    <img src="{{ asset('storage/' . $item->jpeg_path) }}" alt="Image" class="w-24 h-auto rounded shadow-sm border object-cover">
                                @else
                                    <span class="text-[10px] font-semibold text-gray-400 bg-gray-100 px-2 py-1 rounded italic">No Image</span>
                                @endif
                            </td>
                            <td class="p-4 align-middle">
                                @if($item->pdf_path)
                                    <a href="{{ asset('storage/' . $item->pdf_path) }}" target="_blank" title="{{ basename($item->pdf_path) }}" class="text-red-600 font-bold hover:text-red-800 hover:underline flex items-center text-xs whitespace-nowrap">
                                        <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span class="max-w-[150px] truncate">{{ basename($item->pdf_path) }}</span>
                                    </a>
                                @else
                                    <span class="text-[10px] font-semibold text-gray-400 bg-gray-100 px-2 py-1 rounded italic">No PDF</span>
                                @endif
                            </td>
                            <td class="p-4 align-middle">
                                @if($item->excel_path)
                                    <a href="{{ asset('storage/' . $item->excel_path) }}" target="_blank" title="{{ basename($item->excel_path) }}" class="text-green-600 font-bold hover:text-green-800 hover:underline flex items-center text-xs whitespace-nowrap">
                                        <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span class="max-w-[150px] truncate">{{ basename($item->excel_path) }}</span>
                                    </a>
                                @else
                                    <span class="text-[10px] font-semibold text-gray-400 bg-gray-100 px-2 py-1 rounded italic">No File</span>
                                @endif
                            </td>
                            <td class="p-4 text-sm text-gray-500 whitespace-nowrap align-middle">{{ $item->created_at->format('M d, Y') }}</td>
                            <td class="p-4 align-middle">
                                <div class="flex justify-end gap-3 items-center" x-data="{ rowItem: {{ \Illuminate\Support\Js::from($item) }} }">
                                    <button type="button" @click="$dispatch('open-edit-modal', { item: rowItem, url: '{{ route('admin.procurement.update', ['category' => $category, 'id' => $item->id]) }}' })" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase hover:underline">Edit</button>
                                    <button type="button" @click="$dispatch('open-delete-modal', { title: rowItem.title, url: '{{ route('admin.procurement.destroy', ['category' => $category, 'id' => $item->id]) }}' })" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-10 text-center text-gray-500 italic">No {{ strtolower($categoryTitle) }} uploaded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($opportunities->hasPages())
        <div class="mt-4">
            {{ $opportunities->links() }}
        </div>
    @endif

    {{-- Add Modal (Extra Large size, scrollable content) --}}
    <div x-show="addModal" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-5xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]" @click.away="addModal = false">
            
            <div class="bg-red-700 px-8 py-5 flex justify-between items-center text-white flex-shrink-0">
                <h3 class="font-bold text-2xl">Upload New {{ \Illuminate\Support\Str::singular($categoryTitle) }}</h3>
                <button type="button" @click="addModal = false" class="hover:text-gray-200 text-4xl font-bold">&times;</button>
            </div>
            
            <form id="addModalForm" action="{{ route('admin.procurement.store', $category) }}" method="POST" enctype="multipart/form-data" class="flex flex-col overflow-hidden min-h-0">
                @csrf
                
                <div class="p-8 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2">Date <span class="font-normal text-gray-500 text-sm">(Optional)</span></label>
                            <input type="date" name="date" class="w-full border @if(!old('edit_id') && $errors->has('date')) border-red-500 @else border-gray-300 @endif p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none" value="{{ !old('edit_id') ? old('date') : '' }}">
                            @if(!old('edit_id'))
                                @error('date')<p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p>@enderror
                            @endif
                        </div>
                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2">Title <span class="text-red-500">*</span></label>
                            <input type="text" name="title" class="w-full border @if(!old('edit_id') && ($errors->has('title') || session('error_duplicate'))) border-red-500 @else border-gray-300 @endif p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none" required value="{{ !old('edit_id') ? old('title') : '' }}">
                            
                            {{-- Inline Error Design --}}
                            @if(!old('edit_id'))
                                @if(session('error_duplicate'))
                                    <p class="text-red-500 text-base mt-1.5 font-medium">{{ session('error_duplicate') }}</p>
                                @endif
                                @error('title')
                                    <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p>
                                @enderror
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Description</label>
                        <textarea name="description" rows="3" class="w-full border @if(!old('edit_id') && $errors->has('description')) border-red-500 @else border-gray-300 @endif p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none resize-none">{{ !old('edit_id') ? old('description') : '' }}</textarea>
                        @if(!old('edit_id'))
                            @error('description')<p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p>@enderror
                        @endif
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-gray-50 p-6 rounded-lg border border-gray-200">
                        <div class="col-span-full mb-1">
                            <p class="text-lg font-bold text-gray-800">Attachments <span class="text-sm font-normal text-gray-500">(Please provide at least one)</span></p>
                            @if(!old('edit_id') && ($errors->has('jpeg_file') || $errors->has('pdf_file') || $errors->has('excel_file')))
                                <p class="text-red-500 text-base mt-1.5 font-medium">Error: You must upload at least an Image, PDF, or Spreadsheet.</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-gray-800 text-base font-bold mb-2">Cover Image (Max 5MB)</label>
                            <input type="file" name="jpeg_file" accept="image/*" class="w-full border @if(!old('edit_id') && $errors->has('jpeg_file')) border-red-500 @else border-gray-300 @endif p-3.5 rounded-lg text-sm text-gray-600 file:mr-5 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer bg-white">
                        </div>
                        <div>
                            <label class="block text-gray-800 text-base font-bold mb-2">PDF Document (Max 10MB)</label>
                            <input type="file" name="pdf_file" accept=".pdf" class="w-full border @if(!old('edit_id') && $errors->has('pdf_file')) border-red-500 @else border-gray-300 @endif p-3.5 rounded-lg text-sm text-gray-600 file:mr-5 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white">
                        </div>
                        <div>
                            <label class="block text-gray-800 text-base font-bold mb-2">Spreadsheet (Max 10MB)</label>
                            <input type="file" name="excel_file" accept=".csv, .xls, .xlsx" class="w-full border @if(!old('edit_id') && $errors->has('excel_file')) border-red-500 @else border-gray-300 @endif p-3.5 rounded-lg text-sm text-gray-600 file:mr-5 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-bold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer bg-white">
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-4 items-center border-t border-gray-200 flex-shrink-0">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-3.5 px-10 rounded-lg shadow-md transition-colors text-lg">Upload Document</button>
                    <button type="button" @click="addModal = false" class="px-8 py-3.5 text-lg font-bold text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modal (Extra Large size, scrollable content) --}}
    <div x-show="editModal" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-5xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]" @click.away="editModal = false">
            
            <div class="bg-[#a52a2a] px-8 py-5 flex justify-between items-center text-white flex-shrink-0">
                <h3 class="font-bold text-2xl">Edit {{ \Illuminate\Support\Str::singular($categoryTitle) }}</h3>
                <button type="button" @click="editModal = false" class="hover:text-gray-200 text-4xl font-bold">&times;</button>
            </div>
            
            <form id="editForm" :action="editUrl" method="POST" enctype="multipart/form-data" class="flex flex-col overflow-hidden min-h-0">
                @csrf @method('PUT')
                <input type="hidden" name="remove_image" :value="removeImage ? '1' : '0'">
                <input type="hidden" name="remove_pdf" :value="removePdf ? '1' : '0'">
                <input type="hidden" name="remove_excel" :value="removeExcel ? '1' : '0'">
                <input type="hidden" name="edit_id" x-model="editItem.id">
                <input type="hidden" name="edit_url" x-model="editUrl">

                <div class="p-8 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2">Date</label>
                            <input type="date" name="date" x-model="editItem.date" class="w-full border @if(old('edit_id') && $errors->has('date')) border-red-500 @else border-gray-300 @endif p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                            @if(old('edit_id'))
                                @error('date')<p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p>@enderror
                            @endif
                        </div>
                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2">Title <span class="text-red-500">*</span></label>
                            <input type="text" name="title" x-model="editItem.title" class="w-full border @if(old('edit_id') && ($errors->has('title') || session('error_duplicate'))) border-red-500 @else border-gray-300 @endif p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none" required>
                            @if(old('edit_id'))
                                @if(session('error_duplicate'))
                                    <p class="text-red-500 text-base mt-1.5 font-medium">{{ session('error_duplicate') }}</p>
                                @endif
                                @error('title')
                                    <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p>
                                @enderror
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Description</label>
                        <textarea name="description" x-model="editItem.description" rows="3" class="w-full border @if(old('edit_id') && $errors->has('description')) border-red-500 @else border-gray-300 @endif p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none resize-none"></textarea>
                        @if(old('edit_id'))
                            @error('description')<p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p>@enderror
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-gray-50 p-6 rounded-lg border border-gray-200">
                        <div class="col-span-full mb-1">
                            @if(old('edit_id') && ($errors->has('jpeg_file') || $errors->has('pdf_file') || $errors->has('excel_file')))
                                <p class="text-red-500 text-base mt-1.5 font-medium">Error: Files must not exceed limit and be of valid types.</p>
                            @endif
                        </div>

                        <div>
                            <label class="block text-gray-800 text-base font-bold mb-2">Replace Image</label>
                            <input type="file" name="jpeg_file" accept="image/*" class="w-full border @if(old('edit_id') && $errors->has('jpeg_file')) border-red-500 @else border-gray-300 @endif p-3.5 rounded-lg text-sm text-gray-600 file:mr-5 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer bg-white">
                            
                            <template x-if="editItem && editItem.jpeg_path && !removeImage">
                                <div class="mt-3 flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex items-center gap-2">
                                        <img :src="'/storage/' + editItem.jpeg_path" class="h-10 w-12 object-cover rounded shadow-sm">
                                        <span class="text-xs text-blue-800 font-bold truncate max-w-[100px]" x-text="'Current: ' + editItem.jpeg_path.split('/').pop()"></span>
                                    </div>
                                    <button type="button" @click="removeImage = true" class="p-1.5 text-red-500 hover:bg-red-100 rounded-lg transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                </div>
                            </template>
                            @if(old('edit_id'))
                                @error('jpeg_file')<p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p>@enderror
                            @endif
                        </div>

                        <div>
                            <label class="block text-gray-800 text-base font-bold mb-2">Replace PDF</label>
                            <input type="file" name="pdf_file" accept=".pdf" class="w-full border @if(old('edit_id') && $errors->has('pdf_file')) border-red-500 @else border-gray-300 @endif p-3.5 rounded-lg text-sm text-gray-600 file:mr-5 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white">
                            
                            <template x-if="editItem && editItem.pdf_path && !removePdf">
                                <div class="mt-3 flex items-center justify-between p-3 bg-red-50 border border-red-200 rounded-lg">
                                    <div class="flex items-center gap-3">
                                        <div class="p-1.5 bg-white rounded shadow-sm border border-gray-200 text-red-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <span class="text-xs text-red-800 font-bold truncate max-w-[100px]" x-text="'Current: ' + editItem.pdf_path.split('/').pop()"></span>
                                    </div>
                                    <button type="button" @click="removePdf = true" class="p-1.5 text-red-500 hover:bg-red-100 rounded-lg transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                </div>
                            </template>
                            @if(old('edit_id'))
                                @error('pdf_file')<p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p>@enderror
                            @endif
                        </div>

                        <div>
                            <label class="block text-gray-800 text-base font-bold mb-2">Replace Spreadsheet</label>
                            <input type="file" name="excel_file" accept=".csv, .xls, .xlsx" class="w-full border @if(old('edit_id') && $errors->has('excel_file')) border-red-500 @else border-gray-300 @endif p-3.5 rounded-lg text-sm text-gray-600 file:mr-5 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-bold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer bg-white">
                            
                            <template x-if="editItem && editItem.excel_path && !removeExcel">
                                <div class="mt-3 flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex items-center gap-3">
                                        <div class="p-1.5 bg-white rounded shadow-sm border border-gray-200 text-green-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <span class="text-xs text-green-800 font-bold truncate max-w-[100px]" x-text="'Current: ' + editItem.excel_path.split('/').pop()"></span>
                                    </div>
                                    <button type="button" @click="removeExcel = true" class="p-1.5 text-red-500 hover:bg-red-100 rounded-lg transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                </div>
                            </template>
                            @if(old('edit_id'))
                                @error('excel_file')<p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p>@enderror
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-4 items-center border-t border-gray-200 flex-shrink-0">
                    <button type="submit" form="editForm" class="bg-[#a52a2a] hover:bg-red-800 text-white font-bold py-3.5 px-10 rounded-lg shadow-md transition-colors text-lg">Update Details</button>
                    <button type="button" @click="editModal = false" class="px-8 py-3.5 text-lg font-bold text-gray-600 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODERNIZED GLOBAL MODAL: Success Message --}}
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
                <p class="text-gray-500 text-base">
                    @if(session('success'))
                        {{ session('success') }}
                    @else
                        Operation completed successfully.
                    @endif
                </p>
            </div>
            
            <div class="flex">
                <button type="button" @click="successModal = false" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-6 py-3 text-base font-bold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-all">
                    Continue
                </button>
            </div>
        </div>
    </div>

    {{-- MODERNIZED GLOBAL MODAL: Delete Confirmation --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-[110] w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="deleteModal = false">
            
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            
            <div class="text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Delete Content?</h3>
                <p class="text-gray-500 text-sm mb-5">
                    You are about to permanently delete this content:
                </p>
                
                <div class="mb-8 max-h-32 overflow-y-auto custom-scrollbar">
                    <span class="font-bold text-gray-900 break-all text-lg block" x-text="deleteTitle"></span>
                </div>
                
                <p class="text-gray-400 text-sm italic mb-8">
                    This action cannot be undone.
                </p>
            </div>
            
            <div class="flex gap-3">
                <button type="button" @click="deleteModal = false" class="flex-1 inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:ring-offset-1 transition-all">
                    Cancel
                </button>
                
                <form id="deleteForm" method="POST" class="flex-1 m-0 p-0 flex">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-all">
                        Yes, Delete it
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection