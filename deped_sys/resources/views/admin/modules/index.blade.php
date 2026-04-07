@extends('layouts.admin')

@section('page_title', 'Manage Modules')

@section('content')
<style>
    [x-cloak] { display: none !important; }
</style>

<div x-data="{ 
    uploadModal: false, 
    showDeleteModal: false, 
    isEdit: false,
    moduleId: null,
    editItem: null,
    deleteTitle: '',
    removeFile: false,
    removeImage: false,
    formData: { title: '', description: '' },
    openEdit(module) {
        this.isEdit = true;
        this.moduleId = module.id;
        this.editItem = module;
        this.formData.title = module.title;
        this.formData.description = module.description || '';
        this.removeFile = false;
        this.removeImage = false;
        this.uploadModal = true;
    },
    openCreate() {
        this.isEdit = false;
        this.moduleId = null;
        this.editItem = null;
        this.formData.title = '';
        this.formData.description = '';
        this.removeFile = false;
        this.removeImage = false;
        this.uploadModal = true;
    },
    openDelete(module) {
        this.moduleId = module.id;
        this.deleteTitle = module.title;
        this.showDeleteModal = true;
    }
}">

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative shadow-sm">
            <strong class="font-bold text-sm">Oops! Please check your inputs:</strong>
            <ul class="list-disc pl-5 mt-1 text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Modules Management</h2>
            <p class="text-gray-500 text-sm mt-1">Upload and manage educational modules and their assets.</p>
        </div>
        <button @click="openCreate()" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-4 rounded-lg shadow transition-colors text-sm flex items-center uppercase tracking-wider">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Upload New Module
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                        <th class="p-4 border-b whitespace-nowrap w-16 text-center">#</th>
                        <th class="p-4 border-b">Title</th>
                        <th class="p-4 border-b">Cover Image</th>
                        <th class="p-4 border-b">Document</th>
                        <th class="p-4 border-b text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($modules as $index => $module)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 text-sm text-gray-600 font-medium text-center align-middle">
                                {{ $modules->firstItem() + $index }}
                            </td>
                            <td class="p-4 font-bold text-gray-900 align-middle">
                                {{ $module->title }}
                            </td>
                            
                            <td class="p-4 align-middle">
                                @if($module->image_path)
                                    <a href="{{ asset('storage/' . $module->image_path) }}" target="_blank" class="text-blue-600 font-bold hover:text-blue-800 hover:underline inline-flex items-center text-xs whitespace-nowrap">
                                        <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ Str::limit(basename($module->image_path), 15) }}
                                    </a>
                                @else
                                    <span class="text-gray-400 italic text-[10px]">N/A</span>
                                @endif
                            </td>

                            <td class="p-4 align-middle">
                                @if($module->file_path)
                                    @php
                                        $extension = pathinfo($module->file_path, PATHINFO_EXTENSION);
                                        $isWord = in_array(strtolower($extension), ['doc', 'docx']);
                                        $isExcel = in_array(strtolower($extension), ['xls', 'xlsx', 'csv']);
                                        
                                        $docColor = 'text-red-600 hover:text-red-800'; // Default PDF Red
                                        if ($isWord) $docColor = 'text-blue-600 hover:text-blue-800';
                                        if ($isExcel) $docColor = 'text-green-600 hover:text-green-800';
                                    @endphp
                                    <a href="{{ asset('storage/' . $module->file_path) }}" target="_blank" class="{{ $docColor }} font-bold hover:underline inline-flex items-center text-xs whitespace-nowrap transition-colors">
                                        <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        {{ Str::limit(basename($module->file_path), 20) }}
                                    </a>
                                @else
                                    <span class="text-gray-400 italic text-[10px]">N/A</span>
                                @endif
                            </td>

                            <td class="p-4 align-middle">
                                <div class="flex justify-end gap-3 items-center">
                                    <button type="button" @click="openEdit({{ collect($module)->toJson() }})" class="text-blue-600 font-bold uppercase text-xs hover:underline">
                                        Edit
                                    </button>
                                    <button type="button" @click="openDelete({{ collect($module)->toJson() }})" class="text-red-600 font-bold uppercase text-xs hover:underline">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-500 italic">
                                No modules found. Click "Upload New Module" to get started!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
        
    @if($modules->hasPages())
        <div class="mt-4">
            {{ $modules->links() }}
        </div>
    @endif

    {{-- MODAL: ADD/EDIT MODULE --}}
    <div x-show="uploadModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity" style="display: none;">
        <div class="bg-white rounded-xl w-full max-w-2xl shadow-2xl overflow-hidden" @click.away="uploadModal = false">
            <div class="bg-red-700 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg" x-text="isEdit ? 'Edit Module' : 'Upload New Module'"></h3>
                <button type="button" @click="uploadModal = false" class="hover:text-gray-200 text-2xl font-bold">&times;</button>
            </div>

            <form :action="isEdit ? '/admin/modules/' + moduleId : '{{ route('admin.modules.store') }}'" method="POST" enctype="multipart/form-data">
                @csrf
                <template x-if="isEdit"><input type="hidden" name="_method" value="PUT"></template>
                <input type="hidden" name="remove_file" :value="removeFile ? '1' : '0'">
                <input type="hidden" name="remove_image" :value="removeImage ? '1' : '0'">

                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1">Module Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" x-model="formData.title" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1">Detailed Description <span class="text-red-500">*</span></label>
                        <textarea name="description" x-model="formData.description" rows="4" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none resize-none"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-1">Cover Image (JPEG/PNG)</label>
                            <input type="file" name="image" accept="image/*" class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white">
                            
                            <template x-if="isEdit && editItem && editItem.image_path && !removeImage">
                                <div class="mt-2 flex items-center justify-between p-2 bg-blue-50 border border-blue-100 rounded-lg">
                                    <span class="text-[10px] text-blue-700 font-bold truncate max-w-[120px]" x-text="'Current: ' + editItem.image_path.split('/').pop()"></span>
                                    <button type="button" @click="removeImage = true" class="text-red-500 hover:bg-red-50 p-1 rounded-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-1">Document File <span class="text-red-500" x-show="!isEdit">*</span></label>
                            {{-- Added .csv to the accept attribute below --}}
                            <input type="file" name="file" :required="!isEdit" accept=".pdf,.doc,.docx,.xls,.xlsx,.csv" class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white">
                            
                            <template x-if="isEdit && editItem && editItem.file_path && !removeFile">
                                <div class="mt-2 flex items-center justify-between p-2 bg-red-50 border border-red-100 rounded-lg">
                                    <span class="text-[10px] text-red-700 font-bold truncate max-w-[120px]" x-text="'Current: ' + editItem.file_path.split('/').pop()"></span>
                                    <button type="button" @click="removeFile = true" class="text-red-500 hover:bg-red-50 p-1 rounded-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 items-center border-t border-gray-100">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-colors text-sm" x-text="isEdit ? 'Save Changes' : 'Upload Module'"></button>
                    <button type="button" @click="uploadModal = false" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- GLOBAL MODAL: Delete Confirmation --}}
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity" style="display: none;">
        <div class="bg-white rounded-2xl p-8 shadow-2xl z-50 w-full max-w-sm transform transition-all relative" @click.away="showDeleteModal = false">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            
            <h3 class="text-xl font-bold text-gray-800 mb-2 text-center">Confirm Deletion</h3>
            <p class="text-gray-500 text-sm mb-6 text-center">Are you sure you want to delete <br><span class="font-bold text-gray-900" x-text="deleteTitle"></span>? <br>This action cannot be undone.</p>
            
            <div class="flex space-x-3 border-t border-gray-100 pt-4">
                <button type="button" @click="showDeleteModal = false" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                
                <form :action="`/admin/modules/${moduleId}`" method="POST" class="flex-1 m-0 p-0 flex">
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