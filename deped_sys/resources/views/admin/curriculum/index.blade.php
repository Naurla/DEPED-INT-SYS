@extends('layouts.admin')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap');
    .font-cinzel { font-family: 'Cinzel', serif; }
    [x-cloak] { display: none !important; }
</style>

<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-6 font-cinzel text-gray-800">Manage K-12 Curriculum Content</h2>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    {{-- Section 1: Main Banner (Collapsible) --}}
    <div class="bg-white rounded shadow-sm border border-gray-200 mb-8 " 
         x-data="imageUploader('{{ $pageData->banner_image_path ? asset('storage/' . $pageData->banner_image_path) : '' }}')">
        
        {{-- Clickable Header --}}
        <div class="flex justify-between items-center p-6 cursor-pointer hover:bg-gray-50 transition" 
             @click="isExpanded = !isExpanded">
            <h3 class="text-xl font-bold font-cinzel">Main Page Banner</h3>
            
            <div class="flex items-center gap-4 ">
                <template x-if="!imageUrl && isExpanded">
                    <button type="button" @click.stop="$refs.fileInput.click()" class="bg-blue-800 text-white px-4 py-2 rounded hover:bg-blue-900 font-bold shadow flex items-center gap-2 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Add Page Banner
                    </button>
                </template>
                
                {{-- Dropdown Arrow --}}
                <svg class="w-6 h-6 text-gray-500 transform transition-transform duration-300" 
                     :class="{'rotate-180': isExpanded}" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </div>

        {{-- Collapsible Content --}}
        <div x-show="isExpanded" x-collapse x-cloak>
            <div class="px-6 pb-6 border-t border-gray-100 pt-6">
                <form action="{{ route('admin.curriculum.update_page') }}" method="POST" enctype="multipart/form-data" x-ref="bannerForm">
                    @csrf
                    
                    <input type="file" name="banner_image" x-ref="fileInput" @change="fileChosen" class="hidden" accept="image/png, image/jpeg, image/jpg">
                    <input type="hidden" name="remove_image" x-model="removeFlag">

                    <template x-if="imageUrl">
                        <div class="relative w-full rounded-lg bg-gray-50 flex items-center justify-center overflow-hidden group shadow-sm"
                             @mouseenter="hovering = true" 
                             @mouseleave="hovering = false">
                            
                            <div class="w-full relative">
                                <img :src="imageUrl" alt="Banner Preview" class="w-full h-auto block rounded">
                                
                                <div x-show="hovering" 
                                     x-transition.opacity.duration.200ms
                                     class="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center gap-4">
                                    
                                    <button type="button" @click.stop="$refs.fileInput.click()" class="bg-white text-gray-900 px-6 py-2 rounded font-bold hover:bg-gray-200 shadow transition">
                                        Replace
                                    </button>
                                    
                                    <button type="button" @click.stop="removeImage" class="bg-red-600 text-white px-6 py-2 rounded font-bold hover:bg-red-700 shadow transition">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                    
                    <template x-if="!imageUrl">
                        <div class="text-center py-10 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                             <p class="text-gray-500 font-sans italic">No banner currently uploaded.</p>
                             <button type="button" @click="$refs.fileInput.click()" class="mt-4 bg-blue-800 text-white px-4 py-2 rounded hover:bg-blue-900 font-bold shadow inline-flex items-center gap-2 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Upload Banner
                            </button>
                        </div>
                    </template>
                </form>
            </div>
        </div>
    </div>

    {{-- Section 2: Learning Strands and Materials --}}
    <div class="bg-gray-50 p-6 rounded shadow-sm border border-gray-200 mb-8" x-data="{ showModal: false, showFileModal: false, activeStrandId: null }">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold font-cinzel text-gray-800">Learning Strands</h3>
            <button @click="showModal = true" class="bg-[#003366] text-white px-6 py-2 rounded shadow hover:bg-[#002244] transition font-bold font-cinzel tracking-wide">
                + ADD NEW LEARNING STRAND
            </button>
        </div>

        <div class="space-y-6">
            @forelse($strands as $strand)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 flex flex-col md:flex-row overflow-hidden relative"
                     x-data="{ 
                        showEditStrand: false, 
                        descriptions: {{ json_encode(is_array($strand->content_description) && count($strand->content_description) > 0 ? $strand->content_description : (is_string($strand->content_description) && !empty(trim($strand->content_description)) ? [$strand->content_description] : [''])) }} 
                     }">
                    
                    {{-- Action Buttons (Edit & Delete) --}}
                    <div class="absolute top-4 right-4 z-10 flex gap-2">
                        {{-- Edit Strand Button --}}
                        <button type="button" @click="showEditStrand = true" class="text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 p-1.5 rounded transition" title="Edit Strand">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </button>

                        {{-- Delete Strand Button (Triggers Global Modal) --}}
                        <button type="button" @click="$dispatch('open-delete-modal', { action: '{{ route('admin.curriculum.strands.destroy', $strand->id) }}', title: 'Are you sure you want to delete this Strand?' })" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-1.5 rounded transition" title="Delete Strand">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>

                    {{-- Left Side: Text Content --}}
                    <div class="p-6 md:w-2/3 border-b md:border-b-0 md:border-r border-gray-200">
                        <div class="mb-4 pr-20"> {{-- pr-20 prevents text overlapping with buttons --}}
                            <span class="bg-green-50 text-green-700 text-xs font-bold px-2 py-1 rounded inline-block mb-1 tracking-wider font-sans">TITLE</span>
                            <h4 class="text-xl font-bold text-gray-900 font-cinzel">{{ $strand->name }}</h4>
                        </div>
                        
                        <div class="mb-4">
                            <span class="bg-green-50 text-green-700 text-xs font-bold px-2 py-1 rounded inline-block mb-1 tracking-wider font-sans">CONTENT TITLE</span>
                            <h5 class="text-lg font-bold text-gray-800 font-cinzel">{{ $strand->content_title ?: 'No Content Title' }}</h5>
                        </div>
                        
                        <div>
                            <span class="bg-green-50 text-green-700 text-xs font-bold px-2 py-1 rounded inline-block mb-1 tracking-wider font-sans">CONTENT DESCRIPTIONS</span>
                            @if(is_array($strand->content_description) && count($strand->content_description) > 0)
                                <ul class="list-disc list-inside text-gray-600 mt-2 space-y-1 font-sans">
                                    @foreach($strand->content_description as $desc)
                                        @if(!empty($desc))
                                            <li>{{ $desc }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            @elseif(is_string($strand->content_description) && !empty($strand->content_description))
                                <p class="text-gray-600 mt-1 leading-relaxed font-sans">{{ $strand->content_description }}</p>
                            @else
                                <p class="text-gray-400 mt-1 italic font-sans">No descriptions provided.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Right Side: PDF Files --}}
                    <div class="p-6 md:w-1/3 bg-gray-50 flex flex-col justify-between">
                        <div>
                            <h6 class="font-bold text-gray-700 mb-4 border-b border-gray-200 pb-2 font-cinzel">Attached Materials</h6>
                            <ul class="space-y-2 mb-4">
                                @forelse($strand->materials as $material)
                                    <li class="flex items-center justify-between group bg-white px-3 py-2 rounded border border-gray-200 shadow-sm hover:border-blue-300 transition">
                                        <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="flex items-center text-blue-700 hover:text-blue-900 transition flex-grow overflow-hidden pr-2">
                                            <span class="text-red-500 mr-2 text-lg">📄</span>
                                            <span class="text-sm font-semibold truncate font-sans" title="{{ $material->title }}">{{ $material->title }}</span>
                                        </a>
                                        
                                        {{-- Delete Material Button (Triggers Global Modal) --}}
                                        <button type="button" @click="$dispatch('open-delete-modal', { action: '{{ route('admin.curriculum.materials.destroy', $material->id) }}', title: 'Are you sure you want to delete this PDF?' })" class="text-gray-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition p-1" title="Remove PDF">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </li>
                                @empty
                                    <li class="text-sm text-gray-500 italic font-sans">No files attached yet.</li>
                                @endforelse
                            </ul>
                        </div>
                        
                        <button @click="showFileModal = true; activeStrandId = {{ $strand->id }}" class="w-full border-2 border-dashed border-gray-300 bg-white text-gray-600 font-bold py-2 rounded hover:border-blue-500 hover:text-blue-600 hover:bg-blue-50 transition flex justify-center items-center gap-2 mt-4 text-sm font-cinzel tracking-wide">
                            <span class="text-xl leading-none font-sans">+</span> ADD NEW FILE
                        </button>
                    </div>

                    {{-- MODAL: Edit Learning Strand (Inside the loop to easily bind data) --}}
                    <div x-show="showEditStrand" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
                        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                            <div x-show="showEditStrand" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" @click="showEditStrand = false"></div>

                            <div x-show="showEditStrand" x-transition class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg border-t-4 border-blue-500">
                                <div class="flex justify-between items-center mb-5 border-b pb-3">
                                    <h3 class="text-xl font-bold text-gray-900 font-cinzel">Edit Learning Strand</h3>
                                    <button type="button" @click="showEditStrand = false" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>

                                <form action="{{ route('admin.curriculum.strands.update', $strand->id) }}" method="POST" class="space-y-4 font-sans">
                                    @csrf
                                    @method('PUT')
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Title</label>
                                        <input type="text" name="name" value="{{ $strand->name }}" class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Content Title</label>
                                        <input type="text" name="content_title" value="{{ $strand->content_title }}" class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2">
                                    </div>
                                    
                                    {{-- Dynamic Descriptions Input (Edit) --}}
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Content Descriptions (Bulleted List)</label>
                                        <div class="space-y-2">
                                            <template x-for="(desc, index) in descriptions" :key="index">
                                                <div class="flex items-start gap-2">
                                                    <div class="pt-2 text-gray-400">•</div>
                                                    <textarea x-model="descriptions[index]" name="content_description[]" rows="2" class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2" placeholder="Enter a bullet point description..."></textarea>
                                                    <button type="button" @click="descriptions.splice(index, 1)" x-show="descriptions.length > 1" class="text-red-500 hover:bg-red-50 p-2 rounded mt-1" title="Remove bullet">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                        <button type="button" @click="descriptions.push('')" class="mt-2 text-sm text-blue-600 font-bold hover:underline flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            Add another bullet point
                                        </button>
                                    </div>

                                    <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100">
                                        <button type="button" @click="showEditStrand = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 font-bold transition">Cancel</button>
                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-bold shadow-sm transition">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 py-8 font-sans">No learning strands created yet.</p>
            @endforelse
        </div>

        {{-- MODAL: Add New Learning Strand --}}
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="showModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" @click="showModal = false"></div>

                <div x-show="showModal" x-transition class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg border-t-4 border-[#003366]">
                    <div class="flex justify-between items-center mb-5 border-b pb-3">
                        <h3 class="text-xl font-bold text-gray-900 font-cinzel">Create New Learning Strand</h3>
                        <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form action="{{ route('admin.curriculum.strands.store') }}" method="POST" class="space-y-4 font-sans">
                        @csrf
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Title <span class="text-gray-400 font-normal">(e.g., LEARNING STRAND 1)</span></label>
                            <input type="text" name="name" class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-[#003366] focus:border-[#003366] p-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Content Title <span class="text-gray-400 font-normal">(e.g., COMMUNICATION SKILLS)</span></label>
                            <input type="text" name="content_title" class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-[#003366] focus:border-[#003366] p-2">
                        </div>
                        
                        {{-- Dynamic Descriptions Input --}}
                        <div x-data="{ descriptions: [''] }">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Content Descriptions (Bulleted List)</label>
                            <div class="space-y-2">
                                <template x-for="(desc, index) in descriptions" :key="index">
                                    <div class="flex items-start gap-2">
                                        <div class="pt-2 text-gray-400">•</div>
                                        <textarea x-model="descriptions[index]" name="content_description[]" rows="2" class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-[#003366] focus:border-[#003366] p-2" placeholder="Enter a bullet point description..."></textarea>
                                        <button type="button" @click="descriptions.splice(index, 1)" x-show="descriptions.length > 1" class="text-red-500 hover:bg-red-50 p-2 rounded mt-1" title="Remove bullet">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                            <button type="button" @click="descriptions.push('')" class="mt-2 text-sm text-[#003366] font-bold hover:underline flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Add another bullet point
                            </button>
                        </div>

                        <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 font-bold transition">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-[#003366] text-white rounded hover:bg-[#002244] font-bold shadow-sm transition">Save Strand</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL: Add New File --}}
        <div x-show="showFileModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="showFileModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" @click="showFileModal = false"></div>

                <div x-show="showFileModal" x-transition class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg border-t-4 border-green-600">
                    <div class="flex justify-between items-center mb-5 border-b pb-3">
                        <h3 class="text-xl font-bold text-gray-900 font-cinzel">Upload PDF Material</h3>
                        <button type="button" @click="showFileModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form action="{{ route('admin.curriculum.materials.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 font-sans">
                        @csrf
                        <input type="hidden" name="learning_strand_id" x-bind:value="activeStrandId">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">PDF Title</label>
                            <input type="text" name="title" class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-green-600 focus:border-green-600 p-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Select File (.pdf)</label>
                            <input type="file" name="pdf_file" accept=".pdf" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 border border-gray-300 rounded-md p-1" required>
                        </div>
                        <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" @click="showFileModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 font-bold transition">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-bold shadow-sm transition">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- Section 3: Curriculum Guides --}}
    <div class="bg-gray-50 p-6 rounded shadow-sm border border-gray-200" x-data="{ showEditModal: false, editId: '', editTitle: '', editLink: '', editAction: '' }">
        <div class="flex justify-between items-center mb-6 border-b border-gray-200 pb-4">
            <h3 class="text-2xl font-bold font-cinzel text-gray-800">Curriculum Guides</h3>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- Add New Guide Form --}}
            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm h-fit">
                <h4 class="font-bold text-gray-700 uppercase text-sm mb-4 font-cinzel border-b pb-2">Add New Guide</h4>
                <form action="{{ route('admin.curriculum.guides.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-widest mb-1">Track/Guide Title</label>
                        <input type="text" name="title" required placeholder="e.g. Academic Track" class="w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm focus:ring-[#003366] focus:border-[#003366]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-widest mb-1">External Link (URL)</label>
                        <input type="url" name="link" required placeholder="https://..." class="w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm focus:ring-[#003366] focus:border-[#003366]">
                    </div>
                    <button type="submit" class="w-full bg-[#003366] hover:bg-[#002244] text-white font-bold uppercase text-xs py-2.5 rounded shadow transition font-sans tracking-wide">
                        Save Guide
                    </button>
                </form>
            </div>

            {{-- Table of Guides --}}
            <div class="lg:col-span-2 bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-100 text-gray-600 uppercase font-bold text-xs">
                            <tr>
                                <th class="px-5 py-3 border-b">Guide Title</th>
                                <th class="px-5 py-3 border-b">Redirect Link</th>
                                <th class="px-5 py-3 border-b text-center w-28">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @forelse($guides ?? [] as $guide)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-4 font-bold">{{ $guide->title }}</td>
                                    <td class="px-5 py-4 text-blue-600 truncate max-w-[200px]">
                                        <a href="{{ $guide->link }}" target="_blank" class="hover:underline flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                            {{ $guide->link }}
                                        </a>
                                    </td>
                                    <td class="px-5 py-4 flex justify-center gap-2">
                                        <button @click="showEditModal = true; editId = '{{ $guide->id }}'; editTitle = '{{ addslashes($guide->title) }}'; editLink = '{{ addslashes($guide->link) }}'; editAction = '/admin/curriculum/guides/{{ $guide->id }}'" 
                                                class="text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 p-1.5 rounded transition" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>
                                        
                                        {{-- Delete Guide Button (Triggers Global Modal) --}}
                                        <button type="button" @click="$dispatch('open-delete-modal', { action: '{{ route('admin.curriculum.guides.destroy', $guide->id) }}', title: 'Are you sure you want to delete this Guide?' })" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-1.5 rounded transition" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-8 text-center text-gray-500 italic">No curriculum guides added yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- MODAL: Edit Guide --}}
        <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="showEditModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" @click="showEditModal = false"></div>

                <div x-show="showEditModal" x-transition class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg border-t-4 border-blue-500">
                    <div class="flex justify-between items-center mb-5 border-b pb-3">
                        <h3 class="text-xl font-bold text-gray-900 font-cinzel">Edit Curriculum Guide</h3>
                        <button type="button" @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form :action="editAction" method="POST" class="space-y-4 font-sans">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Track/Guide Title</label>
                            <input type="text" name="title" x-model="editTitle" class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">External Link (URL)</label>
                            <input type="url" name="link" x-model="editLink" class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2" required>
                        </div>
                        <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" @click="showEditModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 font-bold transition">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-bold shadow-sm transition">Update Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    {{-- GLOBAL MODAL: Delete Confirmation --}}
    <div x-data="{ showDeleteModal: false, deleteAction: '', deleteTitle: '' }" 
         @open-delete-modal.window="showDeleteModal = true; deleteAction = $event.detail.action; deleteTitle = $event.detail.title"
         x-show="showDeleteModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showDeleteModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" @click="showDeleteModal = false"></div>

            <div x-show="showDeleteModal" x-transition class="inline-block w-full max-w-sm p-6 my-8 overflow-hidden text-center align-middle transition-all transform bg-white shadow-2xl rounded-2xl relative z-10">
                
                {{-- Close X --}}
                <div class="absolute top-4 right-4 cursor-pointer text-gray-400 hover:text-gray-600" @click="showDeleteModal = false">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>

                <div class="flex flex-col items-center justify-center mt-2">
                    {{-- Red Exclamation Icon --}}
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-16 w-16 mb-4 text-red-500">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-16 h-16">
                            <circle cx="12" cy="12" r="10" stroke-width="1.5"></circle>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path>
                        </svg>
                    </div>
                    
                    <h3 class="text-xl font-bold text-gray-900 mb-6 px-4" x-text="deleteTitle"></h3>
                </div>
                
                <form :action="deleteAction" method="POST" class="flex flex-col gap-3 font-sans w-full">
                    @csrf
                    @method('DELETE')
                    
                    <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-3 bg-[#111827] text-base font-medium text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition">
                        Yes, Delete
                    </button>
                    
                    <button type="button" @click="showDeleteModal = false" class="w-full inline-flex justify-center rounded-lg border border-red-500 px-4 py-3 bg-white text-base font-medium text-red-500 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
                        Cancel
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('imageUploader', (initialImage) => ({
            imageUrl: initialImage,
            removeFlag: 0,
            hovering: false,
            isExpanded: false,
            fileChosen(event) {
                const file = event.target.files[0];
                if (file) {
                    this.removeFlag = 0;
                    this.$refs.bannerForm.submit();
                }
            },
            removeImage() {
                this.removeFlag = 1; 
                this.$nextTick(() => { this.$refs.bannerForm.submit(); });
            }
        }));
    });
</script>
@endsection