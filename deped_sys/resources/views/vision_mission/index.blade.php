@extends('layouts.app')

@section('content')

@php
    // Inline Tailwind styling for the Rich Text Editor content, tightened to match Admin
    $richTextClasses = "text-gray-800 text-[15px] leading-relaxed 
        [&_h1]:text-2xl [&_h1]:font-bold [&_h1]:text-gray-900 [&_h1]:mt-4 [&_h1]:mb-2 
        [&_h2]:text-xl [&_h2]:font-bold [&_h2]:text-gray-900 [&_h2]:mt-4 [&_h2]:mb-2 
        [&_h3]:text-lg [&_h3]:font-bold [&_h3]:text-gray-900 [&_h3]:mt-3 [&_h3]:mb-1 
        [&_p]:mb-2
        [&_ul]:list-disc [&_ul]:pl-8 [&_ul]:mb-2 [&_ul]:mt-1 [&_ul]:space-y-0.5
        [&_ol]:list-decimal [&_ol]:pl-8 [&_ol]:mb-2 [&_ol]:mt-1 [&_ol]:space-y-0.5
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
        <span class="text-gray-900 font-bold">Vision, Mission, Core Values, and Mandate</span>
    </div>
</div>

{{-- Main Container --}}
<div class="container mx-auto px-4 md:px-20 max-w-10xl py-6 md:py-8 w-full overflow-hidden min-h-screen">
    
    <div class="mb-6 text-left w-full break-words">
        <h1 class="text-2xl md:text-3xl font-sans font-bold text-gray-900 tracking-wide uppercase">
            Vision, Mission, Core Values, and Mandate
        </h1>
    </div>

    {{-- Dynamic Sections Loop --}}
    <div class="w-full mb-12">
        @if(!empty($data->sections) && count($data->sections) > 0)
            {{-- Changed from space-y-8 to space-y-5 to tightly pack the sections --}}
            <div class="space-y-10">
                @foreach($data->sections as $section)
                    <div class="w-full break-words">
                        
                        @if(!empty($section['title']))
                            <h2 class="text-xl font-bold text-gray-800 mb-4 uppercase tracking-wide">
                                {{ $section['title'] }}
                            </h2>
                        @endif
                        
                        <div class="{{ $richTextClasses }}">
                            {!! $section['content'] !!}
                        </div>
                        
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-10 w-full border border-gray-200 rounded-xl bg-gray-50/50 mt-6">
                <h3 class="text-lg font-bold text-gray-900">No Content Available</h3>
                <p class="text-gray-500 mt-1">The Vision and Mission statements have not been published yet.</p>
            </div>
        @endif
    </div>

</div>

<style>
    .hide-scroll::-webkit-scrollbar { display: none; }
    .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection