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

    {{-- Section 1: Main Banner and Text --}}
    <div class="bg-white p-6 rounded shadow mb-8">
        <h3 class="text-xl font-bold mb-4">Main Page Content</h3>
        <form action="{{ route('admin.curriculum.update_page') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Banner Image</label>
                @if($pageData->banner_image_path)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $pageData->banner_image_path) }}" alt="Current Banner" class="h-32 object-cover rounded">
                    </div>
                @endif
                <input type="file" name="banner_image" class="border p-2 w-full" accept="image/png, image/jpeg">
                <small class="text-gray-500">Leave blank to keep the current image.</small>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Description</label>
                <textarea name="description" rows="5" class="border p-2 w-full">{{ $pageData->description }}</textarea>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update Main Content</button>
        </form>
    </div>

    {{-- Section 2: Learning Strands and Materials --}}
    <div class="bg-white p-6 rounded shadow">
        <h3 class="text-xl font-bold mb-4">Learning Strands & PDF Materials</h3>

        {{-- Add New Strand Form --}}
        <form action="{{ route('admin.curriculum.strands.store') }}" method="POST" class="mb-6 flex gap-2">
            @csrf
            <input type="text" name="name" placeholder="New Strand Name (e.g., STEM)" class="border p-2 flex-grow rounded" required>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Add Strand</button>
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
                        <input type="text" name="name" value="{{ $strand->name }}" class="border p-1 w-full rounded" required>
                        <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded text-sm">Save</button>
                    </form>

                    {{-- Delete Strand Form --}}
                    <form action="{{ route('admin.curriculum.strands.destroy', $strand->id) }}" method="POST" onsubmit="return confirm('Delete this strand and ALL its PDFs?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">Delete Strand</button>
                    </form>
                </div>

                {{-- List of PDFs for this strand --}}
                <div class="ml-4 pl-4 border-l-2 border-gray-300">
                    <h5 class="font-bold text-gray-600 text-sm mb-2">Attached PDFs</h5>
                    <ul class="mb-4">
                        @foreach($strand->materials as $material)
                            <li class="flex justify-between items-center bg-white p-2 border mb-1 rounded text-sm">
                                <span>📄 <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="text-blue-600 hover:underline">{{ $material->title }}</a></span>
                                
                                {{-- Delete PDF --}}
                                <form action="{{ route('admin.curriculum.materials.destroy', $material->id) }}" method="POST" onsubmit="return confirm('Delete this PDF?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">✖ Remove</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Add new PDF to this strand --}}
                    <form action="{{ route('admin.curriculum.materials.store') }}" method="POST" enctype="multipart/form-data" class="flex gap-2 items-center bg-white p-2 border rounded">
                        @csrf
                        <input type="hidden" name="learning_strand_id" value="{{ $strand->id }}">
                        <input type="text" name="title" placeholder="PDF Title" class="border p-1 flex-grow text-sm rounded" required>
                        <input type="file" name="pdf_file" accept=".pdf" class="text-sm" required>
                        <button type="submit" class="bg-gray-800 text-white px-3 py-1 rounded text-sm">Upload PDF</button>
                    </form>
                </div>
            </div>
        @endforeach

    </div>
</div>
@endsection