@extends('layouts.app')

@section('content')
<div class="bg-white min-h-screen py-10 md:py-16">
    <div class="container mx-auto px-6 lg:px-20 max-w-7xl">
        
        {{-- Header Section --}}
        <div class="mb-10 text-center md:text-left border-b border-gray-200 pb-6">
            <h1 class="text-3xl md:text-4xl font-black text-gray-900 uppercase tracking-tight mb-2">
                Organizational Structure
            </h1>
            <h2 class="text-xl md:text-2xl font-bold text-[#a52a2a] uppercase tracking-wide">
                Office of the Schools Division Superintendent
            </h2>
        </div>

        {{-- Chart Display Section --}}
        <div class="space-y-12">
            @forelse($items as $item)
                <div>
                    {{-- Optional Description --}}
                    @if($item->description)
                        <p class="text-gray-700 text-base mb-6 text-center max-w-4xl mx-auto leading-relaxed">
                            {{ $item->description }}
                        </p>
                    @endif

                    {{-- The Chart Image (Constrained to max-w-4xl and centered) --}}
                    @if($item->image_path)
                        <div class="relative group w-full max-w-4xl mx-auto">
                            <img src="{{ route('serve.image', $item->image_path) }}" 
                                 alt="{{ $item->title }}" 
                                 class="w-full h-auto shadow-sm border border-gray-200 rounded-lg bg-white">
                                 
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
                <div class="text-center py-20 bg-gray-50 rounded-xl border border-gray-200 border-dashed">
                    <p class="text-gray-400 font-bold uppercase tracking-widest text-sm">
                        No organizational chart uploaded yet.
                    </p>
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection