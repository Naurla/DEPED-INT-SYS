@extends('layouts.app')

@section('content')
    {{-- BANNER SECTION --}}
    <div class="container mx-auto mt-6 px-4 flex justify-center">
        {{-- w-full on mobile, w-[90%] (90%) on large screens. Fixed heights keep the box constant. --}}
        <div class="relative w-full lg:w-[90%] h-[300px] md:h-[450px] lg:h-[600px] overflow-hidden rounded-xl bg-gray-100 shadow-sm"
             x-data="{ activeSlide: 1, slides: {{ $banners->toJson() ?? '[]' }} }" 
             x-init="if(slides.length > 1) { setInterval(() => { activeSlide = activeSlide === slides.length ? 1 : activeSlide + 1 }, 5000) }">
            
            {{-- Slides --}}
            <template x-for="(slide, index) in slides" :key="index">
                <div x-show="activeSlide === index + 1" 
                     x-transition:enter="transition opacity duration-1000"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition opacity duration-1000"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="absolute inset-0 flex items-center justify-center bg-gray-100">
                     
                    {{-- object-contain ensures small/large images fit inside the box without stretching or cropping --}}
                    <img :src="slide" alt="Hero Banner" class="w-full h-full object-contain object-center drop-shadow-sm">
                </div>
            </template>
            
            {{-- Navigation Dots --}}
            <div x-show="slides.length > 1" class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2 z-10" x-cloak>
                <template x-for="(slide, index) in slides" :key="index">
                    <button @click="activeSlide = index + 1" :class="activeSlide === index + 1 ? 'bg-red-700 w-8' : 'bg-gray-300 w-3'" class="h-3 rounded-full transition-all duration-300 shadow-sm focus:outline-none"></button>
                </template>
            </div>
        </div>
    </div>

    {{-- ADVISORY SECTION --}}
    <section class="container mx-auto mt-16 px-4 mb-24">
        <div class="flex justify-center">
            @if(isset($latestAdvisory))
                <div class="w-full max-w-[800px]"> 
                    <a href="{{ asset('storage/' . $latestAdvisory->pdf_path) }}" target="_blank" class="block transition hover:opacity-90 flex justify-center">
                        <img src="{{ asset('storage/' . $latestAdvisory->image_path) }}" 
                             alt="Latest Advisory" 
                             class="w-full max-h-[500px] md:max-h-[750px] object-contain object-center rounded-lg drop-shadow-md">
                    </a>
                </div>
            @else
                <div class="flex flex-col items-center py-24 w-full max-w-[800px]">
                    <svg class="w-20 h-20 text-gray-200 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2zM14 4v4h4" />
                    </svg>
                    <p class="text-gray-400 font-black uppercase tracking-widest">No Recent Advisories Found</p>
                </div>
            @endif
        </div>
    </section>
@endsection