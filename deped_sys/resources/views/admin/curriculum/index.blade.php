@extends('layouts.admin')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap');
    .font-cinzel { font-family: 'Cinzel', serif; }
</style>

<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-6 font-cinzel text-gray-800">Manage K-12 Curriculum Content</h2>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    {{-- Section 1: Main Banner --}}
    <div class="bg-white p-6 rounded shadow-sm border border-gray-200 mb-8" x-data="imageUploader('{{ $pageData->banner_image_path ? asset('storage/' . $pageData->banner_image_path) : '' }}')">
        
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold font-cinzel">Main Page Banner</h3>
            
            <template x-if="!imageUrl">
                <button type="button" @click="$refs.fileInput.click()" class="bg-blue-800 text-white px-4 py-2 rounded hover:bg-blue-900 font-bold shadow flex items-center gap-2 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add Page Banner
                </button>
            </template>
        </div>

        <form action="{{ route('admin.curriculum.update_page') }}" method="POST" enctype="multipart/form-data" x-ref="bannerForm">
            @csrf
            
            <input type="file" name="banner_image" x-ref="fileInput" @change="fileChosen" class="hidden" accept="image/png, image/jpeg, image/jpg">
            <input type="hidden" name="remove_image" x-model="removeFlag">

            <template x-if="imageUrl">
                <div class="relative w-full rounded-lg bg-gray-50 flex items-center justify-center overflow-hidden group shadow-sm"
                     @mouseenter="hovering = true" 
                     @mouseleave="hovering = false">
                    
                    <div class="w-full relative">
                        {{-- FIX: Removed object-cover and max-h so the whole picture shows naturally --}}
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
        </form>
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
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 flex flex-col md:flex-row overflow-hidden relative">
                    
                    {{-- Delete Strand Button --}}
                    <form action="{{ route('admin.curriculum.strands.destroy', $strand->id) }}" method="POST" onsubmit="return confirm('Delete this strand and ALL its PDFs?');" class="absolute top-4 right-4 z-10">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-1.5 rounded transition" title="Delete Strand">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>

                    {{-- Left Side: Text Content --}}
                    <div class="p-6 md:w-2/3 border-b md:border-b-0 md:border-r border-gray-200">
                        <div class="mb-4">
                            <span class="bg-green-50 text-green-700 text-xs font-bold px-2 py-1 rounded inline-block mb-1 tracking-wider font-sans">TITLE</span>
                            <h4 class="text-xl font-bold text-gray-900 font-cinzel">{{ $strand->name }}</h4>
                        </div>
                        
                        <div class="mb-4">
                            <span class="bg-green-50 text-green-700 text-xs font-bold px-2 py-1 rounded inline-block mb-1 tracking-wider font-sans">CONTENT TITLE</span>
                            <h5 class="text-lg font-bold text-gray-800 font-cinzel">{{ $strand->content_title ?: 'No Content Title' }}</h5>
                        </div>
                        
                        <div>
                            <span class="bg-green-50 text-green-700 text-xs font-bold px-2 py-1 rounded inline-block mb-1 tracking-wider font-sans">CONTENT DESCRIPTIONS</span>
                            <p class="text-gray-600 mt-1 leading-relaxed font-sans">
                                {{ $strand->content_description ?: 'No description provided.' }}
                            </p>
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
                                        
                                        <form action="{{ route('admin.curriculum.materials.destroy', $material->id) }}" method="POST" onsubmit="return confirm('Delete this PDF?');" class="flex-shrink-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition p-1" title="Remove PDF">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </form>
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
                </div>
            @empty
                <p class="text-center text-gray-500 py-8 font-sans">No learning strands created yet.</p>
            @endforelse
        </div>

        {{-- MODAL: Add New Learning Strand --}}
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
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

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Content Description</label>
                            <textarea name="content_description" rows="4" class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-[#003366] focus:border-[#003366] p-2"></textarea>
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
        <div x-show="showFileModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
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
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('imageUploader', (initialImage) => ({
            imageUrl: initialImage,
            removeFlag: 0,
            hovering: false,
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