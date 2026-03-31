@extends('layouts.app')

@section('content')

{{-- Breadcrumb matching the reference layout --}}
<div class="bg-gray-100 border-b border-gray-200 w-full overflow-hidden">
    <div class="container mx-auto px-4 md:px-6 md:pl-20 max-w-10xl py-3 text-xs sm:text-sm text-gray-600 overflow-x-auto whitespace-nowrap">
        <a href="/" class="hover:text-[#003366] transition">Home</a>
        <span class="mx-2">></span>
        <span>About</span>
        <span class="mx-2">></span>
        <span>Organizational Structure</span>
        <span class="mx-2">></span>
        <span class="text-gray-900 font-bold">Curriculum Implementation Division</span>
    </div>
</div>

{{-- Main Container aligned exactly like the reference --}}
<div class="container mx-auto px-4 md:px-6 md:pl-20 max-w-10xl py-8 md:py-12 w-full overflow-hidden min-h-screen">
    
    {{-- Header Section --}}
    <div class="mb-6 md:mb-10 text-left w-full break-words">
        <h1 class="text-2xl md:text-3xl font-sans font-bold text-gray-900 tracking-wide uppercase">Curriculum Implementation Division</h1>
    </div>

    <div id="chart_div" class="overflow-x-auto w-full pb-12"></div>

    {{-- Chart Display Section --}}
    <div class="space-y-12">
        @forelse($items as $item)
            <div class="w-full mb-12">
                
                {{-- Optional Description aligned to left --}}
                @if($item->description)
                    <p class="text-gray-700 text-sm md:text-base mb-6 text-left break-words leading-relaxed w-full">
                        {{ $item->description }}
                    </p>
                @endif

                {{-- The Chart Image (Aligned to left like reference) --}}
                @if($item->image_path)
                    <div class="relative group w-full flex justify-start">
                        <img src="{{ route('serve.image', $item->image_path) }}" 
                             alt="{{ $item->title ?? 'Organizational Chart' }}" 
                             class="w-full h-auto object-contain max-w-full shadow-sm border border-gray-200 rounded-lg bg-white">
                             
                        {{-- Download Button (appears on hover) --}}
                        <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('serve.image', $item->image_path) }}" download class="bg-black/70 hover:bg-black text-white px-4 py-2 rounded-lg text-sm font-bold shadow flex items-center backdrop-blur-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Download Image
                            </a>
                        </div>
                    </div>
                @endif
                
            </div>
        @empty
            <div class="text-center py-12 w-full">
                <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4 text-gray-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">No Content Available</h3>
                <p class="text-gray-500 mt-1">No organizational chart uploaded yet.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection