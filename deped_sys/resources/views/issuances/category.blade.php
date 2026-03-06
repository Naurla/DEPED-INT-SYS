@extends('layouts.app')

@section('content')
<div class="bg-white min-h-screen py-16">
    <div class="container pl-20 px-6 max-w-5xl">
        
        <div class="mb-10">
            <h1 class="text-[1.4rem] font-extrabold text-black uppercase tracking-wide">
                {{ $title }}
            </h1>
        </div>

        <div class="space-y-12">
            @forelse($items as $item)
                <div class="group transition-all duration-300">
                    <a href="{{ route('issuances.show', $item->id) }}" class="block">
                        <h2 class="text-xl md:text-[1.35rem] font-extrabold text-[#333] leading-snug uppercase group-hover:text-blue-800 transition-colors mb-3">
                            {{ strtoupper($item->created_at->format('F d, Y')) }} - {{ $item->title }} @if($item->description) - {{ $item->description }} @endif
                        </h2>
                        
                        @if($item->description)
                            <p class="text-base md:text-[1.05rem] text-[#333] uppercase leading-relaxed mb-4">
                                {{ $item->title }} - {{ $item->description }}
                            </p>
                        @endif
                    </a>
                    
                    <div>
                        <a href="{{ route('issuances.show', $item->id) }}" class="inline-block border border-gray-400 text-gray-500 px-4 py-1.5 text-sm hover:bg-gray-50 hover:text-gray-700 transition-colors">
                            Read More
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
<div class="border-t-4 border-[#a52a2a] pt-10 mt-16 mb-8 bg-gray-50 rounded-b-xl px-8 pb-10 shadow-sm">
            <h2 class="text-2xl font-black text-gray-900 mb-8 uppercase tracking-widest text-center">Recent Updates</h2>
            
            <div class="pl-16 grid grid-cols-1 md:grid-cols-2 gap-12">
                <div>
                    <h3 class="text-lg font-bold text-[#a52a2a] border-b border-gray-300 pb-3 mb-4 uppercase tracking-wider flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        Latest Advisories
                    </h3>
                    <ul class="space-y-4">
                        @forelse($recentAdvisories as $adv)
                            <li class="group">
                                <a href="{{ route('issuances.show', $adv->id) }}" class="block">
                                    <p class="text-sm font-semibold text-gray-800 group-hover:text-[#a52a2a] transition-colors line-clamp-2">{{ $adv->title }}</p>
                                    <span class="text-xs text-gray-500 mt-1 block">{{ $adv->created_at->format('F d, Y') }}</span>
                                </a>
                            </li>
                        @empty
                            <li class="text-sm text-gray-500 italic">No recent advisories.</li>
                        @endforelse
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-blue-800 border-b border-gray-300 pb-3 mb-4 uppercase tracking-wider flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Latest Memoranda
                    </h3>
                    <ul class="space-y-4">
                        @forelse($recentMemoranda as $memo)
                            <li class="group">
                                <a href="{{ route('issuances.show', $memo->id) }}" class="block">
                                    <p class="text-sm font-semibold text-gray-800 group-hover:text-blue-700 transition-colors line-clamp-2">{{ $memo->title }}</p>
                                    <span class="text-xs text-gray-500 mt-1 block">{{ $memo->created_at->format('F d, Y') }}</span>
                                </a>
                            </li>
                        @empty
                            <li class="text-sm text-gray-500 italic">No recent memoranda.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        
    </div>
@endsection