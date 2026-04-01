@extends('layouts.app')

@section('content')

{{-- Breadcrumb matching the reference layout padding (md:px-20) --}}
<div class="bg-gray-100 border-b border-gray-200 w-full overflow-hidden">
    <div class="container mx-auto px-4 md:px-20 max-w-10xl py-3 text-xs sm:text-sm text-gray-600 overflow-x-auto whitespace-nowrap hide-scroll">
        <a href="/" class="hover:text-[#003366] transition">Home</a>
        <span class="mx-2">></span>
        <span>K to 12</span>
        <span class="mx-2">></span>
        <a href="{{ route('learning_materials.index') }}" class="hover:text-[#003366] transition">Learning Materials</a>
        <span class="mx-2">></span>
        <span class="text-gray-900 font-bold">{{ Str::limit($material->title, 40) }}</span>
    </div>
</div>

{{-- Main Container (using the exact same md:px-20 layout padding) --}}
<div class="container mx-auto px-4 md:px-20 max-w-10xl py-8 md:py-12 w-full overflow-hidden min-h-screen">
    
    {{-- Header Section (Aligned naturally to the padding bounds) --}}
    <div class="mb-8 md:mb-10 text-left w-full break-words">
        
        <a href="{{ route('learning_materials.index') }}" class="text-[#a52a2a] hover:text-red-800 font-bold text-sm inline-flex items-center mb-6 uppercase tracking-wider transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to List
        </a>
        
        <h1 class="text-2xl md:text-3xl font-sans font-bold text-gray-900 tracking-wide uppercase mb-4">
            {{ $material->title }}
        </h1>

        @if($material->description)
            <div class="text-[15px] text-gray-700 leading-relaxed mb-6 max-w-4xl">
                {{ strip_tags($material->description) }}
            </div>
        @endif

        <div class="flex flex-wrap items-center text-gray-500 font-semibold gap-x-6 gap-y-3 mb-8">
            <span class="bg-gray-100 border border-gray-200 text-gray-800 px-3 py-1 rounded-sm uppercase tracking-widest text-[11px] whitespace-nowrap">
                K-12 LEARNING MATERIAL
            </span>
            <span class="whitespace-nowrap text-[12px] uppercase tracking-widest">
                Uploaded: {{ $material->created_at->format('M d, Y') }}
            </span>
            
            {{-- Download Link --}}
            <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline flex items-center whitespace-nowrap text-[13px] uppercase tracking-widest transition-colors font-bold" download>
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Download File ({{ strtoupper($material->file_type) }})
            </a>
        </div>
    </div>

    {{-- File Preview Section --}}
    <div class="w-full bg-gray-100 rounded-lg p-2 shadow-inner mb-6 border border-gray-300 h-[70vh] min-h-[600px]">
        @if($material->file_type == 'pdf')
            <iframe 
                src="{{ asset('storage/' . $material->file_path) }}#toolbar=0" 
                class="w-full h-full rounded bg-white" 
                title="{{ $material->title }}">
            </iframe>
        @elseif(in_array($material->file_type, ['ppt', 'pptx', 'doc', 'docx']))
            @php
                $googleViewerUrl = 'https://docs.google.com/viewer?url=' . urlencode(asset('storage/' . $material->file_path)) . '&embedded=true';
            @endphp
            <iframe 
                src="{{ $googleViewerUrl }}" 
                class="w-full h-full rounded bg-white" 
                title="{{ $material->title }}">
            </iframe>
        @else
            <div class="flex flex-col items-center justify-center h-full text-gray-500 bg-white rounded">
                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <p class="font-bold text-[15px]">Preview not available for {{ strtoupper($material->file_type) }}</p>
                <p class="text-sm mt-1">Please use the download button above to view this file.</p>
            </div>
        @endif
    </div>

    <p class="text-gray-400 text-[11px] text-center mb-8 font-bold uppercase tracking-widest">
        Official DepEd Zamboanga City Learning Material
    </p>

</div>

<style>
    /* =========================================================
       HIDE SCROLLBARS (BUT KEEP CONTENT SCROLLABLE) for breadcrumb
       ========================================================= */
    .hide-scroll::-webkit-scrollbar {
        display: none; /* For Chrome, Safari, and Opera */
    }
    
    .hide-scroll {
        -ms-overflow-style: none;  /* For Internet Explorer and Edge */
        scrollbar-width: none;  /* For Firefox */
    }
</style>
@endsection