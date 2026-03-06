@extends('layouts.app')

@section('content')
<div class="bg-white py-10 min-h-screen">
    <div class="container mx-auto px-6 lg:px-20 max-w-6xl">
        
        <div class="mb-8">
            <a href="javascript:history.back()" class="text-[#a52a2a] hover:text-red-800 font-bold text-sm inline-flex items-center mb-6 uppercase tracking-wider">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to List
            </a>
            <h1 class="text-2xl md:text-4xl font-black text-gray-900 leading-tight mb-4">
                {{ $issuance->title }}
            </h1>
            <div class="flex items-center text-gray-500 text-sm font-semibold space-x-4">
                <span class="bg-gray-200 text-gray-800 px-3 py-1 rounded-sm uppercase tracking-wider text-xs">
                    {{ $issuance->type }}
                </span>
                <span>Posted on: {{ $issuance->created_at->format('F d, Y') }}</span>
                
                <a href="{{ asset('storage/' . $issuance->pdf_path) }}" target="_blank" class="text-blue-600 hover:underline flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download PDF
                </a>
            </div>
        </div>

        <div class="w-full bg-gray-100 rounded-lg p-2 shadow-inner mb-16 border border-gray-300 h-[70vh] min-h-[600px]">
            <iframe 
                src="{{ asset('storage/' . $issuance->pdf_path) }}" 
                class="w-full h-full rounded bg-white" 
                title="{{ $issuance->title }}">
            </iframe>
        </div>

        <div class="border-t-4 border-[#a52a2a] pt-10 mt-10 mb-8 bg-gray-50 rounded-b-xl px-8 pb-10 shadow-sm">
            <h2 class="text-2xl font-black text-gray-900 mb-8 uppercase tracking-widest text-center">Recent Updates</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
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
</div>
@endsection