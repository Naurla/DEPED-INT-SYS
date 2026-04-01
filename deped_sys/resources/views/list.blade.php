@extends('layouts.app')

@section('content')
<div class="bg-white min-h-screen py-16">
    <div class="container pl-20 px-6 max-w-10xl">
        
        <div class="mb-12">
            <h1 class="text-[1.4rem] font-extrabold text-black uppercase tracking-wide">
                {{ $type_name ?? 'Bid Opportunities' }}
            </h1>
        </div>

        <div class="space-y-16">
            @forelse($items as $item)
                <div class="group transition-all duration-300">
                  
                    <a href="{{ route('procurement.show', ['category' => $category, 'id' => $item->id]) }}" class="block">
                        <h2 class="text-xl md:text-[1.35rem] font-extrabold text-[#333] leading-snug uppercase group-hover:text-blue-800 transition-colors mb-4">
                            {{ strtoupper($item->display_title) }} @if($item->description) __  {{ $item->description }} @endif
                        </h2>
                    </a>
                    
                    <p class="text-gray-600 text-sm font-medium leading-relaxed mb-6 pr-10">
                      {{ $item->display_title }} @if($item->description) -  {{ $item->description }} @endif
                    </p>
                    
                    <div>
                        <a href="{{ route('procurement.show', ['category' => $category, 'id' => $item->id]) }}" class="inline-block border border-gray-400 text-gray-500 px-6 py-2 text-xs font-black uppercase tracking-widest hover:bg-gray-50 hover:text-gray-700 transition-colors">
                            Read More
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-gray-400 uppercase tracking-widest font-bold">
                    No {{ strtolower($type_name ?? 'items') }} found.
                </div>
            @endforelse
        </div>

        <div class="mt-20">
            {{ $items->links() }}
        </div>
        
    </div>
</div>
@endsection