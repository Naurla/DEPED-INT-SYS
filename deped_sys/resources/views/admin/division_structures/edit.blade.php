@extends('layouts.admin')

@section('page_title', 'Edit Division Office Structure')

@section('content')
<div class="space-y-6">
    
    {{-- Full width header, aligned properly --}}
    <div class="flex justify-between items-center mb-6 w-full">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Edit Entry: {{ $divisionStructure->name }}</h2>
            <p class="text-gray-500 text-sm mt-1">Update your Title, Content, Banner, or add auto-linking PDFs.</p>
        </div>
        <a href="{{ route('admin.division_structures.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200 transition-colors font-bold shadow-sm border border-gray-300 whitespace-nowrap">
            &larr; Back to List
        </a>
    </div>

    {{-- Form container constrained to max-w-3xl and centered --}}
    <div class="bg-white p-6 md:p-8 rounded-lg shadow-sm border border-gray-200 w-full max-w-3xl mx-auto">
        <form action="{{ route('admin.division_structures.update', $divisionStructure) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Title</label>
                <input type="text" name="name" value="{{ $divisionStructure->name }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Content Text</label>
                <p class="text-[11px] text-gray-500 mb-2">Modify your content below.</p>
                <textarea name="descriptions[]" class="rich-text-editor w-full">{{ is_array($divisionStructure->descriptions) && count($divisionStructure->descriptions) > 0 ? $divisionStructure->descriptions[0] : '' }}</textarea>
            </div>

            <div class="border-t border-gray-100 pt-4 space-y-4">
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Replace Banner Photo (Optional)</label>
                    @if($divisionStructure->main_photo)
                        <p class="text-[11px] text-green-600 font-bold mb-2">Current photo exists. Uploading a new one will replace it.</p>
                    @endif
                    <input type="file" name="main_photo" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 border border-gray-300 rounded-lg p-1.5 cursor-pointer">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Add Additional PDFs (Optional)</label>
                    <p class="text-[11px] text-blue-600 font-bold mb-2 leading-tight">Upload more PDFs here. They will automatically become links. To delete old PDFs, go back to the list page and use the (Remove) button.</p>
                    <input type="file" name="pdf_documents[]" multiple accept=".pdf" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 border border-gray-300 rounded-lg p-1.5 cursor-pointer">
                </div>

            </div>

            <div class="mt-8 pt-4 border-t border-gray-100">
                <button type="submit" class="w-full bg-red-700 text-white py-3 px-4 rounded-lg hover:bg-red-800 transition-colors text-sm font-bold shadow-sm tracking-wide">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 

@push('scripts')
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