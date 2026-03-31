@extends('layouts.admin')

@section('page_title', 'Manage Learning Materials')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap');
    .font-cinzel { font-family: 'Cinzel', serif; }
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

    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight font-cinzel">Manage Learning Materials</h2>
            <p class="text-gray-500 text-sm mt-1">Upload and manage educational modules, guides, and resources.</p>
        </div>
        <button @click="openCreate()" class="bg-[#a52a2a] hover:bg-[#801a1a] text-white text-sm font-bold px-4 py-2.5 rounded-lg shadow-md transition-colors flex items-center tracking-wide shrink-0">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            UPLOAD NEW MATERIAL
        </button>
    </div>

    {{-- Main Table Card --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                        <th class="px-6 py-4 border-b whitespace-nowrap">#</th>
                        <th class="px-6 py-4 border-b">Title</th>
                        <th class="px-6 py-4 border-b">Description</th>
                        <th class="px-6 py-4 border-b whitespace-nowrap text-center">Document</th>
                        <th class="px-6 py-4 border-b whitespace-nowrap text-center">Date Uploaded</th>
                        <th class="px-6 py-4 border-b text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($materials as $index => $material)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-600 font-medium">
                                {{ $materials->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-900 max-w-[200px] break-words whitespace-normal">
                                {{ $material->title }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 max-w-xs break-words whitespace-normal">
                                {{ Str::limit($material->description, 100) }}
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                @if($material->file_path)
                                    <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" title="{{ basename($material->file_path) }}" class="text-red-600 font-bold hover:text-red-800 hover:underline flex items-center justify-center text-sm whitespace-nowrap">
                                        <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span class="max-w-[150px] truncate">{{ basename($material->file_path) }}</span>
                                    </a>
                                @else
                                    <span class="text-gray-400 italic text-xs">N/A</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-500 font-medium text-center whitespace-nowrap">
                                {{ $material->created_at ? $material->created_at->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 flex justify-end gap-3 items-center mt-1">
                                <button @click="openEdit({{ collect($material)->toJson() }}, '{{ route('admin.learning-materials.update', $material->id) }}')" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase hover:underline">Edit</button>
                                <button @click="openDelete({{ collect($material)->toJson() }}, '{{ route('admin.learning-materials.destroy', $material->id) }}')" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">
                                No learning materials found. Click "Upload New Material" to get started!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4">
        @if($materials->hasPages())
            {{ $materials->links() }}
        @endif
    </div>

    {{-- MODAL: ADD/EDIT MATERIAL --}}
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75 backdrop-blur-sm" @click="showModal = false"></div>
            
            <div x-show="showModal" x-transition class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-xl border-t-4 border-[#a52a2a] relative z-50">
                
                <div class="flex items-center justify-between mb-5 border-b pb-3">
                    <h3 class="text-xl font-bold text-[#a52a2a] font-cinzel" x-text="isEdit ? 'Edit Learning Material' : 'Upload New Material'"></h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form :action="isEdit ? editUrl : '{{ route('admin.learning-materials.store') }}'" 
                      method="POST" enctype="multipart/form-data" class="font-sans">
                    @csrf
                    <template x-if="isEdit"><input type="hidden" name="_method" value="PUT"></template>
                    
                    {{-- Hidden Removal Input for backend handling --}}
                    <input type="hidden" name="remove_file" :value="formData.removeFile ? '1' : '0'">
                    
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                            <input type="text" name="title" x-model="formData.title" required 
                                   class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-[#a52a2a] outline-none" 
                                   placeholder="Enter material title...">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                            <textarea name="description" x-model="formData.description" rows="4" required 
                                      class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-[#a52a2a] outline-none" 
                                      placeholder="Enter detailed description..."></textarea>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mt-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Document Attachment <span class="text-xs font-normal text-gray-400 ml-1" x-text="isEdit ? '(Leave blank to keep current)' : '(Required)'"></span></label>
                            <input type="file" name="file" accept=".pdf, .ppt, .pptx, .doc, .docx" :required="!isEdit" 
                                   class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-[#a52a2a] hover:file:bg-red-100 cursor-pointer">
                            
                            {{-- Preview Existing File Link on Edit --}}
                            <template x-if="isEdit && formData.currentFile && !formData.removeFile">
                                <div class="mt-3 flex items-center justify-between p-2 bg-red-50/50 border border-red-100 rounded-lg">
                                    <div class="flex items-center gap-3">
                                        <div class="p-1.5 bg-white rounded shadow-sm border border-gray-200 text-red-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-[10px] text-gray-500 uppercase font-bold">Current Document</span>
                                            <a :href="'/storage/' + formData.currentFile" target="_blank" :title="formData.currentFile.split('/').pop()" class="text-xs text-red-600 font-bold hover:underline truncate max-w-[150px]" x-text="formData.currentFile.split('/').pop()"></a>
                                        </div>
                                    </div>
                                    <button type="button" @click="formData.removeFile = true" class="p-1.5 text-red-500 hover:bg-red-100 rounded-md transition-colors" title="Remove File">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </template>
                            <template x-if="formData.removeFile">
                                <span class="text-xs text-red-500 mt-2 block font-medium">Document will be removed upon saving.</span>
                            </template>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="showModal = false" class="px-5 py-2.5 text-sm font-bold text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-[#a52a2a] border border-transparent rounded-lg hover:bg-[#801a1a] shadow-sm transition-colors" x-text="isEdit ? 'Save Changes' : 'Upload Material'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL: DELETE MATERIAL --}}
    <div x-show="showDeleteModal" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="showDeleteModal = false"></div>

            <div x-show="showDeleteModal" x-transition class="bg-white rounded-2xl p-8 shadow-2xl z-[70] w-full max-w-sm transform transition-all relative">
                <div class="absolute top-4 right-4 cursor-pointer text-gray-400 hover:text-gray-600" @click="showDeleteModal = false">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
                <div class="flex flex-col items-center justify-center mt-2">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-16 w-16 mb-4 text-[#a52a2a] bg-red-50 rounded-full">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2 font-cinzel">Confirm Deletion</h3>
                    <p class="text-gray-500 text-sm mb-6 font-sans">
                        Are you sure you want to delete <br>
                        <strong class="text-gray-800 break-words" x-text="deleteTitle"></strong>? <br>
                        This action cannot be undone.
                    </p>
                </div>
                <div class="flex space-x-3 w-full">
                    <button @click="showDeleteModal = false" class="flex-1 px-4 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition">Cancel</button>
                    <form :action="deleteUrl" method="POST" class="flex-1">
                        @csrf 
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-[#a52a2a] text-white rounded-xl font-bold hover:bg-[#801a1a] shadow-lg shadow-red-200 transition">Yes, Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection