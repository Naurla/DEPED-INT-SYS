@extends('layouts.app')

@section('content')

<div class="bg-gray-100 border-b border-gray-200 w-full overflow-hidden">
    <div class="container mx-auto px-4 md:px-20 max-w-10xl py-3 text-xs sm:text-sm text-gray-600 overflow-x-auto whitespace-nowrap hide-scroll">
        <a href="/" class="hover:text-[#003366] transition">Home</a>
        <span class="mx-2">></span>
        <span>K to 12</span>
        <span class="mx-2">></span>
        <span class="text-gray-900 font-bold">{{ $page_title ?? 'Junior High School Curriculum' }}</span>
    </div>
</div>

<div class="container mx-auto px-4 md:px-20 max-w-10xl py-12 w-full min-h-screen">
    
    <form id="filterForm" method="GET" action="{{ url()->current() }}">
        <input type="hidden" name="tab" id="tabInput" value="{{ $tab }}">
        {{-- Hidden input to remember which document is open --}}
        <input type="hidden" name="expand" id="expandInput" value="{{ request('expand') }}">
        
        {{-- Header Section: Title on Left, Controls on Right --}}
        <div class="w-full flex flex-col lg:flex-row lg:items-end justify-between gap-4 mb-6">
            
            <div class="shrink-0">
                <h1 class="text-2xl md:text-3xl font-sans font-bold text-gray-900 tracking-wide uppercase">
                    Junior High School Curriculum
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
                        onkeydown="if(event.key === 'Enter'){ event.preventDefault(); document.getElementById('filterForm').submit(); }"
                    >
                </div>

                {{-- Filter District --}}
                @if(!empty($districts))
                    <select name="district" onchange="document.getElementById('filterForm').submit()" class="w-full sm:w-auto border-gray-400 rounded-full shadow-sm px-5 py-2.5 text-sm focus:border-[#003366] focus:ring focus:ring-[#003366] focus:ring-opacity-20 transition-all cursor-pointer bg-white">
                        <option value="">All Districts</option>
                        @foreach($districts as $districtOption)
                            <option value="{{ $districtOption }}" {{ $districtFilter === $districtOption ? 'selected' : '' }}>
                                {{ $districtOption }}
                            </option>
                        @endforeach
                    </select>
                @endif

                {{-- Clear Button --}}
                @if(request()->filled('search') || request()->filled('district'))
                    <a href="{{ url()->current() }}?tab={{ $tab }}" title="Clear Filters" class="flex items-center justify-center w-full sm:w-auto bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2.5 px-6 rounded-full uppercase text-xs tracking-wider transition-colors shadow-sm">
                        Clear
                    </a>
                @endif

            </div>
        </div>

        {{-- Tabs --}}
        <div class="flex overflow-x-auto hide-scroll mb-10 border-b border-gray-200">
            <button type="button" onclick="setTab('public')"
                    class="{{ $tab === 'public' ? 'border-[#003366] text-[#003366]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} py-4 px-6 font-bold text-sm border-b-2 uppercase tracking-wider whitespace-nowrap transition-colors">
                Public Schools
            </button>
            <button type="button" onclick="setTab('private')"
                    class="{{ $tab === 'private' ? 'border-[#003366] text-[#003366]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} py-4 px-6 font-bold text-sm border-b-2 uppercase tracking-wider whitespace-nowrap transition-colors">
                Private Schools
            </button>
        </div>
    </form>

    {{-- Content Loop (Handles both Public and Private dynamically) --}}
    <div class="space-y-12 w-full">
        @forelse($contents as $item)
            @php
                $extension = strtolower(pathinfo($item->csv_path, PATHINFO_EXTENSION));
                $hasTableData = !empty($item->tableHeader) && $item->tableData;
                $isPdf = $extension === 'pdf';
                $isOfficeDoc = in_array($extension, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']);
                $canPreview = $hasTableData || $isPdf || $isOfficeDoc;
                
                $pageParam = 'page_' . $item->id;
                $isExpanded = (request()->has($pageParam) || request('expand') == $item->id) ? 'true' : 'false';
                $fileUrl = asset('storage/' . $item->csv_path);
            @endphp

            <div id="item-{{ $item->id }}" x-data="{ expanded: {{ $isExpanded }} }" class="border-b border-gray-100 pb-10 last:border-0">
                <h2 class="text-xl md:text-[1.35rem] font-extrabold text-gray-900 uppercase tracking-tight mb-2">
                    {{ $item->created_at->format('F d, Y') }} - {{ $item->title }}
                </h2>
                
                <div class="text-[15px] text-gray-600 mb-6 max-w-4xl leading-relaxed">
                    {{ $item->content }}
                </div>

                <div class="flex items-center gap-4 mt-4">
                    @if($canPreview)
                        {{-- UPDATED: Records the item ID into the hidden input when opened --}}
                        <button @click="expanded = !expanded; document.getElementById('expandInput').value = expanded ? '{{ $item->id }}' : ''" 
                                class="border border-[#003366] px-6 py-2 text-xs font-bold uppercase tracking-widest text-[#003366] hover:bg-blue-50 transition shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path x-show="!expanded" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path x-show="!expanded" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                <path x-show="expanded" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88L3 3m12 12l6.88 6.88" />
                            </svg>
                            <span x-text="expanded ? 'Hide Preview' : 'Preview Document'"></span>
                        </button>
                    @endif

                    @if($item->csv_path)
                        <a href="{{ $fileUrl }}" download
                           class="bg-gray-800 border border-gray-800 px-6 py-2 text-xs font-bold uppercase tracking-widest text-white hover:bg-black transition shadow-sm flex items-center gap-2">
                           <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                           </svg>
                           Download File
                        </a>
                    @endif
                </div>

                <div x-show="expanded" x-collapse x-cloak class="mt-8 bg-white p-1 rounded-lg border border-gray-200 shadow-inner">
                    @if($hasTableData)
                        <div class="overflow-x-auto custom-scrollbar">
                            <table class="min-w-full border-collapse text-sm text-left">
                                <thead>
                                    <tr class="bg-gray-100">
                                        @foreach($item->tableHeader as $header)
                                            <th class="border-b border-r border-gray-300 px-5 py-4 text-gray-800 font-black uppercase tracking-widest text-[10px] whitespace-nowrap last:border-r-0">
                                                {{ $header }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($item->tableData as $row)
                                        <tr class="hover:bg-blue-50/50 transition-colors">
                                            @foreach($row as $cell)
                                                <td class="border-r border-gray-100 px-5 py-3 text-[13px] text-gray-700 whitespace-nowrap last:border-r-0">
                                                    {{ $cell }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 border-t border-gray-100 bg-gray-50">
                            {{-- Keeps URL parameters active during pagination --}}
                            {{ $item->tableData->appends(array_merge(request()->query(), ['expand' => $item->id]))->fragment('item-' . $item->id)->links() }}
                        </div>
                    @elseif($isPdf)
                        <div class="w-full h-[700px] rounded-md overflow-hidden bg-gray-100 flex items-center justify-center">
                            <iframe src="{{ $fileUrl }}#toolbar=0" class="w-full h-full border-0"></iframe>
                        </div>
                    @elseif($isOfficeDoc)
                        <div class="w-full h-[700px] rounded-md overflow-hidden bg-gray-100 flex flex-col">
                            <div class="bg-yellow-50 text-yellow-800 text-xs p-3 border-b border-yellow-200">
                                <strong>Note:</strong> Document preview requires a public internet connection. If you are developing locally, download the file instead.
                            </div>
                            <iframe src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode(url($fileUrl)) }}" class="w-full h-full border-0"></iframe>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-gray-500 font-sans text-[15px] text-center py-20 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                No curriculum data available for the selected filters.
            </div>
        @endforelse
    </div>
</div>

<script>
    function setTab(tabName) {
        document.getElementById('tabInput').value = tabName;
        // Clear the expand state when switching tabs so it doesn't try to open an irrelevant item
        document.getElementById('expandInput').value = ''; 
        document.getElementById('filterForm').submit();
    }
</script>

<style>
    .hide-scroll::-webkit-scrollbar { display: none; }
    .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
    [x-cloak] { display: none !important; }
    .custom-scrollbar::-webkit-scrollbar { height: 8px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
</style>
@endsection