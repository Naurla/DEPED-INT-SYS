@extends('layouts.app')

@section('content')
<div class="bg-gray-50 py-12 min-h-screen">
    <div class="container mx-auto px-6 lg:px-20">
        
        <div class="mb-10 border-b pb-4 flex items-center justify-between">
            <h1 class="text-3xl font-black text-gray-900 uppercase tracking-widest" style="border-left: 6px solid {{ $color }}; padding-left: 15px;">
                {{ $title }}
            </h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($items as $item)
                <div class="bg-white rounded-lg shadow hover:shadow-xl transition-all duration-300 flex flex-col border border-gray-200 overflow-hidden group">
                    <div class="p-6 flex-grow">
                        <div class="flex items-center space-x-3 mb-4">
                            <span class="text-xs font-bold text-white px-3 py-1 rounded-sm uppercase tracking-wider" style="background-color: {{ $color }};">
                                {{ $item->type }}
                            </span>
                            <span class="text-sm font-semibold text-gray-500 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ $item->created_at->format('M d, Y') }}
                            </span>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 leading-tight mb-3 group-hover:text-[#a52a2a] transition-colors">
                            {{ $item->title }}
                        </h2>
                    </div>
                    
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 mt-auto">
                        <a href="{{ route('issuances.show', $item->id) }}" class="text-[#a52a2a] hover:text-red-900 font-bold text-sm flex items-center uppercase tracking-widest w-full">
                            Read More 
                            <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white p-12 rounded-lg shadow-sm text-center text-gray-500 border border-gray-200">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <p class="text-lg">No {{ strtolower($title) }} found at the moment.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection