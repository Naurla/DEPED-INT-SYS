@extends('layouts.app')

@section('content')

{{-- Breadcrumb --}}
<div class="bg-gray-100 border-b border-gray-200 w-full overflow-hidden">
    <div class="container mx-auto px-4 md:px-20 max-w-10xl py-3 text-xs sm:text-sm text-gray-600 overflow-x-auto whitespace-nowrap hide-scroll">
        <a href="/" class="hover:text-[#003366] transition">Home</a>
        <span class="mx-2">></span>
        <span>K to 12</span>
        <span class="mx-2">></span>
        <span class="text-gray-900 font-bold">{{ $page_title ?? 'High School Curriculum' }}</span>
    </div>
</div>

{{-- Main Container --}}
<div class="container mx-auto px-4 md:px-20 max-w-10xl py-12 w-full min-h-screen">
    
    <div class="mb-12 text-left w-full break-words">
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight uppercase">
            Junior High School Curriculum
        </h1>
    </div>

    <div class="space-y-12">
        @forelse($contents as $item)
            @php
                $extension = pathinfo($item->csv_path, PATHINFO_EXTENSION);
                $isCsv = strtolower($extension) === 'csv';
                $isWord = in_array(strtolower($extension), ['doc', 'docx']);
                $isPdf = strtolower($extension) === 'pdf';
                $isExcel = in_array(strtolower($extension), ['xls', 'xlsx']);
            @endphp

            <div x-data="{ expanded: false }" class="border-b border-gray-100 pb-10 last:border-0">
                {{-- Date and Title Heading (Design Match) --}}
                <h2 class="text-xl font-bold text-gray-900 uppercase tracking-tight mb-2">
                    {{ $item->created_at->format('F d, Y') }} - {{ $item->title }}
                </h2>
                
                {{-- Description Preview --}}
                <div class="text-[15px] text-gray-600 mb-6 max-w-4xl leading-relaxed">
                    {{ Str::limit($item->content, 250) }}
                </div>

                {{-- Action Row --}}
                <div class="flex items-center gap-6">
                    @if($isCsv && !empty($item->tableHeader))
                        {{-- Read More Button for CSV Tables --}}
                        <button @click="expanded = !expanded" 
                                class="border border-gray-300 px-6 py-2 text-xs font-bold uppercase tracking-widest text-gray-700 hover:bg-gray-50 transition shadow-sm">
                            <span x-text="expanded ? 'Hide Content' : 'Read More'"></span>
                        </button>
                    @elseif($item->csv_path)
                        {{-- View Document Button (Opens in New Tab) --}}
                        <a href="{{ asset('storage/' . $item->csv_path) }}" target="_blank" rel="noopener noreferrer"
                           class="border border-gray-300 px-6 py-2 text-xs font-bold uppercase tracking-widest text-gray-700 hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                            @if($isPdf) <span class="text-red-600">VIEW PDF</span> 
                            @elseif($isWord) <span class="text-blue-600">VIEW WORD DOC</span> 
                            @else VIEW DOCUMENT @endif
                        </a>
                    @endif

                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">
                        Posted: {{ $item->created_at->format('M d, Y') }}
                    </span>
                </div>

                {{-- Collapsible Table Section (Only if file is CSV) --}}
                @if($isCsv && !empty($item->tableHeader))
                    <div x-show="expanded" x-collapse x-cloak class="mt-8">
                        <div class="overflow-x-auto rounded border border-gray-200 shadow-sm">
                            <table class="min-w-full border-collapse text-sm text-left">
                                <thead>
                                    <tr class="bg-gray-50">
                                        @foreach($item->tableHeader as $header)
                                            <th class="border-b border-r border-gray-200 px-5 py-4 text-gray-800 font-bold uppercase tracking-widest text-[11px] last:border-r-0">
                                                {{ $header }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    @foreach($item->tableData as $row)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            @foreach($row as $cell)
                                                <td class="border-b border-r border-gray-100 px-5 py-3 text-[14px] text-gray-700 last:border-r-0">
                                                    {{ $cell }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{-- CSV Table Pagination --}}
                        <div class="mt-4">
                            {{ $item->tableData->appends(request()->except('page_' . $item->id))->links() }}
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-gray-500 text-center py-20 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                No curriculum data available yet.
            </div>
        @endforelse
    </div>
</div>

<style>
    .hide-scroll::-webkit-scrollbar { display: none; }
    .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
    [x-cloak] { display: none !important; }
</style>
@endsection