@extends('layouts.app')

@section('content')

@php
    // Inline Tailwind styling for the Rich Text Editor content
    $richTextClasses = "text-gray-700 text-[15px] leading-relaxed 
        [&_h1]:text-2xl [&_h1]:font-bold [&_h1]:text-gray-800 [&_h1]:mt-6 [&_h1]:mb-3 
        [&_h2]:text-xl [&_h2]:font-bold [&_h2]:text-gray-800 [&_h2]:mt-6 [&_h2]:mb-3 
        [&_h3]:text-lg [&_h3]:font-bold [&_h3]:text-gray-800 [&_h3]:mt-4 [&_h3]:mb-2 
        [&_p]:mb-4 
        [&_ul]:list-disc [&_ul]:pl-5 [&_ul]:mb-4 
        [&_ol]:list-decimal [&_ol]:pl-5 [&_ol]:mb-4 
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
        <span class="text-gray-900 font-bold">Vision, Mission, Core Values, and Mandate</span>
    </div>
</div>

{{-- Main Container (using the exact same md:px-20 layout padding) --}}
<div class="container mx-auto px-4 md:px-20 max-w-10xl py-8 md:py-12 w-full overflow-hidden min-h-screen">
    
    {{-- Header Section (Aligned naturally to the padding bounds) --}}
    <div class="mb-6 md:mb-10 text-left w-full break-words">
        <h1 class="text-2xl md:text-3xl font-sans font-bold text-gray-900 tracking-wide uppercase">Vision, Mission, Core Values, and Mandate</h1>
    </div>

    <div class="flex flex-col lg:flex-row gap-12">
        
        <div class="w-full lg:w-3/4">
            
            <div class="mb-12 w-full break-words">
                <h2 class="text-xl font-bold text-gray-800 mb-2 uppercase tracking-wide">Vision</h2>
                
                <div class="{{ $richTextClasses }}">
                    {!! $data->vision ?? '<p class="text-gray-400 italic">No content available yet.</p>' !!}
                </div>
            </div>

            <div class="mb-12 w-full break-words">
                <h2 class="text-xl font-bold text-gray-800 mb-2 uppercase tracking-wide">Mission</h2>
                
                <div class="{{ $richTextClasses }}">
                    {!! $data->mission ?? '<p class="text-gray-400 italic">No content available yet.</p>' !!}
                </div>
            </div>

            <div class="mb-12 w-full break-words">
                <h2 class="text-xl font-bold text-gray-800 mb-2 uppercase tracking-wide">Core Values</h2>
                
                <div class="{{ $richTextClasses }}">
                    {!! $data->core_values ?? '<p class="text-gray-400 italic">No content available yet.</p>' !!}
                </div>
            </div>

            <div class="mb-8 w-full break-words">
                <h2 class="text-xl font-bold text-gray-800 mb-2 uppercase tracking-wide">Mandate</h2>
                
                <div class="{{ $richTextClasses }}">
                    {!! $data->mandate ?? '<p class="text-gray-400 italic">No content available yet.</p>' !!}
                </div>
            </div>

        </div>
        
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