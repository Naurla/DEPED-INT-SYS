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
    
    <div class="mb-8 border-b border-gray-100 pb-6">
        {{-- 🟢 NEW: Go Back Button --}}
        <div class="mb-4">
            <a href="javascript:history.back()" class="inline-flex items-center text-xs font-bold text-gray-500 hover:text-[#a52a2a] transition-colors uppercase tracking-wider cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Go Back
            </a>
        </div>
        {{-- 🔴 END Go Back Button --}}

        <h1 class="text-2xl md:text-3xl font-sans font-bold text-gray-900 tracking-wide uppercase">
            Search Results for "{{ $keyword }}"
        </h1>
    </div>

    {{-- The Search Bar so they can search again --}}
    <div class="mb-10 w-full bg-gray-50 p-5 rounded-lg border border-gray-200 shadow-sm">
        <form action="{{ route('pages.search') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="w-full flex-grow">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Search Again</label>
                <input type="text" name="q" value="{{ $keyword }}" class="w-full border-gray-300 rounded-md shadow-sm px-4 py-3 text-sm focus:border-[#003366] focus:ring focus:ring-[#003366] focus:ring-opacity-20 transition-all" required>
            </div>
            <div class="w-full md:w-auto">
                <button type="submit" class="w-full md:w-auto bg-[#003366] hover:bg-blue-900 text-white font-bold py-3 px-8 rounded-md uppercase text-xs tracking-wider transition-colors shadow-sm">Search</button>
            </div>
        </form>
    </div>

    {{-- Results List --}}
    <div class="w-full space-y-6">
        @forelse($pages as $page)
            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                {{-- FIXED: Pointing to the named route passing the slug --}}
                <a href="{{ route('frontend.page', $page->slug) }}" class="block mb-2">
                    <h2 class="text-xl font-bold text-[#a52a2a] hover:underline uppercase">{{ $page->title }}</h2>
                </a>
                <p class="text-gray-600 text-[15px] leading-relaxed">
                    {{ Str::limit(strip_tags($page->content), 250) }}
                </p>
                <div class="mt-4">
                    {{-- FIXED: Pointing to the named route passing the slug --}}
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