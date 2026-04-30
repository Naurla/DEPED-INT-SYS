@extends('layouts.admin')

@section('page_title', 'Manage Division Office Structure')

@section('content')
<style>
    [x-cloak] { display: none !important; }
    
    /* Subtle scrollbar for the delete modal target box */
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
    showDeleteModal: false, 
    deleteAction: '', 
    deleteTitle: '',
    successModal: {{ session('success') ? 'true' : 'false' }} 
}">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Manage Division Office Organization Structure</h2>
            <p class="text-gray-500 text-sm mt-1">Add Title, Content, Banner Photo, and attach PDFs.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        
        {{-- Add New Entry Form --}}
        <div class="xl:col-span-5 bg-white p-6 rounded-lg shadow-sm border border-gray-200 h-fit">
            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3 mb-5">Add New Entry</h3>

            <form action="{{ route('admin.division_structures.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                
                <input type="hidden" name="type" value="Division">

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Title <span class="text-red-600">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g., Office of the Schools Division Superintendent" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm">
                    @error('name') 
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> 
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Content Text</label>
                    <p class="text-xs text-gray-500 mb-2">Type your content here. Highlight text and use the "Link" tool to attach your uploaded PDF URLs.</p>
                    <textarea name="descriptions[]" class="rich-text-editor w-full">{{ old('descriptions.0') }}</textarea>
                </div>

                <div class="border-t border-gray-100 pt-4 space-y-4">
                    
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <label class="block text-sm font-bold text-gray-800 mb-2">Banner Photo</label>
                        <input type="file" name="main_photo" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-md file:border-0 file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 border border-gray-200 rounded-lg cursor-pointer bg-white">
                        @error('main_photo') 
                            <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> 
                        @enderror
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <label class="block text-sm font-bold text-gray-800 mb-1">Upload PDF Document</label>
                        <p class="text-[11px] text-gray-500 mb-3 leading-tight">Upload PDFs here. Once saved, copy the link from the list on the right, and paste it into the Content Text above as a link.</p>
                        <input type="file" name="pdf_documents[]" multiple accept=".pdf" class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-md file:border-0 file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 border border-gray-200 rounded-lg cursor-pointer bg-white">
                        @error('pdf_documents') 
                            <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> 
                        @enderror
                        @error('pdf_documents.*') 
                            <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> 
                        @enderror
                    </div>

                </div>

                <button type="submit" class="w-full mt-3 bg-red-700 text-white py-3 px-4 rounded-lg hover:bg-red-800 transition-colors text-sm font-bold shadow-sm tracking-wide">
                    Save Entry
                </button>
            </form>
        </div>

        {{-- Saved Entries List --}}
        <div class="xl:col-span-7 bg-white p-6 rounded-lg shadow-sm border border-gray-200 h-fit">
            <h3 class="text-lg font-bold text-gray-800 mb-5 border-b border-gray-100 pb-3">Saved Entries & PDF Links</h3>
            
            <div class="space-y-4">
                @forelse($structures as $struct)
                    <div class="border border-gray-200 rounded-lg p-5 bg-gray-50 relative group hover:border-red-300 transition-all shadow-sm">
                        
                        <div class="flex justify-between items-start pr-4 mb-3">
                            <h4 class="font-bold text-gray-900 text-base leading-tight">{{ $struct->name }}</h4>
                            <div class="flex gap-3">
                                <a href="{{ route('admin.division_structures.edit', $struct) }}" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase hover:underline" title="Edit Entry">Edit</a>
                                
                                <button type="button" @click="showDeleteModal = true; deleteAction = '{{ route('admin.division_structures.destroy', $struct) }}'; deleteTitle = '{{ addslashes($struct->name) }}'" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline" title="Delete Entry">
                                    Delete
                                </button>
                            </div>
                        </div>
                        
                        @if($struct->main_photo)
                            <p class="text-[10px] text-green-600 font-bold mb-3 flex items-center gap-1 uppercase">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Banner Photo Uploaded
                            </p>
                        @endif
                        
                        <div class="mt-4 border-t border-gray-200 pt-4">
                            <p class="text-gray-700 font-bold text-xs flex items-center gap-1.5 mb-2 uppercase">
                                <svg class="w-3.5 h-3.5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>
                                PDF Links:
                            </p>
                            @if($struct->pdf_documents && count($struct->pdf_documents) > 0)
                                <ul class="list-none space-y-2 mt-2 text-sm text-gray-700">
                                    @foreach($struct->pdf_documents as $index => $pdf)
                                        <li class="flex items-center justify-between bg-white px-3 py-2 rounded-lg border border-gray-200 shadow-sm">
                                            <a href="{{ asset('storage/' . $pdf['path']) }}" target="_blank" class="hover:underline font-semibold text-blue-600 truncate mr-2">{{ $pdf['original_name'] }}</a>
                                            
                                            <button type="button" @click="showDeleteModal = true; deleteAction = '{{ route('admin.division_structures.destroy_pdf', [$struct->id, $index]) }}'; deleteTitle = 'the PDF: {{ addslashes($pdf['original_name']) }}'" class="text-xs font-bold uppercase text-red-600 hover:text-red-800 hover:underline">
                                                Remove
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-xs text-gray-500 italic block mt-1">No PDFs attached.</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm italic text-center py-10 bg-gray-50 rounded-lg border border-gray-100">No entries created yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- MODERNIZED GLOBAL MODAL: Delete Confirmation --}}
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="showDeleteModal = false">
            
            <!-- Soft Double-Ring Icon -->
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            
            <!-- Text Content -->
            <div class="text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Delete Entry?</h3>
                <p class="text-gray-500 text-sm mb-5">
                    You are about to permanently delete this entry:
                </p>
                
                <!-- Target Highlight (Scrollable, no background, bold dark text) -->
                <div class="mb-8 max-h-32 overflow-y-auto custom-scrollbar">
                    <span class="font-bold text-gray-900 break-all text-lg block" x-text="deleteTitle"></span>
                </div>
                
                <p class="text-gray-400 text-sm italic mb-8">
                    This action cannot be undone.
                </p>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex gap-3">
                <button type="button" @click="showDeleteModal = false" class="flex-1 inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:ring-offset-1 transition-all">
                    Cancel
                </button>
                
                <form :action="deleteAction" method="POST" class="flex-1 m-0 p-0">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-all">
                        Yes, Delete it
                    </button>
                </form>
            </div>

        </div>
    </div>

    {{-- MODERNIZED GLOBAL MODAL: Success Message (Red Theme) --}}
    <div x-show="successModal" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="successModal = false">
            
            <!-- Soft Double-Ring Icon (Red Checkmark) -->
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            
            <!-- Text Content -->
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
            
            <!-- Action Button -->
            <div class="flex">
                <button type="button" @click="successModal = false" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-700 px-6 py-3 text-base font-bold text-white shadow-sm hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-all">
                    Continue
                </button>
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