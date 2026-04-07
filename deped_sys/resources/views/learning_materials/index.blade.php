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
            Learning Materials
        </h1>
    </div>

    <div class="w-full lg:w-3/4 space-y-12">
        @forelse($materials as $material)
            <div class="border-b border-gray-100 pb-10 last:border-0 group">
                
                {{-- Date and Title Heading (Matched to your reference) --}}
                <a href="{{ route('learning_materials.show', $material->id) }}" class="block mb-2">
                    <h2 class="text-xl font-bold text-gray-900 uppercase tracking-tight group-hover:text-red-700 transition-colors">
                        {{ $material->created_at->format('F d, Y') }} - {{ $material->title }}
                    </h2>
                </a>
                
                {{-- Description Preview --}}
                <div class="text-[15px] text-gray-600 mb-6 leading-relaxed max-w-4xl">
                    {{ Str::limit(strip_tags($material->description), 250) }}
                </div>

                {{-- Action Row --}}
                <div class="flex items-center gap-6">
                    {{-- Read More Button (Styled like your image) --}}
                    <a href="{{ route('learning_materials.show', $material->id) }}" 
                       class="border border-gray-300 px-6 py-2 text-xs font-bold uppercase tracking-widest text-gray-700 hover:bg-gray-50 transition shadow-sm rounded-sm">
                        Read More
                    </a>

                    {{-- Posted Meta Tag --}}
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">
                        Uploaded: {{ $material->created_at->format('M d, Y') }}
                    </span>
                </div>
            </div>
        @empty
            <div class="text-gray-500 font-sans text-[15px] bg-gray-50 p-12 rounded-xl border border-dashed border-gray-300 text-center">
                No learning materials available at this time.
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-12 lg:w-3/4">
        {{ $materials->links() }}
    </div>

</div>

<style>
    .hide-scroll::-webkit-scrollbar { display: none; }
    .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection