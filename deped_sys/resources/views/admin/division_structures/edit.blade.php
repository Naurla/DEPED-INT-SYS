@extends('layouts.admin')

@section('page_title', 'Edit Division Office Structure')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap');
    .font-cinzel { font-family: 'Cinzel', serif; }
</style>

<div class="space-y-6">
    
    <div class="flex justify-between items-center mb-6 max-w-3xl mx-auto">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 font-cinzel">Edit Entry: {{ $divisionStructure->name }}</h2>
            <p class="text-gray-500 text-sm mt-1 font-sans">Update your Title, Content, Banner, or add auto-linking PDFs.</p>
        </div>
        <a href="{{ route('admin.division_structures.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-600 transition font-bold shadow-sm">
            &larr; Back to List
        </a>
    </div>

    <div class="bg-white p-6 md:p-8 rounded-lg shadow-sm border border-gray-200 w-full max-w-3xl mx-auto">
        <form action="{{ route('admin.division_structures.update', $divisionStructure) }}" method="POST" enctype="multipart/form-data" class="space-y-5 font-sans">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Title</label>
                <input type="text" name="name" value="{{ $divisionStructure->name }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#a52a2a] focus:ring-1 focus:ring-[#a52a2a] text-sm">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Content Text</label>
                <p class="text-[11px] text-gray-500 mb-2">Modify your content below.</p>
                <textarea name="descriptions[]" class="rich-text-editor w-full">{{ is_array($divisionStructure->descriptions) && count($divisionStructure->descriptions) > 0 ? $divisionStructure->descriptions[0] : '' }}</textarea>
            </div>

            <div class="border-t pt-4 space-y-4">
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Replace Banner Photo (Optional)</label>
                    @if($divisionStructure->main_photo)
                        <p class="text-[11px] text-green-600 font-bold mb-2">Current photo exists. Uploading a new one will replace it.</p>
                    @endif
                    <input type="file" name="main_photo" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#003366] hover:file:bg-blue-100 border border-gray-300 rounded-lg p-1">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Add Additional PDFs (Optional)</label>
                    <p class="text-[11px] text-blue-600 font-bold mb-2 leading-tight">Upload more PDFs here. They will automatically become links. To delete old PDFs, go back to the list page and use the (Delete) button.</p>
                    <input type="file" name="pdf_documents[]" multiple accept=".pdf" class="w-full text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-red-50 file:text-[#a52a2a] hover:file:bg-red-100 border border-gray-300 rounded-lg p-1">
                </div>

            </div>

            <button type="submit" class="w-full mt-4 bg-[#a52a2a] text-white py-3 px-4 rounded-lg hover:bg-[#801a1a] transition text-sm font-bold shadow-md tracking-wide">
                SAVE CHANGES
            </button>
        </form>
    </div>
</div>
@endsection @push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        tinymce.init({
            selector: '.rich-text-editor',
            height: 300,
            menubar: false,
            plugins: 'lists preview',
            toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist | removeformat',
            content_style: 'body { font-family:Inter,Helvetica,Arial,sans-serif; font-size:14px; color:#374151; }'
        });
    });
</script>
@endpush