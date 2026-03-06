@extends('layouts.app')

@section('content')
<div class="bg-white min-h-screen py-16">
    <div class="container mx-auto px-6 max-w-5xl">
        
        <div class="mb-16 border-b border-gray-100 pb-10">
            <h1 class="text-4xl font-black text-gray-900 uppercase tracking-tighter">
                {{ $title }}
            </h1>
        </div>

        <div class="space-y-10">
            @forelse($items as $item)
                <div class="group border-l-4 border-gray-100 hover:border-blue-700 pl-8 transition-all duration-300">
                    <a href="{{ route('issuances.show', $item->id) }}" class="block">
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 leading-tight uppercase tracking-tight group-hover:text-blue-800 transition-colors">
                            {{ $item->created_at->format('F d, Y') }}, {{ $item->title }}
                        </h1>
                    </a>
                    
                    <div class="mt-4">
                        <a href="{{ route('issuances.show', $item->id) }}" class="text-xs font-black uppercase tracking-[0.2em] text-gray-400 group-hover:text-blue-700 inline-flex items-center gap-2">
                            Read More 
                            <span class="text-xl">→</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-gray-400 uppercase tracking-widest font-bold">
                    No items found in this category.
                </div>
            @endforelse
        </div>

        <div class="mt-20">
            {{ $items->links() }}
        </div>
        
    </div>
</div>
@endsection