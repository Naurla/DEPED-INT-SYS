@extends('layouts.admin')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap');
    /* Applying requested font family to headers */
    .admin-header { font-family: 'Cinzel', serif; }
</style>

<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-6 admin-header">Manage K-12 Curriculum Content</h2>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    {{-- Section 1: Main Banner --}}
    <div class="bg-white p-6 rounded shadow mb-8" x-data="imageUploader('{{ $pageData->banner_image_path ? asset('storage/' . $pageData->banner_image_path) : '' }}')">
        
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Main Page Banner</h3>
            
            <template x-if="!imageUrl">
                <button type="button" @click="$refs.fileInput.click()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 font-bold shadow-sm flex items-center gap-2 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add Page Banner
                </button>
            </template>
        </div>

        <form action="{{ route('admin.curriculum.update_page') }}" method="POST" enctype="multipart/form-data" x-ref="bannerForm">
            @csrf
            
            {{-- Hidden inputs for file and deletion flag --}}
            <input type="file" name="banner_image" x-ref="fileInput" @change="fileChosen" class="hidden" accept="image/png, image/jpeg, image/jpg">
            <input type="hidden" name="remove_image" x-model="removeFlag">

            <template x-if="imageUrl">
                <div class="relative w-full rounded-lg bg-gray-50 flex items-center justify-center overflow-hidden group shadow-sm"
                     @mouseenter="hovering = true" 
                     @mouseleave="hovering = false">
                    
                    <div class="w-full relative">
                        <img :src="imageUrl" alt="Banner Preview" class="w-full h-auto block">
                        
                        <div x-show="hovering" 
                             x-transition.opacity.duration.200ms
                             class="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center gap-4">
                            
                            <button type="button" @click.stop="$refs.fileInput.click()" class="bg-white text-gray-900 px-6 py-2 rounded-md font-bold hover:bg-gray-200 shadow transition">
                                Replace
                            </button>
                            
                            <button type="button" @click.stop="removeImage" class="bg-red-500 text-white px-6 py-2 rounded-md font-bold hover:bg-red-600 shadow transition">
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
            </template>

        </form>
    </div>

    {{-- Section 2: Learning Strands and Materials --}}
    <div class="bg-white p-6 rounded shadow">
        <h3 class="text-xl font-bold mb-4">Learning Strands & PDF Materials</h3>

        {{-- Add New Strand Form --}}
        <form action="{{ route('admin.curriculum.strands.store') }}" method="POST" class="mb-6 flex gap-2">
            @csrf
            <input type="text" name="name" placeholder="New Strand Name (e.g., STEM)" class="border p-2 flex-grow rounded" required>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 font-bold shadow-sm">Add Strand</button>
        </form>

        <hr class="mb-6">

        {{-- Loop through existing Strands --}}
        @foreach($strands as $strand)
            <div class="mb-6 p-4 border rounded bg-gray-50">
                <div class="flex justify-between items-center mb-4">
                    
                    {{-- Edit Strand Name Form --}}
                    <form action="{{ route('admin.curriculum.strands.update', $strand->id) }}" method="POST" class="flex gap-2 w-1/2">
                        @csrf
                        @method('PUT')
                        <input type="text" name="name" value="{{ $strand->name }}" class="border p-1 w-full rounded focus:ring-2 focus:ring-blue-500" required>
                        <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded text-sm font-bold shadow-sm">Save</button>
                    </form>

                    {{-- Delete Strand Form --}}
                    <form action="{{ route('admin.curriculum.strands.destroy', $strand->id) }}" method="POST" onsubmit="return confirm('Delete this strand and ALL its PDFs?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600 font-bold shadow-sm">Delete Strand</button>
                    </form>
                </div>

                {{-- List of PDFs for this strand --}}
                <div class="ml-4 pl-4 border-l-2 border-gray-300">
                    <h5 class="font-bold text-gray-600 text-sm mb-2">Attached PDFs</h5>
                    <ul class="mb-4">
                        @foreach($strand->materials as $material)
                            <li class="flex justify-between items-center bg-white p-2 border mb-1 rounded text-sm shadow-sm">
                                <span>📄 <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="text-blue-600 hover:underline font-medium">{{ $material->title }}</a></span>
                                
                                {{-- Delete PDF --}}
                                <form action="{{ route('admin.curriculum.materials.destroy', $material->id) }}" method="POST" onsubmit="return confirm('Delete this PDF?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-bold">✖ Remove</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Add new PDF to this strand --}}
                    <form action="{{ route('admin.curriculum.materials.store') }}" method="POST" enctype="multipart/form-data" class="flex gap-2 items-center bg-white p-2 border rounded shadow-sm">
                        @csrf
                        <input type="hidden" name="learning_strand_id" value="{{ $strand->id }}">
                        <input type="text" name="title" placeholder="PDF Title" class="border p-1 flex-grow text-sm rounded focus:ring-2 focus:ring-blue-500" required>
                        <input type="file" name="pdf_file" accept=".pdf" class="text-sm border p-1 rounded" required>
                        <button type="submit" class="bg-gray-800 text-white px-3 py-1 rounded text-sm font-bold hover:bg-gray-900 transition">Upload PDF</button>
                    </form>
                </div>
            </div>
        @endforeach

    </div>
</div>

{{-- Alpine Component Script for Auto-Submit Image Logic --}}
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
                    // Automatically submit the form to the backend to save the new image
                    this.$refs.bannerForm.submit();
                }
            },
            
            removeImage() {
                this.removeFlag = 1; 
                // Wait for the DOM to update the hidden input, then auto-submit the form
                this.$nextTick(() => {
                    this.$refs.bannerForm.submit();
                });
            }
        }));
    });
</script>
@endsection