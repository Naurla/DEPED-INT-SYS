@extends('layouts.admin')

@section('page_title', 'Manage ' . ucfirst($type) . 's')

@section('content')
<style>
    [x-cloak] { display: none !important; }
    
    /* Subtle scrollbar for the modal target boxes */
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
    isSubmitting: false, /* Prevents modal disappearance and shows loading state */
    
    editIssuance: {
        id: '{{ old('issuance_id') }}',
        title: '{{ addslashes(old('title')) }}',
        date: '{{ old('date') }}',
        description: '{{ addslashes(old('description')) }}',
        link: '{{ addslashes(old('link')) }}',
        pdf_path: '{{ addslashes(old('existing_pdf_path')) }}'
    }, 
    removePdf: {{ old('remove_pdf') == '1' ? 'true' : 'false' }},
    deleteId: null,
    deleteTitle: '',
    
    openEdit(issuance) {
        this.editIssuance = issuance;
        this.removePdf = false;
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
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Manage {{ $type }}s</h2>
            <p class="text-gray-500 text-sm mt-1">Upload and edit public issuance documents or links.</p>
        </div>
        <button @click="addModal = true" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-4 rounded-lg shadow transition-colors text-sm uppercase tracking-wider flex items-center">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
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
                        <th class="p-4 border-b whitespace-nowrap">Document / Link</th>
                        <th class="p-4 border-b whitespace-nowrap">Date Uploaded</th>
                        <th class="p-4 border-b text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($issuances as $issuance)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 text-sm text-gray-600 font-medium text-center align-middle">{{ $issuances->firstItem() + $loop->index }}</td>
                            
                            <td class="p-4 font-bold text-gray-900 align-middle break-words max-w-xs md:max-w-md">
                                {{ $issuance->title }}
                            </td>
                            
                            <td class="p-4 text-sm text-gray-600 align-middle">
                                <div x-data="{ expanded: false }" class="max-w-xs">
                                    <p class="cursor-pointer hover:text-gray-900 transition-colors break-words"
                                       :class="expanded ? '' : 'line-clamp-2 italic'"
                                       @click="expanded = !expanded"
                                       title="Click to show/hide">
                                        {{ $issuance->description ?? 'N/A' }}
                                    </p>
                                </div>
                            </td>
                            
                            <td class="p-4 text-sm whitespace-nowrap align-middle">
                                @if($issuance->pdf_path)
                                    <a href="{{ asset('storage/' . $issuance->pdf_path) }}" target="_blank" title="{{ basename($issuance->pdf_path) }}" class="text-red-600 font-bold hover:text-red-800 hover:underline flex items-center mb-1.5 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span class="max-w-[120px] truncate text-xs">{{ basename($issuance->pdf_path) }}</span>
                                    </a>
                                @endif

                                @if($issuance->link)
                                    <a href="{{ $issuance->link }}" target="_blank" title="{{ $issuance->link }}" class="text-blue-600 font-bold hover:text-blue-800 hover:underline flex items-center transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        <span class="max-w-[120px] truncate text-xs">External Link</span>
                                    </a>
                                @endif

                                @if(!$issuance->pdf_path && !$issuance->link)
                                    <span class="text-gray-400 italic text-xs">No document attached</span>
                                @endif
                            </td>

                            <td class="p-4 text-xs text-gray-500 font-medium whitespace-nowrap align-middle">{{ \Carbon\Carbon::parse($issuance->date)->format('M d, Y') }}</td>
                            <td class="p-4 align-middle">
                                <div class="flex justify-end gap-3 items-center">
                                    <button @click="openEdit({{ $issuance->toJson() }})" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase hover:underline">Edit</button>
                                    <button @click="confirmDelete({{ $issuance->id }}, '{{ addslashes($issuance->title) }}')" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-10 text-center text-gray-500 italic">No records found. Click "Upload New" to get started!</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($issuances->hasPages())
        <div class="mt-4">
            {{ $issuances->links() }}
        </div>
    @endif

    {{-- EXACT MATCH MODAL: ADD ISSUANCE --}}
    <div x-show="addModal" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-5xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]" @click.away="if (!isSubmitting) addModal = false">
            
            <div class="bg-red-700 px-8 py-5 flex justify-between items-center text-white flex-shrink-0">
                <h3 class="font-bold text-2xl">Upload New {{ ucfirst($type) }}</h3>
                <button type="button" @click="addModal = false" :disabled="isSubmitting" class="hover:text-gray-200 text-4xl font-bold leading-none disabled:opacity-50">&times;</button>
            </div>
            
            <form action="{{ route('admin.issuances.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col overflow-hidden min-h-0" @submit="isSubmitting = true">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">
                <input type="hidden" name="form_type" value="add">
                
                <div class="p-8 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2">Document Title <span class="text-red-500">*</span></label>
                            <input type="text" name="title" value="{{ old('title') }}" required class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none" placeholder="e.g. Division Memo No. 1" :readonly="isSubmitting">
                            @error('title') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2">Document Date <span class="font-normal text-gray-500 text-base">(Optional)</span></label>
                            <input type="date" name="date" value="{{ old('date') }}" class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none" :readonly="isSubmitting">
                            @error('date') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Description / Content <span class="font-normal text-gray-500 text-base">(Optional)</span></label>
                        <textarea name="description" rows="5" class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none resize-none" placeholder="Briefly describe what this document contains..." :readonly="isSubmitting">{{ old('description') }}</textarea>
                        @error('description') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 space-y-6">
                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2">Upload PDF Document <span class="font-normal text-gray-500 text-base">(Optional if link is provided)</span></label>
                            <input type="file" name="pdf_file" accept=".pdf" :disabled="isSubmitting" class="w-full border border-gray-300 p-3.5 rounded-lg text-lg text-gray-600 file:mr-5 file:py-3 file:px-6 file:rounded-md file:border-0 file:text-base file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white disabled:opacity-50">
                            @error('pdf_file') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="relative flex py-2 items-center">
                            <div class="flex-grow border-t border-gray-300"></div>
                            <span class="flex-shrink-0 mx-4 text-gray-500 text-sm font-bold uppercase tracking-widest">OR / AND</span>
                            <div class="flex-grow border-t border-gray-300"></div>
                        </div>

                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2">External Link <span class="font-normal text-gray-500 text-base">(Optional if PDF is uploaded)</span></label>
                            <input type="url" name="link" value="{{ old('link') }}" class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none" placeholder="https://example.com/document" :readonly="isSubmitting">
                            @error('link') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-4 items-center border-t border-gray-200 flex-shrink-0">
                    <button type="submit" :disabled="isSubmitting" :class="{'opacity-75 cursor-wait': isSubmitting, 'hover:bg-red-800': !isSubmitting}" class="bg-red-700 text-white font-bold py-3.5 px-10 rounded-lg shadow-md transition-colors text-lg flex items-center justify-center min-w-[200px]">
                        <span x-show="!isSubmitting">Upload Record</span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Uploading...
                        </span>
                    </button>
                    <button type="button" @click="addModal = false" :disabled="isSubmitting" class="px-8 py-3.5 text-lg font-bold text-gray-600 hover:text-gray-800 transition-colors disabled:opacity-50">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- EXACT MATCH MODAL: EDIT ISSUANCE --}}
    <div x-show="editModal" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-5xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]" @click.away="if (!isSubmitting) editModal = false">
            
            <div class="bg-red-700 px-8 py-5 flex justify-between items-center text-white flex-shrink-0">
                <h3 class="font-bold text-2xl">Edit {{ ucfirst($type) }} Entry</h3>
                <button type="button" @click="editModal = false" :disabled="isSubmitting" class="hover:text-gray-200 text-4xl font-bold leading-none disabled:opacity-50">&times;</button>
            </div>
            
            <form :action="`/admin/issuances/${editIssuance?.id}`" method="POST" enctype="multipart/form-data" class="flex flex-col overflow-hidden min-h-0" @submit="isSubmitting = true">
                @csrf @method('PUT')
                <input type="hidden" name="remove_pdf" :value="removePdf ? '1' : '0'">
                <input type="hidden" name="form_type" value="edit">
                <input type="hidden" name="issuance_id" :value="editIssuance?.id">

                <div class="p-8 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2">Document Title <span class="text-red-500">*</span></label>
                            <input type="text" name="title" x-model="editIssuance.title" required class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none" :readonly="isSubmitting">
                            @error('title') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2">Document Date</label>
                            <input type="date" name="date" x-model="editIssuance.date" class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none" :readonly="isSubmitting">
                            @error('date') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Description <span class="font-normal text-gray-500 text-base">(Optional)</span></label>
                        <textarea name="description" x-model="editIssuance.description" rows="5" class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none resize-none" :readonly="isSubmitting"></textarea>
                        @error('description') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 space-y-6">
                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2">Replace PDF Document <span class="font-normal text-gray-500 text-base">(Leave blank to keep current)</span></label>
                            <input type="file" name="pdf_file" accept=".pdf" :disabled="isSubmitting" class="w-full border border-gray-300 p-3.5 rounded-lg text-lg text-gray-600 file:mr-5 file:py-3 file:px-6 file:rounded-md file:border-0 file:text-base file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white disabled:opacity-50">
                            @error('pdf_file') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror
                            
                            <template x-if="editIssuance && editIssuance.pdf_path && !removePdf">
                                <div class="mt-3 flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-white rounded shadow-sm border border-gray-200 text-blue-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">Current PDF</span>
                                            <span class="text-base text-blue-800 font-bold truncate max-w-[300px]" x-text="editIssuance.pdf_path.split('/').pop()"></span>
                                        </div>
                                    </div>
                                    <button type="button" @click="removePdf = true" :disabled="isSubmitting" class="p-2 text-red-500 hover:bg-red-100 rounded-lg transition-colors disabled:opacity-50">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </template>
                            <template x-if="removePdf">
                                <span class="text-sm text-red-500 mt-2 block font-medium italic">Document will be removed upon saving.</span>
                            </template>
                        </div>

                        <div class="relative flex py-2 items-center">
                            <div class="flex-grow border-t border-gray-300"></div>
                            <span class="flex-shrink-0 mx-4 text-gray-500 text-sm font-bold uppercase tracking-widest">AND / OR</span>
                            <div class="flex-grow border-t border-gray-300"></div>
                        </div>

                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2">External Link <span class="font-normal text-gray-500 text-base">(Optional)</span></label>
                            <input type="url" name="link" x-model="editIssuance.link" class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none" placeholder="https://example.com/document" :readonly="isSubmitting">
                            @error('link') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-4 items-center border-t border-gray-200 flex-shrink-0">
                    <button type="submit" :disabled="isSubmitting" :class="{'opacity-75 cursor-wait': isSubmitting, 'hover:bg-red-800': !isSubmitting}" class="bg-red-700 text-white font-bold py-3.5 px-10 rounded-lg shadow-md transition-colors text-lg flex items-center justify-center min-w-[200px]">
                        <span x-show="!isSubmitting">Update Issuance</span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Saving...
                        </span>
                    </button>
                    <button type="button" @click="editModal = false" :disabled="isSubmitting" class="px-8 py-3.5 text-lg font-bold text-gray-600 hover:text-gray-800 transition-colors disabled:opacity-50">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODERNIZED GLOBAL MODAL: Delete Confirmation --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="if (!isSubmitting) deleteModal = false">
            
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            
            <div class="text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Delete Issuance?</h3>
                <p class="text-gray-500 text-sm mb-5">You are about to permanently delete this issuance:</p>
                <div class="mb-8 max-h-32 overflow-y-auto custom-scrollbar">
                    <span class="font-bold text-gray-900 break-all text-lg block" x-text="deleteTitle"></span>
                </div>
                <p class="text-gray-400 text-sm italic mb-8">This action cannot be undone.</p>
            </div>
            
            <div class="flex gap-3">
                <button type="button" @click="deleteModal = false" :disabled="isSubmitting" class="flex-1 inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all disabled:opacity-50">
                    Cancel
                </button>
                
                <form :action="`/admin/issuances/${deleteId}`" method="POST" class="flex-1 m-0 p-0 flex" @submit="isSubmitting = true">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" :disabled="isSubmitting" :class="{'opacity-75 cursor-wait': isSubmitting, 'hover:bg-red-700': !isSubmitting}" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-5 py-3 text-sm font-bold text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 transition-all">
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

    {{-- MODERNIZED GLOBAL MODAL: Success Message (Red Theme) --}}
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
                <button type="button" @click="successModal = false" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-700 px-6 py-3 text-base font-bold text-white shadow-sm hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-all">
                    Continue
                </button>
            </div>

        </div>
    </div>
</div>
@endsection