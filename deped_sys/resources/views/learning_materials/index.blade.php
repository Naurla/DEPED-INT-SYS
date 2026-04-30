@extends('layouts.app')

@section('content')

{{-- Breadcrumb --}}
<div class="bg-gray-100 border-b border-gray-200 w-full overflow-hidden">
    <div class="container mx-auto px-4 md:px-20 max-w-10xl py-3 text-xs sm:text-sm text-gray-600 overflow-x-auto whitespace-nowrap hide-scroll">
        <a href="/" class="hover:text-[#003366] transition">Home</a>
        <span class="mx-2">></span>
        <span>K to 12</span>
        <span class="mx-2">></span>
        <span class="text-gray-900 font-bold">Learning Materials</span>
    </div>
</div>

{{-- Main Container --}}
<div class="container mx-auto px-4 md:px-20 max-w-10xl py-12 w-full min-h-screen">
    
    <div class="mb-12 text-left w-full break-words">
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight uppercase">
            LEARNING MATERIALS
        </h1>
    </div>

    {{-- Changed from lg:w-3/4 to w-full so it spans the whole page --}}
    <div class="w-full space-y-12">
        @forelse($materials as $material)
            <div class="border-b border-gray-100 pb-10 last:border-0 group">
                
                {{-- Date and Title Heading --}}
                <a href="{{ route('learning_materials.show', $material->id) }}" class="block mb-4">
                    <h2 class="text-[1.3rem] font-bold text-gray-900 uppercase tracking-tight hover:text-gray-700 transition-colors leading-snug">
                        {{ strtoupper($material->created_at->format('F d, Y')) }} - {{ strtoupper($material->title) }}
                    </h2>
                </a>
                
                {{-- Description Preview --}}
                <div class="text-[15px] text-gray-600 mb-6 leading-relaxed w-full">
                    {{ Str::limit(strip_tags($material->description), 250) }}
                </div>

                {{-- Action Row --}}
                <div class="flex items-center gap-6">
                    {{-- Read More Button --}}
                    <a href="{{ route('learning_materials.show', $material->id) }}" 
                       class="border border-gray-200 px-6 py-2.5 text-xs font-bold uppercase tracking-widest text-gray-700 hover:bg-gray-50 transition shadow-sm rounded-sm">
                        READ MORE
                    </a>

                    {{-- Posted Meta Tag --}}
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">
                        POSTED: {{ strtoupper($material->created_at->format('M d, Y')) }}
                    </span>
                </div>
            </div>
        @empty
            <div class="text-gray-500 font-sans text-[15px] bg-gray-50 p-12 rounded-xl border border-dashed border-gray-300 text-center">
                No learning materials available at this time.
            </div>
        @endforelse
    </div>

    {{-- Pagination (Also updated to w-full to match) --}}
    <div class="mt-12 w-full">
        {{ $materials->links() }}
    </div>

</div>

<style>
    .hide-scroll::-webkit-scrollbar { display: none; }
    .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection