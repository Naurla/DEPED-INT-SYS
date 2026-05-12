@extends('layouts.app')

@section('content')

@php
    // Inline Tailwind styling for the Rich Text Editor content
    $richTextClasses = "text-gray-700 text-[15px] leading-relaxed 
        [&_h1]:text-2xl [&_h1]:font-bold [&_h1]:text-gray-800 [&_h1]:mt-6 [&_h1]:mb-3 
        [&_h2]:text-xl [&_h2]:font-bold [&_h2]:text-gray-800 [&_h2]:mt-6 [&_h2]:mb-3 
        [&_h3]:text-lg [&_h3]:font-bold [&_h3]:text-gray-800 [&_h3]:mt-4 [&_h3]:mb-2 
        [&_p]:mb-4 
        [&_ul]:list-disc [&_ul]:pl-8 [&_ul]:mb-4 [&_ul]:space-y-1
        [&_ol]:list-decimal [&_ol]:pl-8 [&_ol]:mb-4 [&_ol]:space-y-1
        [&_li]:mb-1 
        [&_strong]:font-bold [&_strong]:text-gray-900 
        [&_b]:font-bold [&_b]:text-gray-900 
        [&_a]:text-[#a52a2a] hover:[&_a]:underline transition-colors duration-200";
@endphp

{{-- Breadcrumb matching the reference layout padding (md:px-20) --}}
<div class="bg-gray-100 border-b border-gray-200 w-full overflow-hidden">
    <div class="container mx-auto px-4 md:px-20 max-w-10xl py-3 text-xs sm:text-sm text-gray-600 overflow-x-auto whitespace-nowrap hide-scroll">
        <a href="/" class="hover:text-[#003366] transition">Home</a>
        <span class="mx-2">></span>
        <span>About</span>
        <span class="mx-2">></span>
        <span class="text-gray-900 font-bold">Data Privacy Notice</span>
    </div>
</div>

{{-- Main Container (using the exact same md:px-20 layout padding) --}}
<div class="container mx-auto px-4 md:px-20 max-w-10xl py-8 md:py-12 w-full overflow-hidden min-h-screen">
    
    {{-- Header Section (Aligned naturally to the padding bounds) --}}
    <div class="mb-6 md:mb-10 text-left w-full break-words">
        <h1 class="text-2xl md:text-3xl font-sans font-bold text-gray-900 tracking-wide uppercase">Data Privacy Notice</h1>
    </div>

    {{-- Content Display Section --}}
    <div class="w-full mb-12">
        @if(!empty($data->sections) && count($data->sections) > 0)
            <div class="space-y-10"> {{-- Adds generous spacing between each section --}}
                @foreach($data->sections as $section)
                    <div class="w-full break-words">
                        {{-- Optional Title --}}
                        @if(!empty($section['title']))
                            <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-4 border-b pb-2 border-gray-200">
                                {{ $section['title'] }}
                            </h2>
                        @endif
                        
                        {{-- Section Content --}}
                        <div class="{{ $richTextClasses }}">
                            {!! $section['content'] !!}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Empty State (Matched with the other templates) --}}
            <div class="text-center py-12 w-full">
                <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4 text-gray-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">No Content Available</h3>
                <p class="text-gray-500 mt-1">The data privacy notice has not been published yet.</p>
            </div>
        @endif
    </div>

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