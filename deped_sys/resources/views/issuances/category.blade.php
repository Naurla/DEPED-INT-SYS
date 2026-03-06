@extends('layouts.app')

@section('content')
<<<<<<< HEAD
<div class="bg-white py-16 min-h-screen">
    <div class="container mx-auto px-6 lg:px-32">
        
        <div class="mb-16">
            <h1 class="text-4xl font-black text-gray-900 uppercase tracking-tighter mb-2">
=======
@php
    // Theme colors mapping based on the controller's $color variable
    $themeBg = [
        'red' => 'bg-red-700',
        'blue' => 'bg-blue-800',
        'yellow' => 'bg-yellow-600'
    ][$color] ?? 'bg-gray-700';

    $themeText = [
        'red' => 'text-red-700',
        'blue' => 'text-blue-800',
        'yellow' => 'text-yellow-600'
    ][$color] ?? 'text-gray-700';
@endphp

<div class="bg-white py-12 min-h-screen">
    <div class="container mx-auto px-4 lg:px-20 max-w-5xl">
        
        <div class="mb-8 border-b-2 border-gray-100 pb-4 flex items-center">
            <div class="w-2 h-8 {{ $themeBg }} mr-4 rounded-full"></div>
            <h1 class="text-3xl font-black text-gray-900 uppercase tracking-widest">
>>>>>>> ddd5aff75e85c9d46ac83d92e0f797c9f2b3f366
                {{ $title }}
            </h1>
            <div class="h-1.5 w-20" style="background-color: {{ $color }};"></div>
        </div>

<<<<<<< HEAD
        <div class="space-y-12">
            @forelse($items as $item)
                <div class="group flex flex-col md:flex-row gap-8 pb-12 border-b border-gray-100 last:border-0">
                    
                    <div class="md:w-40 shrink-0">
                        <div class="flex flex-col space-y-2">
                            <span class="text-[10px] font-bold uppercase tracking-[0.2em] opacity-60" style="color: {{ $color }};">
                                {{ $item->type }}
                            </span>
                            <span class="text-sm font-medium text-gray-400 font-mono">
                                {{ $item->created_at->format('M d, Y') }}
                            </span>
                        </div>
                    </div>

                    <div class="flex-grow">
                        <a href="{{ route('issuances.show', $item->id) }}" class="block">
                            <h2 class="text-2xl font-bold text-gray-900 leading-tight mb-4 group-hover:text-[#a52a2a] transition-colors duration-300">
                                {{ $item->title }}
                            </h2>
                        </a>

                        @if($item->description)
                            <p class="text-gray-500 text-lg leading-relaxed mb-6 max-w-3xl">
                                {{ $item->description }}
                            </p>
                        @endif

                        <a href="{{ route('issuances.show', $item->id) }}" class="inline-flex items-center text-sm font-bold uppercase tracking-widest text-gray-900 group-hover:text-[#a52a2a] transition-colors">
                            <span>Read Full Issuance</span>
                            <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-2 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
=======
        <div class="space-y-0 border-t border-gray-100">
            @forelse($items as $item)
                <div class="flex flex-col md:flex-row border-b border-gray-100 py-8 hover:bg-gray-50 transition-colors duration-200 px-4 group">
                    
                    <div class="flex-shrink-0 w-24 mb-4 md:mb-0 md:mr-8 shadow-sm rounded-md overflow-hidden">
                        <div class="{{ $themeBg }} text-white text-center py-1.5 text-xs font-bold uppercase tracking-widest">
                            {{ $item->created_at->format('M') }}
                        </div>
                        <div class="bg-white border border-t-0 border-gray-200 text-center py-2.5">
                            <div class="text-3xl font-black text-gray-800 leading-none mb-1">
                                {{ $item->created_at->format('d') }}
                            </div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                {{ $item->created_at->format('Y') }}
                            </div>
                        </div>
>>>>>>> ddd5aff75e85c9d46ac83d92e0f797c9f2b3f366
                    </div>

                    <div class="flex-1 flex flex-col justify-start">
                        <a href="{{ route('issuances.show', $item->id) }}" class="text-xl font-black text-gray-900 group-hover:{{ $themeText }} transition-colors leading-tight mb-3">
                            {{ $item->title }}
                        </a>
                        
                        @if($item->description)
                            <div class="text-gray-600 text-sm leading-relaxed mb-5 line-clamp-3 pr-4">
                                {!! nl2br(e($item->description)) !!}
                            </div>
                        @endif

                        <div class="mt-auto flex items-center gap-4">
                            <a href="{{ asset('storage/' . $item->pdf_path) }}" target="_blank" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-gray-600 bg-white border border-gray-200 hover:border-gray-300 shadow-sm hover:shadow px-4 py-2 rounded transition-all">
                                <img src="{{ asset('images/pdf_icon.png') }}" class="w-4 h-4">
                                View PDF
                            </a>
                            <a href="{{ route('issuances.show', $item->id) }}" class="text-xs font-bold uppercase tracking-wider {{ $themeText }} hover:opacity-75 transition-opacity flex items-center gap-1">
                                Read Details 
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>

                </div>
            @empty
<<<<<<< HEAD
                <div class="py-20 text-center border-2 border-dashed border-gray-100 rounded-xl">
                    <p class="text-gray-400 italic text-lg">No {{ strtolower($title) }} found at the moment.</p>
=======
                <div class="py-16 text-center text-gray-400 bg-gray-50 rounded border border-gray-100 mt-4">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <p class="text-sm font-bold uppercase tracking-widest">No {{ strtolower($title) }} available.</p>
>>>>>>> ddd5aff75e85c9d46ac83d92e0f797c9f2b3f366
                </div>
            @endforelse
        </div>

        <div class="mt-20">
            {{ $items->links() }}
        </div>
        
    </div>
</div>
@endsection