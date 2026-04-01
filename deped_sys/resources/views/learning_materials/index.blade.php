@extends('layouts.app')

@section('content')

{{-- Breadcrumb matching the reference layout padding (md:px-20) --}}
<div class="bg-gray-100 border-b border-gray-200 w-full overflow-hidden">
    <div class="container mx-auto px-4 md:px-20 max-w-10xl py-3 text-xs sm:text-sm text-gray-600 overflow-x-auto whitespace-nowrap hide-scroll">
        <a href="/" class="hover:text-[#003366] transition">Home</a>
        <span class="mx-2">></span>
        <span>K to 12</span>
        <span class="mx-2">></span>
        <span class="text-gray-900 font-bold">Learning Materials</span>
    </div>
</div>

{{-- Main Container (using the exact same md:px-20 layout padding) --}}
<div class="container mx-auto px-4 md:px-20 max-w-10xl py-8 md:py-12 w-full overflow-hidden min-h-screen">
    
    {{-- Header Section (Aligned naturally to the padding bounds) --}}
    <div class="mb-8 md:mb-12 text-left w-full break-words border-b border-gray-100 pb-6">
        <h1 class="text-2xl md:text-3xl font-sans font-bold text-gray-900 tracking-wide uppercase">Learning Materials</h1>
    </div>

    <div class="w-full lg:w-3/4">
        <div class="space-y-16">
            @forelse($materials as $material)
                <div class="group transition-all duration-300">
                  
                    <a href="{{ route('learning_materials.show', $material->id) }}" class="block">
                        <h2 class="text-xl md:text-[1.35rem] font-extrabold text-gray-900 leading-snug uppercase group-hover:text-[#003366] transition-colors mb-4">
                            {{ $material->title }} __ {{ Str::limit(strip_tags($material->description), 100) }}
                        </h2>
                    </a>
                    
                    <p class="text-gray-600 text-[15px] font-medium leading-relaxed mb-6 pr-10">
                      {{ $material->title }} - {{ Str::limit(strip_tags($material->description), 200) }}
                    </p>
                    
                    <div class="flex items-center gap-6">
                        <a href="{{ route('learning_materials.show', $material->id) }}" class="inline-block border border-gray-300 text-gray-600 px-6 py-2 text-xs font-bold uppercase tracking-widest hover:bg-gray-50 hover:text-gray-900 transition-colors rounded-sm shadow-sm">
                            Read More
                        </a>
                        <span class="text-gray-400 text-[11px] font-bold uppercase tracking-widest">
                            Uploaded: {{ $material->created_at->format('M d, Y') }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-gray-500 font-sans text-[15px] bg-gray-50 p-6 rounded-lg border border-dashed border-gray-200">
                    No learning materials found.
                </div>
            @endforelse
        </div>

        {{-- Pagination Links Added Here --}}
        <div class="mt-16">
            {{ $materials->links() }}
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