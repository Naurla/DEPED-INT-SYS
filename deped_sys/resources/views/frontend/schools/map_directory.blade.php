@extends('layouts.app') 

@section('content')

{{-- Breadcrumb matching the reference layout padding (md:px-20) --}}
<div class="bg-gray-100 border-b border-gray-200 w-full overflow-hidden">
    <div class="container mx-auto px-4 md:px-20 max-w-10xl py-3 text-xs sm:text-sm text-gray-600 overflow-x-auto whitespace-nowrap hide-scroll">
        <a href="/" class="hover:text-[#003366] transition">Home</a>
        <span class="mx-2">></span>
        <span class="text-gray-900">Division Data</span>
        <span class="mx-2">></span>
        <span class="text-gray-900 font-bold">Interactive Map</span>
    </div>
</div>

{{-- Main Container (using the exact same md:px-20 layout padding for consistency) --}}
<div class="container mx-auto px-4 md:px-20 max-w-10xl py-8 md:py-12 w-full overflow-hidden">
    
    {{-- Header Section (Aligned naturally to the padding bounds) --}}
    <div class="mb-6 md:mb-10 text-left w-full break-words">
        <h1 class="text-2xl md:text-3xl font-sans font-bold text-gray-900 tracking-wide uppercase">DepEd Zamboanga City - Interactive Map</h1>
    </div>

    {{-- Map Display Section --}}
    <div class="w-full h-[75vh] min-h-[600px] border border-gray-300 rounded-lg overflow-hidden bg-gray-100 shadow-sm">
        <iframe 
            src="http://10.10.10.109:8000/?embed=true" 
            class="w-full h-full"
            frameborder="0" 
            allowfullscreen>
        </iframe>
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