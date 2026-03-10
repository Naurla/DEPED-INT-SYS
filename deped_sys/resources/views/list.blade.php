@extends('layouts.app')

@section('content')
<div class="bg-white min-h-screen py-16">
    <div class="container pl-6 md:pl-20 px-6 max-w-5xl mx-auto">
        
        <div class="mb-10 border-b-4 border-[#a52a2a] pb-4">
            <h1 class="text-3xl md:text-5xl font-black text-gray-800 uppercase tracking-tight font-cinzel">
                {{ $type_name ?? 'Bid Opportunities' }}
            </h1>
            <p class="text-gray-500 text-sm font-bold uppercase tracking-[0.2em] mt-2 italic">
                Official Division Documents
            </p>
        </div>

        <div class="space-y-12">
            @forelse($items as $item)
                <div class="group transition-all duration-300">
                    
                    <a href="{{ route('procurement.bid-opportunities.show', $item->id) }}" class="block">
                        <h2 class="text-xl md:text-[1.35rem] font-extrabold text-[#333] leading-snug uppercase group-hover:text-[#a52a2a] transition-colors mb-3">
                            {{ strtoupper($item->created_at->format('F d, Y')) }} - {{ $item->title }}
                        </h2>
                    </a>
                    
                    <div>
                        <a href="{{ route('procurement.bid-opportunities.show', $item->id) }}" class="inline-block border border-gray-400 text-gray-500 px-4 py-1.5 text-sm hover:bg-gray-50 hover:text-gray-700 transition-colors">
                            Read More
                        </a>
                    </div>
                    
                </div>
            @empty
                <div class="py-12 border-2 border-dashed border-gray-200 text-center">
                    <p class="text-gray-400 uppercase tracking-widest font-bold">
                        No bid opportunities found.
                    </p>
                </div>
            @endforelse
        </div>

        <div class="mt-20">
            {{ $items->links() }}
        </div>
        
    </div>
</div>
@endsection