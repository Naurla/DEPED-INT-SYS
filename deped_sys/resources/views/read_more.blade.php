@extends('layouts.app')

@section('content')
<div class="bg-white py-10 min-h-screen">
    <div class="container mx-auto px-6 lg:px-20 max-w-6xl">
        
        {{-- Header Section --}}
        <div class="mb-8">
            <a href="{{ route('procurement.index', $category) }}" class="text-[#a52a2a] hover:text-red-800 font-bold text-sm inline-flex items-center mb-6 uppercase tracking-wider">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to List
            </a>
            
            <h1 class="text-2xl md:text-4xl font-black text-gray-900 leading-tight mb-3">
                {{ $item->title }}
            </h1>

            {{-- ADDED DESCRIPTION RIGHT HERE (GLOBAL LAYOUT) --}}
            @if($item->description)
                <h2 class="text-base md:text-lg text-gray-700 font-bold leading-relaxed mb-6 uppercase tracking-wide max-w-5xl">
                    {{ $item->description }}
                </h2>
            @else
                <div class="mb-4"></div> {{-- Spacing if no description exists --}}
            @endif

            <div class="flex flex-wrap items-center text-gray-500 text-sm font-semibold gap-x-6 gap-y-2">
                <span class="bg-gray-200 text-gray-800 px-3 py-1 rounded-sm uppercase tracking-wider text-xs whitespace-nowrap">
                    {{ $type_name ?? 'Bid Opportunity' }}
                </span>
                <span class="whitespace-nowrap">Posted on: {{ $item->created_at->format('F d, Y') }}</span>
                
                {{-- Secure PDF Download Link --}}
                @if($item->pdf_path)
                <a href="{{ route('procurement.file.access', [$item->id, 'pdf']) }}" target="_blank" class="text-blue-600 hover:underline flex items-center whitespace-nowrap">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download PDF
                </a>
                @endif

                {{-- Secure Image Download Link --}}
                @if($item->jpeg_path)
                    <a href="{{ route('serve.image', $item->jpeg_path) }}" target="_blank" class="text-blue-600 hover:underline flex items-center whitespace-nowrap" download>
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Download Image
                    </a>
                @endif
            </div>
        </div>

        {{-- Main Content Section --}}
        
        {{-- 1. DISPLAY IMAGE PREVIEW (If it exists) --}}
        @if($item->jpeg_path)
        <div class="mb-8 w-full flex justify-center bg-gray-50 rounded-lg p-4 border border-gray-200 shadow-inner">
            <img src="{{ route('serve.image', $item->jpeg_path) }}" alt="{{ $item->title }}" class="max-w-full h-auto rounded shadow-sm border border-gray-300">
        </div>
        @endif

        {{-- 2. DISPLAY PDF PREVIEW (If it exists) --}}
        @if($item->pdf_path)
        <div class="w-full bg-gray-100 rounded-lg p-2 shadow-inner mb-4 border border-gray-300 h-[70vh] min-h-[600px]">
            <iframe 
                src="{{ route('procurement.file.access', [$item->id, 'pdf']) }}" 
                class="w-full h-full rounded bg-white" 
                title="{{ $item->title }}">
            </iframe>
        </div>
        @endif

        <p class="text-gray-400 text-xs text-center mb-16 font-medium">This document is restricted to authorized accounts only.</p>

    </div>
</div>
@endsection