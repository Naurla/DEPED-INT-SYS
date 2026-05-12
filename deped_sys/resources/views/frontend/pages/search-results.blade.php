@extends('layouts.app')

@section('content')
<div class="bg-gray-100 border-b border-gray-200 w-full overflow-hidden">
    <div class="container mx-auto px-4 md:px-20 max-w-10xl py-3 text-xs sm:text-sm text-gray-600">
        <a href="/" class="hover:text-[#a52a2a] transition">Home</a>
        <span class="mx-2">></span>
        <span class="text-gray-900 font-bold">Search Results</span>
    </div>
</div>

<div class="container mx-auto px-4 md:px-20 max-w-10xl py-8 md:py-12 w-full min-h-screen">
    
    {{-- 🟢 Go Back Button --}}
    <div class="mb-4">
        <a href="javascript:history.back()" class="inline-flex items-center text-xs font-bold text-gray-500 hover:text-[#003366] transition-colors uppercase tracking-wider cursor-pointer">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Go Back
        </a>
    </div>

    {{-- 🟢 HEADER: Inline Title and Round Search Bar --}}
    <div class="mb-8 md:mb-12 flex flex-col md:flex-row justify-between items-start md:items-center w-full border-b border-gray-200 pb-6 gap-6">
        
        <h1 class="text-2xl md:text-3xl font-sans font-bold text-gray-900 tracking-wide uppercase flex-1 break-words">
            Search Results for "{{ $keyword }}"
        </h1>

        <div class="w-full md:w-auto md:min-w-[320px]">
            <form action="{{ route('pages.search') }}" method="GET" class="relative flex items-center w-full shadow-sm rounded-full border border-gray-300 bg-white focus-within:border-[#003366] focus-within:ring-1 focus-within:ring-[#003366] transition-all overflow-hidden">
                <div class="pl-4 flex items-center justify-center text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input 
                    type="text" 
                    name="q" 
                    value="{{ $keyword }}" 
                    class="w-full border-none pl-3 pr-5 py-2.5 text-sm focus:ring-0 outline-none bg-transparent text-gray-700 placeholder-gray-400" 
                    placeholder="Search & hit enter..." 
                    required
                >
            </form>
        </div>

    </div>
    {{-- 🔴 END HEADER --}}

    {{-- Results List --}}
    <div class="w-full space-y-6">
        @forelse($pages as $page)
            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                <a href="{{ route('frontend.page', $page->slug) }}" class="block mb-2">
                    {{-- FIXED: Black titles with a blue hover effect --}}
                    <h2 class="text-xl font-bold text-gray-900 hover:text-blue-800 transition-colors uppercase">{{ $page->title }}</h2>
                </a>
                <p class="text-gray-600 text-[15px] leading-relaxed">
                    {{-- FIXED: Added html_entity_decode to clean up &nbsp; and other html codes --}}
                    {{ Str::limit(html_entity_decode(strip_tags($page->content)), 250) }}
                </p>
                <div class="mt-4">
                    <a href="{{ route('frontend.page', $page->slug) }}" class="text-sm font-bold text-[#003366] uppercase tracking-wider hover:underline">Read Page &rarr;</a>
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-gray-50 rounded-lg border border-dashed border-gray-300 text-gray-500">
                No pages found matching "{{ $keyword }}". Please try a different search term.
            </div>
        @endforelse
    </div>

    <div class="mt-10">
        {{ $pages->appends(request()->query())->links() }}
    </div>

</div>
@endsection