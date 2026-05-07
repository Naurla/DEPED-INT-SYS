@extends('layouts.admin')

@section('page_title', 'Advisory Management')

@section('content')
<style>
    [x-cloak] { display: none !important; }
    
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #fca5a5; border-radius: 10px; }
</style>

<div class="container mx-auto px-4 py-6 w-full" x-data="{ 
    uploadModal: {{ $errors->any() ? 'true' : 'false' }}, 
    deleteModal: false,
    successModal: {{ session('success') ? 'true' : 'false' }},
    editMode: {{ (old('_method') == 'PUT' || (isset($advisoryId) && $errors->any())) ? 'true' : 'false' }},
    isSubmitting: false,
    advisoryId: '{{ old('advisory_id') }}',
    advisoryTitle: '',
    formData: { 
        title: '{!! addslashes(old('title', '')) !!}',
        currentImage: '',
        currentPdf: ''
    },
    openEdit(advisory) {
        this.editMode = true;
        this.advisoryId = advisory.id;
        this.formData.title = advisory.title;
        this.formData.currentImage = advisory.image_path; 
        this.formData.currentPdf = advisory.pdf_path;     
        this.uploadModal = true;
    },
    openCreate() {
        this.editMode = false;
        this.advisoryId = null;
        this.formData.title = '';
        this.formData.currentImage = '';
        this.formData.currentPdf = '';
        this.uploadModal = true;
    },
    confirmDelete(advisory) {
        this.advisoryId = advisory.id;
        this.advisoryTitle = advisory.title;
        this.deleteModal = true;
    }
}">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 w-full">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Manage Public Advisories</h2>
            <p class="text-sm text-gray-500 mt-1">Create, edit, and publish official advisories to the public portal.</p>
        </div>
        <button @click="openCreate()" class="bg-red-700 hover:bg-red-800 text-white text-sm font-bold py-2.5 px-5 rounded-lg shadow transition-colors flex items-center shrink-0 uppercase tracking-wider">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New Advisory
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6 w-full">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                        <th class="p-4 border-b w-16 text-center">#</th>
                        <th class="p-4 border-b w-1/3">Title</th>
                        <th class="p-4 border-b">Cover Image</th>
                        <th class="p-4 border-b">PDF Document</th>
                        <th class="p-4 border-b whitespace-nowrap">Date Uploaded</th>
                        <th class="p-4 border-b text-right whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($advisories as $index => $advisory)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 text-sm text-gray-600 font-medium text-center align-middle">{{ $advisories->firstItem() + $index }}</td>
                            <td class="p-4 font-bold text-gray-900 align-middle break-words max-w-xs md:max-w-md">{{ $advisory->title }}</td>
                            <td class="p-4 align-middle">
                                @if($advisory->image_path)
                                    <a href="{{ asset('storage/' . $advisory->image_path) }}" target="_blank" class="text-blue-600 font-bold hover:underline inline-flex items-center text-xs whitespace-nowrap transition-colors">
                                        <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ Str::limit(basename($advisory->image_path), 20) }}
                                    </a>
                                @else
                                    <span class="text-gray-400 italic text-[10px]">N/A</span>
                                @endif
                            </td>
                            <td class="p-4 align-middle">
                                @if($advisory->pdf_path)
                                    <a href="{{ asset('storage/' . $advisory->pdf_path) }}" target="_blank" class="text-red-600 font-bold hover:underline inline-flex items-center text-xs whitespace-nowrap transition-colors">
                                        <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        {{ Str::limit(basename($advisory->pdf_path), 20) }}
                                    </a>
                                @else
                                    <span class="text-gray-400 italic text-[10px]">N/A</span>
                                @endif
                            </td>
                            <td class="p-4 text-sm text-gray-500 whitespace-nowrap align-middle">{{ $advisory->created_at->format('M d, Y') }}</td>
                            <td class="p-4 align-middle text-right">
                                <div class="flex justify-end gap-3 items-center">
                                    <button @click="openEdit({{ $advisory }})" class="text-blue-600 font-bold uppercase text-xs hover:underline">Edit</button>
                                    <button @click="confirmDelete({{ $advisory }})" class="text-red-600 font-bold uppercase text-xs hover:underline">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-10 text-center text-gray-500 italic">No advisories found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($advisories->hasPages())
        <div class="mt-4 mb-6 w-full">
            {{ $advisories->links() }}
        </div>
    @endif

    {{-- MODAL: ADD/EDIT ADVISORY (Extra Large size) --}}
    <div x-show="uploadModal" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-5xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]" @click.away="if(!isSubmitting) uploadModal = false">
            <div class="bg-red-700 px-8 py-5 flex justify-between items-center text-white flex-shrink-0">
                <h3 class="font-bold text-2xl uppercase tracking-tight" x-text="editMode ? 'Edit Advisory' : 'Create New Advisory'"></h3>
                <button type="button" @click="uploadModal = false" :disabled="isSubmitting" class="hover:text-gray-200 text-4xl font-bold disabled:opacity-50">&times;</button>
            </div>
            
            <form :action="editMode ? '/admin/advisories/' + advisoryId : '{{ route('admin.advisories.store') }}'" method="POST" enctype="multipart/form-data" class="flex flex-col overflow-hidden min-h-0" @submit="isSubmitting = true">
                @csrf
                <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>
                <input type="hidden" name="advisory_id" x-model="advisoryId">

                <div class="p-8 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Advisory Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" x-model="formData.title" required :readonly="isSubmitting"
                               class="w-full border @error('title') border-red-500 @else border-gray-300 @enderror p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none transition-shadow" 
                               placeholder="e.g. Schedule of Re-assessment for Administrative Positions">
                        @error('title') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-lg border border-gray-200">
                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2" x-text="editMode ? 'Replace Banner Image' : 'Banner Image Attachment'"></label>
                            <span x-show="!editMode" class="text-red-500 hidden">*</span>
                            <input type="file" name="image" accept="image/*" :required="!editMode" :disabled="isSubmitting"
                                   class="w-full border border-gray-300 p-3.5 rounded-lg text-sm text-gray-600 file:mr-5 file:py-2.5 file:px-6 file:rounded-md file:border-0 file:text-base file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white disabled:opacity-50">
                            @error('image') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror
                            
                            <template x-if="editMode && formData.currentImage">
                                <div class="mt-4 flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex items-center">
                                        <img :src="'/storage/' + formData.currentImage" class="h-12 w-16 object-cover rounded shadow-sm border border-white mr-3">
                                        <span class="text-xs font-bold text-blue-800 truncate max-w-[200px]" x-text="'Current: ' + formData.currentImage.split('/').pop()"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                        
                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2" x-text="editMode ? 'Replace PDF Document' : 'PDF Document Attachment'"></label>
                            <span x-show="!editMode" class="text-red-500 hidden">*</span>
                            <input type="file" name="pdf" accept=".pdf" :required="!editMode" :disabled="isSubmitting"
                                   class="w-full border border-gray-300 p-3.5 rounded-lg text-sm text-gray-600 file:mr-5 file:py-2.5 file:px-6 file:rounded-md file:border-0 file:text-base file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white disabled:opacity-50">
                            @error('pdf') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror
                            
                            <template x-if="editMode && formData.currentPdf">
                                <div class="mt-4 flex items-center justify-between p-3 bg-red-50 border border-red-200 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="p-1.5 bg-white rounded shadow-sm mr-3 border border-gray-200"><svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg></div>
                                        <span class="text-xs font-bold text-red-800 truncate max-w-[200px]" x-text="'Current: ' + formData.currentPdf.split('/').pop()"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-4 items-center border-t border-gray-200 flex-shrink-0">
                    <button type="submit" :disabled="isSubmitting" :class="{'opacity-75 cursor-wait': isSubmitting, 'hover:bg-red-800': !isSubmitting}" class="bg-red-700 text-white font-bold py-3.5 px-10 rounded-lg shadow-md transition-colors text-lg flex justify-center min-w-[200px]">
                        <span x-show="!isSubmitting" x-text="editMode ? 'Save Changes' : 'Publish Advisory'"></span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Saving...
                        </span>
                    </button>
                    <button type="button" @click="uploadModal = false" :disabled="isSubmitting" class="px-8 py-3.5 text-lg font-bold text-gray-600 hover:text-gray-800 transition-colors disabled:opacity-50">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODERNIZED DELETE MODAL --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="if(!isSubmitting) deleteModal = false">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <div class="text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-2 text-center">Delete Advisory?</h3>
                <p class="text-gray-500 text-sm mb-5 text-center px-4">You are about to permanently delete this official advisory:</p>
                <div class="mb-8 max-h-32 overflow-y-auto custom-scrollbar text-center">
                    <span class="font-bold text-gray-900 break-all text-lg" x-text="advisoryTitle"></span>
                </div>
                <p class="text-gray-400 text-sm italic mb-8">This action cannot be undone.</p>
            </div>
            <div class="flex gap-3">
                <button type="button" @click="deleteModal = false" :disabled="isSubmitting" class="flex-1 inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-all disabled:opacity-50">Cancel</button>
                <form :action="'/admin/advisories/' + advisoryId" method="POST" class="flex-1 m-0 p-0" @submit="isSubmitting = true">
                    @csrf @method('DELETE')
                    <button type="submit" :disabled="isSubmitting" :class="{'opacity-75 cursor-wait': isSubmitting, 'hover:bg-red-700': !isSubmitting}" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition-all focus:ring-2 focus:ring-red-500">
                        <span x-show="!isSubmitting">Yes, Delete it</span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Deleting...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- MODERNIZED SUCCESS MODAL --}}
    <div x-show="successModal" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="successModal = false">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
            <div class="text-center mb-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-2 text-center uppercase tracking-tight">Success!</h3>
                <p class="text-gray-500 text-base leading-relaxed px-4 text-center">
                    @if(session('success')) {{ session('success') }} @else Operation completed successfully. @endif
                </p>
            </div>
            <div class="flex">
                <button type="button" @click="successModal = false" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-700 px-6 py-3.5 text-base font-bold text-white shadow-sm hover:bg-red-800 transition-all focus:ring-2 focus:ring-red-500 uppercase tracking-widest">Continue</button>
            </div>
        </div>
    </div>

</div>
@endsection