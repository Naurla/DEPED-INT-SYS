@extends('layouts.admin')

@section('page_title', 'Advisory Management')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap');
    .font-cinzel { font-family: 'Cinzel', serif; }
    [x-cloak] { display: none !important; }
</style>

<div x-data="{ 
    uploadModal: false, 
    deleteModal: false,
    editMode: false,
    advisoryId: null,
    advisoryTitle: '',
    formData: { 
        title: '',
        currentImage: '',
        currentPdf: '' 
    },
    openEdit(advisory) {
        this.editMode = true;
        this.advisoryId = advisory.id;
        this.formData.title = advisory.title;
        this.formData.currentImage = advisory.image_path; // Store current image
        this.formData.currentPdf = advisory.pdf_path;     // Store current pdf
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

    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight font-cinzel">Manage Public Advisories</h2>
            <p class="text-gray-500 text-sm mt-1">Create, edit, and publish official advisories to the public portal.</p>
        </div>
        <button @click="openCreate()" class="bg-[#a52a2a] hover:bg-[#801a1a] text-white text-sm font-bold px-4 py-2.5 rounded-lg shadow-md transition-colors flex items-center tracking-wide shrink-0">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            ADD NEW ADVISORY
        </button>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Main Table Card (Issuances Style) --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                        <th class="p-4 border-b whitespace-nowrap">ID</th>
                        <th class="p-4 border-b">Title</th>
                        <th class="p-4 border-b whitespace-nowrap">Cover Image</th>
                        <th class="p-4 border-b whitespace-nowrap">PDF Document</th>
                        <th class="p-4 border-b whitespace-nowrap">Date Uploaded</th>
                        <th class="p-4 border-b text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($advisories as $advisory)
                        <tr class="hover:bg-gray-50 border-b transition-colors">
                            <td class="p-4 text-sm text-gray-600 font-medium">
                                {{ $advisories->firstItem() + $loop->index }}
                            </td>
                            <td class="p-4 font-semibold text-gray-800">
                                {{ $advisory->title }}
                            </td>
                            
                            {{-- Cover Image Link --}}
                            <td class="p-4 text-sm whitespace-nowrap">
                                @if($advisory->image_path)
                                    <a href="{{ asset('storage/' . $advisory->image_path) }}" target="_blank" class="text-blue-600 font-bold hover:underline flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        View Image
                                    </a>
                                @else
                                    <span class="text-gray-400 italic text-xs">N/A</span>
                                @endif
                            </td>

                            {{-- PDF Document Link (NOW RED) --}}
                            <td class="p-4 text-sm whitespace-nowrap">
                                @if($advisory->pdf_path)
                                    <a href="{{ asset('storage/' . $advisory->pdf_path) }}" target="_blank" class="text-red-600 font-bold hover:text-red-800 hover:underline flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        View PDF
                                    </a>
                                @else
                                    <span class="text-gray-400 italic text-xs">N/A</span>
                                @endif
                            </td>

                            <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $advisory->created_at->format('M d, Y') }}
                            </td>
                            
                            {{-- Text-based Actions (Edit / Delete) --}}
                            <td class="p-4 flex justify-end gap-3 mt-1">
                                <button @click="openEdit({{ $advisory }})" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase hover:underline">Edit</button>
                                <button @click="confirmDelete({{ $advisory }})" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-gray-500 italic">No advisories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4">
        @if($advisories->hasPages())
            {{ $advisories->appends(request()->query())->links() }}
        @endif
    </div>

    {{-- MODAL: ADD/EDIT ADVISORY --}}
    <div x-show="uploadModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="uploadModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75 backdrop-blur-sm" @click="uploadModal = false"></div>
            
            <div x-show="uploadModal" x-transition class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-xl border-t-4 border-[#a52a2a] relative z-50">
                
                <div class="flex items-center justify-between mb-5 border-b pb-3">
                    <h3 class="text-xl font-bold text-[#a52a2a] font-cinzel" x-text="editMode ? 'Edit Advisory' : 'Create New Advisory'"></h3>
                    <button @click="uploadModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form :action="editMode ? '/admin/advisories/' + advisoryId : '{{ route('admin.advisories.store') ?? '#' }}'" 
                      method="POST" enctype="multipart/form-data" class="font-sans">
                    @csrf
                    <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>
                    
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Advisory Title</label>
                            <input type="text" name="title" x-model="formData.title" required 
                                   class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-[#a52a2a] outline-none" 
                                   placeholder="Enter the title here...">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Banner Image <span class="text-xs font-normal text-gray-400 ml-1">(Optional on Edit)</span></label>
                            <input type="file" name="image" accept="image/*" :required="!editMode" 
                                   class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-[#a52a2a] hover:file:bg-red-100 cursor-pointer">
                            
                            {{-- Preview Existing Image on Edit --}}
                            <template x-if="editMode && formData.currentImage">
                                <div class="mt-2 flex items-center gap-3 p-2 bg-blue-50/50 border border-blue-100 rounded-lg w-fit">
                                    <img :src="'/storage/' + formData.currentImage" class="h-10 w-12 object-cover rounded shadow-sm border border-gray-200">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] text-gray-500 uppercase font-bold">Current Image</span>
                                        <a :href="'/storage/' + formData.currentImage" target="_blank" class="text-xs text-blue-600 hover:underline">View File</a>
                                    </div>
                                </div>
                            </template>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">PDF Document <span class="text-xs font-normal text-gray-400 ml-1">(Optional on Edit)</span></label>
                            <input type="file" name="pdf" accept=".pdf" :required="!editMode" 
                                   class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer">
                            
                            {{-- Preview Existing PDF on Edit (NOW RED) --}}
                            <template x-if="editMode && formData.currentPdf">
                                <div class="mt-2 flex items-center gap-3 p-2 bg-red-50/50 border border-red-100 rounded-lg w-fit">
                                    <div class="p-1.5 bg-white rounded shadow-sm border border-gray-200 text-red-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[10px] text-gray-500 uppercase font-bold">Current PDF</span>
                                        <a :href="'/storage/' + formData.currentPdf" target="_blank" class="text-xs text-red-600 hover:text-red-800 hover:underline">View File</a>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="uploadModal = false" class="px-5 py-2.5 text-sm font-bold text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-[#a52a2a] border border-transparent rounded-lg hover:bg-[#801a1a] shadow-sm transition-colors" x-text="editMode ? 'Save Changes' : 'Publish Advisory'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL: DELETE ADVISORY --}}
    <div x-show="deleteModal" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="deleteModal = false"></div>

            <div x-show="deleteModal" x-transition class="bg-white rounded-2xl p-8 shadow-2xl z-[70] w-full max-w-sm transform transition-all relative">
                
                <div class="absolute top-4 right-4 cursor-pointer text-gray-400 hover:text-gray-600" @click="deleteModal = false">
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
                        <strong class="text-gray-800 break-words" x-text="advisoryTitle"></strong>? <br>
                        This action cannot be undone.
                    </p>
                </div>
                
                <div class="flex space-x-3 w-full">
                    <button @click="deleteModal = false" class="flex-1 px-4 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition">
                        Cancel
                    </button>
                    
                    <form :action="'/admin/advisories/' + advisoryId" method="POST" class="flex-1">
                        @csrf 
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-[#a52a2a] text-white rounded-xl font-bold hover:bg-[#801a1a] shadow-lg shadow-red-200 transition">
                            Yes, Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection