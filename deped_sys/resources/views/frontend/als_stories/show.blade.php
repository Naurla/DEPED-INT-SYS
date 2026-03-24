@extends('layouts.app')

@section('content')
<div class="bg-white py-10 min-h-screen">
    <div class="container mx-auto px-6 lg:px-20 max-w-6xl">
        
        {{-- Header Section --}}
        <div class="mb-8">
            <a href="{{ route('als-stories.index') }}" class="text-[#a52a2a] hover:text-red-800 font-bold text-sm inline-flex items-center mb-6 uppercase tracking-wider">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to List
            </a>
            
            <h1 class="text-2xl md:text-4xl font-black text-gray-900 leading-tight mb-3">
                {{ $item->title }}
            </h1>

            <div class="flex flex-wrap items-center text-gray-500 text-sm font-semibold gap-x-6 gap-y-2 mb-6">
                <span class="bg-gray-200 text-gray-800 px-3 py-1 rounded-sm uppercase tracking-wider text-xs whitespace-nowrap">
                    {{ $type_name ?? 'ALS Story' }}
                </span>
                <span class="whitespace-nowrap">Posted on: {{ $item->created_at->format('F d, Y') }}</span>
                
                @if($item->file_path)
                <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" class="text-blue-600 hover:underline flex items-center whitespace-nowrap">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download Attachment
                </a>
                @endif
            </div>

            {{-- TEXT CONTENT --}}
            @if($item->content)
                <div class="text-base text-gray-700 leading-relaxed mb-8 max-w-5xl whitespace-pre-line">
                    {{ $item->content }}
                </div>
            @endif
        </div>

        {{-- MAIN CONTENT SECTION --}}
        
        {{-- 1. DISPLAY IMAGE PREVIEW (Background removed) --}}
        @if($item->image_path)
        <div class="mb-8 w-full flex justify-center">
            <img src="{{ route('serve.image', $item->image_path) }}" alt="{{ $item->title }}" class="max-w-full h-auto">
        </div>
        @endif

        {{-- 2. DISPLAY PDF PREVIEW --}}
        @if($item->file_path && strtolower(pathinfo($item->file_path, PATHINFO_EXTENSION)) === 'pdf')
        <div class="w-full bg-gray-100 rounded-lg p-2 shadow-inner mb-4 border border-gray-300 h-[70vh] min-h-[600px]">
            <iframe 
                src="{{ asset('storage/' . $item->file_path) }}" 
                class="w-full h-full rounded bg-white" 
                title="{{ $item->title }}">
            </iframe>
        </div>
        @endif

        <p class="text-gray-400 text-xs text-center mb-16 font-medium">This story is published by DepEd Zamboanga City.</p>

    </div>
</div>
@endsection