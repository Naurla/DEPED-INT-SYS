@extends('layouts.app')

@section('content')

@php
    // Determine the parent category name and route based on the issuance type
    $parentRoute = 'javascript:history.back()';
    $parentName = 'Issuances';
    
    switch(strtolower($issuance->type)) {
        case 'advisory':
            $parentRoute = route('issuances.advisories');
            $parentName = 'Division Advisories';
            break;
        case 'memorandum':
            $parentRoute = route('issuances.memoranda');
            $parentName = 'Division Memoranda';
            break;
        case 'hrmpsb':
            $parentRoute = route('issuances.hrmpsb');
            $parentName = 'HRMPSB Assessment Results';
            break;
    }
@endphp

{{-- Breadcrumb matching the reference layout padding (md:px-20) --}}
<div class="bg-gray-100 border-b border-gray-200 w-full overflow-hidden">
    <div class="container mx-auto px-4 md:px-20 max-w-10xl py-3 text-xs sm:text-sm text-gray-600 overflow-x-auto whitespace-nowrap hide-scroll">
        <a href="/" class="hover:text-[#003366] transition">Home</a>
        <span class="mx-2">></span>
        <span>Issuances</span>
        <span class="mx-2">></span>
        <a href="{{ $parentRoute }}" class="hover:text-[#003366] transition">{{ $parentName }}</a>
        <span class="mx-2">></span>
        <span class="text-gray-900 font-bold">{{ Str::limit($issuance->display_title, 40) }}</span>
    </div>
</div>

{{-- Main Container (using the exact same md:px-20 layout padding) --}}
<div class="container mx-auto px-4 md:px-20 max-w-10xl py-8 md:py-12 w-full overflow-hidden min-h-screen">
    
    {{-- Header Section (Aligned naturally to the padding bounds) --}}
    <div class="mb-8 md:mb-10 text-left w-full break-words">
        
        {{-- Updated Back Link --}}
        <a href="{{ $parentRoute }}" class="text-[#a52a2a] hover:text-red-800 font-bold text-sm inline-flex items-center mb-6 uppercase tracking-wider transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to {{ $parentName }}
        </a>
        
        <h1 class="text-2xl md:text-3xl font-sans font-bold text-gray-900 tracking-wide uppercase mb-4">
            {{ $issuance->display_title }}
        </h1>

        @if($issuance->description)
            <div class="text-[15px] text-gray-700 leading-relaxed mb-6 max-w-4xl font-bold uppercase tracking-wide">
                {{ $issuance->description }}
            </div>
        @endif

        <div class="flex flex-wrap items-center text-gray-500 font-semibold gap-x-6 gap-y-3 mb-8">
            <span class="bg-gray-100 border border-gray-200 text-gray-800 px-3 py-1 rounded-sm uppercase tracking-widest text-[11px] whitespace-nowrap">
                {{ $issuance->type }}
            </span>
            <span class="whitespace-nowrap text-[12px] uppercase tracking-widest">
                Posted: {{ $issuance->date ? \Carbon\Carbon::parse($issuance->date)->format('M d, Y') : $issuance->created_at->format('M d, Y') }}
            </span>
            
            {{-- Document Download Link --}}
            @if($issuance->pdf_path)
            <a href="{{ asset('storage/' . $issuance->pdf_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline flex items-center whitespace-nowrap text-[13px] uppercase tracking-widest transition-colors font-bold" download>
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Download PDF
            </a>
            @endif

            {{-- Secure Image Download Link --}}
            @if($issuance->image_path)
                <a href="{{ route('serve.image', $issuance->image_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline flex items-center whitespace-nowrap text-[13px] uppercase tracking-widest transition-colors font-bold" download>
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download Image
                </a>
            @endif

            {{-- External Link Button --}}
            @if($issuance->link)
            <a href="{{ $issuance->link }}" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline flex items-center whitespace-nowrap text-[13px] uppercase tracking-widest transition-colors font-bold">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                Visit External Link
            </a>
            @endif
        </div>
    </div>

    {{-- =======================================================
         MAIN CONTENT PREVIEW SECTION (SMART LOGIC APPLIED HERE)
         ======================================================= --}}
    
    @if($issuance->image_path && $issuance->pdf_path)
        {{-- SCENARIO 1: Both Image and PDF Exist --}}
        <div class="mb-8 w-full flex flex-col items-center">
            <a href="{{ asset('storage/' . $issuance->pdf_path) }}" target="_blank" class="block w-full text-center group cursor-pointer" title="Click to view full document">
                
                <img src="{{ route('serve.image', $issuance->image_path) }}" alt="{{ $issuance->display_title }}" class="max-w-full h-auto mx-auto rounded shadow-sm border border-gray-200 group-hover:opacity-90 group-hover:shadow-md transition-all duration-300">
                
                <span class="mt-4 inline-flex items-center text-[13px] font-bold text-blue-600 group-hover:text-blue-800 uppercase tracking-widest transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z"></path></svg>
                    Click image to view full document
                </span>
            </a>
        </div>

    @elseif($issuance->image_path)
        {{-- SCENARIO 2: Only Image Exists --}}
        <div class="mb-8 w-full flex justify-center bg-gray-50 rounded-lg p-6 border border-gray-200 shadow-inner">
            <img src="{{ route('serve.image', $issuance->image_path) }}" alt="{{ $issuance->display_title }}" class="max-w-full h-auto rounded shadow-sm border border-gray-300">
        </div>

    @elseif($issuance->pdf_path)
        {{-- SCENARIO 3: Only PDF Exists --}}
        <div class="w-full bg-gray-100 rounded-lg p-2 shadow-inner mb-4 border border-gray-300 h-[70vh] min-h-[600px]">
            <iframe 
                src="{{ asset('storage/' . $issuance->pdf_path) }}#toolbar=0" 
                class="w-full h-full rounded bg-white" 
                title="{{ $issuance->display_title }}">
            </iframe>
        </div>
        
    @elseif($issuance->link)
        {{-- SCENARIO 4: External Link Exists (Clean UI Card instead of iframe) --}}
        <div class="w-full bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
            {{-- Card Header --}}
            <div class="bg-blue-50 border-b border-blue-100 p-4 flex items-center">
                <div class="bg-blue-100 p-2 rounded-lg mr-3 shadow-sm text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-blue-900 text-lg uppercase tracking-wide">External Link Provided</h3>
                    <p class="text-blue-700 text-xs font-bold uppercase tracking-wider mt-0.5">This document is hosted on an external website.</p>
                </div>
            </div>
            
            {{-- Card Body --}}
            <div class="p-8 md:p-12 flex flex-col items-center justify-center text-center bg-gray-50/50 min-h-[350px]">
                <svg class="w-20 h-20 text-gray-300 mb-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                
                <h4 class="text-gray-800 font-bold text-xl mb-3">Preview Not Available</h4>
                <p class="text-gray-500 mb-8 max-w-md text-sm leading-relaxed">
                    For security reasons, external websites (like Facebook or Google Drive) do not allow their content to be embedded directly. Please click the button below to view the content securely in a new tab.
                </p>
                
                <a href="{{ $issuance->link }}" target="_blank" class="inline-flex items-center justify-center px-8 py-3.5 border border-transparent text-sm font-bold uppercase tracking-widest rounded-lg text-white bg-blue-600 hover:bg-blue-700 shadow transition-all hover:shadow-md hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Open Link in New Tab
                    <svg class="ml-2.5 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
                
                <div class="mt-8 flex items-center max-w-full px-4">
                    <span class="text-xs text-gray-400 font-bold uppercase tracking-wider mr-3 whitespace-nowrap">Target URL:</span>
                    <p class="text-xs text-gray-500 font-mono break-all bg-white px-3 py-2 rounded shadow-sm border border-gray-200 line-clamp-1 truncate" title="{{ $issuance->link }}">
                        {{ $issuance->link }}
                    </p>
                </div>
            </div>
        </div>

    @else
        {{-- SCENARIO 5: No preview available --}}
        <div class="flex flex-col items-center justify-center h-64 text-gray-500 bg-gray-50 border border-gray-200 rounded-lg shadow-inner w-full mb-8">
            <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <p class="font-bold text-[15px]">Preview not available</p>
        </div>
    @endif

    <p class="text-gray-400 text-[11px] text-center mb-8 mt-12 font-bold uppercase tracking-widest">
        Official DepEd Zamboanga City Document
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