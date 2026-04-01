@extends('layouts.app')

@section('content')

{{-- Breadcrumb matching the reference layout padding (md:px-20) --}}
<div class="bg-gray-100 border-b border-gray-200 w-full overflow-hidden">
    <div class="container mx-auto px-4 md:px-20 max-w-10xl py-3 text-xs sm:text-sm text-gray-600 overflow-x-auto whitespace-nowrap hide-scroll">
        <a href="/" class="hover:text-[#003366] transition">Home</a>
        <span class="mx-2">></span>
        <span>K to 12</span>
        <span class="mx-2">></span>
        <span class="text-gray-900 font-bold">{{ $page_title ?? 'Senior High Curriculum' }}</span>
    </div>
</div>

{{-- Main Container (using the exact same md:px-20 layout padding) --}}
<div class="container mx-auto px-4 md:px-20 max-w-10xl py-8 md:py-12 w-full overflow-hidden min-h-screen">
    
    {{-- Header Section (Aligned naturally to the padding bounds) --}}
    <div class="mb-8 md:mb-12 text-left w-full break-words border-b border-gray-100 pb-4">
        <h1 class="text-2xl md:text-3xl font-sans font-bold text-gray-900 tracking-wide uppercase">
            List of Senior High School Curriculum Content
        </h1>
    </div>

    {{-- Used full width here instead of 3/4 so tables don't get squished --}}
    <div class="w-full">
        @forelse($contents as $item)
            <div class="mb-16">
                
                {{-- Item Title --}}
                <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-4 uppercase tracking-wide">
                    {{ $item->title }}
                </h2>
                
                {{-- Item Description (Restricted width for readability) --}}
                <div class="text-[15px] text-gray-700 leading-relaxed mb-8 max-w-5xl whitespace-pre-line">
                    {!! nl2br(e($item->content)) !!}
                </div>

                {{-- Table Section --}}
                @if(!empty($item->tableHeader) && $item->tableData)
                    <div class="overflow-x-auto rounded-lg border border-gray-300 shadow-sm">
                        <table class="min-w-full border-collapse text-sm text-left">
                            <thead>
                                <tr>
                                    @foreach($item->tableHeader as $header)
                                        <th class="border-b border-r border-gray-300 px-5 py-4 bg-gray-100 text-gray-800 font-bold uppercase tracking-widest text-[12px] last:border-r-0">
                                            {{ $header }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                {{-- We loop directly over tableData, which is now the Paginator --}}
                                @foreach($item->tableData as $row)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        @foreach($row as $cell)
                                            <td class="border-b border-r border-gray-200 px-5 py-3 text-[14px] text-gray-700 leading-relaxed last:border-r-0">
                                                {{ $cell }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination Links right under the specific table --}}
                    <div class="mt-6">
                        {{ $item->tableData->appends(request()->except('page_' . $item->id))->links() }}
                    </div>
                @endif
            </div>
        @empty
            <div class="text-gray-500 font-sans text-[15px] bg-gray-50 p-8 rounded-lg border border-dashed border-gray-200 text-center">
                No Senior High School curriculum data available yet.
            </div>
        @endforelse
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