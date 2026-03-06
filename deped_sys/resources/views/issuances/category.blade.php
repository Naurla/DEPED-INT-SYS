@extends('layouts.app')

@section('content')
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
                {{ $title }}
            </h1>
        </div>

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
                <div class="py-16 text-center text-gray-400 bg-gray-50 rounded border border-gray-100 mt-4">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <p class="text-sm font-bold uppercase tracking-widest">No {{ strtolower($title) }} available.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $items->links() }}
        </div>
        
    </div>
</div>
@endsection