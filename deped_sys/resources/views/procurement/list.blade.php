@extends('layouts.app')

@section('content')

{{-- Breadcrumb matching the reference layout padding (md:px-20) --}}
<div class="bg-gray-100 border-b border-gray-200 w-full overflow-hidden">
    <div class="container mx-auto px-4 md:px-20 max-w-10xl py-3 text-xs sm:text-sm text-gray-600 overflow-x-auto whitespace-nowrap hide-scroll">
        <a href="/" class="hover:text-[#003366] transition">Home</a>
        <span class="mx-2">></span>
        <span>Procurement</span>
        <span class="mx-2">></span>
        <span class="text-gray-900 font-bold">{{ $type_name ?? 'Bid Opportunities' }}</span>
    </div>
</div>

{{-- Main Container (Perfectly balanced left and right padding using md:px-20) --}}
<div class="container mx-auto px-4 md:px-20 max-w-10xl py-8 md:py-12 w-full overflow-hidden min-h-screen">
    
    {{-- Header Section --}}
    <div class="mb-6 md:mb-8 text-left w-full break-words border-b border-gray-100 pb-6">
        <h1 class="text-2xl md:text-3xl font-sans font-bold text-gray-900 tracking-wide uppercase">
            {{ $type_name ?? 'Bid Opportunities' }}
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
                    placeholder="Search by title or description..." 
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

    {{-- w-full ensures it consumes the page evenly, matching the exact right padding to the left padding --}}
    <div class="w-full">
        <div class="space-y-16">
            @forelse($items as $item)
                <div class="group transition-all duration-300">
                  
                    <a href="{{ route('procurement.show', ['category' => $category, 'id' => $item->id]) }}" class="block">
                        <h2 class="text-xl md:text-[1.35rem] font-extrabold text-gray-900 leading-snug uppercase group-hover:text-[#003366] transition-colors mb-4">
                            {{ strtoupper($item->display_title) }} @if($item->description) __  {{ $item->description }} @endif
                        </h2>
                    </a>
                    
                    <p class="text-gray-600 text-[15px] font-medium leading-relaxed mb-6">
                      {{ $item->display_title }} @if($item->description) -  {{ $item->description }} @endif
                    </p>
                    
                    <div class="flex items-center gap-6">
                        <a href="{{ route('procurement.show', ['category' => $category, 'id' => $item->id]) }}" class="inline-block border border-gray-300 text-gray-600 px-6 py-2 text-xs font-bold uppercase tracking-widest hover:bg-gray-50 hover:text-gray-900 transition-colors rounded-sm shadow-sm">
                            Read More
                        </a>
                        <span class="text-gray-400 text-[11px] font-bold uppercase tracking-widest">
                            Posted: {{ $item->date ? \Carbon\Carbon::parse($item->date)->format('M d, Y') : $item->created_at->format('M d, Y') }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-gray-500 font-sans text-[15px] bg-gray-50 p-6 rounded-lg border border-dashed border-gray-200">
                    No {{ strtolower($type_name ?? 'items') }} found matching your criteria.
                </div>
            @endforelse
        </div>

        {{-- Pagination Links --}}
        <div class="mt-16">
            {{ $items->appends(request()->query())->links() }}
        </div>
        
    </div>
</div>

<style>
    /* =========================================================
       HIDE SCROLLBARS (BUT KEEP CONTENT SCROLLABLE) for breadcrumb
       ========================================================= */
    .hide-scroll::-webkit-scrollbar {
        display: none; /* For Chrome, Safari, and Opera */
    }
    
    .hide-scroll {
        -ms-overflow-style: none;  /* For Internet Explorer and Edge */
        scrollbar-width: none;  /* For Firefox */
    }
</style>
@endsection