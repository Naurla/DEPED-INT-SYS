@extends('layouts.app')

@section('content')

{{-- Breadcrumb matching the reference layout padding (md:px-20) --}}
<div class="bg-gray-100 border-b border-gray-200 w-full overflow-hidden">
    <div class="container mx-auto px-4 md:px-20 max-w-10xl py-3 text-xs sm:text-sm text-gray-600 overflow-x-auto whitespace-nowrap hide-scroll">
        <a href="/" class="hover:text-[#003366] transition">Home</a>
        <span class="mx-2">></span>
        <a href="{{ route('procurement.index', $category) }}" class="hover:text-[#003366] transition">Procurement</a>
        <span class="mx-2">></span>
        <a href="{{ route('procurement.index', $category) }}" class="hover:text-[#003366] transition">{{ $type_name }}</a>
        <span class="mx-2">></span>
        <span class="text-gray-900 font-bold">{{ Str::limit($item->display_title, 40) }}</span>
    </div>
</div>

{{-- Main Container (using the exact same md:px-20 layout padding) --}}
<div class="container mx-auto px-4 md:px-20 max-w-10xl py-8 md:py-12 w-full overflow-hidden min-h-screen">
    
    {{-- Header Section (Aligned naturally to the padding bounds) --}}
    <div class="mb-8 md:mb-10 text-left w-full break-words">
        
        <a href="{{ route('procurement.index', $category) }}" class="text-[#a52a2a] hover:text-red-800 font-bold text-sm inline-flex items-center mb-6 uppercase tracking-wider transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to List
        </a>
        
        <h1 class="text-2xl md:text-3xl font-sans font-bold text-gray-900 tracking-wide uppercase mb-4">
            {{ $item->display_title }}
        </h1>

        @if($item->description)
            <div class="text-[15px] text-gray-700 leading-relaxed mb-6 max-w-4xl font-bold uppercase tracking-wide">
                {{ $item->description }}
            </div>
        @endif

        <div class="flex flex-wrap items-center text-gray-500 font-semibold gap-x-6 gap-y-3 mb-8">
            <span class="bg-gray-100 border border-gray-200 text-gray-800 px-3 py-1 rounded-sm uppercase tracking-widest text-[11px] whitespace-nowrap">
                {{ $type_name ?? 'Bid Opportunity' }}
            </span>
            <span class="whitespace-nowrap text-[12px] uppercase tracking-widest">
                Posted: {{ $item->date ? \Carbon\Carbon::parse($item->date)->format('M d, Y') : $item->created_at->format('M d, Y') }}
            </span>
            
            {{-- Secure PDF Download Link --}}
            @if($item->pdf_path)
            <a href="{{ route('procurement.file.access', [$item->id, 'pdf']) }}" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline flex items-center whitespace-nowrap text-[13px] uppercase tracking-widest transition-colors font-bold" download>
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Download PDF
            </a>
            @endif

            {{-- Secure Image Download Link --}}
            @if($item->jpeg_path)
                <a href="{{ route('serve.image', $item->jpeg_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline flex items-center whitespace-nowrap text-[13px] uppercase tracking-widest transition-colors font-bold" download>
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download Image
                </a>
            @endif
        </div>
    </div>

    {{-- =======================================================
         MAIN CONTENT PREVIEW SECTION (SMART LOGIC APPLIED HERE)
         ======================================================= --}}
    
    @if($item->jpeg_path && $item->pdf_path)
        {{-- SCENARIO 1: Both Image and PDF Exist --}}
        {{-- Show ONLY the image, but wrap it in a link to open the PDF --}}
        <div class="mb-8 w-full flex flex-col items-center">
            <a href="{{ route('procurement.file.access', [$item->id, 'pdf']) }}" target="_blank" class="block w-full text-center group cursor-pointer" title="Click to view full document">
                
                <img src="{{ route('serve.image', $item->jpeg_path) }}" alt="{{ $item->title }}" class="max-w-full h-auto mx-auto rounded shadow-sm border border-gray-200 group-hover:opacity-90 group-hover:shadow-md transition-all duration-300">
                
                <span class="mt-4 inline-flex items-center text-[13px] font-bold text-blue-600 group-hover:text-blue-800 uppercase tracking-widest transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z"></path></svg>
                    Click image to view full document
                </span>
            </a>
        </div>

    @elseif($item->jpeg_path)
        {{-- SCENARIO 2: Only Image Exists --}}
        <div class="mb-8 w-full flex justify-center bg-gray-50 rounded-lg p-6 border border-gray-200 shadow-inner">
            <img src="{{ route('serve.image', $item->jpeg_path) }}" alt="{{ $item->title }}" class="max-w-full h-auto rounded shadow-sm border border-gray-300">
        </div>

    @elseif($item->pdf_path)
        {{-- SCENARIO 3: Only PDF Exists --}}
        <div class="w-full bg-gray-100 rounded-lg p-2 shadow-inner mb-4 border border-gray-300 h-[70vh] min-h-[600px]">
            <iframe 
                src="{{ route('procurement.file.access', [$item->id, 'pdf']) }}#toolbar=0" 
                class="w-full h-full rounded bg-white" 
                title="{{ $item->title }}">
            </iframe>
        </div>
        
    @else
        {{-- SCENARIO 4: No preview available --}}
        <div class="flex flex-col items-center justify-center h-64 text-gray-500 bg-gray-50 border border-gray-200 rounded-lg shadow-inner w-full mb-8">
            <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <p class="font-bold text-[15px]">Preview not available</p>
        </div>
    @endif

    <p class="text-gray-400 text-[11px] text-center mb-8 mt-12 font-bold uppercase tracking-widest">
        This document is restricted to authorized accounts only.
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