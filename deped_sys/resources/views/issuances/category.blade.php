@extends('layouts.app')

@section('content')
<div class="bg-white py-16 min-h-screen">
    <div class="container mx-auto px-6 lg:px-32">
        
        <div class="mb-16">
            <h1 class="text-4xl font-black text-gray-900 uppercase tracking-tighter mb-2">
                {{ $title }}
            </h1>
            <div class="h-1.5 w-20" style="background-color: {{ $color }};"></div>
        </div>

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
                    </div>
                </div>
            @empty
                <div class="py-20 text-center border-2 border-dashed border-gray-100 rounded-xl">
                    <p class="text-gray-400 italic text-lg">No {{ strtolower($title) }} found at the moment.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-20">
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection