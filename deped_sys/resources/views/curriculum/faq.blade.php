@extends('layouts.app')

@section('content')

{{-- Breadcrumb matching the reference layout padding (md:px-20) --}}
<div class="bg-gray-100 border-b border-gray-200 w-full overflow-hidden">
    <div class="container mx-auto px-4 md:px-20 max-w-10xl py-3 text-xs sm:text-sm text-gray-600 overflow-x-auto whitespace-nowrap hide-scroll">
        <a href="/" class="hover:text-[#003366] transition">Home</a>
        <span class="mx-2">></span>
        <span>K to 12</span>
        <span class="mx-2">></span>
        <span>About</span>
        <span class="mx-2">></span>
        <span class="text-gray-900 font-bold">Frequently Asked Questions</span>
    </div>
</div>

{{-- Main Container (using the exact same md:px-20 layout padding) --}}
<div class="container mx-auto px-4 md:px-20 max-w-10xl py-8 md:py-12 w-full overflow-hidden min-h-screen">
    
    {{-- Header Section --}}
    <div class="mb-6 md:mb-10 text-left w-full break-words">
        <h1 class="text-2xl md:text-3xl font-sans font-bold text-gray-900 tracking-wide uppercase">Frequently Asked Questions</h1>
        <p class="mt-2 text-gray-600 text-[15px]">Find answers to common questions about the K to 12 Curriculum.</p>
    </div>

    {{-- FAQ Accordion Section (Now spanning full width) --}}
    <div class="w-full">
        <div class="space-y-4">
            @forelse($faqs as $faq)
                <div x-data="{ expanded: false }" class="bg-white border border-gray-200 rounded-lg shadow-sm">
                    <button @click="expanded = !expanded" class="w-full flex items-center justify-between px-6 py-4 focus:outline-none hover:bg-gray-50 transition-colors">
                        <span class="text-lg font-bold text-gray-900 text-left">{{ $faq->question }}</span>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-200 flex-shrink-0 ml-4" :class="{'rotate-180': expanded}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    
                    <div x-show="expanded" x-collapse x-cloak>
                        <div class="px-8 pb-5 pt-2 border-t border-gray-100">
                            <ul class="list-disc pl-5 space-y-2 text-[15px] text-gray-700 leading-relaxed marker:text-gray-400">
                                {{-- Check if it's an array (JSON cast) or a string (Newline separated) --}}
                                @php
                                    $answers = is_array($faq->answer) ? $faq->answer : explode("\n", $faq->answer);
                                @endphp

                                @foreach($answers as $line)
                                    @if(trim($line) != '')
                                        <li>{{ $line }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 py-8 bg-white border border-gray-200 rounded-lg shadow-sm font-sans text-[15px]">
                    Check back later. We are currently updating our FAQs.
                </div>
            @endforelse
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