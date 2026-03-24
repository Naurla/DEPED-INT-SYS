@extends('layouts.admin')

@section('page_title', 'Manage Division Office Structure')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap');
    .font-cinzel { font-family: 'Cinzel', serif; }
</style>

<div class="space-y-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 font-cinzel">Manage Division Office Organization Structure</h2>
            <p class="text-gray-500 text-sm mt-1 font-sans">Add Title, Content, Banner Photo, and attach PDFs.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm">
            <p class="text-sm text-green-700 font-bold whitespace-pre-wrap">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        
        <div class="xl:col-span-5 bg-white p-6 rounded-lg shadow-sm border border-gray-200 h-max">
            <h3 class="text-lg font-bold text-[#003366] font-cinzel border-b pb-3 mb-5">Add New Entry</h3>

            <form action="{{ route('admin.division_structures.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5 font-sans">
                @csrf
                
                <input type="hidden" name="type" value="Division">

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Title</label>
                    <input type="text" name="name" required placeholder="e.g., Office of the Schools Division Superintendent" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#a52a2a] focus:ring-1 focus:ring-[#a52a2a] text-sm">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Content Text</label>
                    <p class="text-xs text-gray-500 mb-2">Type your content here. Highlight text and use the "Link" tool to attach your uploaded PDF URLs.</p>
                    <textarea name="descriptions[]" class="rich-text-editor w-full"></textarea>
                </div>

                <div class="border-t pt-4 space-y-4">
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Banner Photo</label>
                        <input type="file" name="main_photo" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#003366] hover:file:bg-blue-100 border border-gray-300 rounded-lg p-1">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Upload PDF Document</label>
                        <p class="text-[11px] text-gray-500 mb-2 leading-tight">Upload PDFs here. Once saved, copy the blue link from the table on the right, and paste it into the Content Text above as a link.</p>
                        <input type="file" name="pdf_documents[]" multiple accept=".pdf" class="w-full text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-red-50 file:text-[#a52a2a] hover:file:bg-red-100 border border-gray-300 rounded-lg p-1">
                    </div>

                </div>

                <button type="submit" class="w-full mt-3 bg-[#a52a2a] text-white py-2.5 px-4 rounded-lg hover:bg-[#801a1a] transition text-sm font-bold shadow-md tracking-wide">
                    SAVE ENTRY
                </button>
            </form>
        </div>

        <div class="xl:col-span-7 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-lg font-bold text-[#003366] mb-5 border-b pb-3 font-cinzel">Saved Entries & PDF Links</h3>
            
            <div class="space-y-4">
                @forelse($structures as $struct)
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 relative group hover:border-gray-300 transition-all">
                        
                        <h4 class="font-bold text-gray-900 text-base mb-1.5 pr-16">{{ $struct->name }}</h4>
                        
                        @if($struct->main_photo)
                            <p class="text-[10px] text-green-600 font-bold mb-3 flex items-center gap-1 uppercase">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2-2v12a2 2 0 002 2z"></path></svg>
                                Banner Photo Uploaded
                            </p>
                        @endif
                        
                        <div class="mt-3 border-t border-gray-200 pt-3">
                            <p class="text-gray-700 font-bold text-xs flex items-center gap-1.5 mb-1.5 uppercase">
                                <svg class="w-3.5 h-3.5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>
                                PDF Links:
                            </p>
                            @if($struct->pdf_documents && count($struct->pdf_documents) > 0)
                                <ul class="list-disc pl-5 mt-1 text-[13px] text-blue-600 space-y-1">
                                    @foreach($struct->pdf_documents as $index => $pdf)
                                        <li class="flex items-center flex-wrap gap-2">
                                            <a href="{{ asset('storage/' . $pdf['path']) }}" target="_blank" class="hover:underline font-medium break-all">{{ $pdf['original_name'] }}</a>
                                            <form action="{{ route('admin.division_structures.destroy_pdf', [$struct->id, $index]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this PDF?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 text-[10px] hover:underline font-bold bg-transparent border-none p-0 cursor-pointer">(Delete)</button>
                                            </form>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-[11px] text-gray-400 italic pl-1">No PDFs attached.</span>
                            @endif
                        </div>

                        <div class="absolute top-3 right-3 flex space-x-1.5">
                            <a href="{{ route('admin.division_structures.edit', $struct) }}" class="text-blue-500 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 p-1.5 rounded transition" title="Edit Entry">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <form action="{{ route('admin.division_structures.destroy', $struct) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this entirely?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-800 bg-red-50 hover:bg-red-100 p-1.5 rounded transition" title="Delete Entry">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                        
                    </div>
                @empty
                    <p class="text-gray-500 text-sm italic text-center py-10">No entries created yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        tinymce.init({
            selector: '.rich-text-editor',
            height: 280,
            menubar: false,
            plugins: 'advlist autolink lists link preview',
            toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist | link | removeformat',
            content_style: 'body { font-family:Inter,Helvetica,Arial,sans-serif; font-size:14px; color:#374151; }'
        });
    });
</script>
@endpush