@extends('layouts.app')

@section('content')
<div class="bg-white min-h-screen py-16">
    <div class="container pl-20 px-6 max-w-10xl mx-auto">
        
        <div class="mb-10">
            <h1 class="text-[1.4rem] font-extrabold text-black uppercase tracking-wide">
                Search Results For: "{{ $query }}"
            </h1>
        </div>

        <div class="space-y-12">
            @forelse($results as $item)
                <div class="group transition-all duration-300">
                    <a href="{{ $item->url }}" class="block">
                        <h2 class="text-xl md:text-[1.35rem] font-extrabold text-[#333] leading-snug uppercase group-hover:text-blue-800 transition-colors mb-3">
                            {{ strtoupper(\Carbon\Carbon::parse($item->date)->format('F d, Y')) }} - [{{ strtoupper(str_replace('_', ' ', $item->type)) }}] {{ $item->title }}
                        </h2>
                        
                        @if($item->description)
                            <p class="text-base md:text-[1.05rem] text-[#333] leading-relaxed mb-4">
                                {{ \Illuminate\Support\Str::limit(strip_tags($item->description), 200) }}
                            </p>
                        @endif
                    </a>
                    
                    <div>
                        <a href="{{ $item->url }}" class="inline-block border border-gray-400 text-gray-500 px-4 py-1.5 text-sm hover:bg-gray-50 hover:text-gray-700 transition-colors uppercase">
                            Read More
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-gray-400 uppercase tracking-widest font-bold">
                    No results found for "{{ $query }}".
                </div>
            @endforelse
        </div>

        <div class="mt-20">
            {{ $results->appends(['q' => $query])->links() }}
        </div>
        
    </div>
</div>
@endsection