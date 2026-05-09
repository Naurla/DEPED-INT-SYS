@extends('layouts.app')

@section('content')

{{-- Breadcrumb --}}
<div class="bg-gray-100 border-b border-gray-200 w-full overflow-hidden">
    <div class="container mx-auto px-4 md:px-20 max-w-10xl py-3 text-xs sm:text-sm text-gray-600 overflow-x-auto whitespace-nowrap hide-scroll">
        <a href="/" class="hover:text-[#003366] transition">Home</a>
        <span class="mx-2">></span>
        <span class="text-gray-900 font-bold">{{ $type_name ?? 'ALS Stories' }}</span>
    </div>
</div>

{{-- Main Container --}}
<div class="container mx-auto px-4 md:px-20 max-w-10xl py-12 w-full min-h-screen">
    
    <div class="mb-8 md:mb-12 text-left w-full break-words border-b border-gray-100 pb-6">
        <h1 class="text-2xl md:text-3xl font-sans font-bold text-gray-900 tracking-wide uppercase">
            {{ $type_name ?? 'ALS STORIES' }}
        </h1>
    </div>

    {{-- 🟢 NEW: Auto-Submitting Filter Bar --}}
    <div class="mb-10 w-full bg-gray-50 p-5 rounded-lg border border-gray-200 shadow-sm">
        <form action="{{ url()->current() }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            
            <div class="w-full md:flex-[2]">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Search Keyword</label>
                <input 
                    type="text" 
                    name="search" 
                    class="w-full border-gray-300 rounded-md shadow-sm px-4 py-3 text-sm focus:border-[#003366] focus:ring focus:ring-[#003366] focus:ring-opacity-20 transition-all" 
                    placeholder="Search title or content..." 
                    value="{{ request('search') }}"
                >
            </div>

            <div class="w-full md:flex-1">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Filter Year</label>
                <select name="year" onchange="this.form.submit()" class="w-full border-gray-300 rounded-md shadow-sm px-4 py-3 text-sm focus:border-[#003366] focus:ring focus:ring-[#003366] focus:ring-opacity-20 transition-all cursor-pointer">
                    <option value="">All Years</option>
                    @php $currentYear = date('Y'); @endphp
                    @for($i = $currentYear; $i >= $currentYear - 5; $i--)
                        <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div class="w-full md:flex-1">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Filter Month</label>
                <select name="month" onchange="this.form.submit()" class="w-full border-gray-300 rounded-md shadow-sm px-4 py-3 text-sm focus:border-[#003366] focus:ring focus:ring-[#003366] focus:ring-opacity-20 transition-all cursor-pointer">
                    <option value="">All Months</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ request('month') == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="w-full md:w-auto flex gap-2">
                <button type="submit" class="w-full md:w-auto bg-[#003366] hover:bg-blue-900 text-white font-bold py-3 px-8 rounded-md uppercase text-xs tracking-wider transition-colors shadow-sm">
                    Search
                </button>

                @if(request()->filled('search') || request()->filled('year') || request()->filled('month'))
                    <a href="{{ url()->current() }}" class="flex items-center justify-center w-full md:w-auto bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 px-6 rounded-md uppercase text-xs tracking-wider transition-colors shadow-sm">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>
    {{-- 🔴 END Filter Bar --}}

    {{-- w-full applied to consume the whole page --}}
    <div class="w-full space-y-12">
        @forelse($items as $item)
            <div class="border-b border-gray-100 pb-10 last:border-0 group">
                
                {{-- Date and Title Heading --}}
                <a href="{{ route('als-stories.show', ['id' => $item->id]) }}" class="block mb-4">
                    <h2 class="text-[1.3rem] font-bold text-gray-900 uppercase tracking-tight hover:text-gray-700 transition-colors leading-snug">
                        {{ strtoupper($item->created_at->format('F d, Y')) }} - {{ strtoupper($item->title) }}
                    </h2>
                </a>
                
                {{-- Description Preview --}}
                <div class="text-[15px] text-gray-600 mb-6 leading-relaxed w-full">
                    {{ Str::limit(strip_tags($item->content), 250) }}
                </div>

                {{-- Action Row --}}
                <div class="flex items-center gap-6">
                    {{-- Read More Button --}}
                    <a href="{{ route('als-stories.show', ['id' => $item->id]) }}" 
                       class="border border-gray-200 px-6 py-2.5 text-xs font-bold uppercase tracking-widest text-gray-700 hover:bg-gray-50 transition shadow-sm rounded-sm">
                        READ MORE
                    </a>

                    {{-- Posted Meta Tag --}}
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">
                        POSTED: {{ strtoupper($item->created_at->format('M d, Y')) }}
                    </span>
                </div>
            </div>
        @empty
            <div class="text-gray-500 font-sans text-[15px] bg-gray-50 p-12 rounded-xl border border-dashed border-gray-300 text-center">
                No {{ strtolower($type_name ?? 'items') }} found matching your search.
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-12 w-full">
        {{ $items->appends(request()->query())->links() }}
    </div>

</div>

<style>
    .hide-scroll::-webkit-scrollbar { display: none; }
    .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection