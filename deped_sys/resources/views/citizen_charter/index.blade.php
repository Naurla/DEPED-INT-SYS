@extends('layouts.app')

@section('content')

@php
    // Tightly packed styling to exactly match the Admin CKEditor spacing
    $richTextClasses = "text-gray-800 text-[15px] leading-[1.6] 
        [&_h1]:text-2xl [&_h1]:font-bold [&_h1]:text-gray-900 [&_h1]:mt-4 [&_h1]:mb-2 
        [&_h2]:text-xl [&_h2]:font-bold [&_h2]:text-gray-900 [&_h2]:mt-4 [&_h2]:mb-2 
        [&_h3]:text-lg [&_h3]:font-bold [&_h3]:text-gray-900 [&_h3]:mt-3 [&_h3]:mb-1 
        [&_p]:mb-1
        [&_ul]:list-disc [&_ul]:pl-8 [&_ul]:mb-1 [&_ul]:mt-0.5 [&_ul]:space-y-0.5
        [&_ol]:list-decimal [&_ol]:pl-8 [&_ol]:mb-1 [&_ol]:mt-0.5 [&_ol]:space-y-0.5
        [&_li]:mb-0 
        [&_strong]:font-bold [&_strong]:text-gray-900 
        [&_b]:font-bold [&_b]:text-gray-900 
        [&_a]:text-[#a52a2a] hover:[&_a]:underline transition-colors duration-200";
@endphp

{{-- Breadcrumb --}}
<div class="bg-gray-100 border-b border-gray-200 w-full overflow-hidden">
    <div class="container mx-auto px-4 md:px-20 max-w-10xl py-3 text-xs sm:text-sm text-gray-600 overflow-x-auto whitespace-nowrap hide-scroll">
        <a href="/" class="hover:text-[#003366] transition">Home</a>
        <span class="mx-2">></span>
        <span>About</span>
        <span class="mx-2">></span>
        <span class="text-gray-900 font-bold">Citizen's Charter</span>
    </div>
</div>

{{-- Main Container --}}
<div class="container mx-auto px-4 md:px-20 max-w-10xl py-6 md:py-8 w-full overflow-hidden min-h-screen">
    
    {{-- Header Section --}}
    <div class="mb-6 md:mb-10 text-left w-full break-words">
        <h1 class="text-2xl md:text-3xl font-sans font-bold text-gray-900 tracking-wide uppercase">Citizen's Charter</h1>
    </div>

    <div class="mb-4 text-left w-full break-words">
        <h3 class="text-1xl md:text-2xl font-sans font-bold text-gray-900 tracking-wide uppercase">
            {{ !empty($data->title) ? $data->title : "" }}
        </h3>
    </div>

    {{-- Content Display Section --}}
    @if(!empty($data->title) || !empty($data->content) || !empty($data->file_path) || (!empty($data->links) && count($data->links) > 0))
        <div class="w-full mb-8">
            
            {{-- 1. Main Content Section (Rich Text HTML Output) --}}
            @if(!empty($data->content))
                <div class="{{ $richTextClasses }} w-full break-words">
                    {!! $data->content !!}
                </div>
            @endif

            {{-- 2. External Links (Bulleted List seamlessly matching the rich text) --}}
            @if(!empty($data->links) && count($data->links) > 0)
                <div class="w-full mt-1 mb-2">
                    <ul class="list-disc pl-8 space-y-0.5 text-[15px] text-gray-800">
                        @foreach($data->links as $link)
                            <li>
                                <a href="{{ $link['url'] }}" target="_blank" class="text-blue-700 hover:text-blue-900 hover:underline transition-colors">
                                    {{ $link['name'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 3. Clean Download Link MOVED TO THE BOTTOM --}}
            @if(!empty($data->file_path))
                <div class="mt-6 mb-6">
                    <a href="{{ asset('storage/' . $data->file_path) }}" target="_blank" class="text-blue-700 hover:text-blue-900 hover:underline inline-flex items-center font-bold text-[15px] transition-colors" download>
                        Click here to download {{ $data->file_name ?? 'PDF Document' }}
                    </a>
                </div>
            @endif
            
            {{-- 4. DISPLAY PDF PREVIEW --}}
            @if(!empty($data->file_path))
                <div class="w-full bg-gray-100 rounded-lg p-2 shadow-inner border border-gray-300 h-[70vh] min-h-[600px]">
                    <iframe 
                        src="{{ asset('storage/' . $data->file_path) }}" 
                        class="w-full h-full rounded bg-white" 
                        title="{{ $data->file_name ?? 'Citizen\'s Charter PDF' }}">
                    </iframe>
                </div>
            @endif

        </div>
    @else
        {{-- Empty State --}}
        <div class="text-center py-10 w-full border border-gray-200 rounded-xl bg-gray-50/50 mt-6">
            <div class="mx-auto w-16 h-16 bg-white rounded-full flex items-center justify-center mb-4 text-gray-400 shadow-sm border border-gray-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900">No Content Available</h3>
            <p class="text-gray-500 mt-1">The citizen's charter has not been published yet.</p>
        </div>
    @endif

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