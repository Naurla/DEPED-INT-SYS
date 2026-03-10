@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-6 px-4 mb-24">
    
    <div class="flex items-end justify-between border-b-4 border-[#a52a2a] pb-4 mb-12 mt-8">
        <div>
            <h2 class="text-3xl md:text-5xl font-black text-gray-800 uppercase tracking-tight font-cinzel">
                {{ $type_name }}
            </h2>
            <p class="text-gray-500 text-sm font-bold uppercase tracking-[0.2em] mt-2 italic">
                Official Division Documents
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($items as $item)
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 flex flex-col group hover:shadow-2xl transition-all duration-300">
                
                <div class="aspect-video bg-gray-100 overflow-hidden relative">
                    @if(isset($item->jpeg_path))
                        <img src="{{ route('procurement.file.access', [$item->id, 'jpeg']) }}" 
                             alt="{{ $item->title }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <img src="{{ asset('storage/' . $item->image_path) }}" 
                             alt="{{ $item->title }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @endif
                    
                    <div class="absolute top-4 left-4">
                        <span class="bg-[#a52a2a] text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest shadow-lg">
                            {{ $type_name }}
                        </span>
                    </div>
                </div>

                <div class="p-8 flex flex-col flex-grow">
                    <p class="text-gray-400 text-[11px] font-bold uppercase tracking-widest mb-3">
                        Published • {{ $item->created_at->format('M d, Y') }}
                    </p>
                    
                    <h3 class="text-gray-900 font-black text-xl leading-tight mb-6 flex-grow group-hover:text-[#a52a2a] transition-colors">
                        {{ $item->title }}
                    </h3>

                    <div class="pt-6 border-t border-gray-50 mt-auto">
                        <a href="{{ route('procurement.bid-opportunities.show', $item->id) }}" 
                           class="inline-flex items-center text-[#a52a2a] font-black text-xs uppercase tracking-[0.2em] group/btn">
                            Read More
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2 group-hover/btn:translate-x-2 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-24 flex flex-col items-center justify-center bg-white rounded-3xl border-2 border-dashed border-gray-200">
                <svg class="w-20 h-20 text-gray-200 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-gray-400 font-black uppercase tracking-widest">No Items Found</p>
            </div>
        @endforelse
    </div>

    <div class="mt-16">
        {{ $items->links() }}
    </div>
</div>
@endsection