@extends('layouts.admin')

@section('page_title', 'Manage Learning Materials')

@section('content')
<style>
    [x-cloak] { display: none !important; }
</style>

<div x-data="{ 
    showModal: false, 
    showDeleteModal: false,
    isEdit: false,
    editId: null,
    deleteId: null,
    deleteTitle: '',
    editUrl: '',
    deleteUrl: '',
    formData: {
        title: '',
        description: '',
        currentFile: '',
        removeFile: false
    },
    openCreate() {
        this.isEdit = false;
        this.editId = null;
        this.editUrl = '';
        this.formData.title = '';
        this.formData.description = '';
        this.formData.currentFile = '';
        this.formData.removeFile = false;
        this.showModal = true;
    },
    openEdit(material, url) {
        this.isEdit = true;
        this.editId = material.id;
        this.editUrl = url;
        this.formData.title = material.title;
        this.formData.description = material.description;
        this.formData.currentFile = material.file_path;
        this.formData.removeFile = false;
        this.showModal = true;
    },
    openDelete(material, url) {
        this.deleteId = material.id;
        this.deleteTitle = material.title;
        this.deleteUrl = url;
        this.showDeleteModal = true;
    }
}">

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Manage Learning Materials</h2>
            <p class="text-gray-500 text-sm mt-1">Upload and manage educational modules, guides, and resources.</p>
        </div>
        <button @click="openCreate()" class="bg-red-700 hover:bg-red-800 text-white text-sm font-bold px-4 py-2.5 rounded-lg shadow transition-colors flex items-center shrink-0 uppercase tracking-wider">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Upload New Material
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                        <th class="px-6 py-4 border-b whitespace-nowrap text-center w-16">#</th>
                        <th class="px-6 py-4 border-b">Title</th>
                        <th class="px-6 py-4 border-b">Description</th>
                        <th class="px-6 py-4 border-b whitespace-nowrap text-center">Document</th>
                        <th class="px-6 py-4 border-b whitespace-nowrap text-center">Date Uploaded</th>
                        <th class="px-6 py-4 border-b text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($materials as $index => $material)
                        @php
                            $extension = pathinfo($material->file_path, PATHINFO_EXTENSION);
                            $isWord = in_array(strtolower($extension), ['doc', 'docx']);
                            $isExcel = in_array(strtolower($extension), ['xls', 'xlsx', 'csv']);
                            $isPdf = strtolower($extension) === 'pdf';
                            
                            $colorClass = 'text-gray-600 hover:text-gray-800';
                            if ($isWord) $colorClass = 'text-blue-600 hover:text-blue-800';
                            if ($isExcel) $colorClass = 'text-green-600 hover:text-green-800';
                            if ($isPdf) $colorClass = 'text-red-600 hover:text-red-800';
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-600 font-medium text-center align-middle">
                                {{ $materials->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-900 max-w-[200px] align-middle">
                                {{ $material->title }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 max-w-xs align-middle">
                                <div x-data="{ expanded: false }">
                                    <p class="cursor-pointer hover:text-gray-900 transition-colors whitespace-normal break-words"
                                       :class="expanded ? '' : 'line-clamp-2 italic'"
                                       @click="expanded = !expanded"
                                       title="Click to show/hide">
                                        {{ $material->description }}
                                    </p>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 text-center align-middle">
                                @if($material->file_path)
                                    <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="{{ $colorClass }} font-bold hover:underline inline-flex items-center text-xs whitespace-nowrap transition-colors">
                                        <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        {{ Str::limit(basename($material->file_path), 15) }}
                                    </a>
                                @else
                                    <span class="text-gray-400 italic text-[10px]">N/A</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-xs text-gray-500 font-medium text-center whitespace-nowrap align-middle">
                                {{ $material->created_at ? $material->created_at->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 align-middle">
                                <div class="flex justify-end gap-3 items-center">
                                    <button @click="openEdit({{ collect($material)->toJson() }}, '{{ route('admin.learning-materials.update', $material->id) }}')" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase hover:underline">Edit</button>
                                    <button @click="openDelete({{ collect($material)->toJson() }}, '{{ route('admin.learning-materials.destroy', $material->id) }}')" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">
                                No learning materials found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($materials->hasPages())
        <div class="mt-4">
            {{ $materials->links() }}
        </div>
    @endif

    {{-- MODAL: ADD/EDIT MATERIAL --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity" style="display: none;">
        <div class="bg-white rounded-xl w-full max-w-lg shadow-2xl overflow-hidden" @click.away="showModal = false">
            <div class="bg-red-700 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg" x-text="isEdit ? 'Edit Learning Material' : 'Upload New Material'"></h3>
                <button @click="showModal = false" class="hover:text-gray-200 text-2xl font-bold">&times;</button>
            </div>
            
            <form :action="isEdit ? editUrl : '{{ route('admin.learning-materials.store') }}'" method="POST" enctype="multipart/form-data">
                @csrf
                <template x-if="isEdit"><input type="hidden" name="_method" value="PUT"></template>
                <input type="hidden" name="remove_file" :value="formData.removeFile ? '1' : '0'">
                
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" x-model="formData.title" required 
                               class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" 
                               placeholder="Enter material title...">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                        <textarea name="description" x-model="formData.description" rows="4" required 
                                  class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none resize-none" 
                                  placeholder="Enter detailed description..."></textarea>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Document Attachment</label>
                        <input type="file" name="file" accept=".pdf, .ppt, .pptx, .doc, .docx, .xls, .xlsx, .csv" :required="!isEdit" 
                               class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white">
                        
                        <template x-if="isEdit && formData.currentFile && !formData.removeFile">
                            <div class="mt-3 flex items-center justify-between p-2 bg-blue-50 border border-blue-100 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="p-1.5 bg-white rounded shadow-sm border border-gray-200 text-blue-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">Current File</span>
                                        <span class="text-xs text-blue-700 font-bold truncate max-w-[150px]" x-text="formData.currentFile.split('/').pop()"></span>
                                    </div>
                                </div>
                                <button type="button" @click="formData.removeFile = true" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </template>
                        <template x-if="formData.removeFile">
                            <span class="text-xs text-red-500 mt-2 block font-medium italic">Document will be removed upon saving.</span>
                        </template>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 items-center border-t border-gray-100">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-colors text-sm" x-text="isEdit ? 'Save Changes' : 'Upload Material'"></button>
                    <button type="button" @click="showModal = false" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: DELETE MATERIAL --}}
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity" style="display: none;">
        <div class="bg-white rounded-2xl p-8 shadow-2xl z-50 w-full max-w-sm transform transition-all relative" @click.away="showDeleteModal = false">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            
            <h3 class="text-xl font-bold text-gray-800 mb-2 text-center">Confirm Deletion</h3>
            <p class="text-gray-500 text-sm mb-6 text-center">
                Are you sure you want to delete <br>
                <strong class="text-gray-800 break-words" x-text="deleteTitle"></strong>? <br>
                This action cannot be undone.
            </p>
            
            <div class="flex space-x-3 border-t border-gray-100 pt-4">
                <button @click="showDeleteModal = false" type="button" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                
                <form :action="deleteUrl" method="POST" class="flex-1 m-0 p-0 flex">
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