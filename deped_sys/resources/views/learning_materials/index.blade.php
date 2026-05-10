@extends('layouts.app')

@section('content')

{{-- Breadcrumb --}}
<div class="bg-gray-100 border-b border-gray-200 w-full overflow-hidden">
    <div class="container mx-auto px-4 md:px-20 max-w-10xl py-3 text-xs sm:text-sm text-gray-600 overflow-x-auto whitespace-nowrap hide-scroll">
        <a href="/" class="hover:text-[#003366] transition">Home</a>
        <span class="mx-2">></span>
        <span>K to 12</span>
        <span class="mx-2">></span>
        <span class="text-gray-900 font-bold">Learning Materials</span>
    </div>
</div>

{{-- Main Container --}}
<div class="container mx-auto px-4 md:px-20 max-w-10xl py-12 w-full min-h-screen">
    
    {{-- Wrap the header and filters in a single form --}}
    <form action="{{ url()->current() }}" method="GET">
        
        {{-- Header Section: Title on Left, Controls on Right --}}
        <div class="mb-10 w-full border-b border-gray-100 pb-6 flex flex-col lg:flex-row lg:items-end justify-between gap-4">
            
            <div class="shrink-0">
                <h1 class="text-2xl md:text-3xl font-sans font-bold text-gray-900 tracking-wide uppercase">
                    LEARNING MATERIALS
                </h1>
            </div>

            {{-- Unified Filter & Search Bar moved to Top Right --}}
            <div class="flex flex-col sm:flex-row flex-wrap items-center gap-3 w-full lg:w-auto lg:justify-end">

                {{-- Search Bar with Icon --}}
                <div class="w-full sm:w-64 relative">
                    <label class="sr-only">Search Keyword</label>
                    
                    {{-- Magnifying Glass SVG Icon --}}
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <input 
                        type="text" 
                        name="search" 
                        class="w-full border-gray-400 rounded-full shadow-sm pl-10 pr-5 py-2.5 text-sm focus:border-[#003366] focus:ring focus:ring-[#003366] focus:ring-opacity-20 transition-all bg-white" 
                        placeholder="Search & hit Enter..." 
                        value="{{ request('search') }}"
                    >
                </div>

                {{-- Filter Year --}}
                <select name="year" onchange="this.form.submit()" class="w-full sm:w-auto border-gray-400 rounded-full shadow-sm px-5 py-2.5 text-sm focus:border-[#003366] focus:ring focus:ring-[#003366] focus:ring-opacity-20 transition-all cursor-pointer bg-white">
                    <option value="">All Years</option>
                    @php $currentYear = date('Y'); @endphp
                    @for($i = $currentYear; $i >= $currentYear - 5; $i--)
                        <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>

                {{-- Filter Month --}}
                <select name="month" onchange="this.form.submit()" class="w-full sm:w-auto border-gray-400 rounded-full shadow-sm px-5 py-2.5 text-sm focus:border-[#003366] focus:ring focus:ring-[#003366] focus:ring-opacity-20 transition-all cursor-pointer bg-white">
                    <option value="">All Months</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ request('month') == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endforeach
                </select>

                {{-- Clear Button --}}
                @if(request()->filled('search') || request()->filled('year') || request()->filled('month'))
                    <a href="{{ url()->current() }}" title="Clear Filters" class="flex items-center justify-center w-full sm:w-auto bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2.5 px-6 rounded-full uppercase text-xs tracking-wider transition-colors shadow-sm">
                        Clear
                    </a>
                @endif

            </div>
        </div>
    </form>

    {{-- Changed from lg:w-3/4 to w-full so it spans the whole page --}}
    <div class="w-full space-y-12">
        @forelse($materials as $material)
            <div class="border-b border-gray-100 pb-10 last:border-0 group">
                
                {{-- Date and Title Heading --}}
                <a href="{{ route('learning_materials.show', $material->id) }}" class="block mb-4">
                    <h2 class="text-[1.3rem] font-bold text-gray-900 uppercase tracking-tight hover:text-gray-700 transition-colors leading-snug">
                        {{ strtoupper($material->created_at->format('F d, Y')) }} - {{ strtoupper($material->title) }}
                    </h2>
                </a>
                
                {{-- Description Preview --}}
                <div class="text-[15px] text-gray-600 mb-6 leading-relaxed w-full">
                    {{ Str::limit(strip_tags($material->description), 250) }}
                </div>

                {{-- Action Row --}}
                <div class="flex items-center gap-6">
                    {{-- Read More Button --}}
                    <a href="{{ route('learning_materials.show', $material->id) }}" 
                       class="border border-gray-200 px-6 py-2.5 text-xs font-bold uppercase tracking-widest text-gray-700 hover:bg-gray-50 transition shadow-sm rounded-sm">
                        READ MORE
                    </a>

                    {{-- Posted Meta Tag --}}
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">
                        POSTED: {{ strtoupper($material->created_at->format('M d, Y')) }}
                    </span>
                </div>
            </div>
        @empty
            <div class="text-gray-500 font-sans text-[15px] bg-gray-50 p-12 rounded-xl border border-dashed border-gray-300 text-center">
                No learning materials found matching your search.
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-12 w-full">
        {{ $materials->appends(request()->query())->links() }}
    </div>

</div>

<style>
    .hide-scroll::-webkit-scrollbar { display: none; }
    .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection