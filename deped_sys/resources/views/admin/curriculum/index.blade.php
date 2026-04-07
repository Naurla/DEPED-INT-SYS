@extends('layouts.admin')

@section('content')
<div class="container mx-auto p-4 space-y-6">
    
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Manage K-12 Curriculum Content</h2>
            <p class="text-gray-500 text-sm mt-1">Update the page banner, learning strands, and curriculum guides.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Section 1: Main Banner (Collapsible) --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden" 
         x-data="imageUploader('{{ $pageData->banner_image_path ? asset('storage/' . $pageData->banner_image_path) : '' }}')">
        
        {{-- Clickable Header --}}
        <div class="flex justify-between items-center p-6 cursor-pointer hover:bg-gray-50 transition-colors" 
             @click="isExpanded = !isExpanded">
            <h3 class="text-lg font-bold text-gray-800">Main Page Banner</h3>
            
            <div class="flex items-center gap-4">
                <template x-if="!imageUrl && isExpanded">
                    <button type="button" @click.stop="$refs.fileInput.click()" class="bg-red-700 text-white px-4 py-2 rounded-lg hover:bg-red-800 font-bold shadow-sm flex items-center gap-2 transition-colors text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Add Page Banner
                    </button>
                </template>
                
                {{-- Dropdown Arrow --}}
                <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-300" 
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
                        <div class="relative w-full rounded-lg bg-gray-50 flex items-center justify-center overflow-hidden group shadow-sm border border-gray-200"
                             @mouseenter="hovering = true" 
                             @mouseleave="hovering = false">
                            
                            <div class="w-full relative">
                                <img :src="imageUrl" alt="Banner Preview" class="w-full h-auto block rounded-lg">
                                
                                <div x-show="hovering" 
                                     x-transition.opacity.duration.200ms
                                     class="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center gap-4 rounded-lg">
                                    
                                    <button type="button" @click.stop="$refs.fileInput.click()" class="bg-white text-gray-900 px-6 py-2 rounded-lg font-bold hover:bg-gray-100 shadow transition-colors text-sm">
                                        Replace
                                    </button>
                                    
                                    <button type="button" @click.stop="removeImage" class="bg-red-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-red-700 shadow transition-colors text-sm">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                    
                    <template x-if="!imageUrl">
                        <div class="text-center py-10 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                             <p class="text-gray-500 text-sm italic">No banner currently uploaded.</p>
                             <button type="button" @click="$refs.fileInput.click()" class="mt-4 bg-red-700 text-white px-4 py-2.5 rounded-lg hover:bg-red-800 font-bold shadow-sm inline-flex items-center gap-2 transition-colors text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Upload Banner
                            </button>
                        </div>
                    </template>
                </form>
            </div>
        </div>
    </div>

    {{-- Section 2: Learning Strands and Materials --}}
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200" x-data="{ showModal: false, showFileModal: false, activeStrandId: null }">
        <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
            <h3 class="text-lg font-bold text-gray-800">Learning Strands</h3>
            <button @click="showModal = true" class="bg-red-700 text-white px-4 py-2.5 rounded-lg shadow-sm hover:bg-red-800 transition-colors font-bold text-sm">
                + Add New Strand
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
                    <div class="absolute top-4 right-4 z-10 flex gap-3 items-center bg-white/90 backdrop-blur-sm p-1 rounded-lg">
                        <button type="button" @click="showEditStrand = true" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase hover:underline" title="Edit Strand">
                            Edit
                        </button>
                        <button type="button" @click="$dispatch('open-delete-modal', { action: '{{ route('admin.curriculum.strands.destroy', $strand->id) }}', title: 'Are you sure you want to delete this Strand?' })" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline" title="Delete Strand">
                            Delete
                        </button>
                    </div>

                    {{-- Left Side: Text Content --}}
                    <div class="p-6 md:w-2/3 border-b md:border-b-0 md:border-r border-gray-200">
                        <div class="mb-4 pr-24"> 
                            <span class="bg-gray-100 text-gray-700 border border-gray-200 text-[10px] font-bold px-2 py-1 rounded inline-block mb-2 uppercase tracking-wider">Title</span>
                            <h4 class="text-xl font-bold text-gray-900">{{ $strand->name }}</h4>
                        </div>
                        
                        <div class="mb-4">
                            <span class="bg-gray-100 text-gray-700 border border-gray-200 text-[10px] font-bold px-2 py-1 rounded inline-block mb-2 uppercase tracking-wider">Content Title</span>
                            <h5 class="text-lg font-bold text-gray-800">{{ $strand->content_title ?: 'No Content Title' }}</h5>
                        </div>
                        
                        <div>
                            <span class="bg-gray-100 text-gray-700 border border-gray-200 text-[10px] font-bold px-2 py-1 rounded inline-block mb-2 uppercase tracking-wider">Descriptions</span>
                            @if(is_array($strand->content_description) && count($strand->content_description) > 0)
                                <ul class="list-disc list-inside text-gray-600 mt-1 space-y-1 text-sm">
                                    @foreach($strand->content_description as $desc)
                                        @if(!empty($desc))
                                            <li>{{ $desc }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            @elseif(is_string($strand->content_description) && !empty($strand->content_description))
                                <p class="text-gray-600 mt-1 leading-relaxed text-sm">{{ $strand->content_description }}</p>
                            @else
                                <p class="text-gray-400 mt-1 italic text-sm">No descriptions provided.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Right Side: PDF Files --}}
                    <div class="p-6 md:w-1/3 bg-gray-50 flex flex-col justify-between">
                        <div>
                            <h6 class="font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2">Attached Materials</h6>
                            <ul class="space-y-2 mb-4">
                                @forelse($strand->materials as $material)
                                    <li class="flex items-center justify-between bg-white px-3 py-2.5 rounded-lg border border-gray-200 shadow-sm hover:border-red-300 transition-colors">
                                        <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="flex items-center text-blue-600 hover:text-blue-800 transition flex-grow overflow-hidden pr-2">
                                            <span class="text-lg mr-2">📄</span>
                                            <span class="text-sm font-semibold truncate" title="{{ $material->title }}">{{ $material->title }}</span>
                                        </a>
                                        
                                        <button type="button" @click="$dispatch('open-delete-modal', { action: '{{ route('admin.curriculum.materials.destroy', $material->id) }}', title: 'Are you sure you want to delete this PDF?' })" class="text-xs font-bold uppercase text-red-600 hover:text-red-800 hover:underline shrink-0" title="Remove PDF">
                                            Remove
                                        </button>
                                    </li>
                                @empty
                                    <li class="text-sm text-gray-500 italic text-center py-4">No files attached yet.</li>
                                @endforelse
                            </ul>
                        </div>
                        
                        <button @click="showFileModal = true; activeStrandId = {{ $strand->id }}" class="w-full border-2 border-dashed border-gray-300 bg-white text-gray-600 font-bold py-2.5 rounded-lg hover:border-red-700 hover:text-red-700 hover:bg-red-50 transition-colors flex justify-center items-center gap-2 mt-4 text-sm">
                            <span class="text-lg leading-none">+</span> Add New File
                        </button>
                    </div>

                    {{-- MODAL: Edit Learning Strand --}}
                    <div x-show="showEditStrand" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity" style="display: none;">
                        <div class="bg-white rounded-xl w-full max-w-lg shadow-2xl overflow-hidden" @click.away="showEditStrand = false">
                            <div class="bg-red-700 px-6 py-4 flex justify-between items-center text-white">
                                <h3 class="font-bold text-lg">Edit Learning Strand</h3>
                                <button type="button" @click="showEditStrand = false" class="hover:text-gray-200 text-2xl font-bold">&times;</button>
                            </div>

                            <form action="{{ route('admin.curriculum.strands.update', $strand->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="p-6 space-y-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Title</label>
                                        <input type="text" name="name" value="{{ $strand->name }}" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Content Title</label>
                                        <input type="text" name="content_title" value="{{ $strand->content_title }}" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                                    </div>
                                    
                                    {{-- Dynamic Descriptions Input (Edit) --}}
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Content Descriptions (Bulleted List)</label>
                                        <div class="space-y-2">
                                            <template x-for="(desc, index) in descriptions" :key="index">
                                                <div class="flex items-start gap-2">
                                                    <div class="pt-2 text-gray-400">•</div>
                                                    <textarea x-model="descriptions[index]" name="content_description[]" rows="2" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none resize-y" placeholder="Enter description..."></textarea>
                                                    <button type="button" @click="descriptions.splice(index, 1)" x-show="descriptions.length > 1" class="text-red-500 hover:bg-red-50 p-2 rounded-lg mt-1 transition-colors" title="Remove bullet">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                        <button type="button" @click="descriptions.push('')" class="mt-2 text-xs font-bold uppercase text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1">
                                            + Add Bullet
                                        </button>
                                    </div>
                                </div>

                                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 items-center border-t border-gray-100">
                                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-colors text-sm">Save Changes</button>
                                    <button type="button" @click="showEditStrand = false" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                    <p class="text-gray-500 text-sm italic">No learning strands created yet.</p>
                </div>
            @endforelse
        </div>

        {{-- MODAL: Add New Learning Strand --}}
        <div x-show="showModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity" style="display: none;">
            <div class="bg-white rounded-xl w-full max-w-lg shadow-2xl overflow-hidden" @click.away="showModal = false">
                <div class="bg-red-700 px-6 py-4 flex justify-between items-center text-white">
                    <h3 class="font-bold text-lg">Create New Learning Strand</h3>
                    <button type="button" @click="showModal = false" class="hover:text-gray-200 text-2xl font-bold">&times;</button>
                </div>

                <form action="{{ route('admin.curriculum.strands.store') }}" method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Title <span class="text-gray-400 font-normal text-xs">(e.g., LEARNING STRAND 1)</span></label>
                            <input type="text" name="name" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Content Title <span class="text-gray-400 font-normal text-xs">(e.g., COMMUNICATION SKILLS)</span></label>
                            <input type="text" name="content_title" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                        </div>
                        
                        {{-- Dynamic Descriptions Input --}}
                        <div x-data="{ descriptions: [''] }">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Content Descriptions (Bulleted List)</label>
                            <div class="space-y-2">
                                <template x-for="(desc, index) in descriptions" :key="index">
                                    <div class="flex items-start gap-2">
                                        <div class="pt-2 text-gray-400">•</div>
                                        <textarea x-model="descriptions[index]" name="content_description[]" rows="2" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none resize-y" placeholder="Enter description..."></textarea>
                                        <button type="button" @click="descriptions.splice(index, 1)" x-show="descriptions.length > 1" class="text-red-500 hover:bg-red-50 p-2 rounded-lg mt-1 transition-colors" title="Remove bullet">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                            <button type="button" @click="descriptions.push('')" class="mt-2 text-xs font-bold uppercase text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1">
                                + Add Bullet
                            </button>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 items-center border-t border-gray-100">
                        <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-colors text-sm">Save Strand</button>
                        <button type="button" @click="showModal = false" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL: Add New File --}}
        <div x-show="showFileModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity" style="display: none;">
            <div class="bg-white rounded-xl w-full max-w-md shadow-2xl overflow-hidden" @click.away="showFileModal = false">
                <div class="bg-red-700 px-6 py-4 flex justify-between items-center text-white">
                    <h3 class="font-bold text-lg">Upload PDF Material</h3>
                    <button type="button" @click="showFileModal = false" class="hover:text-gray-200 text-2xl font-bold">&times;</button>
                </div>

                <form action="{{ route('admin.curriculum.materials.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="learning_strand_id" x-bind:value="activeStrandId">
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">PDF Title</label>
                            <input type="text" name="title" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Select File (.pdf)</label>
                            <input type="file" name="pdf_file" accept=".pdf" class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white" required>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 items-center border-t border-gray-100">
                        <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-colors text-sm">Upload</button>
                        <button type="button" @click="showFileModal = false" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- Section 3: Curriculum Guides --}}
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200" x-data="{ showEditModal: false, editId: '', editTitle: '', editLink: '', editAction: '' }">
        <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
            <h3 class="text-lg font-bold text-gray-800">Curriculum Guides</h3>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- Add New Guide Form --}}
            <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 shadow-sm h-fit">
                <h4 class="font-bold text-gray-800 text-sm mb-4 border-b border-gray-200 pb-2">Add New Guide</h4>
                <form action="{{ route('admin.curriculum.guides.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Track/Guide Title</label>
                        <input type="text" name="title" required placeholder="e.g. Academic Track" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">External Link (URL)</label>
                        <input type="url" name="link" required placeholder="https://..." class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none bg-white">
                    </div>
                    <button type="submit" class="w-full bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 rounded-lg shadow-sm transition-colors text-sm">
                        Save Guide
                    </button>
                </form>
            </div>

            {{-- Table of Guides --}}
            <div class="lg:col-span-2 bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-100 text-gray-600 uppercase font-bold text-xs border-b border-gray-200">
                            <tr>
                                <th class="px-5 py-4 border-b">Guide Title</th>
                                <th class="px-5 py-4 border-b">Redirect Link</th>
                                <th class="px-5 py-4 border-b text-right w-28">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @forelse($guides ?? [] as $guide)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-4 font-bold">{{ $guide->title }}</td>
                                    <td class="px-5 py-4 text-blue-600 truncate max-w-[200px]">
                                        <a href="{{ $guide->link }}" target="_blank" class="hover:underline flex items-center hover:text-blue-800 transition-colors">
                                            <svg class="w-3 h-3 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                            <span class="truncate">{{ $guide->link }}</span>
                                        </a>
                                    </td>
                                    <td class="px-5 py-4 flex justify-end gap-3 items-center">
                                        <button type="button" @click="showEditModal = true; editId = '{{ $guide->id }}'; editTitle = '{{ addslashes($guide->title) }}'; editLink = '{{ addslashes($guide->link) }}'; editAction = '/admin/curriculum/guides/{{ $guide->id }}'" 
                                                class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase hover:underline" title="Edit">
                                            Edit
                                        </button>
                                        
                                        <button type="button" @click="$dispatch('open-delete-modal', { action: '{{ route('admin.curriculum.guides.destroy', $guide->id) }}', title: 'Are you sure you want to delete this Guide?' })" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline" title="Delete">
                                            Delete
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
        <div x-show="showEditModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity" style="display: none;">
            <div class="bg-white rounded-xl w-full max-w-md shadow-2xl overflow-hidden" @click.away="showEditModal = false">
                <div class="bg-red-700 px-6 py-4 flex justify-between items-center text-white">
                    <h3 class="font-bold text-lg">Edit Curriculum Guide</h3>
                    <button type="button" @click="showEditModal = false" class="hover:text-gray-200 text-2xl font-bold">&times;</button>
                </div>

                <form :action="editAction" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Track/Guide Title</label>
                            <input type="text" name="title" x-model="editTitle" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">External Link (URL)</label>
                            <input type="url" name="link" x-model="editLink" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" required>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 items-center border-t border-gray-100">
                        <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-colors text-sm">Save Changes</button>
                        <button type="button" @click="showEditModal = false" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    {{-- GLOBAL MODAL: Delete Confirmation --}}
    <div x-data="{ showDeleteModal: false, deleteAction: '', deleteTitle: '' }" 
         @open-delete-modal.window="showDeleteModal = true; deleteAction = $event.detail.action; deleteTitle = $event.detail.title"
         x-show="showDeleteModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity" style="display: none;" x-cloak>
        
        <div class="bg-white rounded-2xl p-8 shadow-2xl z-50 w-full max-w-sm transform transition-all relative" @click.away="showDeleteModal = false">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            
            <h3 class="text-xl font-bold text-gray-800 mb-2 text-center">Confirm Deletion</h3>
            <p class="text-gray-500 text-sm mb-6 text-center" x-text="deleteTitle"></p>
            
            <div class="flex space-x-3 border-t border-gray-100 pt-4">
                <button type="button" @click="showDeleteModal = false" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                
                <form :action="deleteAction" method="POST" class="flex-1 m-0 p-0 flex">
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

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush